<?php

use Illuminate\Database\Seeder;
use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Models\Member;
use App\Models\Nasabah;
use Carbon\Carbon;

class CollectibilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ensure we have at least one member or nasabah
        $member = Member::first();
        if (!$member) {
            $member = factory(Member::class)->create();
        }

        $statuses = [
            'Lancar',
            'Dalam Perhatian Khusus',
            'Kurang Lancar',
            'Diragukan',
            'Macet'
        ];

        foreach ($statuses as $index => $status) {
            // Create a loan for each status
            $loan = Loan::create([
                'kode_pinjaman' => 'LOAN-COL-' . ($index + 1),
                'anggota_id' => $member->id,
                'jenis_pinjaman' => 'Biasa',
                'jumlah_pinjaman' => 10000000,
                'tenor' => 12,
                'suku_bunga' => 10,
                'satuan_bunga' => 'bulan', // added field based on migration check
                'tempo_angsuran' => 1,
                'jenis_bunga' => 'flat',
                'biaya_admin' => 0,
                'denda_keterlambatan' => 0, // added field
                'tanggal_pengajuan' => Carbon::now()->subMonths(6),
                'tanggal_persetujuan' => Carbon::now()->subMonths(6),
                'status' => ($status == 'Macet') ? 'macet' : 'dicairkan',
                'kolektabilitas' => $status,
                'keterangan' => 'Dummy Loan for ' . $status,
            ]);

            // Create unpaid installments for this loan to generate "Piutang"
            // Let's say 5 installments are unpaid
            for ($i = 1; $i <= 5; $i++) {
                LoanInstallment::create([
                    'pinjaman_id' => $loan->id,
                    'angsuran_ke' => $i,
                    'tanggal_jatuh_tempo' => Carbon::now()->addMonths($i),
                    'total_angsuran' => 1000000,
                    'pokok' => 800000,
                    'bunga' => 200000,
                    'sisa_pinjaman' => 10000000 - ($i * 800000),
                    'denda' => 0, // added field
                    'status' => 'belum_lunas'
                ]);
            }
        }
    }
}
