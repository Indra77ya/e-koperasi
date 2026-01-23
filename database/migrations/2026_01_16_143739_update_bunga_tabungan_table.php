<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class UpdateBungaTabunganTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('bunga_tabungan', function (Blueprint $table) {
            $table->unsignedBigInteger('nasabah_id')->nullable()->after('anggota_id');
            $table->foreign('nasabah_id')->references('id')->on('nasabahs')->onDelete('cascade');
        });

        // Make anggota_id nullable
        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE bunga_tabungan MODIFY COLUMN anggota_id BIGINT UNSIGNED NULL');
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('bunga_tabungan', function (Blueprint $table) {
            $table->dropForeign(['nasabah_id']);
            $table->dropColumn('nasabah_id');
        });

        if (DB::connection()->getDriverName() !== 'sqlite') {
            DB::statement('ALTER TABLE bunga_tabungan MODIFY COLUMN anggota_id BIGINT UNSIGNED NOT NULL');
        }
    }
}
