<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            // Convert status column to VARCHAR(30) permanently to allow any capitalized/lowercase status values
            DB::statement("ALTER TABLE invoices MODIFY COLUMN status VARCHAR(30) NOT NULL DEFAULT 'Unpaid'");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('Active','Partially Credited','Overdue','Closed','pending','settled','paid') NOT NULL DEFAULT 'pending'");
        }
    }
};
