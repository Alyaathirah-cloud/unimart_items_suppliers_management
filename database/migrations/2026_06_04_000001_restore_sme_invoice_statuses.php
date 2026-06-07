<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();
        if ($driver !== 'mysql') return;

        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('Active','Partially Credited','Overdue','Closed','Pending','Settled','Paid') NOT NULL DEFAULT 'Active'");

        DB::statement("UPDATE invoices SET status = 'Active' WHERE status = 'Pending'");
        DB::statement("UPDATE invoices SET status = 'Partially Credited' WHERE status = 'Settled'");
        DB::statement("UPDATE invoices SET status = 'Closed' WHERE status = 'Paid'");

        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('Active','Partially Credited','Overdue','Closed') NOT NULL DEFAULT 'Active'");
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();
        if ($driver !== 'mysql') return;

        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('Active','Partially Credited','Overdue','Closed','Pending','Settled','Paid') NOT NULL DEFAULT 'Pending'");

        DB::statement("UPDATE invoices SET status = 'Pending' WHERE status = 'Active'");
        DB::statement("UPDATE invoices SET status = 'Settled' WHERE status = 'Partially Credited'");
        DB::statement("UPDATE invoices SET status = 'Paid' WHERE status = 'Closed'");
        DB::statement("UPDATE invoices SET status = 'Pending' WHERE status = 'Overdue'");

        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('Pending','Settled','Paid') NOT NULL DEFAULT 'Pending'");
    }
};
