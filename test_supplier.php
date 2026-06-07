<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$user = \App\Models\User::where('role', 'supplier')->latest()->first();
if ($user) {
    echo "Supplier User: " . $user->email . "\n";
    echo "Current Hash: " . $user->password . "\n";
    $user->password = bcrypt('password123'); // Set to known password
    $user->save();
    echo "New Hash: " . $user->password . "\n";
    echo "Password reset to: password123\n";
} else {
    echo "No supplier found.\n";
}
