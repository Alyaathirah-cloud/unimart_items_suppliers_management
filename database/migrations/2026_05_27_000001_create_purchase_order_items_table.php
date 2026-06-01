<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('purchase_order_id')->constrained('purchase_orders')->onDelete('cascade');
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2)->default(0);
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->timestamps();
        });

        // Migrate existing single-item POs into the new table
        $pos = DB::table('purchase_orders')
            ->whereNotNull('item_id')
            ->get();

        foreach ($pos as $po) {
            DB::table('purchase_order_items')->insert([
                'purchase_order_id' => $po->id,
                'item_id'           => $po->item_id,
                'quantity'          => $po->quantity ?? 0,
                'unit_price'        => $po->unit_price ?? 0,
                'subtotal'          => $po->total_amount ?? 0,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_order_items');
    }
};
