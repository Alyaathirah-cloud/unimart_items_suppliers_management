<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class TestEmailCommand extends Command
{
    protected $signature   = 'email:test {to?}';
    protected $description = 'Send a test email to verify SMTP configuration';

    public function handle()
    {
        $to = $this->argument('to') ?? config('mail.from.address');

        $this->info("Sending test email to: {$to}");
        $this->info("SMTP Host   : " . config('mail.mailers.smtp.host'));
        $this->info("SMTP Port   : " . config('mail.mailers.smtp.port'));
        $this->info("From Address: " . config('mail.from.address'));

        try {
            Mail::raw(
                "✅ This is a test email from 22UniMart.\n\nIf you received this, your SMTP configuration is working correctly.\n\nSent at: " . now()->toDateTimeString(),
                function ($message) use ($to) {
                    $message->to($to)
                            ->subject('[22UniMart] Test Email — SMTP OK');
                }
            );
            $this->info("✅ Email sent successfully to {$to}!");
            return 0;
        } catch (\Exception $e) {
            $this->error("❌ Email failed: " . $e->getMessage());
            return 1;
        }
    }
}
