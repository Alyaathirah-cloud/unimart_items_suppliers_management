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
        Schema::table('return_requests', function (Blueprint $table) {
            if (! Schema::hasColumn('return_requests', 'invoice_number')) {
                $table->string('invoice_number')->nullable()->after('reason');
            }
            if (! Schema::hasColumn('return_requests', 'notes')) {
                $table->text('notes')->nullable()->after('invoice_number');
            }
            if (! Schema::hasColumn('return_requests', 'request_date')) {
                $table->date('request_date')->nullable()->after('status');
            }
            if (! Schema::hasColumn('return_requests', 'created_by')) {
                $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete()->after('request_date');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            if (Schema::hasColumn('return_requests', 'created_by')) {
                $table->dropConstrainedForeignId('created_by');
            }
            if (Schema::hasColumn('return_requests', 'request_date')) {
                $table->dropColumn('request_date');
            }
            if (Schema::hasColumn('return_requests', 'notes')) {
                $table->dropColumn('notes');
            }
            if (Schema::hasColumn('return_requests', 'invoice_number')) {
                $table->dropColumn('invoice_number');
            }
        });
    }
};
