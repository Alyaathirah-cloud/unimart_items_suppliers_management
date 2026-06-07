<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; margin-bottom: 30px; }
        .header h1 { margin: 0; font-size: 24px; }
        .header p { margin: 5px 0; color: #666; }
        .details { display: flex; justify-content: space-between; margin-bottom: 30px; }
        .supplier, .invoice-info { width: 48%; }
        .supplier h3, .invoice-info h3 { margin-top: 0; font-size: 16px; }
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
    </div>

    <div class="details">
        <div class="supplier">
            <h3>Supplier Information</h3>
            <p><strong>{{ $invoice->supplier->name }}</strong></p>
            @if($invoice->supplier->address_line_1 || $invoice->supplier->city || $invoice->supplier->country)
                <p>
                    {{ $invoice->supplier->address_line_1 }}<br>
                    @if($invoice->supplier->address_line_2){{ $invoice->supplier->address_line_2 }}<br>@endif
                    @if($invoice->supplier->city || $invoice->supplier->postal_code)
                        {{ $invoice->supplier->postal_code }} {{ $invoice->supplier->city }}@if($invoice->supplier->state), {{ $invoice->supplier->state }}@endif<br>
                    @endif
                    {{ $invoice->supplier->country }}
                </p>
            @endif
            <p>{{ $invoice->supplier->contact_email }}</p>
            <p>{{ $invoice->supplier->contact_phone }}</p>

            <h3>Buyer / Owner Information</h3>
            <p><strong>{{ auth()->user()->name }}</strong></p>
            <p>{{ auth()->user()->email }}</p>
        </div>

        <div class="invoice-info">
            <h3>Invoice Details</h3>
            <p><strong>Invoice No:</strong> {{ $invoice->invoice_number }}</p>
            <p><strong>Date:</strong> {{ $invoice->invoice_date->format('M d, Y') }}</p>
            <p><strong>Payment Due:</strong> {{ $invoice->payment_due_date ? $invoice->payment_due_date->format('M d, Y') : 'N/A' }}</p>
            <p><strong>Status:</strong> {{ ucfirst($invoice->status) }}</p>
        </div>
    </div>

    <h3>Items</h3>
    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Quantity</th>
                <th>Unit Price</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoice->lines as $line)
                <tr>
                    <td>{{ optional($line->item)->name ?? 'N/A' }}</td>
                    <td>{{ $line->quantity }}</td>
                    <td>RM {{ number_format($line->unit_price, 2) }}</td>
                    <td>RM {{ number_format($line->invoice_line_total, 2) }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" style="text-align:center;color:#777;">No invoice line items available.</td>
                </tr>
            @endforelse
        </tbody>
        <tfoot>
            <tr>
                <th colspan="3" class="total">Subtotal:</th>
                <th>RM {{ number_format($invoice->lines->sum('invoice_line_total'), 2) }}</th>
            </tr>
            @if($invoice->getTotalCreditDeduction() > 0)
                <tr>
                    <th colspan="3" class="total">Credit Deduction:</th>
                    <th>- RM {{ number_format($invoice->getTotalCreditDeduction(), 2) }}</th>
                </tr>
            @endif
            <tr>
                <th colspan="3" class="total">Amount Due:</th>
                <th>RM {{ number_format($invoice->getNetPayableAmount(), 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="footer">
        @if($invoice->purchaseOrder)
            <p><strong>Purchase Order:</strong> {{ $invoice->purchaseOrder->po_number }}</p>
            <p><strong>Ordered Date:</strong> {{ $invoice->purchaseOrder->order_date->format('M d, Y') }}</p>
        @endif
    </div>
</body>
</html>