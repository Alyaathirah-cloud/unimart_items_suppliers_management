<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramWebhookController extends Controller
{
    public function handle(Request $request)
    {
        // ── 1. Log the raw incoming update ─────────────────────────────────
        $update = $request->all();
        Log::channel('telegram')->info('Telegram webhook received', ['update' => $update]);

        // ── 2. Guard: we only act on text messages ──────────────────────────
        if (!isset($update['message']['text'])) {
            Log::channel('telegram')->info('Telegram update ignored: no text message');
            return response()->json(['status' => 'ok']);
        }

        $message  = $update['message'];
        $text     = $message['text'];
        $chatId   = $message['chat']['id'];
        $fromUser = $message['from'] ?? [];

        Log::channel('telegram')->info('Telegram message received', [
            'chat_id' => $chatId,
            'text'    => $text,
            'from'    => $fromUser,
        ]);

        // ── 3. Handle /start <token> deep-link ─────────────────────────────
        if (preg_match('/^\/start\s+(\S+)$/', trim($text), $matches)) {
            $token = $matches[1];

            Log::channel('telegram')->info('Telegram /start token extracted', [
                'token'   => $token,
                'chat_id' => $chatId,
            ]);

            // ── 4. Find supplier by token ───────────────────────────────────
            $supplier = Supplier::where('telegram_connection_token', $token)->first();

            if (!$supplier) {
                Log::channel('telegram')->warning('Telegram connection failed: no supplier found for token', [
                    'token'   => $token,
                    'chat_id' => $chatId,
                ]);
                $this->sendMessage($chatId, "❌ Invalid or expired connection link.\n\nPlease request a new Telegram connection link from the portal owner.");
                return response()->json(['status' => 'ok']);
            }

            // ── 5. Check if already connected to a different account ────────
            if ($supplier->telegram_connected && $supplier->telegram_chat_id && $supplier->telegram_chat_id != $chatId) {
                Log::channel('telegram')->warning('Telegram account already connected to a different chat', [
                    'supplier_id'         => $supplier->id,
                    'existing_chat_id'    => $supplier->telegram_chat_id,
                    'new_chat_id'         => $chatId,
                ]);
            }

            // ── 6. Save Telegram connection details ─────────────────────────
            $telegramUsername = $fromUser['username'] ?? null;

            $supplier->update([
                'telegram_chat_id'          => (string) $chatId,
                'telegram_username'         => $telegramUsername,
                'telegram_connected'        => true,
                'telegram_connected_at'     => now(),
                'telegram_connection_token' => null, // Invalidate after use
            ]);

            Log::channel('telegram')->info('Telegram chat_id saved successfully', [
                'supplier_id'       => $supplier->id,
                'supplier_name'     => $supplier->name,
                'chat_id'           => $chatId,
                'telegram_username' => $telegramUsername,
            ]);

            // ── 7. Send confirmation message to supplier ────────────────────
            $this->sendMessage(
                $chatId,
                "✅ *Your Telegram account has been successfully connected to your supplier account.*\n\n" .
                "🏢 *Supplier:* {$supplier->name}\n" .
                "📱 You will now receive real-time notifications for:\n" .
                "• Purchase Orders\n" .
                "• Return Requests\n" .
                "• Invoice & Credit Note updates\n" .
                "• Delivery reminders\n\n" .
                "You can now close this window.",
                'Markdown'
            );

            return response()->json(['status' => 'ok']);
        }

        // ── 8. Handle plain /start (no token) ──────────────────────────────
        if (trim($text) === '/start') {
            $this->sendMessage(
                $chatId,
                "👋 Welcome to 22UniMart Notification Bot!\n\n" .
                "To connect your supplier account, please use the unique link from your invitation email.\n" .
                "The link looks like:\n" .
                "https://t.me/naa_um_bot?start=<your_token>"
            );
            return response()->json(['status' => 'ok']);
        }

        return response()->json(['status' => 'ok']);
    }

    /**
     * Send a message to a Telegram chat.
     */
    private function sendMessage(string|int $chatId, string $text, string $parseMode = null): void
    {
        try {
            $botToken = config('services.telegram.bot_token', env('TELEGRAM_BOT_TOKEN'));
            $payload  = [
                'chat_id' => $chatId,
                'text'    => $text,
            ];
            if ($parseMode) {
                $payload['parse_mode'] = $parseMode;
            }

            $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", $payload);

            if ($response->failed()) {
                Log::channel('telegram')->error('Telegram sendMessage failed', [
                    'chat_id'  => $chatId,
                    'response' => $response->body(),
                ]);
            }
        } catch (\Throwable $e) {
            Log::channel('telegram')->error('Telegram sendMessage exception', [
                'chat_id' => $chatId,
                'error'   => $e->getMessage(),
            ]);
        }
    }
}
