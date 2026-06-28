<?php

use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Artisan;

Artisan::command('email:test', function () {
    try {
        Mail::raw('Test email dari 22UniMart - SMTP OK', function ($msg) {
            $msg->to(config('mail.from.address'))
                ->subject('[22UniMart] Test Email Connection');
        });
        $this->info('✅ Email sent to ' . config('mail.from.address'));
    } catch (\Exception $e) {
        $this->error('❌ Email failed: ' . $e->getMessage());
    }
});
