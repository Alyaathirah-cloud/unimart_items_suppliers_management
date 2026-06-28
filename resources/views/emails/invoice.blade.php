<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background:#f4f4f4; padding:20px;">
    <div style="max-width:600px; margin:auto; background:#fff; border-radius:8px; padding:30px; box-shadow:0 2px 8px rgba(0,0,0,0.07);">
        <h2 style="color:#1d4ed8; margin-top:0;">📄 Invoice #{{ $invoiceNumber }}</h2>
        <p>Dear {{ $supplierName }},</p>
        <p>Please find your invoice from 22UniMart attached to this email (or referenced below).</p>
        <table style="width:100%; border-collapse:collapse; margin-top:15px; background:#eff6ff; border-radius:6px; overflow:hidden;">
            <tr>
                <td style="padding:12px 16px; font-weight:bold; width:40%;">Invoice Number</td>
                <td style="padding:12px 16px; font-weight:bold;">#{{ $invoiceNumber }}</td>
            </tr>
        </table>
        <p style="margin-top:20px;">If you have any questions regarding this invoice, please contact us directly.</p>
        <hr style="margin-top:30px; border:none; border-top:1px solid #eee;">
        <p style="font-size:12px; color:#999; margin-bottom:0;">22UniMart Inventory Control System</p>
    </div>
</body>
</html>
