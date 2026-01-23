<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdatePenarikanTableAddNasabah extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // 1. Make anggota_id nullable
        // Use raw SQL to avoid doctrine/dbal dependency for changing columns
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE penarikan MODIFY anggota_id BIGINT UNSIGNED NULL');
        }

        // 2. Add nasabah_id column
        Schema::table('penarikan', function (Blueprint $table) {
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
        DB::table('penarikan')->whereNull('anggota_id')->delete();

        // Drop nasabah_id
        Schema::table('penarikan', function (Blueprint $table) {
             $table->dropColumn('nasabah_id');
        });

        // Revert anggota_id to NOT NULL
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE penarikan MODIFY anggota_id BIGINT UNSIGNED NOT NULL');
        }
    }
}
