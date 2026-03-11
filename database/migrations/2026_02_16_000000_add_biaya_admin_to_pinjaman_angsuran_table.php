<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBiayaAdminToPinjamanAngsuranTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pinjaman_angsuran', function (Blueprint $table) {
            if (!Schema::hasColumn('pinjaman_angsuran', 'biaya_admin')) {
                $table->decimal('biaya_admin', 15, 2)->default(0)->after('bunga');
            }
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
            $table->dropColumn('biaya_admin');
        });
    }
}
