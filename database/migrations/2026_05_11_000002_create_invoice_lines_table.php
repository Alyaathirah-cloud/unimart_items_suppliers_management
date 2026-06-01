<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('invoice_lines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('invoice_id')->constrained('invoices')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->string('uom', 50)->default('unit');
            $table->integer('quantity');
            $table->decimal('invoice_line_total', 10, 2);  // as stated on physical invoice
            $table->decimal('unit_price', 10, 2);          // line_total ÷ pieces_per_uom
            $table->decimal('selling_price', 10, 2);       // unit_price × markup
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('invoice_lines');
    }
};
