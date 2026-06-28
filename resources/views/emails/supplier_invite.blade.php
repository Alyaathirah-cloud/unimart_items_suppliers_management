Hi {{ $data['contact_person'] ?? ($contactPerson ?? 'Supplier') }},

You have been registered as a supplier on {{ $data['company_name'] ?? ($companyName ?? '22UniMart') }}.

Your portal access details:
-----------------------------------------
Portal Link : {{ $data['portal_url'] ?? ($loginUrl ?? '') }}
Email       : {{ $data['email'] ?? ($contactEmail ?? '') }}
Password    : {{ $data['password'] ?? ($password ?? '') }}
-----------------------------------------

* Note: You will be required to change this temporary password immediately after your first login.

=========================================
📱 TELEGRAM NOTIFICATIONS (IMPORTANT)
=========================================
To receive instant updates for Purchase Orders, Return Requests, Invoices, and Credit Notes, please connect your Telegram account by clicking the link below:

https://t.me/{{ config('services.telegram.bot_username', env('TELEGRAM_BOT_USERNAME', 'naa_um_bot')) }}?start={{ $data['telegram_token'] ?? ($telegramToken ?? '') }}

(Open the link in Telegram and press "Start")

Thank you,
{{ $data['company_name'] ?? ($companyName ?? '22UniMart') }}
