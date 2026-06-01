<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin']);
    }

    public function edit()
    {
        $reorder = Setting::get('reorder_point_threshold', 0);
        $expiry = Setting::get('expiry_warning_days', 7);
        $ownerWhatsApp = Setting::get('owner_whatsapp_number', '');
        $callMeBotPhone = Setting::get('callmebot_phone', '');
        $callMeBotApiKey = Setting::get('callmebot_api_key', '');
        $whatsappEnabled = Setting::get('whatsapp_enabled', true);
        $lastWhatsappTest = Setting::get('whatsapp_last_test', null);
        $whatsappConnectionStatus = Setting::get('whatsapp_connection_status', null);
        $lowStockThreshold = Setting::get('low_stock_threshold', 10);

        return view('admin.settings.edit', compact(
            'reorder',
            'expiry',
            'ownerWhatsApp',
            'callMeBotPhone',
            'callMeBotApiKey',
            'lowStockThreshold',
            'whatsappEnabled',
            'lastWhatsappTest',
            'whatsappConnectionStatus'
        ));
    }

    public function update(Request $request)
    {
        $request->validate([
            'reorder_point_threshold' => 'required|integer|min:0',
            'expiry_warning_days' => 'required|integer|min:0',
            'low_stock_threshold' => 'required|integer|min:0',
            'owner_whatsapp_number' => 'nullable|string|max:30',
            'callmebot_phone' => 'nullable|string|max:30',
            'callmebot_api_key' => 'nullable|string|max:255',
            'whatsapp_enabled' => 'sometimes|boolean',
        ]);

        Setting::set('reorder_point_threshold', $request->reorder_point_threshold);
        Setting::set('expiry_warning_days', $request->expiry_warning_days);
        Setting::set('low_stock_threshold', $request->low_stock_threshold);
        Setting::set('owner_whatsapp_number', $request->owner_whatsapp_number);
        Setting::set('callmebot_phone', $request->callmebot_phone);
        Setting::set('callmebot_api_key', $request->callmebot_api_key);
        Setting::set('whatsapp_enabled', $request->boolean('whatsapp_enabled'));

        return back()->with('success', 'Settings saved');
    }

    public function testWhatsApp(Request $request)
    {
        $request->validate([
            'callmebot_phone' => 'nullable|string|max:30',
            'callmebot_api_key' => 'nullable|string|max:255',
            'owner_whatsapp_number' => 'nullable|string|max:30',
        ]);

        $testMessage = "✅ 22UniMart WhatsApp notifications are configured successfully.";
        $callMeBotPhone = $request->callmebot_phone ?: Setting::get('callmebot_phone');
        $callMeBotApiKey = $request->callmebot_api_key ?: Setting::get('callmebot_api_key');

        try {
            $service = new \App\Services\CallMeBotService();
            // try sending to owner if available, otherwise to configured callmebot phone
            $ownerPhone = $request->owner_whatsapp_number ?: Setting::get('owner_whatsapp_number');
            $target = $ownerPhone ?: $callMeBotPhone;
            if (!$target) {
                return back()->with('error', 'No recipient configured for test. Set Owner WhatsApp or CallMeBot phone first.');
            }

            $service->sendToNumber($target, $testMessage);
            Setting::set('whatsapp_last_test', now()->toDateTimeString());
            Setting::set('whatsapp_connection_status', 'ok');
            return back()->with('success', 'Test WhatsApp notification sent successfully.');
        } catch (\Throwable $e) {
            Setting::set('whatsapp_last_test', now()->toDateTimeString());
            Setting::set('whatsapp_connection_status', 'failed: ' . $e->getMessage());
            return back()->with('error', 'Test WhatsApp notification failed: ' . $e->getMessage());
        }
    }
}
