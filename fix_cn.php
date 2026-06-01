<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$returns = \App\Models\ReturnRequest::where('status', 'Approved')->doesntHave('creditNote')->get();
$count = 0;
foreach($returns as $return) {
    $item = $return->item;
    $amount = ($item->unit_price ?? 0) * $return->quantity;
    \App\Models\CreditNote::create([
        'credit_note_id' => 'CN' . now()->format('Ymd') . str_pad($return->id, 6, '0', STR_PAD_LEFT),
        'return_id' => $return->id,
        'supplier_id' => $return->supplier_id,
        'amount' => $amount,
        'remaining_balance' => $amount,
        'issue_date' => now()->toDateString(),
        'status' => 'Unused'
    ]);
    echo 'Created for RR: ' . $return->id . "\n";
    $count++;
}
echo "Created $count credit notes.\n";
