<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Add whatsapp_number to users table.
 *
 * This stores the owner's WhatsApp number in the database so it can be
 * configured via the UI instead of being hardcoded in .env.
 * Suppliers already have contact_phone which is reused for WhatsApp.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'whatsapp_number')) {
                // WhatsApp number in E.164 format, e.g. +60123456789
                $table->string('whatsapp_number', 30)->nullable()->after('phone');
            }
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (Schema::hasColumn('users', 'whatsapp_number')) {
                $table->dropColumn('whatsapp_number');
            }
        });
    }
};
