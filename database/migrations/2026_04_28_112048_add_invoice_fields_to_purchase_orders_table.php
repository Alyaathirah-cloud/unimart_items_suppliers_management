<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // First, expand the enum to include both old and new values
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'draft', 'sent', 'received', 'partial', 'cancelled'])->default('draft')->change();
        });

        // Then update existing statuses
        DB::statement("UPDATE purchase_orders SET status = CASE 
            WHEN status = 'Pending' THEN 'draft' 
            WHEN status = 'Approved' THEN 'sent' 
            WHEN status = 'Rejected' THEN 'cancelled' 
            ELSE status END");

        // Add new columns
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->string('invoice_number')->nullable();
            $table->date('invoice_date')->nullable();
            $table->string('invoice_file')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['invoice_number', 'invoice_date', 'invoice_file']);
            // Revert status back
            DB::statement("UPDATE purchase_orders SET status = CASE 
                WHEN status = 'draft' THEN 'Pending' 
                WHEN status = 'sent' THEN 'Approved' 
                WHEN status = 'cancelled' THEN 'Rejected' 
                ELSE status END");
            $table->enum('status', ['Pending', 'Approved', 'Rejected'])->default('Pending')->change();
        });
    }
};
