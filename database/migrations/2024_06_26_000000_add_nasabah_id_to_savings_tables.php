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
        // Use raw SQL to avoid doctrine/dbal dependency for changing columns
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE setoran MODIFY anggota_id BIGINT UNSIGNED NULL');
        }

        Schema::table('setoran', function (Blueprint $table) {
            $table->unsignedBigInteger('nasabah_id')->nullable()->after('anggota_id');
        });

        // 2. Update tabungan table
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE tabungan MODIFY anggota_id BIGINT UNSIGNED NULL');
        }

        Schema::table('tabungan', function (Blueprint $table) {
            $table->unsignedBigInteger('nasabah_id')->nullable()->after('anggota_id');
        });

        // 3. Update riwayat_tabungan table
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE riwayat_tabungan MODIFY anggota_id BIGINT UNSIGNED NULL');
        }

        Schema::table('riwayat_tabungan', function (Blueprint $table) {
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

        // Revert columns
        Schema::table('setoran', function (Blueprint $table) {
             $table->dropColumn('nasabah_id');
        });
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE setoran MODIFY anggota_id BIGINT UNSIGNED NOT NULL');
        }

        Schema::table('tabungan', function (Blueprint $table) {
             $table->dropColumn('nasabah_id');
        });
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE tabungan MODIFY anggota_id BIGINT UNSIGNED NOT NULL');
        }

        Schema::table('riwayat_tabungan', function (Blueprint $table) {
             $table->dropColumn('nasabah_id');
        });
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE riwayat_tabungan MODIFY anggota_id BIGINT UNSIGNED NOT NULL');
        }
    }
}
