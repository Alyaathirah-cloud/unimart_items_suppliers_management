<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Return Request Created</title>
</head>
<body style="font-family: 'Inter', Arial, sans-serif; background:#f0f4f8; margin:0; padding:0;">
    <div style="max-width:620px; margin:28px auto; background:#fff; border:1px solid #dfe7f3; border-radius:14px; overflow:hidden; box-shadow:0 4px 24px rgba(15,32,68,0.08);">

        {{-- Header --}}
        <div style="background: linear-gradient(135deg, #0f2044 0%, #1e3a6e 100%); padding:28px 32px; color:#fff;">
            <div style="font-size:1.5rem; font-weight:800; letter-spacing:-0.5px;">22UniMart</div>
            <div style="font-size:0.7rem; color:rgba(255,255,255,0.65); text-transform:uppercase; letter-spacing:1.5px; margin-top:4px;">Supplier Portal Notification</div>
        </div>

        {{-- Body --}}
        <div style="padding:32px; color:#1a2744;">
            <div style="font-size:1.8rem; margin-bottom:8px;">⚠️</div>
            <h2 style="margin:0 0 8px; font-size:1.3rem; font-weight:800; color:#0f2044;">New Return Request</h2>
            <p style="margin:0 0 20px; color:#5a6a85; line-height:1.6;">A new return request has been submitted by <strong>22UniMart</strong>. Please log in to your portal to review the request and take action.</p>

            {{-- Details Box --}}
            <div style="background:#fef2f2; border:1px solid #fecaca; border-radius:10px; padding:18px 20px; margin-bottom:24px;">
                <div style="font-size:0.72rem; font-weight:700; color:#dc2626; text-transform:uppercase; letter-spacing:1px; margin-bottom:12px;">Return Summary</div>
                <table style="width:100%; border-collapse:collapse; font-size:0.92rem;">
                    <tr>
                        <td style="padding:8px 0; color:#5a6a85; font-weight:600; width:40%; border-bottom:1px solid #fecaca;">Return ID</td>
                        <td style="padding:8px 0; color:#1a2744; border-bottom:1px solid #fecaca; font-weight:700;">{{ $returnNumber }}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0; color:#5a6a85; font-weight:600; border-bottom:1px solid #fecaca;">Item(s)</td>
                        <td style="padding:8px 0; color:#1a2744; border-bottom:1px solid #fecaca;">{{ $itemName }}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0; color:#5a6a85; font-weight:600;">Reason</td>
                        <td style="padding:8px 0; color:#1a2744;">{{ $reason }}</td>
                    </tr>
                </table>
            </div>

            {{-- CTA Button --}}
            <div style="text-align:center; margin-bottom:24px;">
                <a href="{{ $loginUrl ?? url('/supplier/login') }}" style="display:inline-block; background:linear-gradient(135deg, #0f2044, #1e3a6e); color:#fff; text-decoration:none; padding:14px 36px; border-radius:8px; font-weight:700; font-size:0.95rem;">
                    🚀 Open Supplier Portal
                </a>
            </div>

        </div>

        {{-- Footer --}}
        <div style="background:#f8fafc; border-top:1px solid #e8eef5; padding:16px 32px;">
            <p style="margin:0; font-size:0.78rem; color:#9daec5; line-height:1.6;">
                This email was sent automatically by <strong>22UniMart Inventory Control System</strong>.
            </p>
        </div>
    </div>
</body>
</html>
