<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('return_request_lines', function (Blueprint $table) {
            // null = not reviewed yet, 0 = fully rejected, >0 = partially/fully approved
            $table->integer('approved_qty')->nullable()->after('quantity');
            $table->decimal('approved_subtotal', 10, 2)->nullable()->after('approved_qty');
        });
    }

    public function down(): void
    {
        Schema::table('return_request_lines', function (Blueprint $table) {
            $table->dropColumn(['approved_qty', 'approved_subtotal']);
        });
    }
};
