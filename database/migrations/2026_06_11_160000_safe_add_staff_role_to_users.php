<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Retrieve the column type from the database
        $columnType = DB::select("SHOW COLUMNS FROM users WHERE Field = 'role'")[0]->Type;
        
        if (str_starts_with($columnType, 'enum')) {
            // It's an ENUM column, use DB::statement to safely alter it
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'owner', 'supplier', 'staff') NOT NULL DEFAULT 'owner'");
        } else {
            // It's a VARCHAR/string column, just update the comment to document the accepted values
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('owner')->comment('admin, owner, supplier, staff')->change();
            });
        }
    }

    public function down(): void
    {
        $columnType = DB::select("SHOW COLUMNS FROM users WHERE Field = 'role'")[0]->Type;
        
        if (str_starts_with($columnType, 'enum')) {
            DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('admin', 'owner', 'supplier') NOT NULL DEFAULT 'owner'");
        } else {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('owner')->comment('admin, owner, supplier')->change();
            });
        }
    }
};
