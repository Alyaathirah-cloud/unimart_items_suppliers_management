<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSupplierProfileFieldsToSuppliersTable extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->string('contact_person')->nullable()->after('contact_phone');
            $table->string('company_registration_number')->nullable()->after('contact_person');
            $table->string('tax_number')->nullable()->after('company_registration_number');
            $table->string('address_line_1')->nullable()->after('tax_number');
            $table->string('address_line_2')->nullable()->after('address_line_1');
            $table->string('city')->nullable()->after('address_line_2');
            $table->string('state')->nullable()->after('city');
            $table->string('postal_code')->nullable()->after('state');
            $table->string('country')->nullable()->after('postal_code');
            $table->string('bank_name')->nullable()->after('country');
            $table->string('bank_account')->nullable()->after('bank_name');
            $table->boolean('portal_enabled')->default(true)->after('bank_account');
            $table->string('portal_link')->nullable()->after('portal_enabled');
            $table->string('temporary_password')->nullable()->after('portal_link');
            $table->string('invite_email_status')->default('pending')->after('temporary_password');
            $table->string('invite_whatsapp_status')->default('pending')->after('invite_email_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::table('suppliers', function (Blueprint $table) {
            $table->dropColumn([
                'contact_person',
                'company_registration_number',
                'tax_number',
                'address_line_1',
                'address_line_2',
                'city',
                'state',
                'postal_code',
                'country',
                'bank_name',
                'bank_account',
                'portal_enabled',
                'portal_link',
                'temporary_password',
                'invite_email_status',
                'invite_whatsapp_status',
            ]);
        });
    }
}
