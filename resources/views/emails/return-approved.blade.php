<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background:#f4f4f4; padding:20px;">
    <div style="max-width:600px; margin:auto; background:#fff; border-radius:8px; padding:30px; box-shadow:0 2px 8px rgba(0,0,0,0.07);">
        <h2 style="color:#16a34a; margin-top:0;">✅ Return Request Approved</h2>
        <p>
            @if($recipientType === 'supplier')
                A return request has been approved. Please proceed with the return process and issue a Credit Note.
            @else
                Your return request has been approved by the supplier. A Credit Note will be processed shortly.
            @endif
        </p>
        <table style="width:100%; border-collapse:collapse; margin-top:15px; background:#f0fdf4; border-radius:6px; overflow:hidden;">
            <tr>
                <td style="padding:12px 16px; font-weight:bold; width:40%;">Return ID</td>
                <td style="padding:12px 16px; font-weight:bold;">#{{ $returnId }}</td>
            </tr>
        </table>
        <p style="margin-top:20px;">
            @if($recipientType === 'supplier')
                Log in to the Supplier Portal to review and confirm the credit note details.
            @else
                Log in to the system to track the credit note status.
            @endif
        </p>
        <hr style="margin-top:30px; border:none; border-top:1px solid #eee;">
        <p style="font-size:12px; color:#999; margin-bottom:0;">22UniMart Inventory Control System</p>
    </div>
</body>
</html>
