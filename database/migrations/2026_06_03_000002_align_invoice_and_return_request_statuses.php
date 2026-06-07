<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Rename invoice statuses to align with business spec:
     *   Active           → Pending
     *   Partially Credited → Settled
     *   Closed           → Paid
     *   Overdue          → removed as stored value (computed dynamically)
     *
     * Also adds submitted_at to return_requests for Draft→Pending flow.
     */
    public function up(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            // ── 1. Update invoice status ENUM to include old and new values ───
            DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('Active','Partially Credited','Overdue','Closed','Pending','Settled','Paid') NOT NULL DEFAULT 'Pending'");
        }

        // ── 2. Remap invoice statuses ────────────────────────────────────────
        DB::statement("UPDATE invoices SET status = 'Pending'  WHERE status = 'Active'");
        DB::statement("UPDATE invoices SET status = 'Settled'  WHERE status = 'Partially Credited'");
        DB::statement("UPDATE invoices SET status = 'Paid'     WHERE status = 'Closed'");
        DB::statement("UPDATE invoices SET status = 'Pending'  WHERE status = 'Overdue'");

        if ($driver === 'mysql') {
            // ── 3. Shrink invoice status ENUM (remove old values) ─────────────────
            DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('Pending','Settled','Paid') NOT NULL DEFAULT 'Pending'");
        }

        // ── 4. Add submitted_at to return_requests ───────────────────────────
        Schema::table('return_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('return_requests', 'submitted_at')) {
                $table->timestamp('submitted_at')->nullable()->after('request_date')
                      ->comment('When the owner moved the return request from Draft to Pending/submitted');
            }
        });

        if ($driver === 'mysql') {
            // ── 5. Add Draft to return_requests status ENUM ─────────────────────
            DB::statement("ALTER TABLE return_requests MODIFY COLUMN status ENUM('Draft','Pending','Approved','Rejected','Credit Applied') NOT NULL DEFAULT 'Draft'");
        }
    }

    public function down(): void
    {
        $driver = DB::connection()->getDriverName();

        if ($driver === 'mysql') {
            // Revert invoice statuses enum to include both
            DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('Active','Partially Credited','Overdue','Closed','Pending','Settled','Paid') NOT NULL DEFAULT 'Active'");
        }

        DB::statement("UPDATE invoices SET status = 'Active'             WHERE status = 'Pending'");
        DB::statement("UPDATE invoices SET status = 'Partially Credited' WHERE status = 'Settled'");
        DB::statement("UPDATE invoices SET status = 'Closed'             WHERE status = 'Paid'");

        if ($driver === 'mysql') {
            DB::statement("ALTER TABLE invoices MODIFY COLUMN status ENUM('Active','Partially Credited','Overdue','Closed') NOT NULL DEFAULT 'Active'");
        }

        // Remove submitted_at
        Schema::table('return_requests', function (Blueprint $table) {
            $table->dropColumn('submitted_at');
        });

        if ($driver === 'mysql') {
            // Revert return_requests status enum
            DB::statement("ALTER TABLE return_requests MODIFY COLUMN status ENUM('Pending','Approved','Rejected','Credit Applied') NOT NULL DEFAULT 'Pending'");
        }
    }
};
