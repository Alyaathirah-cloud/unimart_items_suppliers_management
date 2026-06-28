<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Orders Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Purchase Orders Report</h1>
    <table>
        <thead>
            <tr>
                <th>PO Number</th>
                <th>Item</th>
                <th>Supplier</th>
                <th>Quantity</th>
                <th>Total Amount</th>
                <th>Status</th>
                <th>Order Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($orders as $order)
            <tr>
                <td>{{ $order->po_number }}</td>
                <td>{{ $order->item->name ?? 'N/A' }}</td>
                <td>{{ $order->supplier->name ?? 'N/A' }}</td>
                <td>{{ $order->quantity }}</td>
                <td>RM {{ number_format($order->total_amount, 2) }}</td>
                <td>{{ $order->status }}</td>
                <td>{{ $order->order_date ? $order->order_date->format('Y-m-d') : 'N/A' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
