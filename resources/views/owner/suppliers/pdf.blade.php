<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Suppliers Report</title>
    <style>
        body { font-family: Arial, sans-serif; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Suppliers Report</h1>
    <table>
        <thead>
            <tr>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
            </tr>
        </thead>
        <tbody>
            @foreach($suppliers as $supplier)
            <tr>
                <td>{{ $supplier->name }}</td>
                <td>{{ $supplier->contact_email }}</td>
                <td>{{ $supplier->contact_phone }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>