<?php

use Illuminate\Database\Seeder;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Nasabah;
use Carbon\Carbon;

class LoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ensure we have Members and Nasabahs
        if (Member::count() == 0) {
            // Create a dummy member if none exists (though usually other seeders handle this)
            // But assume MemberSeeder ran.
        }

        $member = Member::first();
        $nasabah = Nasabah::first();

        // Create a Loan for Member
        if ($member) {
            Loan::create([
                'kode_pinjaman' => 'P-' . date('Ymd') . '-001',
                'anggota_id' => $member->id,
                'jenis_pinjaman' => 'produktif',
                'jumlah_pinjaman' => 10000000,
                'tenor' => 12,
                'suku_bunga' => 10,
                'jenis_bunga' => 'flat',
                'biaya_admin' => 50000,
                'tanggal_pengajuan' => Carbon::now()->subDays(5),
                'status' => 'diajukan',
                'keterangan' => 'Modal usaha warung',
            ]);
        }

        // Create a Loan for Nasabah (that is already disbursed)
        if ($nasabah) {
            $loan = Loan::create([
                'kode_pinjaman' => 'P-' . date('Ymd') . '-002',
                'nasabah_id' => $nasabah->id,
                'jenis_pinjaman' => 'konsumtif',
                'jumlah_pinjaman' => 5000000,
                'tenor' => 6,
                'suku_bunga' => 12,
                'jenis_bunga' => 'anuitas',
                'biaya_admin' => 25000,
                'tanggal_pengajuan' => Carbon::now()->subMonth(),
                'tanggal_persetujuan' => Carbon::now()->subMonth()->addDay(),
                'status' => 'disetujui', // Will disburse manually or via logic
                'keterangan' => 'Renovasi rumah',
            ]);

            // Simulate disbursement for this one
            // Call the controller logic conceptually, or just direct DB insert
            // Since we are in seeder, we can't easily call controller methods that return redirects.
            // We replicate the logic.

            DB::transaction(function () use ($loan) {
                $loan->update(['status' => 'berjalan']);

                // Calculate Annuity Schedule
                $amount = $loan->jumlah_pinjaman;
                $tenor = $loan->tenor;
                $rate = $loan->suku_bunga;

                $balance = $amount;
                $ratePerMonth = ($rate / 100) / 12;
                $pmt = ($amount * $ratePerMonth) / (1 - pow(1 + $ratePerMonth, -$tenor));

                for ($i = 1; $i <= $tenor; $i++) {
                    $interest = $balance * $ratePerMonth;
                    $principal = $pmt - $interest;
                    $balance -= $principal;

                    \App\Models\LoanInstallment::create([
                        'pinjaman_id' => $loan->id,
                        'angsuran_ke' => $i,
                        'tanggal_jatuh_tempo' => Carbon::now()->subMonth()->addMonths($i),
                        'total_angsuran' => $pmt,
                        'pokok' => $principal,
                        'bunga' => $interest,
                        'sisa_pinjaman' => max(0, $balance),
                        'status' => 'belum_lunas',
                    ]);
                }
            });
        }
    }
}
