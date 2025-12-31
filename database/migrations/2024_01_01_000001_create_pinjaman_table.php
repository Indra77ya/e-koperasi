<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePinjamanTable extends Migration
{
    public function up()
    {
        Schema::create('pinjaman', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('nasabah_id');
            $table->decimal('jumlah_pinjaman', 15, 2);
            $table->date('tanggal_pinjaman');
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->string('status')->default('pending'); // pending, lunas, macet, active
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('nasabah_id')->references('id')->on('nasabah')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('pinjaman');
    }
}
