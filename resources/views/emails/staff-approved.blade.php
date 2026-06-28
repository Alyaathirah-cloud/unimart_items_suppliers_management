<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Staff Account Approved</title>
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

            <div style="font-size:1.6rem; margin-bottom:8px;">🎉</div>
            <h2 style="margin:0 0 8px; font-size:1.3rem; font-weight:800; color:#0f2044;">Great news, {{ $staffName }}!</h2>
            <p style="margin:0 0 20px; color:#5a6a85; line-height:1.6;">Your staff account at <strong>22UniMart</strong> has been <span style="color:#1d8348; font-weight:700;">approved</span> by the owner. You can now log in and start using the system.</p>

            {{-- Credentials Box --}}
            <div style="background:#f4f8ff; border:1px solid #bfdbfe; border-radius:10px; padding:18px 20px; margin-bottom:24px;">
                <div style="font-size:0.72rem; font-weight:700; color:#3a7bd5; text-transform:uppercase; letter-spacing:1px; margin-bottom:12px;">Your Login Details</div>
                <table style="width:100%; border-collapse:collapse; font-size:0.92rem;">
                    <tr>
                        <td style="padding:6px 0; color:#5a6a85; font-weight:600; width:40%;">Login URL</td>
                        <td style="padding:6px 0;">
                            <a href="{{ $loginUrl }}" style="color:#3a7bd5; text-decoration:none; font-weight:600;">{{ $loginUrl }}</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0; color:#5a6a85; font-weight:600;">Email</td>
                        <td style="padding:6px 0; color:#1a2744;">{{ $staffEmail }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0; color:#5a6a85; font-weight:600;">Role</td>
                        <td style="padding:6px 0;">
                            <span style="background:#e8f4ff; color:#1e3a6e; border-radius:20px; padding:3px 12px; font-size:0.78rem; font-weight:700;">Staff</span>
                        </td>
                    </tr>
                </table>
            </div>

            {{-- CTA Button --}}
            <div style="text-align:center; margin-bottom:24px;">
                <a href="{{ $loginUrl }}" style="display:inline-block; background:linear-gradient(135deg, #0f2044, #1e3a6e); color:#fff; text-decoration:none; padding:14px 36px; border-radius:8px; font-weight:700; font-size:0.95rem; letter-spacing:0.3px;">
                    🚀 Log In Now
                </a>
            </div>

            {{-- Note --}}
            <div style="background:#fef9e7; border:1px solid #ffe082; border-radius:8px; padding:14px 16px; font-size:0.85rem; color:#856404; line-height:1.6;">
                <strong>⚠️ Security reminder:</strong> Please use your registered email and password to sign in. If you have forgotten your password, contact your owner to reset it.
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
