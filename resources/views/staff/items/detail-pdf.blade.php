<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { 
            font-family: DejaVu Sans, sans-serif; 
            margin: 20px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 3px solid #0f2044;
            padding-bottom: 15px;
        }
        .company-name { 
            font-size: 18px; 
            font-weight: bold; 
            color: #0f2044;
        }
        .title {
            font-size: 14px;
            color: #666;
            margin-top: 5px;
        }
        .section {
            margin-bottom: 25px;
        }
        .section-title {
            font-size: 12px;
            font-weight: bold;
            color: #fff;
            background-color: #0f2044;
            padding: 8px 12px;
            margin-bottom: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .detail-row {
            display: flex;
            margin-bottom: 8px;
            page-break-inside: avoid;
        }
        .detail-label {
            width: 120px;
            font-weight: bold;
            color: #0f2044;
            font-size: 11px;
        }
        .detail-value {
            flex: 1;
            font-size: 11px;
            color: #333;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: bold;
        }
        .status-ok {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .status-low-stock {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .status-near-expiry {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .status-expired {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert {
            padding: 10px 12px;
            border-radius: 4px;
            font-size: 10px;
            margin-bottom: 12px;
            page-break-inside: avoid;
        }
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .alert-warning {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeaa7;
        }
        .divider {
            border-top: 1px solid #ddd;
            margin: 15px 0;
        }
        .footer {
            margin-top: 30px;
            padding-top: 15px;
            border-top: 1px solid #ddd;
            text-align: center;
            font-size: 10px;
            color: #666;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        td {
            padding: 8px;
            font-size: 11px;
            border-bottom: 1px solid #ddd;
        }
        .label-cell {
            font-weight: bold;
            color: #0f2044;
            width: 40%;
            background-color: #f8f9fa;
        }
        .value-cell {
            color: #333;
            width: 60%;
        }
    </style>
    <title>Item Details - {{ $item->name }}</title>
</head>
<body>
    <div class="header">
        <div class="company-name">22UniMart</div>
        <div class="title">Item Details Report</div>
    </div>

    <!-- Main Item Information -->
    <div class="section">
        <div class="section-title">Item Information</div>
        <table>
            <tr>
                <td class="label-cell">Item Name:</td>
                <td class="value-cell">{{ $item->name }}</td>
            </tr>
            <tr>
                <td class="label-cell">Category:</td>
                <td class="value-cell">{{ $item->category ?? 'Not specified' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Item ID:</td>
                <td class="value-cell">{{ $item->id }}</td>
            </tr>
        </table>
    </div>

    <!-- Stock Information -->
    <div class="section">
        <div class="section-title">Stock Information</div>
        <table>
            <tr>
                <td class="label-cell">Current Quantity:</td>
                <td class="value-cell"><strong>{{ $item->quantity }} units</strong></td>
            </tr>
            <tr>
                <td class="label-cell">Reorder Point:</td>
                <td class="value-cell">{{ $item->reorder_point }} units</td>
            </tr>
            <tr>
                <td class="label-cell">Unit Price:</td>
                <td class="value-cell">{{ $item->unit_price ? 'RM ' . number_format($item->unit_price, 2) : 'Not set' }}</td>
            </tr>
        </table>
    </div>

    <!-- Expiry Information -->
    @if($item->expiry_date)
    <div class="section">
        <div class="section-title">Expiry Information</div>
        <table>
            <tr>
                <td class="label-cell">Expiry Date:</td>
                <td class="value-cell">{{ $item->expiry_date->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="label-cell">Days Until Expiry:</td>
                <td class="value-cell">
                    @php
                        $daysUntilExpiry = now()->diffInDays($item->expiry_date, false);
                    @endphp
                    @if($daysUntilExpiry < 0)
                        <strong>{{ abs($daysUntilExpiry) }} days expired</strong>
                    @else
                        {{ $daysUntilExpiry }} days remaining
                    @endif
                </td>
            </tr>
        </table>
    </div>
    @endif

    <!-- Supplier Information -->
    @if($item->supplier)
    <div class="section">
        <div class="section-title">Supplier Information</div>
        <table>
            <tr>
                <td class="label-cell">Supplier Name:</td>
                <td class="value-cell">{{ $item->supplier->name }}</td>
            </tr>
            <tr>
                <td class="label-cell">Contact Email:</td>
                <td class="value-cell">{{ $item->supplier->contact_email ?? 'Not provided' }}</td>
            </tr>
            <tr>
                <td class="label-cell">Contact Phone:</td>
                <td class="value-cell">{{ $item->supplier->contact_phone ?? 'Not provided' }}</td>
            </tr>
        </table>
    </div>
    @endif

    <!-- Status Alerts -->
    <div class="section">
        <div class="section-title">Status & Alerts</div>
        
        @php
            $statusClass = 'status-ok';
            $statusLabel = $item->statusLabel();
            
            if ($item->expiry_date && $item->expiry_date < now()) {
                $statusClass = 'status-expired';
            } elseif ($item->quantity <= $item->reorder_point) {
                $statusClass = 'status-low-stock';
            } elseif ($item->expiry_date && $item->expiry_date <= now()->addDays($warningDays)) {
                $statusClass = 'status-near-expiry';
            }
        @endphp
        
        <div style="margin-bottom: 12px;">
            <strong>Current Status:</strong> 
            <span class="status-badge {{ $statusClass }}">{{ $statusLabel }}</span>
        </div>

        @if($item->expiry_date && $item->expiry_date < now())
            <div class="alert alert-danger">
                <strong>⚠️ EXPIRED:</strong> This item expired on {{ $item->expiry_date->format('d/m/Y') }}. Please remove from inventory.
            </div>
        @elseif($item->expiry_date && $item->expiry_date <= now()->addDays($warningDays))
            <div class="alert alert-warning">
                <strong>⚠️ EXPIRING SOON:</strong> This item will expire on {{ $item->expiry_date->format('d/m/Y') }} ({{ $warningDays }}-day warning threshold).
            </div>
        @endif

        @if($item->quantity <= $item->reorder_point)
            <div class="alert alert-warning">
                <strong>⚠️ LOW STOCK:</strong> Current quantity ({{ $item->quantity }}) is at or below the reorder point ({{ $item->reorder_point }}). Consider reordering.
            </div>
        @endif
    </div>

    <!-- Additional Information -->
    <div class="section">
        <div class="section-title">Record Information</div>
        <table>
            <tr>
                <td class="label-cell">Created Date:</td>
                <td class="value-cell">{{ $item->created_at->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <td class="label-cell">Last Updated:</td>
                <td class="value-cell">{{ $item->updated_at->format('d/m/Y H:i') }}</td>
            </tr>
        </table>
    </div>

    <div class="footer">
        <p>Report generated on {{ now()->format('d/m/Y H:i:s') }}</p>
        <p>22UniMart Inventory Management System</p>
    </div>
</body>
</html>
