<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Services\TelegramService;
use Carbon\Carbon;

class CheckExpiryAlert extends Command
{
    protected $signature = 'expiry:check';
    protected $description = 'Check product expiry and send Telegram alerts';

    public function handle()
    {
        $telegram = new TelegramService();

        $products = Product::where('alert_sent', false)
            ->whereDate('expiry_date', '<=', Carbon::now()->addDays(30))
            ->get();

        if ($products->isEmpty()) {
            $this->info('No products expiring soon that require alerts.');
            return;
        }

        $today = Carbon::now();

        foreach ($products as $product) {

            $daysLeft = $today->diffInDays($product->expiry_date, false);

            if ($daysLeft <= 30) {
                $telegram->send(
                    "⚠️ EXPIRY ALERT\n\n" .
                    "Product: {$product->name}\n" .
                    "Days Left: {$daysLeft}"
                );

                $product->alert_sent = true;
                $product->save();
            }
        }

        $this->info('Expiry check completed');
    }
}