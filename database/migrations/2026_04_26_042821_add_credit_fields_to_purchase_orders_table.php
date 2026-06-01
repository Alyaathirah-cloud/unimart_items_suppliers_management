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
            $table->decimal('total_amount', 10, 2)->after('quantity');
            $table->decimal('credit_deducted', 10, 2)->default(0)->after('total_amount');
            $table->decimal('final_amount', 10, 2)->after('credit_deducted');
            $table->json('applied_credit_notes')->nullable()->after('final_amount'); // Store IDs and amounts
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('purchase_orders', function (Blueprint $table) {
            $table->dropColumn(['total_amount', 'credit_deducted', 'final_amount', 'applied_credit_notes']);
        });
    }
};
