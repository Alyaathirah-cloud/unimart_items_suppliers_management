<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('return_request_lines', function (Blueprint $table) {
            if (!Schema::hasColumn('return_request_lines', 'damage_remark')) {
                $table->text('damage_remark')->nullable()->after('reason')
                    ->comment('Freetext remark required when reason = damaged');
            }
        });
    }

    public function down(): void
    {
        Schema::table('return_request_lines', function (Blueprint $table) {
            $table->dropColumn('damage_remark');
        });
    }
};
