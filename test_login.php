<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$credentials = [
    'email' => 'bluedaisywithu@gmail.com',
    'password' => 'password123'
];

if (Auth::guard('supplier')->attempt($credentials)) {
    echo "Login successful!\n";
    $user = Auth::guard('supplier')->user();
    echo "User ID: " . $user->id . "\n";
    $supplier = $user->supplier;
    if ($supplier) {
        echo "Supplier Profile found: " . $supplier->name . "\n";
    } else {
        echo "NO SUPPLIER PROFILE FOUND!\n";
    }
} else {
    echo "Login failed!\n";
}
