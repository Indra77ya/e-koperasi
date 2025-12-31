<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePembayaranPinjamanTable extends Migration
{
    public function up()
    {
        Schema::create('pembayaran_pinjaman', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pinjaman_id');
            $table->decimal('jumlah_bayar', 15, 2);
            $table->date('tanggal_bayar');
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('pinjaman_id')->references('id')->on('pinjaman')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pembayaran_pinjaman');
    }
}
