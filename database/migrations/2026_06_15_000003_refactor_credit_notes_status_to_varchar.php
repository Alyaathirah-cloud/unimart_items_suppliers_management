<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Simplifies credit_notes.status from a 3-value enum
 * (Unused / Used / Partially Used) to a plain varchar
 * with a single value 'Issued'.
 *
 * Credit notes are purely evidence of supplier approval; no balance
 * tracking is required.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            // Change enum to varchar first, then update existing rows
            DB::statement("ALTER TABLE credit_notes MODIFY COLUMN status VARCHAR(50) NOT NULL DEFAULT 'Issued'");
            DB::statement("UPDATE credit_notes SET status = 'Issued'");

            // Remove remaining_balance – no longer used
            if (Schema::hasColumn('credit_notes', 'remaining_balance')) {
                Schema::table('credit_notes', function (Blueprint $table) {
                    $table->dropColumn('remaining_balance');
                });
            }
        }
    }

    public function down(): void
    {
        if (DB::connection()->getDriverName() === 'mysql') {
            // Restore remaining_balance
            if (!Schema::hasColumn('credit_notes', 'remaining_balance')) {
                Schema::table('credit_notes', function (Blueprint $table) {
                    $table->decimal('remaining_balance', 10, 2)->default(0)->after('amount');
                });
            }
            // Restore enum — all rows become 'Unused'
            DB::statement("ALTER TABLE credit_notes MODIFY COLUMN status ENUM('Unused','Used','Partially Used') NOT NULL DEFAULT 'Unused'");
            DB::statement("UPDATE credit_notes SET status = 'Unused'");
        }
    }
};
