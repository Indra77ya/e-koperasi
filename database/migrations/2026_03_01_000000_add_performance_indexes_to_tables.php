<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPerformanceIndexesToTables extends Migration
{
    /**
     * Add index to table if column exists and index doesn't already exist.
     *
     * @param string $table
     * @param string $column
     * @return void
     */
    private function addIndexSafely($table, $column)
    {
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
            return;
        }

        $indexName = "{$table}_{$column}_index";
        try {
            $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
            if (empty($indexes)) {
                Schema::table($table, function (Blueprint $tableBlueprint) use ($column) {
                    $tableBlueprint->index($column);
                });
            }
        } catch (\Exception $e) {
            // Silence exception if index creation fails
        }
    }

    /**
     * Drop index from table if it exists.
     *
     * @param string $table
     * @param string $column
     * @return void
     */
    private function dropIndexSafely($table, $column)
    {
        if (!Schema::hasTable($table) || !Schema::hasColumn($table, $column)) {
            return;
        }

        $indexName = "{$table}_{$column}_index";
        try {
            $indexes = DB::select("SHOW INDEX FROM `{$table}` WHERE Key_name = ?", [$indexName]);
            if (!empty($indexes)) {
                Schema::table($table, function (Blueprint $tableBlueprint) use ($column) {
                    $tableBlueprint->dropIndex([$column]);
                });
            }
        } catch (\Exception $e) {
            // Silence exception if index drop fails
        }
    }

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $this->addIndexSafely('pinjaman', 'status');
        $this->addIndexSafely('pinjaman', 'tanggal_pengajuan');

        $this->addIndexSafely('pinjaman_angsuran', 'pinjaman_id');
        $this->addIndexSafely('pinjaman_angsuran', 'tanggal_jatuh_tempo');
        $this->addIndexSafely('pinjaman_angsuran', 'status');

        $this->addIndexSafely('tabungan', 'anggota_id');
        $this->addIndexSafely('tabungan', 'nasabah_id');

        $this->addIndexSafely('jaminan', 'pinjaman_id');
        $this->addIndexSafely('jaminan', 'status');
        $this->addIndexSafely('jaminan', 'jenis');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $this->dropIndexSafely('pinjaman', 'status');
        $this->dropIndexSafely('pinjaman', 'tanggal_pengajuan');

        $this->dropIndexSafely('pinjaman_angsuran', 'pinjaman_id');
        $this->dropIndexSafely('pinjaman_angsuran', 'tanggal_jatuh_tempo');
        $this->dropIndexSafely('pinjaman_angsuran', 'status');

        $this->dropIndexSafely('tabungan', 'anggota_id');
        $this->dropIndexSafely('tabungan', 'nasabah_id');

        $this->dropIndexSafely('jaminan', 'pinjaman_id');
        $this->dropIndexSafely('jaminan', 'status');
        $this->dropIndexSafely('jaminan', 'jenis');
    }
}
