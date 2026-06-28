<?php

namespace App\Console\Commands;

use App\Models\InventoryAlert;
use App\Models\Item;
use App\Models\Setting;
use App\Models\User;
use App\Services\CallMeBotService;
use App\Services\WhatsAppService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendInventoryAlerts extends Command
{
    protected $signature = 'inventory:send-alerts';

    protected $description = 'Send low stock and expiring item alerts to the owner via WhatsApp';

    public function handle()
    {
        // ── Resolve owner WhatsApp number ─────────────────────────────────────
        // Priority: 1) users.whatsapp_number (owner role, set via My Profile)
        //           2) settings table (legacy admin panel)
        //           3) .env config (last-resort fallback)
        $ownerUser  = User::where('role', 'owner')->first();
        $ownerPhone = trim((string) (
            ($ownerUser && !empty($ownerUser->whatsapp_number))
                ? $ownerUser->whatsapp_number
                : Setting::get('owner_whatsapp_number', config('services.twilio.owner_whatsapp_number'))
        ));

        $callMeBotPhone  = trim((string) Setting::get('callmebot_phone', config('services.callmebot.phone')));
        $callMeBotApiKey = trim((string) Setting::get('callmebot_api_key', config('services.callmebot.apikey')));
        $lowStockThreshold = Setting::get('low_stock_threshold', 10);
        $expiryWarningDays = Setting::get('expiry_warning_days', 30);

        if ($callMeBotPhone !== '' && $callMeBotApiKey !== '') {
            $messaging = new CallMeBotService();
        } elseif (config('services.twilio.sid') && config('services.twilio.token') && config('services.twilio.whatsapp_from')) {
            $messaging = new WhatsAppService();
        } else {
            Log::channel('whatsapp_alerts')->warning('No WhatsApp notification provider is configured.');
            $this->warn('No WhatsApp notification provider is configured.');
            return Command::SUCCESS;
        }

        if ($ownerPhone === '') {
            Log::channel('whatsapp_alerts')->warning(
                'Owner WhatsApp number is not configured. '
                . 'Set it via My Profile → WhatsApp Number, or in the Settings panel.'
            );
            $this->warn('Owner WhatsApp number is not configured. Set it via My Profile → WhatsApp Number.');
            return Command::SUCCESS;
        }

        $today = now()->startOfDay();
        $expiryThreshold = now()->addDays($expiryWarningDays)->endOfDay();

        $lowStockItems = Item::where(function ($query) use ($lowStockThreshold) {
            $query->whereNotNull('reorder_point')
                ->whereColumn('quantity', '<=', 'reorder_point')
                ->orWhere(function ($query) use ($lowStockThreshold) {
                    $query->whereNull('reorder_point')
                        ->where('quantity', '<=', $lowStockThreshold);
                });
        })->get();

        foreach ($lowStockItems as $item) {
            $alert = InventoryAlert::firstOrCreate([
                'item_id' => $item->id,
                'alert_type' => 'low_stock',
                'sent_on' => $today->toDateString(),
            ]);

            if (!$alert->wasRecentlyCreated) {
                continue;
            }

            try {
                if ($messaging instanceof CallMeBotService) {
                    $messaging->sendLowStockAlert($item, $ownerPhone);
                } else {
                    $message = "⚠ LOW STOCK ALERT\n\nItem: {$item->name}\nCurrent Stock: {$item->quantity}\nReorder Point: {$item->reorder_point}\n\nPlease create a Purchase Order to replenish stock.";
                    $messaging->sendMessage($ownerPhone, $message);
                }
                Log::channel('whatsapp_alerts')->info('Low stock alert sent', [
                    'item_id' => $item->id,
                    'item_name' => $item->name,
                    'quantity' => $item->quantity,
                    'rop_threshold' => $item->reorder_point,
                ]);
                $this->info("Sent low stock alert for {$item->name}");
            } catch (\Throwable $e) {
                Log::channel('whatsapp_alerts')->error('Failed to send low stock alert', [
                    'item_id' => $item->id,
                    'item_name' => $item->name,
                    'error' => $e->getMessage(),
                ]);
                $this->error("Failed to send low stock alert for {$item->name}: {$e->getMessage()}");
            }
        }

        $expiringItems = Item::whereNotNull('expiry_date')
            ->whereBetween('expiry_date', [$today, $expiryThreshold])
            ->get();

        foreach ($expiringItems as $item) {
            $alert = InventoryAlert::firstOrCreate([
                'item_id' => $item->id,
                'alert_type' => 'expiry',
                'sent_on' => $today->toDateString(),
            ]);

            if (!$alert->wasRecentlyCreated) {
                continue;
            }

            try {
                if ($messaging instanceof CallMeBotService) {
                    $daysRemaining = now()->diffInDays($item->expiry_date);
                    $messaging->sendExpiryAlert($item, $ownerPhone, $daysRemaining);
                } else {
                    $daysRemaining = now()->diffInDays($item->expiry_date);
                    $message = "⏰ EXPIRY REMINDER\n\nItem: {$item->name}\nExpiry Date: {$item->expiry_date->format('Y-m-d')}\nDays Remaining: {$daysRemaining}\n\nPlease inspect and take appropriate action.";
                    $messaging->sendMessage($ownerPhone, $message);
                }
                Log::channel('whatsapp_alerts')->info('Expiry alert sent', [
                    'item_id' => $item->id,
                    'item_name' => $item->name,
                    'expiry_date' => $item->expiry_date->format('Y-m-d'),
                    'quantity' => $item->quantity,
                ]);
                $this->info("Sent expiry alert for {$item->name}");
            } catch (\Throwable $e) {
                Log::channel('whatsapp_alerts')->error('Failed to send expiry alert', [
                    'item_id' => $item->id,
                    'item_name' => $item->name,
                    'error' => $e->getMessage(),
                ]);
                $this->error("Failed to send expiry alert for {$item->name}: {$e->getMessage()}");
            }
        }

        return Command::SUCCESS;
    }
}
