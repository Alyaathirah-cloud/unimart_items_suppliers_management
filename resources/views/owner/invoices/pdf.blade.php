<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Invoice {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #333;
            font-size: 13px;
        }
        .text-blue { color: #1E3A5F; }
        .bg-blue { background-color: #1E3A5F; color: #ffffff; }
        .w-100 { width: 100%; }
        table { width: 100%; border-collapse: collapse; }
        .top-section { margin-bottom: 30px; }
        .top-section td { vertical-align: top; }
        .company-info { width: 50%; }
        .invoice-header { width: 50%; text-align: right; }
        .invoice-title { font-size: 32px; font-weight: bold; margin-bottom: 10px; }
        .invoice-meta-table { width: auto; float: right; }
        .invoice-meta-table td { padding: 4px 8px; text-align: left; }
        .invoice-meta-table .label { font-weight: bold; }
        
        .bill-ship-section { margin-bottom: 20px; }
        .bill-ship-section td { vertical-align: top; width: 50%; padding-right: 20px; }
        .box-header { padding: 6px 10px; font-weight: bold; font-size: 14px; margin-bottom: 10px; }
        
        .middle-section { margin-bottom: 20px; }
        .middle-section th { padding: 8px 10px; font-weight: bold; font-size: 12px; text-align: left; }
        .middle-section td { padding: 8px 10px; background-color: #F7F9FC; border: 1px solid #fff; }
        
        .items-table th { padding: 10px; font-size: 12px; font-weight: bold; text-align: left; border-right: 1px solid #fff; }
        .items-table td { padding: 10px; border-bottom: 1px solid #ddd; }
        .items-table .row-even { background-color: #F7F9FC; }
        .items-table .row-odd { background-color: #ffffff; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .bottom-section { margin-top: 30px; }
        .bottom-section td { vertical-align: top; }
        .comments-box { width: 60%; padding-right: 30px; }
        .totals-box { width: 40%; }
        .totals-table td { padding: 6px 10px; }
        .totals-table .total-row td { font-weight: bold; font-size: 16px; border-top: 2px solid #1E3A5F; padding-top: 10px; }
        
        .footer { margin-top: 50px; text-align: center; font-size: 12px; }
        .footer-thanks { font-style: italic; font-size: 14px; margin-top: 10px; font-weight: bold; }
        
        .empty-row td { color: transparent; border-bottom: 1px solid #ddd; }
    </style>
</head>
<body>

    <table class="top-section">
        <tr>
            <td class="company-info">
                <h2 style="margin-top: 0; color: #1E3A5F;">{{ $invoice->supplier->name }}</h2>
                @if($invoice->supplier->address_line_1 || $invoice->supplier->city || $invoice->supplier->country)
                    {{ $invoice->supplier->address_line_1 }}<br>
                    @if($invoice->supplier->address_line_2){{ $invoice->supplier->address_line_2 }}<br>@endif
                    @if($invoice->supplier->city || $invoice->supplier->postal_code)
                        {{ $invoice->supplier->city }}, @if($invoice->supplier->state){{ $invoice->supplier->state }}, @endif {{ $invoice->supplier->postal_code }}<br>
                    @endif
                    {{ $invoice->supplier->country }}<br>
                @endif
                @if($invoice->supplier->contact_phone) Phone: {{ $invoice->supplier->contact_phone }}<br> @endif
                @if($invoice->supplier->contact_email) Email: {{ $invoice->supplier->contact_email }} @endif
            </td>
            <td class="invoice-header text-blue">
                <div class="invoice-title">INVOICE</div>
                <table class="invoice-meta-table">
                    <tr>
                        <td class="label">DATE:</td>
                        <td>{{ $invoice->invoice_date->format('M d, Y') }}</td>
                    </tr>
                    <tr>
                        <td class="label">INVOICE #:</td>
                        <td>{{ $invoice->invoice_number }}</td>
                    </tr>
                    <tr>
                        <td class="label">CUSTOMER ID:</td>
                        <td>{{ auth()->user()->id ?? auth()->user()->name }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <table class="bill-ship-section w-100">
        <tr>
            <td>
                <div class="box-header bg-blue">BILL TO</div>
                <strong>{{ auth()->user()->name }}</strong><br>
                Company: 22UniMart<br>
                @if(auth()->user()->phone) Phone: {{ auth()->user()->phone }} @endif
            </td>
            <td style="padding-right: 0;">
                <div class="box-header bg-blue">SHIP TO</div>
                <strong>{{ auth()->user()->name }}</strong><br>
                Company: 22UniMart<br>
                @if(auth()->user()->phone) Phone: {{ auth()->user()->phone }} @endif
            </td>
        </tr>
    </table>

    <table class="middle-section w-100">
        <tr class="bg-blue">
            <th>SUPPLIER</th>
            <th>PO #</th>
            <th>DELIVERY DATE</th>
            <th>PAYMENT TERMS</th>
        </tr>
        <tr>
            <td>{{ $invoice->supplier->name }}</td>
            <td>{{ optional($invoice->purchaseOrder)->po_number ?? 'N/A' }}</td>
            <td>{{ optional($invoice->purchaseOrder)->order_date ? $invoice->purchaseOrder->order_date->format('M d, Y') : 'N/A' }}</td>
            <td>Net 30 Days</td>
        </tr>
    </table>

    <table class="items-table w-100">
        <tr class="bg-blue">
            <th>ITEM #</th>
            <th>DESCRIPTION</th>
            <th class="text-center">QTY</th>
            <th class="text-right">UNIT PRICE</th>
            <th class="text-right">TOTAL</th>
        </tr>
        @php
            $rows = 0;
            $subtotal = 0;
        @endphp
        @forelse($invoice->lines as $index => $line)
            @php 
                $rows++; 
                $subtotal += $line->invoice_line_total;
            @endphp
            <tr class="{{ $index % 2 == 0 ? 'row-even' : 'row-odd' }}">
                <td>{{ optional($line->item)->id ?? 'N/A' }}</td>
                <td>{{ optional($line->item)->name ?? 'Unknown Item' }}</td>
                <td class="text-center">{{ $line->quantity }}</td>
                <td class="text-right">RM {{ number_format($line->unit_price, 2) }}</td>
                <td class="text-right">RM {{ number_format($line->invoice_line_total, 2) }}</td>
            </tr>
        @empty
            @if($invoice->purchaseOrder && $invoice->purchaseOrder->quantity)
                @php 
                    $rows++; 
                    $subtotal = $invoice->total_amount;
                @endphp
                <tr class="row-even">
                    <td>{{ optional($invoice->purchaseOrder->item)->id ?? 'N/A' }}</td>
                    <td>{{ optional($invoice->purchaseOrder->item)->name ?? 'Unknown Item' }}</td>
                    <td class="text-center">{{ $invoice->purchaseOrder->quantity }}</td>
                    <td class="text-right">RM {{ number_format($invoice->purchaseOrder->unit_price, 2) }}</td>
                    <td class="text-right">RM {{ number_format($invoice->total_amount, 2) }}</td>
                </tr>
            @endif
        @endforelse
        
        {{-- Fill remaining rows --}}
        @for($i = $rows; $i < 8; $i++)
            <tr class="{{ $i % 2 == 0 ? 'row-even' : 'row-odd' }} empty-row">
                <td>-</td>
                <td>-</td>
                <td class="text-center">-</td>
                <td class="text-right">-</td>
                <td class="text-right">-</td>
            </tr>
        @endfor
    </table>

    <table class="bottom-section w-100">
        <tr>
            <td class="comments-box">
                <strong>Comments / Instructions:</strong><br><br>
                1. Total payment due in 30 days<br>
                2. Please include the invoice number on your payment
            </td>
            <td class="totals-box">
                <table class="totals-table w-100">
                    <tr>
                        <td>SUBTOTAL</td>
                        <td class="text-right">RM {{ number_format($subtotal, 2) }}</td>
                    </tr>
                    <tr>
                        <td>TAX RATE</td>
                        <td class="text-right">0%</td>
                    </tr>
                    <tr>
                        <td>TAX</td>
                        <td class="text-right">RM 0.00</td>
                    </tr>
                    @if($invoice->getTotalCreditDeduction() > 0)
                    <tr>
                        <td>CREDIT</td>
                        <td class="text-right">- RM {{ number_format($invoice->getTotalCreditDeduction(), 2) }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td>NET TO PAY</td>
                        <td class="text-right">RM {{ number_format($invoice->getNetPayableAmount(), 2) }}</td>
                    </tr>
                    <tr class="total-row text-blue">
                        <td>TOTAL</td>
                        <td class="text-right">RM {{ number_format($invoice->getNetPayableAmount(), 2) }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <div class="footer">
        Make all payments payable to {{ $invoice->supplier->name }}<br>
        If you have any questions, please contact {{ $invoice->supplier->contact_email ?? 'us' }}<br>
        <div class="footer-thanks text-blue">Thank You For Your Business!</div>
    </div>

</body>
</html>