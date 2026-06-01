<?php

namespace App\Services;

use App\Mail\SupplierInvitationMail;
use App\Models\Notification;
use App\Models\Setting;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use RuntimeException;

class SupplierInviteService
{
    public function sendInvitation(Supplier $supplier, string $password, User $owner): array
    {
        $companyName = Setting::get('company_name', config('app.name'));
        $ownerContact = Setting::get('owner_whatsapp_number', $owner->email ?? 'Owner');
        $portalUrl = route('supplier.login');

        $notification = Notification::create([
            'user_id' => $supplier->user_id,
            'type' => 'supplier_invited',
            'message' => 'Your supplier portal account has been created.',
            'data' => [
                'supplier_name' => $supplier->name,
                'contact_person' => $supplier->contact_person ?? $supplier->name,
                'contact_email' => $supplier->contact_email,
                'password' => $password,
                'login_url' => $portalUrl,
                'company_name' => $companyName,
                'owner_contact' => $ownerContact,
            ],
        ]);

        $emailResult = $this->sendEmailInvite($supplier, $password, $companyName, $ownerContact, $portalUrl);
        $whatsappResult = $this->sendWhatsAppInvite($supplier, $password, $companyName, $ownerContact, $portalUrl);

        return [
            'notification_id' => $notification->id,
            'email_sent' => $emailResult['sent'],
            'email_error' => $emailResult['error'],
            'whatsapp_sent' => $whatsappResult['sent'],
            'whatsapp_error' => $whatsappResult['error'],
        ];
    }

    public function resendEmailInvite(Supplier $supplier, string $password, User $owner): array
    {
        $companyName = Setting::get('company_name', config('app.name'));
        $ownerContact = Setting::get('owner_whatsapp_number', $owner->email ?? 'Owner');
        $portalUrl = route('supplier.login');

        return $this->sendEmailInvite($supplier, $password, $companyName, $ownerContact, $portalUrl);
    }

    public function resendWhatsAppInvite(Supplier $supplier, string $password, User $owner): array
    {
        $companyName = Setting::get('company_name', config('app.name'));
        $ownerContact = Setting::get('owner_whatsapp_number', $owner->email ?? 'Owner');
        $portalUrl = route('supplier.login');

        return $this->sendWhatsAppInvite($supplier, $password, $companyName, $ownerContact, $portalUrl);
    }

    protected function sendEmailInvite(Supplier $supplier, string $password, string $companyName, string $ownerContact, string $portalUrl): array
    {
        try {
            Mail::to($supplier->contact_email)->send(new SupplierInvitationMail(
                $supplier->contact_person ?? $supplier->name,
                $supplier->contact_email,
                $password,
                $portalUrl,
                $companyName,
                $ownerContact
            ));

            return ['sent' => true, 'error' => null];
        } catch (\Throwable $e) {
            Log::error('Supplier invite email failed', [
                'supplier_id' => $supplier->id,
                'email' => $supplier->contact_email,
                'error' => $e->getMessage(),
            ]);

            return ['sent' => false, 'error' => $e->getMessage()];
        }
    }

    protected function sendWhatsAppInvite(Supplier $supplier, string $password, string $companyName, string $ownerContact, string $portalUrl): array
    {
        $phone = trim((string) $supplier->contact_phone);
        if ($phone === '') {
            return ['sent' => false, 'error' => 'Missing supplier phone number.'];
        }

        $endpoint = config('services.whatsapp.endpoint');
        $token = config('services.whatsapp.token');

        if (empty($endpoint)) {
            Log::warning('Supplier WhatsApp provider endpoint is not configured.', [
                'supplier_id' => $supplier->id,
            ]);

            return ['sent' => false, 'error' => 'WhatsApp provider is not configured.'];
        }

        $message = implode("\n", [
            "Hi {$supplier->contact_person ?? $supplier->name},",
            '',
            "You have been registered as a supplier on {$companyName} portal.",
            '',
            "Login here: {$portalUrl}",
            "Email: {$supplier->contact_email}",
            "Password: {$password}",
            '',
            'Please change your password after first login.',
            "For help, contact: {$ownerContact}",
            '',
            "Thank you,",
            $companyName,
        ]);

        try {
            $response = Http::withToken($token)->post($endpoint, [
                'to' => $phone,
                'message' => $message,
                'supplier_id' => $supplier->id,
                'supplier_email' => $supplier->contact_email,
            ]);

            if ($response->failed()) {
                $error = $response->body();
                Log::error('Supplier WhatsApp invite failed', [
                    'supplier_id' => $supplier->id,
                    'phone' => $phone,
                    'status' => $response->status(),
                    'response' => $error,
                ]);

                return ['sent' => false, 'error' => $error ?: 'WhatsApp invite failed to send.'];
            }

            return ['sent' => true, 'error' => null];
        } catch (\Throwable $e) {
            Log::error('Supplier WhatsApp invite failed', [
                'supplier_id' => $supplier->id,
                'phone' => $phone,
                'error' => $e->getMessage(),
            ]);

            return ['sent' => false, 'error' => $e->getMessage()];
        }
    }
}
