<?php

namespace App\Services;

use Twilio\Rest\Client;

class WhatsAppService
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $sid    = config('services.twilio.sid');
        $token  = config('services.twilio.token');
        $this->from = config('services.twilio.whatsapp_from'); 

        if ($sid && $token) {
            $this->client = new Client($sid, $token);
        }
    }

    protected function normalizePhone($phone)
    {
        $phone = preg_replace('/[^0-9+]/', '', $phone);

        if (str_starts_with($phone, '0') && strlen($phone) === 10) {
            $phone = '+6' . substr($phone, 1);
        } elseif (preg_match('/^01\d{8,9}$/', $phone)) {
            $phone = '+6' . substr($phone, 1);
        } elseif (!str_starts_with($phone, '+')) {
            $phone = '+' . $phone;
        }

        return $phone;
    }

    public function formatPhoneForDisplay($phone)
    {
        return $this->normalizePhone($phone);
    }

    public function sendMessage($phone, $messageBody)
    {
        if (!$this->client || empty($this->from)) {
            throw new \Exception("Twilio is not configured.");
        }

        $phone = $this->normalizePhone($phone);

        if (!str_starts_with($phone, 'whatsapp:')) {
            $phone = 'whatsapp:' . $phone;
        }

        $this->client->messages->create(
            $phone,
            [
                'from' => $this->from,
                'body' => $messageBody
            ]
        );
    }

    public function sendInvite($phone, $data)
    {
        $messageBody = "Hi {$data['contact_person']},\n\n";
        $messageBody .= "You have been registered as a supplier on {$data['company_name']} portal.\n\n";
        $messageBody .= "Login here: {$data['portal_url']}\n";
        $messageBody .= "Email: {$data['email']}\n";
        $messageBody .= "Password: {$data['password']}\n\n";
        $messageBody .= "Please change your password after first login.\n";
        $messageBody .= "For help, contact: {$data['owner_phone']}\n\n";
        $messageBody .= "Thank you,\n";
        $messageBody .= "{$data['company_name']}";

        $this->sendMessage($phone, $messageBody);
    }
}
