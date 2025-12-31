<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePinjamanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pinjaman', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('kode_pinjaman')->unique();
            $table->unsignedInteger('anggota_id');
            $table->string('jenis_pinjaman'); // produktif, konsumtif
            $table->decimal('jumlah_pinjaman', 15, 2);
            $table->integer('tenor'); // in months
            $table->decimal('suku_bunga', 5, 2); // percent per year
            $table->string('jenis_bunga'); // flat, efektif, anuitas
            $table->decimal('biaya_admin', 15, 2)->default(0);
            $table->date('tanggal_pengajuan');
            $table->date('tanggal_persetujuan')->nullable();
            $table->string('status')->default('diajukan'); // diajukan, disetujui, ditolak, berjalan, lunas
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('anggota_id')->references('id')->on('anggota')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pinjaman');
    }
}
