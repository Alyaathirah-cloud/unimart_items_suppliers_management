<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramService
{
    /**
     * Send a plain text message to a Telegram chat.
     *
     * @param  string      $message
     * @param  string|int|null $customChatId  Override the default chat ID from config
     * @param  string|null $parseMode       'Markdown' | 'HTML' | null
     */
    public function send(string $message, $customChatId = null, ?string $parseMode = null)
    {
        $token  = config('services.telegram.bot_token');
        $chatId = $customChatId ?? config('services.telegram.chat_id');

        if (empty($token) || empty($chatId)) {
            Log::channel('telegram')->warning('TelegramService: bot_token or chat_id missing', [
                'has_token'  => !empty($token),
                'has_chatId' => !empty($chatId),
            ]);
            return null;
        }

        try {
            $payload = [
                'chat_id' => $chatId,
                'text'    => $message,
            ];
            if ($parseMode) {
                $payload['parse_mode'] = $parseMode;
            }

            $response = Http::post(
                "https://api.telegram.org/bot{$token}/sendMessage",
                $payload
            );

            if ($response->failed()) {
                Log::channel('telegram')->error('TelegramService::send failed', [
                    'chat_id'  => $chatId,
                    'response' => $response->body(),
                ]);
            }

            return $response;
        } catch (\Throwable $e) {
            Log::channel('telegram')->error('TelegramService::send exception', [
                'chat_id' => $chatId,
                'error'   => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Send a notification to a supplier using their stored telegram_chat_id.
     */
    public function sendToSupplier(\App\Models\Supplier $supplier, string $message, ?string $parseMode = null)
    {
        if (!$supplier->isTelegramConnected()) {
            Log::channel('telegram')->info('TelegramService::sendToSupplier skipped – supplier not connected', [
                'supplier_id' => $supplier->id,
            ]);
            return null;
        }

        return $this->send($message, $supplier->telegram_chat_id, $parseMode);
    }
}