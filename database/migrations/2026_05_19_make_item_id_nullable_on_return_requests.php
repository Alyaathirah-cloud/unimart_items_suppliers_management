<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id')->nullable()->change();
            $table->integer('quantity')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('return_requests', function (Blueprint $table) {
            $table->unsignedBigInteger('item_id')->nullable(false)->change();
            $table->integer('quantity')->nullable(false)->change();
        });
    }
};
