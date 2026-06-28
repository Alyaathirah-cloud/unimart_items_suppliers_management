<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('invoices', 'sold_estimates')) {
                $table->json('sold_estimates')
                      ->nullable()
                      ->after('confirmed_by')
                      ->comment('Owner overrides for est. sold qty per item. Format: {"item_id": est_sold_qty}');
            }
        });
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'sold_estimates')) {
                $table->dropColumn('sold_estimates');
            }
        });
    }
};
