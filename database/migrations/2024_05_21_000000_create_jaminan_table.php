<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateJaminanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('jaminan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pinjaman_id');
            $table->string('jenis'); // BPKB, Sertifikat, dll.
            $table->string('nomor')->nullable(); // Nomor sertifikat/BPKB
            $table->string('pemilik')->nullable(); // Nama di dokumen
            $table->decimal('nilai_taksasi', 15, 2)->default(0);
            $table->text('foto')->nullable(); // JSON or path
            $table->text('dokumen')->nullable(); // JSON or path
            $table->string('status')->default('disimpan'); // disimpan, dikembalikan
            $table->string('lokasi_penyimpanan')->nullable();
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->string('diterima_oleh')->nullable();
            $table->string('diserahkan_kepada')->nullable();
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('pinjaman_id')->references('id')->on('pinjaman')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('jaminan');
    }
}
