<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Welcome to 22UniMart</title>
</head>
<body style="font-family: 'Inter', Arial, sans-serif; background:#f0f4f8; margin:0; padding:0;">
    <div style="max-width:620px; margin:28px auto; background:#fff; border:1px solid #dfe7f3; border-radius:14px; overflow:hidden; box-shadow:0 4px 24px rgba(15,32,68,0.08);">

        {{-- Header --}}
        <div style="background: linear-gradient(135deg, #0f2044 0%, #1e3a6e 100%); padding:28px 32px; color:#fff;">
            <div style="font-size:1.5rem; font-weight:800; letter-spacing:-0.5px;">22UniMart</div>
            <div style="font-size:0.7rem; color:rgba(255,255,255,0.65); text-transform:uppercase; letter-spacing:1.5px; margin-top:4px;">Inventory Control System</div>
        </div>

        {{-- Body --}}
        <div style="padding:32px; color:#1a2744;">
            <div style="font-size:1.8rem; margin-bottom:8px;">👋</div>
            <h2 style="margin:0 0 8px; font-size:1.3rem; font-weight:800; color:#0f2044;">Welcome, {{ $staffName }}!</h2>
            <p style="margin:0 0 20px; color:#5a6a85; line-height:1.6;">Your staff account has been created by the system owner. Use the credentials below to log in for the first time.</p>

            {{-- Credentials Box --}}
            <div style="background:#f4f8ff; border:1px solid #bfdbfe; border-radius:10px; padding:18px 20px; margin-bottom:24px;">
                <div style="font-size:0.72rem; font-weight:700; color:#3a7bd5; text-transform:uppercase; letter-spacing:1px; margin-bottom:12px;">Your Login Credentials</div>
                <table style="width:100%; border-collapse:collapse; font-size:0.92rem;">
                    <tr>
                        <td style="padding:8px 0; color:#5a6a85; font-weight:600; width:40%; border-bottom:1px solid #e2e8f0;">Email</td>
                        <td style="padding:8px 0; color:#1a2744; border-bottom:1px solid #e2e8f0;">{{ $email }}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0; color:#5a6a85; font-weight:600;">Temporary Password</td>
                        <td style="padding:8px 0;">
                            <span style="background:#0f2044; color:#fff; padding:4px 14px; border-radius:6px; font-family:monospace; font-size:1rem; letter-spacing:2px; font-weight:700;">{{ $temporaryPassword }}</span>
                        </td>
                    </tr>
                </table>
            </div>

            {{-- CTA Button --}}
            <div style="text-align:center; margin-bottom:24px;">
                <a href="{{ url('/login') }}" style="display:inline-block; background:linear-gradient(135deg, #0f2044, #1e3a6e); color:#fff; text-decoration:none; padding:14px 36px; border-radius:8px; font-weight:700; font-size:0.95rem;">
                    🚀 Log In Now
                </a>
            </div>

            {{-- Security warning --}}
            <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:8px; padding:14px 16px; font-size:0.85rem; color:#dc2626; line-height:1.6;">
                <strong>⚠️ Security:</strong> Please change your password immediately after your first login for the security of your account.
            </div>
        </div>

        {{-- Footer --}}
        <div style="background:#f8fafc; border-top:1px solid #e8eef5; padding:16px 32px;">
            <p style="margin:0; font-size:0.78rem; color:#9daec5; line-height:1.6;">
                This email was sent by <strong>22UniMart Inventory Control System</strong>.<br>
                If you did not expect this, please contact the system owner.
            </p>
        </div>
    </div>
</body>
</html>
