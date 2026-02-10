<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class AddRoleAndRefToTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add role to users
        if (!Schema::hasColumn('users', 'role')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('role')->default('petugas')->after('password');
            });
        }

        // Set admin@example.com as admin
        DB::table('users')->where('email', 'admin@example.com')->update(['role' => 'admin']);

        // Add ref columns to riwayat_tabungan
        if (!Schema::hasColumn('riwayat_tabungan', 'ref_type')) {
            Schema::table('riwayat_tabungan', function (Blueprint $table) {
                $table->string('ref_type')->nullable()->after('saldo');
                $table->unsignedBigInteger('ref_id')->nullable()->after('ref_type');
                $table->index(['ref_type', 'ref_id']);
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });

        Schema::table('riwayat_tabungan', function (Blueprint $table) {
            $table->dropIndex(['ref_type', 'ref_id']);
            $table->dropColumn(['ref_type', 'ref_id']);
        });
    }
}
