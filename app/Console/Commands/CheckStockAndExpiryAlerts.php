<?php

namespace App\Console\Commands;

use App\Services\TwilioWhatsAppService;
use App\Services\EmailNotificationService;
use App\Models\Item;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CheckStockAndExpiryAlerts extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'whatsapp:check-stock-expiry';

    /**
     * The console command description.
     */
    protected $description = 'Check for low stock and nearing expiry products, then send WhatsApp alerts to the owner';

    protected TwilioWhatsAppService $whatsapp;
    protected EmailNotificationService $email;

    public function __construct(TwilioWhatsAppService $whatsapp, EmailNotificationService $email)
    {
        parent::__construct();
        $this->whatsapp = $whatsapp;
        $this->email    = $email;
    }

    public function handle()
    {
        // Fetch the owner's whatsapp_number and email from the User model
        $owner = User::where('role', 'owner')->first();

        if (!$owner || empty($owner->whatsapp_number)) {
            $this->error('Owner WhatsApp number is missing in the database.');
            Log::channel('whatsapp_alerts')->warning('whatsapp:check-stock-expiry skipped - Owner WhatsApp number is missing.');
            return;
        }

        $ownerPhone = $owner->whatsapp_number;
        $ownerEmail = $owner->email ?? null; // Email is optional; alerts log a warning if missing

        $this->checkLowStock($ownerPhone, $ownerEmail);
        $this->checkExpiringProducts($ownerPhone, $ownerEmail);

        $this->info('Stock and expiry check completed.');
    }

    /**
     * Find items below their low-stock threshold and notify owner via WhatsApp + email.
     */
    protected function checkLowStock(string $ownerPhone, string $ownerEmail): void
    {
        $threshold = 10; // adjust as needed, or pull per-product threshold from DB

        $lowStockProducts = Item::where('quantity', '<=', $threshold)->get();
        $telegram = new \App\Services\TelegramService();

        foreach ($lowStockProducts as $product) {
            // WhatsApp notification
            $waResult = $this->whatsapp->notifyLowStock(
                $ownerPhone,
                $product->name,
                $product->quantity
            );
            $this->logResult('Low stock (WhatsApp)', $product->name, $waResult);

            // Email notification
            $emailResult = $this->email->sendLowStockAlert(
                $ownerEmail,
                $product->name,
                $product->quantity
            );
            $this->logResult('Low stock (Email)', $product->name, $emailResult);

            // Telegram notification
            $telegramMsg = "📦 *LOW STOCK ALERT*\n\nProduct: {$product->name}\nCurrent Stock: {$product->quantity}\nThreshold: {$threshold}";
            $tgResult = $telegram->send($telegramMsg, null, 'Markdown');
            $this->logResult('Low stock (Telegram)', $product->name, ['success' => (bool)$tgResult, 'error' => $tgResult ? '' : 'Failed']);
        }
    }

    /**
     * Find items expiring within the next N days and notify owner via WhatsApp + email.
     */
    protected function checkExpiringProducts(string $ownerPhone, string $ownerEmail): void
    {
        $daysAhead = 7; // alert if expiring within 7 days

        $expiringProducts = Item::whereDate('expiry_date', '<=', now()->addDays($daysAhead))
            ->whereDate('expiry_date', '>=', now())
            ->get();
        
        $telegram = new \App\Services\TelegramService();

        foreach ($expiringProducts as $product) {
            $expiryFormatted = $product->expiry_date->format('d/m/Y');

            // WhatsApp notification
            $waResult = $this->whatsapp->notifyExpiryNearing(
                $ownerPhone,
                $product->name,
                $expiryFormatted
            );
            $this->logResult('Expiry reminder (WhatsApp)', $product->name, $waResult);

            // Email notification
            $emailResult = $this->email->sendExpiryReminder(
                $ownerEmail,
                $product->name,
                $expiryFormatted
            );
            $this->logResult('Expiry reminder (Email)', $product->name, $emailResult);

            // Telegram notification
            $telegramMsg = "⏳ *EXPIRY REMINDER*\n\nProduct: {$product->name}\nExpiring On: {$expiryFormatted}";
            $tgResult = $telegram->send($telegramMsg, null, 'Markdown');
            $this->logResult('Expiry reminder (Telegram)', $product->name, ['success' => (bool)$tgResult, 'error' => $tgResult ? '' : 'Failed']);
        }
    }

    /**
     * Helper to log + print result of each notification attempt.
     */
    protected function logResult(string $type, string $productName, array $result): void
    {
        if ($result['success']) {
            $this->info("{$type} sent for: {$productName}");
        } else {
            $this->error("{$type} FAILED for: {$productName} - {$result['error']}");
            Log::channel('whatsapp_alerts')->error("{$type} failed", ['product' => $productName, 'error' => $result['error']]);
        }
    }
}
