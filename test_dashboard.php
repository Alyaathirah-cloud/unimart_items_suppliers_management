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
    $request = \Illuminate\Http\Request::create('/supplier/dashboard', 'GET');
    $request->setSession(session()); // attach session
    $response = $app->handle($request);
    echo "Status: " . $response->getStatusCode() . "\n";
    echo "Content: " . substr($response->getContent(), 0, 500) . "\n";
} else {
    echo "Login failed!\n";
}
