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
        Schema::create('credit_notes', function (Blueprint $table) {
            $table->id();
            $table->string('credit_note_id')->unique();
            $table->unsignedBigInteger('return_id');
            $table->unsignedBigInteger('supplier_id');
            $table->decimal('amount', 10, 2);
            $table->decimal('remaining_balance', 10, 2)->default(0);
            $table->date('issue_date');
            $table->enum('status', ['Unused', 'Used', 'Partially Used'])->default('Unused');
            $table->timestamps();

            $table->foreign('return_id')->references('id')->on('return_requests')->onDelete('cascade');
            $table->foreign('supplier_id')->references('id')->on('suppliers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('credit_notes');
    }
};
