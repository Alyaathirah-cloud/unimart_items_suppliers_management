<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * This migration only adds one column, no data is dropped.
 *
 * Adds accepts_returns (boolean, default true) to the suppliers table.
 * This flag records whether a supplier accepts returns of expired or
 * damaged stock, and is used to drive the return recommendation banner
 * on the Create Return Request page.
 */
class AddAcceptsReturnsToSuppliersTable extends Migration
{
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            // Default true: most suppliers accept returns unless explicitly opted out.
            $table->boolean('accepts_returns')->default(true)->after('portal_link');
        });
    }

    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn('accepts_returns');
        });
    }
}
