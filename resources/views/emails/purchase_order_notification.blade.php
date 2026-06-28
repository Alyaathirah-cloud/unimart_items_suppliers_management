<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Purchase Order Notification</title>
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
            <div style="font-size:1.8rem; margin-bottom:8px;">📦</div>
            <h2 style="margin:0 0 8px; font-size:1.3rem; font-weight:800; color:#0f2044;">
                {{ $action === 'updated' ? 'Purchase Order Updated' : 'New Purchase Order' }}
            </h2>
            <p style="margin:0 0 20px; color:#5a6a85; line-height:1.6;">
                A purchase order has been {{ $action === 'updated' ? 'updated' : 'placed' }} by <strong>22UniMart</strong>. Please review the details below.
            </p>

            {{-- Details Box --}}
            <div style="background:#f4f8ff; border:1px solid #bfdbfe; border-radius:10px; padding:18px 20px; margin-bottom:24px;">
                <div style="font-size:0.72rem; font-weight:700; color:#3a7bd5; text-transform:uppercase; letter-spacing:1px; margin-bottom:12px;">Order Summary</div>
                <table style="width:100%; border-collapse:collapse; font-size:0.92rem;">
                    <tr>
                        <td style="padding:8px 0; color:#5a6a85; font-weight:600; width:40%; border-bottom:1px solid #e2e8f0;">PO Number</td>
                        <td style="padding:8px 0; color:#1a2744; border-bottom:1px solid #e2e8f0; font-weight:700;">{{ $purchaseOrder->po_number }}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0; color:#5a6a85; font-weight:600; border-bottom:1px solid #e2e8f0;">Order Date</td>
                        <td style="padding:8px 0; color:#1a2744; border-bottom:1px solid #e2e8f0;">{{ $purchaseOrder->created_at->format('d M Y') }}</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 0; color:#5a6a85; font-weight:600; border-bottom:1px solid #e2e8f0;">Expected Delivery</td>
                        <td style="padding:8px 0; color:#1a2744; border-bottom:1px solid #e2e8f0;">{{ $purchaseOrder->expected_delivery_date ? \Carbon\Carbon::parse($purchaseOrder->expected_delivery_date)->format('d M Y') : 'Not Specified' }}</td>
                    </tr>
                </table>

                <div style="margin-top:16px;">
                    <div style="font-size:0.72rem; font-weight:700; color:#3a7bd5; text-transform:uppercase; letter-spacing:1px; margin-bottom:8px;">Ordered Items</div>
                    <table style="width:100%; border-collapse:collapse; font-size:0.85rem;">
                        <thead style="background:rgba(58, 123, 213, 0.1); color:#3a7bd5;">
                            <tr>
                                <th style="text-align:left; padding:6px 8px; border-radius:4px 0 0 4px;">Item</th>
                                <th style="text-align:center; padding:6px 8px;">Qty</th>
                                <th style="text-align:right; padding:6px 8px;">Price</th>
                                <th style="text-align:right; padding:6px 8px; border-radius:0 4px 4px 0;">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if($purchaseOrder->orderItems && $purchaseOrder->orderItems->count() > 0)
                                @foreach($purchaseOrder->orderItems as $poItem)
                                <tr>
                                    <td style="padding:8px; border-bottom:1px solid #e2e8f0; color:#1a2744;">{{ optional($poItem->item)->name ?? 'Unknown' }}</td>
                                    <td style="padding:8px; border-bottom:1px solid #e2e8f0; text-align:center; color:#5a6a85;">{{ $poItem->quantity }}</td>
                                    <td style="padding:8px; border-bottom:1px solid #e2e8f0; text-align:right; color:#5a6a85;">RM {{ number_format($poItem->unit_price, 2) }}</td>
                                    <td style="padding:8px; border-bottom:1px solid #e2e8f0; text-align:right; font-weight:600; color:#1a2744;">RM {{ number_format($poItem->subtotal, 2) }}</td>
                                </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td style="padding:8px; border-bottom:1px solid #e2e8f0; color:#1a2744;">{{ optional($purchaseOrder->item)->name ?? 'Unknown' }}</td>
                                    <td style="padding:8px; border-bottom:1px solid #e2e8f0; text-align:center; color:#5a6a85;">{{ $purchaseOrder->quantity }}</td>
                                    <td style="padding:8px; border-bottom:1px solid #e2e8f0; text-align:right; color:#5a6a85;">RM {{ number_format($purchaseOrder->unit_price, 2) }}</td>
                                    <td style="padding:8px; border-bottom:1px solid #e2e8f0; text-align:right; font-weight:600; color:#1a2744;">RM {{ number_format($purchaseOrder->total_amount, 2) }}</td>
                                </tr>
                            @endif
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="3" style="text-align:right; padding:12px 8px 0; font-weight:600; color:#5a6a85;">Total Amount:</td>
                                <td style="text-align:right; padding:12px 8px 0; font-weight:800; color:#0f2044;">RM {{ number_format($purchaseOrder->total_amount, 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            {{-- CTA Button --}}
            <div style="text-align:center; margin-bottom:24px;">
                @php
                    $supplier = $purchaseOrder->supplier;
                    $loginUrl = $supplier ? ($supplier->portal_link ?? url('/supplier/login')) : url('/supplier/login');
                @endphp
                <a href="{{ $loginUrl }}" style="display:inline-block; background:linear-gradient(135deg, #0f2044, #1e3a6e); color:#fff; text-decoration:none; padding:14px 36px; border-radius:8px; font-weight:700; font-size:0.95rem;">
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
