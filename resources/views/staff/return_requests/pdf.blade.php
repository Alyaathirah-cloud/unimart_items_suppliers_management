<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Return Requests Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Return Requests Report</h1>
    <table>
        <thead>
            <tr>
                <th>Return Number</th>
                <th>Item</th>
                <th>Supplier</th>
                <th>Quantity</th>
                <th>Reason</th>
                <th>Credit Note</th>
                <th>Status</th>
                <th>Created At</th>
            </tr>
        </thead>
        <tbody>
            @foreach($requests as $request)
            <tr>
                <td>{{ $request->return_number }}</td>
                <td>{{ $request->item->name ?? 'N/A' }}</td>
                <td>{{ $request->supplier->name ?? 'N/A' }}</td>
                <td>{{ $request->quantity }}</td>
                <td>{{ $request->reason }}</td>
                <td>{{ $request->creditNote ? $request->creditNote->credit_note_id : 'N/A' }}</td>
                <td>{{ $request->status }}</td>
                <td>{{ $request->created_at->format('Y-m-d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
