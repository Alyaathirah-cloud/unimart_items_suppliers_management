<?php

// This migration only removes columns. No data is seeded or altered.
// Legacy single-item fields (item_id, quantity, reason) on return_requests
// are superseded by the return_request_lines table which stores per-line data.

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            // Drop the foreign key on item_id before dropping the column
            if (Schema::hasColumn('return_requests', 'item_id')) {
                // Try to drop the FK constraint (name follows Laravel convention)
                try {
                    $table->dropForeign(['item_id']);
                } catch (\Throwable $e) {
                    // FK may already be gone or have a non-standard name — safe to continue
                }
                $table->dropColumn('item_id');
            }

            if (Schema::hasColumn('return_requests', 'quantity')) {
                $table->dropColumn('quantity');
            }

            if (Schema::hasColumn('return_requests', 'reason')) {
                $table->dropColumn('reason');
            }
        });
    }

    public function down(): void
    {
        // This migration only removes columns. No data is dropped.
        // Restore the columns for rollback (as nullable to avoid breaking existing rows).
        Schema::table('return_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id')->nullable()->after('return_number');
            $table->integer('quantity')->nullable()->after('item_id');
            $table->string('reason')->nullable()->after('quantity');
        });
    }
};
