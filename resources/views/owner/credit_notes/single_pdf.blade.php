<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Credit Note {{ $creditNote->credit_note_id }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0; color: #666; }
        .status-stamp { text-align: center; font-size: 18px; font-weight: bold; margin: 20px 0; padding: 10px; border: 2px solid; }
        .approved { color: green; border-color: green; }
        .credit-applied { color: blue; border-color: blue; }
        .details { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .from, .to { width: 48%; }
        .from h3, .to h3 { margin-top: 0; font-size: 16px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f5f5f5; }
        .total { text-align: right; font-weight: bold; }
        .footer { margin-top: 40px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="header">
        <h1>22UniMart</h1>
        <p>Inventory Management System</p>
        <h2>Credit Note</h2>
    </div>

    <div class="status-stamp {{ $creditNote->status === 'Approved' ? 'approved' : ($creditNote->status === 'Credit Applied' ? 'credit-applied' : '') }}">
        {{ strtoupper($creditNote->status) }}
    </div>

    <div class="details">
        <div class="from">
            <h3>From: Supplier</h3>
            <p><strong>{{ $creditNote->supplier->name }}</strong></p>
            <p>{{ $creditNote->supplier->contact_email }}</p>
            <p>{{ $creditNote->supplier->contact_phone }}</p>
        </div>

        <div class="to">
            <h3>To: Buyer / Owner</h3>
            <p><strong>{{ auth()->user()->name }}</strong></p>
            <p>{{ auth()->user()->email }}</p>
            <p>22UniMart Facility</p>
        </div>
    </div>

    <div class="credit-info" style="margin-bottom: 30px;">
        <p><strong>Credit Note No:</strong> {{ $creditNote->credit_note_id }}</p>
        <p><strong>Issue Date:</strong> {{ $creditNote->issue_date->format('M d, Y') }}</p>
        <p><strong>PO Reference:</strong> {{ $creditNote->purchaseOrder ? $creditNote->purchaseOrder->po_number : 'N/A' }}</p>
    </div>

    <h3>Line Items</h3>
    <table>
        <thead>
            <tr>
                <th>Item Name</th>
                <th>Approved Qty</th>
                <th>Unit Price (RM)</th>
                <th>Subtotal (RM)</th>
            </tr>
        </thead>
        <tbody>
            @php
                $items = $creditNote->items ?? collect();
                if($items->isEmpty() && $creditNote->returnRequest) {
                    $items = $creditNote->returnRequest->lines;
                }
            @endphp
            @forelse($items as $creditItem)
                @php
                    $itemName = optional($creditItem->item)->name ?? '—';
                    $qty = $creditItem->quantity ?? 0;
                    $unitPrice = $creditItem->unit_price ?? 0;
                    $subtotal = $creditItem->subtotal ?? 0;
                @endphp
                <tr>
                    <td>{{ $itemName }}</td>
                    <td>{{ $qty }}</td>
                    <td>{{ number_format($unitPrice, 2) }}</td>
                    <td>{{ number_format($subtotal, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4">No itemized details available.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="total">Original Invoice Total:</th>
                <th>RM {{ number_format(optional($creditNote->invoice)->total_amount ?? 0, 2) }}</th>
            </tr>
            <tr>
                <th colspan="3" class="total">Total Credit Amount:</th>
                <th>- RM {{ number_format($creditNote->totalAmount(), 2) }}</th>
            </tr>
            <tr>
                <th colspan="3" class="total">Net to Pay:</th>
                <th>RM {{ number_format(max(0, (optional($creditNote->invoice)->total_amount ?? 0) - $creditNote->totalAmount()), 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        <p><strong>Reason for Credit:</strong> {{ $creditNote->reason ?? 'N/A' }}</p>
        <p>This document is a credit note record. It does not confirm payment or refund unless stamped CREDIT APPLIED.</p>
    </div>
</body>
</html>