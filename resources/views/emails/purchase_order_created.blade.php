<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Purchase Order Created</title>
</head>
<body>
    <h2>22UniMart Purchase Order</h2>
    <p>A new purchase order has been created. Please log in to review and take action.</p>
    <p><strong>Purchase Order ID:</strong> {{ $poNumber }}</p>
    <p><strong>Item:</strong> {{ $itemName }}</p>
    <p><strong>Quantity:</strong> {{ $quantity }}</p>
    <p><a href="{{ $loginUrl }}">Log in to 22UniMart</a></p>
    <p>Thank you.</p>
</body>
</html>
