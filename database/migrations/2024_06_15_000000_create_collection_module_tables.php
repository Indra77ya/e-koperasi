<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCollectionModuleTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add kolektabilitas to pinjaman table
        Schema::table('pinjaman', function (Blueprint $table) {
            $table->string('kolektabilitas')->default('Lancar')->after('status');
        });

        // Create penagihan_log table (History Timeline)
        Schema::create('penagihan_log', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pinjaman_id');
            $table->unsignedBigInteger('user_id')->nullable(); // Petugas yang melakukan penagihan
            $table->string('metode_penagihan'); // Telepon, Kunjungan, WA, Surat
            $table->string('hasil_penagihan'); // Janji Bayar, Tidak Ada Respon, dll
            $table->date('tanggal_janji_bayar')->nullable();
            $table->text('catatan')->nullable();
            $table->string('bukti_foto')->nullable(); // Path to photo if field visit
            $table->timestamps();

            $table->foreign('pinjaman_id')->references('id')->on('pinjaman')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
        });

        // Create penagihan_lapangan table (Field Queue)
        Schema::create('penagihan_lapangan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pinjaman_id');
            $table->unsignedBigInteger('petugas_id')->nullable(); // User ID of collector
            $table->date('tanggal_rencana_kunjungan');
            $table->string('status')->default('baru'); // baru, dalam_proses, selesai, batal
            $table->text('catatan_tugas')->nullable();
            $table->timestamps();

            $table->foreign('pinjaman_id')->references('id')->on('pinjaman')->onDelete('cascade');
            $table->foreign('petugas_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('penagihan_lapangan');
        Schema::dropIfExists('penagihan_log');

        Schema::table('pinjaman', function (Blueprint $table) {
            $table->dropColumn('kolektabilitas');
        });
    }
}
