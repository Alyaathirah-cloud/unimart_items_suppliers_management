<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// This migration only adds columns, no data is dropped
return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add pending, settled, paid to the invoice status enum
        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('Active','Partially Credited','Overdue','Closed','pending','settled','paid') NOT NULL DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert to original SME invoice statuses
        DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('Active','Partially Credited','Overdue','Closed') NOT NULL DEFAULT 'Active'");
    }
};
