<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (! Schema::hasColumn('invoices', 'source')) {
                $table->enum('source', ['auto', 'manual'])->default('manual')->after('status');
            }
        });

        // Mark all existing records as auto-generated
        \DB::table('invoices')->update(['source' => 'auto']);
    }

    public function down(): void
    {
        Schema::table('invoices', function (Blueprint $table) {
            if (Schema::hasColumn('invoices', 'source')) {
                $table->dropColumn('source');
            }
        });
    }
};
