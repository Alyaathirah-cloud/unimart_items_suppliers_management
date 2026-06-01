<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Credit Notes Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Credit Notes Report</h1>
    <table>
        <thead>
            <tr>
                <th>Credit Note ID</th>
                <th>Supplier</th>
                <th>Return Request</th>
                <th>Amount</th>
                <th>Remaining Balance</th>
                <th>Status</th>
                <th>Issue Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($creditNotes as $note)
            <tr>
                <td>{{ $note->credit_note_id }}</td>
                <td>{{ $note->supplier->name ?? 'N/A' }}</td>
                <td>{{ $note->returnRequest->return_number ?? 'N/A' }}</td>
                <td>RM {{ number_format($note->amount, 2) }}</td>
                <td>RM {{ number_format($note->remaining_balance, 2) }}</td>
                <td>{{ $note->status }}</td>
                <td>{{ $note->issue_date->format('Y-m-d') }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>