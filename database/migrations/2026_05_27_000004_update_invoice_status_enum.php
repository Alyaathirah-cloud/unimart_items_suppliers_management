<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("UPDATE invoices SET status = 'Active' WHERE status IN ('unpaid', 'Unpaid', 'active')");
        DB::statement("UPDATE invoices SET status = 'Closed' WHERE status IN ('paid', 'Paid', 'closed')");
    }

    public function down(): void
    {
        DB::statement("UPDATE invoices SET status = 'unpaid' WHERE status = 'Active'");
        DB::statement("UPDATE invoices SET status = 'paid' WHERE status = 'Closed'");
    }
};