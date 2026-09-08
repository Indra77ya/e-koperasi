<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPerformanceIndexesToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pinjaman', function (Blueprint $table) {
            $table->index('status');
            $table->index('tanggal_pengajuan');
        });

        Schema::table('pinjaman_angsuran', function (Blueprint $table) {
            $table->index('pinjaman_id');
            $table->index('jatuh_tempo');
            $table->index('status_bayar');
        });

        Schema::table('tabungan', function (Blueprint $table) {
            $table->index('anggota_id');
            $table->index('nasabah_id');
            $table->index('status');
        });

        Schema::table('jaminan', function (Blueprint $table) {
            $table->index('pinjaman_id');
            $table->index('status');
            $table->index('jenis');
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
            $table->dropIndex(['status']);
            $table->dropIndex(['tanggal_pengajuan']);
        });

        Schema::table('pinjaman_angsuran', function (Blueprint $table) {
            $table->dropIndex(['pinjaman_id']);
            $table->dropIndex(['jatuh_tempo']);
            $table->dropIndex(['status_bayar']);
        });

        Schema::table('tabungan', function (Blueprint $table) {
            $table->dropIndex(['anggota_id']);
            $table->dropIndex(['nasabah_id']);
            $table->dropIndex(['status']);
        });

        Schema::table('jaminan', function (Blueprint $table) {
            $table->dropIndex(['pinjaman_id']);
            $table->dropIndex(['status']);
            $table->dropIndex(['jenis']);
        });
    }
}
