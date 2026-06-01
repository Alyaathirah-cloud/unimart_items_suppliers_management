<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('return_request_lines', function (Blueprint $table) {
            $table->foreignId('invoice_line_id')->nullable()->after('return_request_id')->constrained('invoice_lines')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('return_request_lines', function (Blueprint $table) {
            $table->dropForeign(['invoice_line_id']);
            $table->dropColumn('invoice_line_id');
        });
    }
};
