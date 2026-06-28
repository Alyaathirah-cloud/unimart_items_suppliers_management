<?php
/**
 * Three-step enum fix:
 * 1. Convert status column to VARCHAR(30) to allow unrestricted updates
 * 2. Migrate all legacy status values to canonical Unpaid/Settled/Paid
 * 3. Lock column back to ENUM('Unpaid','Settled','Paid')
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

$driver = DB::connection()->getDriverName();

if ($driver === 'mysql') {
    // Step 1: Convert to VARCHAR so we can freely update values
    DB::statement("ALTER TABLE invoices MODIFY COLUMN status VARCHAR(30) NOT NULL DEFAULT 'Unpaid'");
    echo "Step 1 done: status changed to VARCHAR(30).\n";

    // Step 2: Migrate legacy / lowercase statuses to canonical capitalized values
    DB::table('invoices')->whereIn('status', ['Active','active','Overdue','overdue','pending','Pending','unpaid','Closed'])->update(['status' => 'Unpaid']);
    DB::table('invoices')->whereIn('status', ['Partially Credited','partially credited','settled'])->update(['status' => 'Settled']);
    DB::table('invoices')->where('status', 'paid')->update(['status' => 'Paid']);
    echo "Step 2 done: existing rows migrated to canonical statuses.\n";

    // Step 3: Lock back to a clean ENUM
    DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('Unpaid','Settled','Paid') NOT NULL DEFAULT 'Unpaid'");
    echo "Step 3 done: enum locked to Unpaid/Settled/Paid.\n";
} else {
    // Non-MySQL: just migrate the data (column is already VARCHAR)
    DB::table('invoices')->whereIn('status', ['Active','active','Overdue','overdue','pending','Pending','unpaid','Closed'])->update(['status' => 'Unpaid']);
    DB::table('invoices')->whereIn('status', ['Partially Credited','partially credited','settled'])->update(['status' => 'Settled']);
    DB::table('invoices')->where('status', 'paid')->update(['status' => 'Paid']);
    echo "Done: data migrated (non-MySQL driver: {$driver}).\n";
}
