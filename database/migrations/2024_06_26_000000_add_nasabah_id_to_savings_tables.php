<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddNasabahIdToSavingsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Update setoran table
        Schema::table('setoran', function (Blueprint $table) {
            $table->unsignedBigInteger('anggota_id')->nullable()->change();
            $table->unsignedBigInteger('nasabah_id')->nullable()->after('anggota_id');
        });

        // 2. Update tabungan table
        Schema::table('tabungan', function (Blueprint $table) {
            $table->unsignedBigInteger('anggota_id')->nullable()->change();
            $table->unsignedBigInteger('nasabah_id')->nullable()->after('anggota_id');
        });

        // 3. Update riwayat_tabungan table
        Schema::table('riwayat_tabungan', function (Blueprint $table) {
            $table->unsignedBigInteger('anggota_id')->nullable()->change();
            $table->unsignedBigInteger('nasabah_id')->nullable()->after('anggota_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Clean up data that would violate the non-null constraint
        DB::table('setoran')->whereNull('anggota_id')->delete();
        DB::table('tabungan')->whereNull('anggota_id')->delete();
        DB::table('riwayat_tabungan')->whereNull('anggota_id')->delete();

        Schema::table('setoran', function (Blueprint $table) {
             $table->dropColumn('nasabah_id');
             $table->unsignedBigInteger('anggota_id')->nullable(false)->change();
        });
        Schema::table('tabungan', function (Blueprint $table) {
             $table->dropColumn('nasabah_id');
             $table->unsignedBigInteger('anggota_id')->nullable(false)->change();
        });
        Schema::table('riwayat_tabungan', function (Blueprint $table) {
             $table->dropColumn('nasabah_id');
             $table->unsignedBigInteger('anggota_id')->nullable(false)->change();
        });
    }
}
