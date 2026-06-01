<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Alter ENUM to accept old and new values
        DB::statement("ALTER TABLE invoices MODIFY status ENUM('unpaid', 'paid', 'Active', 'Closed', 'Partially Credited', 'Overdue') DEFAULT 'Active'");

        // Rename existing statuses to new terminology
        DB::statement("UPDATE invoices SET status = 'Active' WHERE status IN ('unpaid', 'Unpaid', 'active')");
        DB::statement("UPDATE invoices SET status = 'Closed' WHERE status IN ('paid', 'Paid', 'closed')");
        
        // Remove old values from ENUM
        DB::statement("ALTER TABLE invoices MODIFY status ENUM('Active', 'Closed', 'Partially Credited', 'Overdue') DEFAULT 'Active'");
    }

    public function down(): void
    {
        DB::statement("UPDATE invoices SET status = 'unpaid' WHERE status = 'Active'");
        DB::statement("UPDATE invoices SET status = 'paid' WHERE status = 'Closed'");
    }
};
