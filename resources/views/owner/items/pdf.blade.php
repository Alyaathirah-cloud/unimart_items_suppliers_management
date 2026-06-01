<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #444; padding: 6px; font-size: 12px; }
        th { background: #f2f2f2; }
    </style>
    <title>Inventory List</title>
</head>
<body>
    <h2>22UniMart Inventory List</h2>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Name</th>
                <th>Category</th>
                <th>Qty</th>
                <th>Expiry</th>
                <th>Supplier</th>
                <th>ROP</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->name }}</td>
                    <td>{{ $item->category ?? '-' }}</td>
                    <td>{{ $item->quantity }}</td>
                    <td>{{ $item->expiry_date ? $item->expiry_date->format('Y-m-d') : '-' }}</td>
                    <td>{{ optional($item->supplier)->name ?? '-' }}</td>
                    <td>{{ $item->reorder_point }}</td>
                    <td>{{ $item->statusLabel() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
