<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class RegisterTelegramWebhook extends Command
{
    protected $signature   = 'telegram:set-webhook {url? : The public HTTPS URL of your webhook (e.g. from ngrok)}';
    protected $description = 'Register the Telegram webhook URL with the Telegram Bot API';

    public function handle(): int
    {
        $botToken = config('services.telegram.bot_token');

        if (empty($botToken)) {
            $this->error('TELEGRAM_BOT_TOKEN is not set in your .env file.');
            return self::FAILURE;
        }

        // Use the provided URL or fall back to APP_URL
        $baseUrl = $this->argument('url') ?? config('app.url');
        $webhookUrl = rtrim($baseUrl, '/') . '/api/telegram/webhook';

        $this->info("Registering webhook: {$webhookUrl}");

        $response = Http::post("https://api.telegram.org/bot{$botToken}/setWebhook", [
            'url'             => $webhookUrl,
            'allowed_updates' => ['message'],
        ]);

        $result = $response->json();

        if ($result['ok'] ?? false) {
            $this->info('✅ Webhook registered successfully!');
            $this->line('Description: ' . ($result['description'] ?? 'OK'));
        } else {
            $this->error('❌ Failed to register webhook.');
            $this->line('Response: ' . json_encode($result, JSON_PRETTY_PRINT));
            return self::FAILURE;
        }

        // Also print current webhook info
        $infoResponse = Http::get("https://api.telegram.org/bot{$botToken}/getWebhookInfo");
        $info = $infoResponse->json();
        $this->newLine();
        $this->info('Current Webhook Info:');
        $this->table(
            ['Field', 'Value'],
            [
                ['URL',                 $info['result']['url'] ?? 'None'],
                ['Has custom cert',     ($info['result']['has_custom_certificate'] ?? false) ? 'Yes' : 'No'],
                ['Pending update count',$info['result']['pending_update_count'] ?? 0],
                ['Last error',          $info['result']['last_error_message'] ?? 'None'],
                ['Last error date',     isset($info['result']['last_error_date']) ? date('Y-m-d H:i:s', $info['result']['last_error_date']) : 'None'],
            ]
        );

        return self::SUCCESS;
    }
}
