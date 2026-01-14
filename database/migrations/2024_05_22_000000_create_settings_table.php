<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class CreateSettingsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default values
        $defaults = [
            // Instansi
            ['key' => 'company_name', 'value' => 'Koperasi Majapahit'],
            ['key' => 'company_address', 'value' => 'Jl. Koperasi No. 1, Jakarta'],
            ['key' => 'company_phone', 'value' => '021-12345678'],
            ['key' => 'company_email', 'value' => 'info@koperasi.com'],
            ['key' => 'company_logo', 'value' => null], // Path to storage

            // Pinjaman
            ['key' => 'default_interest_rate', 'value' => '10'],
            ['key' => 'default_admin_fee', 'value' => '1'],
            ['key' => 'default_penalty', 'value' => '50000'],
            ['key' => 'loan_limit', 'value' => '50000000'],

            // Akuntansi (Defaults based on existing hardcoded values)
            ['key' => 'coa_cash', 'value' => '1101'],
            ['key' => 'coa_receivable', 'value' => '1103'],
            ['key' => 'coa_revenue_interest', 'value' => '4101'],
            ['key' => 'coa_revenue_admin', 'value' => '4102'],
            ['key' => 'coa_revenue_penalty', 'value' => '4103'],

            // Sistem
            ['key' => 'notification_due_date_threshold', 'value' => '0'], // 0 days (today)
        ];

        DB::table('settings')->insert($defaults);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('settings');
    }
}
