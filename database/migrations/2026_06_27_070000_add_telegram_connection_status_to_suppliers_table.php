<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('telegram_username')->nullable()->after('telegram_connection_token');
            $table->boolean('telegram_connected')->default(false)->after('telegram_username');
            $table->timestamp('telegram_connected_at')->nullable()->after('telegram_connected');
        });
    }

    public function down(): void
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn(['telegram_username', 'telegram_connected', 'telegram_connected_at']);
        });
    }
};
