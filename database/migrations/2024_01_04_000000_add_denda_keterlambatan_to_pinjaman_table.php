<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDendaKeterlambatanToPinjamanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pinjaman', function (Blueprint $table) {
            $table->decimal('denda_keterlambatan', 15, 2)->default(0)->after('biaya_admin');
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
            $table->dropColumn('denda_keterlambatan');
        });
    }
}
