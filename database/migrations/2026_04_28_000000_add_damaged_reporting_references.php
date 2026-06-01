<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            if (! Schema::hasColumn('purchase_orders', 'unit_price')) {
                $table->decimal('unit_price', 10, 2)->nullable()->after('quantity');
            }
            if (! Schema::hasColumn('purchase_orders', 'credit_note_id')) {
                $table->foreignId('credit_note_id')->nullable()->constrained('credit_notes')->nullOnDelete()->after('supplier_id');
            }
        });

        Schema::table('return_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('return_requests', 'purchase_order_id')) {
                $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete()->after('supplier_id');
            }
        });

        Schema::table('credit_notes', function (Blueprint $table) {
            if (! Schema::hasColumn('credit_notes', 'purchase_order_id')) {
                $table->foreignId('purchase_order_id')->nullable()->constrained('purchase_orders')->nullOnDelete()->after('supplier_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_notes', function (Blueprint $table) {
            if (Schema::hasColumn('credit_notes', 'purchase_order_id')) {
                $table->dropConstrainedForeignId('purchase_order_id');
            }
        });

        Schema::table('return_requests', function (Blueprint $table) {
            if (Schema::hasColumn('return_requests', 'purchase_order_id')) {
                $table->dropConstrainedForeignId('purchase_order_id');
            }
        });

        Schema::table('purchase_orders', function (Blueprint $table) {
            if (Schema::hasColumn('purchase_orders', 'credit_note_id')) {
                $table->dropConstrainedForeignId('credit_note_id');
            }
            if (Schema::hasColumn('purchase_orders', 'unit_price')) {
                $table->dropColumn('unit_price');
            }
        });
    }
};
