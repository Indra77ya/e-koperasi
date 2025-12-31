<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNasabahTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nasabah', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nik')->unique();
            $table->string('nama');
            $table->text('alamat')->nullable();
            $table->string('no_hp', 20)->nullable();
            $table->string('pekerjaan')->nullable();
            $table->text('detail_usaha')->nullable();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('file_ktp')->nullable();
            $table->string('file_jaminan')->nullable();
            $table->enum('status', ['aktif', 'non-aktif', 'blacklist', 'berisiko'])->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nasabah');
    }
}
