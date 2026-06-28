<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background:#f4f4f4; padding:20px;">
    <div style="max-width:600px; margin:auto; background:#fff; border-radius:8px; padding:30px; box-shadow:0 2px 8px rgba(0,0,0,0.07);">
        <h2 style="color:#16a34a; margin-top:0;">✅ Purchase Order Approved</h2>
        <p>
            @if($recipientType === 'supplier')
                A purchase order assigned to you has been confirmed and is ready for fulfilment.
            @else
                Your purchase order has been approved by the supplier.
            @endif
        </p>
        <table style="width:100%; border-collapse:collapse; margin-top:15px; background:#f0fdf4; border-radius:6px; overflow:hidden;">
            <tr>
                <td style="padding:12px 16px; font-weight:bold; border-bottom:1px solid #dcfce7; width:40%;">PO Number</td>
                <td style="padding:12px 16px; border-bottom:1px solid #dcfce7; font-weight:bold;">{{ $poNumber }}</td>
            </tr>
            @if($details)
            <tr>
                <td style="padding:12px 16px; font-weight:bold;">Details</td>
                <td style="padding:12px 16px;">{{ $details }}</td>
            </tr>
            @endif
        </table>
        <p style="margin-top:20px;">
            @if($recipientType === 'supplier')
                Please log in to the Supplier Portal to review the delivery schedule and take further action.
            @else
                Please log in to the system to check the delivery schedule set by the supplier.
            @endif
        </p>
        <hr style="margin-top:30px; border:none; border-top:1px solid #eee;">
        <p style="font-size:12px; color:#999; margin-bottom:0;">22UniMart Inventory Control System</p>
    </div>
</body>
</html>
