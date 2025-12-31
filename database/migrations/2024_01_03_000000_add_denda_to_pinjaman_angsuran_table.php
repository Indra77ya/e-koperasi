<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDendaToPinjamanAngsuranTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pinjaman_angsuran', function (Blueprint $table) {
            $table->decimal('denda', 15, 2)->default(0)->after('sisa_pinjaman');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pinjaman_angsuran', function (Blueprint $table) {
            $table->dropColumn('denda');
        });
    }
}
