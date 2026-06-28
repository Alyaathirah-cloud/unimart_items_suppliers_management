<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Product;
use App\Services\TelegramService;

class CheckLowStock extends Command
{
    protected $signature = 'low-stock:check';
    protected $description = 'Check for low stock products and send Telegram alerts';

    public function handle()
    {
        $products = Product::whereColumn('stock', '<=', 'min_stock')
            ->where('low_stock_alert_sent', false)
            ->get();

        if ($products->isEmpty()) {
            $this->info('No low stock products.');
            return;
        }

        $telegram = new TelegramService();

        foreach ($products as $product) {
            $telegram->send(
                "📦 LOW STOCK ALERT\n" .
                "Product: {$product->name}\n" .
                "Current Stock: {$product->stock}\n" .
                "Minimum Stock: {$product->min_stock}"
            );

            $product->low_stock_alert_sent = true;
            $product->save();
        }

        $this->info('Low stock check completed');
    }
}
