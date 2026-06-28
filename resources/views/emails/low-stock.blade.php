<!DOCTYPE html>
<html>
<body style="font-family: Arial, sans-serif; background:#f4f4f4; padding:20px;">
    <div style="max-width:600px; margin:auto; background:#fff; border-radius:8px; padding:30px; box-shadow:0 2px 8px rgba(0,0,0,0.07);">
        <h2 style="color:#d97706; margin-top:0;">⚠️ Low Stock Alert</h2>
        <p>The following product is running low on stock and may need to be restocked soon:</p>
        <table style="width:100%; border-collapse:collapse; margin-top:15px; background:#fffbeb; border-radius:6px; overflow:hidden;">
            <tr>
                <td style="padding:12px 16px; font-weight:bold; border-bottom:1px solid #fef3c7; width:40%;">Product</td>
                <td style="padding:12px 16px; border-bottom:1px solid #fef3c7;">{{ $productName }}</td>
            </tr>
            <tr>
                <td style="padding:12px 16px; font-weight:bold;">Remaining Quantity</td>
                <td style="padding:12px 16px; color:#dc2626; font-weight:bold;">{{ $currentQty }}</td>
            </tr>
        </table>
        <p style="margin-top:20px;">Please raise a Purchase Order to replenish this item before it runs out.</p>
        <hr style="margin-top:30px; border:none; border-top:1px solid #eee;">
        <p style="font-size:12px; color:#999; margin-bottom:0;">22UniMart Inventory Control System — Automated Alert</p>
    </div>
</body>
</html>
