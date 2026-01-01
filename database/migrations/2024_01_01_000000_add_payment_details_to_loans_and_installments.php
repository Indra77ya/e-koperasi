<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPaymentDetailsToLoansAndInstallments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add tempo_angsuran to pinjaman table if it doesn't exist
        if (!Schema::hasColumn('pinjaman', 'tempo_angsuran')) {
            Schema::table('pinjaman', function (Blueprint $table) {
                $table->string('tempo_angsuran', 20)->default('bulanan')->after('tenor');
                // Values: harian, mingguan, bulanan
            });
        }

        // Add payment details to pinjaman_angsuran table
        if (!Schema::hasColumn('pinjaman_angsuran', 'metode_pembayaran')) {
            Schema::table('pinjaman_angsuran', function (Blueprint $table) {
                $table->string('metode_pembayaran', 50)->nullable()->after('tanggal_bayar');
                // tunai, transfer, dll
                $table->text('keterangan_pembayaran')->nullable()->after('metode_pembayaran');
                $table->string('bukti_pembayaran')->nullable()->after('keterangan_pembayaran');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pinjaman', function (Blueprint $table) {
            $table->dropColumn('tempo_angsuran');
        });

        Schema::table('pinjaman_angsuran', function (Blueprint $table) {
            $table->dropColumn(['metode_pembayaran', 'keterangan_pembayaran', 'bukti_pembayaran']);
        });
    }
}
