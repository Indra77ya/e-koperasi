<?php

use Illuminate\Database\Seeder;
use App\Models\Loan;
use App\Models\Member;
use App\Models\LoanInstallment;

class LoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ensure there is at least one member
        $member = Member::first();
        if (!$member) {
            return;
        }

        // Create a Pending Loan
        Loan::create([
            'kode_pinjaman' => 'P-20240101-001',
            'anggota_id' => $member->id,
            'jenis_pinjaman' => 'produktif',
            'jumlah_pinjaman' => 10000000,
            'tenor' => 12,
            'suku_bunga' => 10, // 10%
            'jenis_bunga' => 'flat',
            'biaya_admin' => 50000,
            'tanggal_pengajuan' => now()->subDays(2),
            'status' => 'diajukan',
            'keterangan' => 'Modal usaha warung',
        ]);

        // Create an Active (Disbursed) Loan
        $activeLoan = Loan::create([
            'kode_pinjaman' => 'P-20240101-002',
            'anggota_id' => $member->id,
            'jenis_pinjaman' => 'konsumtif',
            'jumlah_pinjaman' => 5000000,
            'tenor' => 6,
            'suku_bunga' => 12, // 12%
            'jenis_bunga' => 'flat',
            'biaya_admin' => 25000,
            'tanggal_pengajuan' => now()->subMonth(),
            'tanggal_persetujuan' => now()->subMonth(),
            'status' => 'berjalan',
            'keterangan' => 'Biaya sekolah',
        ]);

        // Generate Installments for the active loan
        $amount = $activeLoan->jumlah_pinjaman;
        $tenor = $activeLoan->tenor;
        $rate = $activeLoan->suku_bunga;

        $principal = $amount / $tenor;
        $interest = ($amount * ($rate / 100)) / 12;
        $total = $principal + $interest;
        $balance = $amount;

        for ($i = 1; $i <= $tenor; $i++) {
            $balance -= $principal;

            LoanInstallment::create([
                'pinjaman_id' => $activeLoan->id,
                'angsuran_ke' => $i,
                'tanggal_jatuh_tempo' => now()->subMonth()->addMonths($i),
                'total_angsuran' => round($total, 2),
                'pokok' => round($principal, 2),
                'bunga' => round($interest, 2),
                'sisa_pinjaman' => max(0, round($balance, 2)),
                'status' => 'belum_lunas',
            ]);
        }
    }
}
