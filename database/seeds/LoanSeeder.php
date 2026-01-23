<?php

use Illuminate\Database\Seeder;
use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Models\Collateral;
use App\Models\Member;
use App\Models\Nasabah;
use Carbon\Carbon;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class LoanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        Loan::truncate();
        LoanInstallment::truncate();
        Collateral::truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

        $faker = Faker::create('id_ID');

        // Check if members/nasabahs exist, if not call their seeders
        if (Member::count() == 0) {
            $this->call(AnggotaTableSeeder::class);
        }
        if (Nasabah::count() == 0) {
            $this->call(NasabahTableSeeder::class);
        }

        $members = Member::all();
        $nasabahs = Nasabah::all();

        // 1. Process Members (approx 70% get loans)
        foreach ($members as $index => $member) {
            // Force the first few members to have a loan due today
            $forceDueToday = ($index < 3);
            if ($forceDueToday || rand(1, 100) <= 70) {
                $this->createLoan($member, 'anggota', $faker, $forceDueToday);
            }
        }

        // 2. Process Nasabahs (approx 60% get loans)
        foreach ($nasabahs as $index => $nasabah) {
             // Force the first few nasabahs to have a loan due today
            $forceDueToday = ($index < 3);
            if ($forceDueToday || rand(1, 100) <= 60) {
                $this->createLoan($nasabah, 'nasabah', $faker, $forceDueToday);
            }
        }
    }

    private function createLoan($entity, $type, $faker, $forceDueToday = false)
    {
        $status = $faker->randomElement(['diajukan', 'disetujui', 'ditolak', 'berjalan', 'lunas', 'macet']);

        if ($forceDueToday) {
            $status = 'berjalan'; // Must be active to be due
        }

        $submitDate = Carbon::now()->subMonths(rand(1, 24));

        // Adjust submit date if we need to force a due date today
        if ($forceDueToday) {
            // If we want an installment due today, the loan must have been approved X months ago.
            // Let's say we want the 1st installment to be due today.
            // Approval date should be 1 month ago.
            // Submit date should be slightly before that.
            $monthsAgo = rand(1, 5); // 1st to 5th installment due today
            $approvalDate = Carbon::today()->subMonths($monthsAgo);
            $submitDate = $approvalDate->copy()->subDays(rand(1, 5));
        } else {
            $approvalDate = null;
            if ($status != 'diajukan' && $status != 'ditolak') {
                $approvalDate = $submitDate->copy()->addDays(rand(1, 7));
            } elseif ($status == 'ditolak') {
                 $approvalDate = $submitDate->copy()->addDays(rand(1, 7));
            }
        }

        // Loan Details
        $amount = $faker->randomElement([5000000, 10000000, 15000000, 20000000, 25000000, 50000000]);
        $tenor = $faker->randomElement([6, 12, 18, 24, 36]);
        $rate = $faker->randomFloat(2, 10, 18); // 10-18% per year
        $adminFee = 50000;

        $data = [
            'kode_pinjaman' => 'P-' . $submitDate->format('Ymd') . '-' . rand(1000, 9999),
            'jenis_pinjaman' => $faker->randomElement(['produktif', 'konsumtif', 'investasi']),
            'jumlah_pinjaman' => $amount,
            'tenor' => $tenor,
            'suku_bunga' => $rate,
            'satuan_bunga' => 'tahun',
            'tempo_angsuran' => 'bulan',
            'jenis_bunga' => 'anuitas',
            'biaya_admin' => $adminFee,
            'tanggal_pengajuan' => $submitDate,
            'tanggal_persetujuan' => $approvalDate,
            'status' => ($status == 'macet') ? 'berjalan' : $status,
            'keterangan' => $faker->sentence,
            'kolektabilitas' => ($status == 'macet') ? 'Macet' : 'Lancar',
        ];

        if ($type == 'anggota') {
            $data['anggota_id'] = $entity->id;
        } else {
            $data['nasabah_id'] = $entity->id;
        }

        $loan = Loan::create($data);

        // Add Collateral (Jaminan) for most loans
        if (rand(1, 100) <= 90) { // 90% have collateral
            $this->createCollateral($loan, $faker);
        }

        // Generate Installments
        if (in_array($status, ['berjalan', 'lunas', 'macet'])) {
            $this->generateInstallments($loan, $status, $approvalDate);
        }
    }

    private function createCollateral($loan, $faker)
    {
        Collateral::create([
            'pinjaman_id' => $loan->id,
            'jenis' => $faker->randomElement(['BPKB Motor', 'BPKB Mobil', 'Sertifikat Tanah', 'Sertifikat Rumah']),
            'nomor' => strtoupper($faker->bothify('??####??')),
            'pemilik' => $faker->name,
            'nilai_taksasi' => $loan->jumlah_pinjaman * 1.5, // Collateral value usually higher than loan
            'status' => ($loan->status == 'lunas') ? 'dikembalikan' : 'disimpan',
            'lokasi_penyimpanan' => 'Lemari Besi A1',
            'tanggal_masuk' => $loan->tanggal_pengajuan,
            'tanggal_keluar' => ($loan->status == 'lunas') ? Carbon::now() : null,
            'keterangan' => $faker->sentence,
        ]);
    }

    private function generateInstallments($loan, $status, $approvalDate)
    {
        $amount = $loan->jumlah_pinjaman;
        $tenor = $loan->tenor;
        $rate = $loan->suku_bunga;

        // Annuity Calculation
        $balance = $amount;
        $ratePerMonth = ($rate / 100) / 12;
        $pmt = ($amount * $ratePerMonth) / (1 - pow(1 + $ratePerMonth, -$tenor));

        $dueDate = Carbon::parse($approvalDate)->addMonth();

        for ($i = 1; $i <= $tenor; $i++) {
            $interest = $balance * $ratePerMonth;
            $principal = $pmt - $interest;
            $balance -= $principal;

            $instStatus = 'belum_lunas';
            $paidDate = null;
            $paidAmount = 0;

            if ($status == 'lunas') {
                $instStatus = 'lunas';
                $paidDate = $dueDate->copy()->subDays(rand(0, 5));
                $paidAmount = $pmt;
            } elseif ($status == 'berjalan') {
                if ($dueDate->isPast() && !$dueDate->isToday()) {
                    // Past dues are likely paid unless we want arrears
                    if (rand(0, 100) > 10) { // 90% paid on time
                         $instStatus = 'lunas';
                         $paidDate = $dueDate->copy()->subDays(rand(0, 5));
                         $paidAmount = $pmt;
                    }
                }
                // If isToday(), leave as belum_lunas so it shows up in "Due Today"
            } elseif ($status == 'macet') {
                if ($i <= 3 && $dueDate->isPast()) {
                    $instStatus = 'lunas';
                    $paidDate = $dueDate->copy()->subDays(rand(0, 5));
                    $paidAmount = $pmt;
                } elseif ($dueDate->isPast()) {
                     $instStatus = 'belum_lunas';
                }
            }

            LoanInstallment::create([
                'pinjaman_id' => $loan->id,
                'angsuran_ke' => $i,
                'tanggal_jatuh_tempo' => $dueDate->copy(),
                'tanggal_bayar' => $paidDate,
                'total_angsuran' => $pmt,
                'pokok' => $principal,
                'bunga' => $interest,
                'sisa_pinjaman' => max(0, $balance),
                'status' => $instStatus,
                'metode_pembayaran' => ($instStatus == 'lunas') ? 'tunai' : null,
            ]);

            $dueDate->addMonth();
        }
    }
}
