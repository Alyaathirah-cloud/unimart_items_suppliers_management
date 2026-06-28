<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$items = \App\Models\Item::whereNull('unit_price')->orWhere('unit_price', 0)->get();
$count = 0;
foreach($items as $item) {
    $item->update(['unit_price' => rand(5, 50) + (rand(0, 99) / 100)]);
    $count++;
}
echo "Updated $count items with random unit price.\n";

$notes = \App\Models\CreditNote::all();
$nc = 0;
foreach($notes as $note) {
    if($note->amount == 0 && $note->returnRequest) {
        $amount = $note->returnRequest->lines()->sum('subtotal');
        $note->update([
            'amount' => $amount,
            'remaining_balance' => $amount
        ]);
        $nc++;
    }
}
echo "Recalculated $nc credit notes.\n";
