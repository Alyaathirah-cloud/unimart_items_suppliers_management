<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('direct_purchase_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('direct_purchase_id')->constrained('direct_purchases')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->integer('quantity');
            $table->string('uom', 50)->default('unit');
            $table->decimal('amount_paid', 10, 2);
            $table->decimal('unit_price', 10, 2)->default(0);    // amount_paid ÷ quantity
            $table->decimal('selling_price', 10, 2)->default(0); // unit_price × 1.30
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('direct_purchase_lines');
    }
};
