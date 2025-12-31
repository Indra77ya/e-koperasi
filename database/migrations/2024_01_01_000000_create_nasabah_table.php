<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNasabahTable extends Migration
{
    public function up()
    {
        Schema::create('nasabah', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('nik')->unique();
            $table->string('nama');
            $table->text('alamat');
            $table->string('no_hp');
            $table->string('pekerjaan');
            $table->text('info_bisnis')->nullable();
            $table->string('file_ktp')->nullable();
            $table->string('file_jaminan')->nullable();
            $table->string('status_risiko')->default('safe'); // safe, blacklist, warning
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('nasabah');
    }
}
