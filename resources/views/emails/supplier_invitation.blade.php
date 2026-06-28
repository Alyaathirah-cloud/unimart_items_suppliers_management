<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Supplier Portal Access</title>
</head>
<body style="font-family: Inter, Arial, sans-serif; background:#f8fafc; margin:0; padding:0;">
    <div style="max-width:620px;margin:24px auto;background:#fff;border:1px solid #dfe7f3;border-radius:12px;overflow:hidden;">
        <div style="background:#0f2044;padding:22px 24px;color:#fff;">
            <div style="font-size:1.4rem;font-weight:800;">{{ $companyName }}</div>
            <div style="font-size:0.72rem;color:rgba(255,255,255,0.7);text-transform:uppercase;letter-spacing:1px;">Supplier Portal Invitation</div>
        </div>
        <div style="padding:24px; color:#1a2744;">
            <p style="margin:0 0 12px; font-size:1.02rem; font-weight:700;">Hi {{ $contactPerson }},</p>
            <p style="margin:0 0 14px; line-height:1.55; color:#5a6a85;">You have been registered as a supplier by {{ $companyName }}.</p>
            <p style="margin:0 0 14px; line-height:1.55; color:#5a6a85;">Your portal login details are as follows:</p>
            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px 16px;margin:0 0 18px;line-height:1.55;color:#1a2744;font-size:0.92rem;">
                <p style="margin:0 0 8px;"><strong>Portal Link :</strong> {{ $loginUrl }}</p>
                <p style="margin:0 0 8px;"><strong>Email :</strong> {{ $contactEmail }}</p>
                <p style="margin:0;"><strong>Password :</strong> {{ $password }}</p>
            </div>
            <p style="margin:0 0 16px; line-height:1.55; color:#5a6a85;">Please log in and change your password after first login.</p>
            
            <div style="background:#e8f0fb;border:1px solid #b5d4f4;border-radius:10px;padding:14px 16px;margin:0 0 18px;line-height:1.55;color:#1e3a6e;font-size:0.92rem;">
                <p style="margin:0 0 10px; font-weight:700;">📱 Telegram Notifications</p>
                <p style="margin:0 0 10px;">To receive instant notifications for Purchase Orders, Return Requests, Invoices, Credit Notes, and reminders, please connect your Telegram account by clicking the link below:</p>
                <p style="margin:0 0 10px;"><a href="https://t.me/naa_um_bot?start={{ $telegramToken ?? ($data['telegram_token'] ?? '') }}" style="color:#0f2044;font-weight:700;text-decoration:underline;">https://t.me/naa_um_bot?start={{ $telegramToken ?? ($data['telegram_token'] ?? '') }}</a></p>
                <p style="margin:0; font-size:0.85rem;">Open the bot and press <strong>Start</strong> to activate Telegram notifications.</p>
            </div>

            <p style="margin:0 0 16px; line-height:1.55; color:#5a6a85;"><strong>If you have any questions, please contact:</strong><br>{{ $ownerContact }}</p>
            <p style="margin:0; line-height:1.5; color:#7a8fa8; font-size:0.88rem;">Thank you.<br>{{ $companyName }}</p>
        </div>
    </div>
</body>
</html>
