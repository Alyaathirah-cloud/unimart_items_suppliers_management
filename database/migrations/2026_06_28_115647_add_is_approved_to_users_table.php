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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'is_approved')) {
                $table->boolean('is_approved')->default(0)->after('role');
            }
        });

        $columnType = \Illuminate\Support\Facades\DB::select("SHOW COLUMNS FROM users WHERE Field = 'role'")[0]->Type;
        if (str_starts_with($columnType, 'enum')) {
            \Illuminate\Support\Facades\DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'owner', 'supplier', 'staff') NOT NULL DEFAULT 'owner'");
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('owner')->comment('admin, owner, supplier, staff')->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('is_approved');
        });
    }
};
