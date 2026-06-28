<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            // Paid date recorded when supplier confirms payment received
            if (!Schema::hasColumn('invoices', 'paid_date')) {
                $table->date('paid_date')->nullable()->after('payment_due_date');
            }
            // Foreign key to supplier user who clicked "Confirm Payment Received"
            if (!Schema::hasColumn('invoices', 'confirmed_by')) {
                $table->unsignedBigInteger('confirmed_by')->nullable()->after('paid_date');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            $table->dropColumnIfExists('paid_date');
            $table->dropColumnIfExists('confirmed_by');
        });
    }
};
