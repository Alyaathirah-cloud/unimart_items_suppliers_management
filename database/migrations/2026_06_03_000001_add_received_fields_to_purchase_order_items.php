<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Add received-quantity tracking fields to purchase_order_items.
     * These allow the delivery checklist to record per-item received,
     * damaged, and expiry data independently of the ordered quantity.
     */
    public function up(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->integer('received_quantity')->nullable()->after('quantity')
                  ->comment('Actual units received from supplier');
            $table->integer('damaged_quantity')->default(0)->after('received_quantity')
                  ->comment('Units found damaged on receipt');
            $table->integer('good_quantity')->nullable()->after('damaged_quantity')
                  ->comment('received_quantity - damaged_quantity');
            $table->date('expiry_date')->nullable()->after('good_quantity')
                  ->comment('Expiry date stamped on this delivery batch');
        });
    }

    public function down(): void
    {
        Schema::table('purchase_order_items', function (Blueprint $table) {
            $table->dropColumn(['received_quantity', 'damaged_quantity', 'good_quantity', 'expiry_date']);
        });
    }
};
