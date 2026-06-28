<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$rrs = \App\Models\ReturnRequest::orderBy('id', 'desc')->take(5)->get(['id', 'return_number', 'status', 'supplier_id', 'created_by', 'created_at']);
foreach($rrs as $rr) {
    echo json_encode($rr) . "\n";
}
