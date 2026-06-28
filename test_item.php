<?php
$item = \App\Models\Item::latest()->first();
if (!$item) {
    echo "No items found.";
    exit;
}
echo "Item: " . $item->name . "\n";
echo "isLowStock: " . (int)$item->isLowStock() . "\n";
echo "isExpired: " . (int)$item->isExpired() . "\n";
echo "isNearExpiry: " . (int)$item->isNearExpiry() . "\n";
echo "isDamaged: " . (int)$item->isDamaged() . "\n";
echo "selling_price: " . $item->selling_price . "\n";
echo "hasPendingReturn: " . (int)$item->hasPendingReturn() . "\n";
echo "All ok.\n";
