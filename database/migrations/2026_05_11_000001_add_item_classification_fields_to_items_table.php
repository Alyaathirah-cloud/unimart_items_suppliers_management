<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('items', function (Blueprint $table) {
            if (! Schema::hasColumn('items', 'source_type')) {
                $table->enum('source_type', ['supplier', 'direct'])->default('supplier')->after('name');
            }
            if (! Schema::hasColumn('items', 'markup_percentage')) {
                $table->decimal('markup_percentage', 5, 2)->default(20.00)->after('source_type');
            }
            if (! Schema::hasColumn('items', 'uom')) {
                $table->string('uom', 50)->default('unit')->after('markup_percentage');
            }
            if (! Schema::hasColumn('items', 'pieces_per_uom')) {
                $table->integer('pieces_per_uom')->default(1)->after('uom');
            }
            if (! Schema::hasColumn('items', 'is_critical')) {
                $table->boolean('is_critical')->default(false)->after('pieces_per_uom');
            }
            if (! Schema::hasColumn('items', 'reorder_frequency')) {
                $table->string('reorder_frequency', 50)->nullable()->after('is_critical');
            }
            if (! Schema::hasColumn('items', 'selling_price')) {
                $table->decimal('selling_price', 10, 2)->nullable()->after('unit_price');
            }
        });
    }

    public function down(): void
    {
        Schema::table('items', function (Blueprint $table) {
            $cols = ['source_type', 'markup_percentage', 'uom', 'pieces_per_uom', 'is_critical', 'reorder_frequency', 'selling_price'];
            foreach ($cols as $col) {
                if (Schema::hasColumn('items', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
