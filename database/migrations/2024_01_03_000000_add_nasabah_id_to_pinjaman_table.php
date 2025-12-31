<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddNasabahIdToPinjamanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pinjaman', function (Blueprint $table) {
            $table->unsignedBigInteger('anggota_id')->nullable()->change();
            $table->unsignedBigInteger('nasabah_id')->nullable()->after('anggota_id');

            $table->foreign('nasabah_id')->references('id')->on('nasabahs')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pinjaman', function (Blueprint $table) {
            $table->dropForeign(['nasabah_id']);
            $table->dropColumn('nasabah_id');
            $table->unsignedBigInteger('anggota_id')->nullable(false)->change();
        });
    }
}
