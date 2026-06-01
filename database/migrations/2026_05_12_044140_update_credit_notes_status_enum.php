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
        Schema::table('credit_notes', function (Blueprint $table) {
            $table->enum('status', ['Unused', 'Partially Used', 'Used', 'Credit Applied'])
                  ->default('Unused')
                  ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('credit_notes', function (Blueprint $table) {
            $table->enum('status', ['Unused', 'Used', 'Partially Used'])
                  ->default('Unused')
                  ->change();
        });
    }
};
