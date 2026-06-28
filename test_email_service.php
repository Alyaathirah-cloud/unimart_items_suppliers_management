<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$app->boot();

use App\Services\EmailNotificationService;

$service = new EmailNotificationService();
$to = config('mail.from.address');

echo "Sending staff approval email...\n";
$staffResult = $service->sendStaffApproved($to, 'Test Staff Name');
if ($staffResult['success']) {
    echo "✅ Staff approval email sent!\n";
} else {
    echo "❌ Staff approval email failed: " . $staffResult['error'] . "\n";
}

echo "\nSending supplier invitation email...\n";
$supplierData = [
    'contact_person' => 'Supplier Contact',
    'company_name'   => 'Test Supplier Co',
    'portal_url'     => 'http://127.0.0.1:8000/supplier/login',
    'email'          => $to,
    'password'       => 'TempPass123!',
    'owner_phone'    => '+60123456789',
    'telegram_token' => 'test-token-123'
];
$supplierResult = $service->sendSupplierInvitation($to, $supplierData);
if ($supplierResult['success']) {
    echo "✅ Supplier invitation email sent!\n";
} else {
    echo "❌ Supplier invitation email failed: " . $supplierResult['error'] . "\n";
}
