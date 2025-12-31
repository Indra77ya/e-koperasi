<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePinjamanAngsuranTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pinjaman_angsuran', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('pinjaman_id');
            $table->integer('angsuran_ke');
            $table->date('tanggal_jatuh_tempo');
            $table->date('tanggal_bayar')->nullable();
            $table->decimal('total_angsuran', 15, 2);
            $table->decimal('pokok', 15, 2);
            $table->decimal('bunga', 15, 2);
            $table->decimal('sisa_pinjaman', 15, 2);
            $table->string('status')->default('belum_lunas'); // belum_lunas, lunas
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
        Schema::dropIfExists('pinjaman_angsuran');
    }
}
