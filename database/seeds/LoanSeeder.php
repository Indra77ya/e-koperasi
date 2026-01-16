<?php

use Illuminate\Database\Seeder;
use App\Models\Loan;
use App\Models\LoanInstallment;
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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Loan::truncate();
        LoanInstallment::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $faker = Faker::create('id_ID');

        // Check if members/nasabahs exist, if not call their seeders (optional, but good for safety)
        if (Member::count() == 0) {
            $this->call(AnggotaTableSeeder::class);
        }
        if (Nasabah::count() == 0) {
            $this->call(NasabahTableSeeder::class);
        }

        $members = Member::all();
        $nasabahs = Nasabah::all();

        // 1. Process Members (approx 70% get loans)
        foreach ($members as $member) {
            if (rand(1, 100) <= 70) {
                $this->createLoan($member, 'anggota', $faker);
            }
        }

        // 2. Process Nasabahs (approx 60% get loans)
        foreach ($nasabahs as $nasabah) {
            if (rand(1, 100) <= 60) {
                $this->createLoan($nasabah, 'nasabah', $faker);
            }
        }
    }

    private function createLoan($entity, $type, $faker)
    {
        $status = $faker->randomElement(['diajukan', 'disetujui', 'ditolak', 'berjalan', 'lunas', 'macet']);

        // Adjust dates based on status
        $submitDate = Carbon::now()->subMonths(rand(1, 24));
        $approvalDate = null;

        if ($status != 'diajukan' && $status != 'ditolak') {
            $approvalDate = $submitDate->copy()->addDays(rand(1, 7));
        } elseif ($status == 'ditolak') {
             // Rejected loans also have "processed" date usually, but let's keep it simple
             $approvalDate = $submitDate->copy()->addDays(rand(1, 7));
        }

        // Loan Details
        $amount = $faker->randomElement([5000000, 10000000, 15000000, 20000000, 25000000, 50000000]);
        $tenor = $faker->randomElement([6, 12, 18, 24, 36]);
        $rate = $faker->randomFloat(2, 10, 18); // 10-18% per year
        $adminFee = 50000;

        // Prepare data array
        $data = [
            'kode_pinjaman' => 'P-' . $submitDate->format('Ymd') . '-' . rand(1000, 9999),
            'jenis_pinjaman' => $faker->randomElement(['produktif', 'konsumtif', 'investasi']),
            'jumlah_pinjaman' => $amount,
            'tenor' => $tenor,
            'suku_bunga' => $rate,
            'satuan_bunga' => 'tahun', // Defaulting to annual rate
            'tempo_angsuran' => 'bulan',
            'jenis_bunga' => 'anuitas', // Focusing on Annuity for complex calculation
            'biaya_admin' => $adminFee,
            'tanggal_pengajuan' => $submitDate,
            'tanggal_persetujuan' => $approvalDate,
            'status' => ($status == 'macet') ? 'berjalan' : $status, // 'macet' is logic status, db status usually 'berjalan' with bad collection
            'keterangan' => $faker->sentence,
            'kolektabilitas' => ($status == 'macet') ? 'Macet' : 'Lancar', // Helper for CollectionSeeder
        ];

        if ($type == 'anggota') {
            $data['anggota_id'] = $entity->id;
        } else {
            $data['nasabah_id'] = $entity->id;
        }

        $loan = Loan::create($data);

        // Generate Installments if active/paid/macet
        if (in_array($status, ['berjalan', 'lunas', 'macet'])) {
            $this->generateInstallments($loan, $status);
        }
    }

    private function generateInstallments($loan, $status)
    {
        $amount = $loan->jumlah_pinjaman;
        $tenor = $loan->tenor;
        $rate = $loan->suku_bunga;

        // Annuity Calculation
        $balance = $amount;
        $ratePerMonth = ($rate / 100) / 12;
        $pmt = ($amount * $ratePerMonth) / (1 - pow(1 + $ratePerMonth, -$tenor));

        // Start from approval date (next month usually)
        $dueDate = Carbon::parse($loan->tanggal_persetujuan)->addMonth();

        for ($i = 1; $i <= $tenor; $i++) {
            $interest = $balance * $ratePerMonth;
            $principal = $pmt - $interest;
            $balance -= $principal;

            // Determine status of this installment
            $instStatus = 'belum_lunas';
            $paidDate = null;
            $paidAmount = 0;

            if ($status == 'lunas') {
                $instStatus = 'lunas';
                $paidDate = $dueDate->copy()->subDays(rand(0, 5));
                $paidAmount = $pmt;
            } elseif ($status == 'berjalan') {
                if ($dueDate->isPast()) {
                    $instStatus = 'lunas';
                    $paidDate = $dueDate->copy()->subDays(rand(0, 5));
                    $paidAmount = $pmt;
                }
            } elseif ($status == 'macet') {
                // Pay first 3 months, then stop
                if ($i <= 3 && $dueDate->isPast()) {
                    $instStatus = 'lunas';
                    $paidDate = $dueDate->copy()->subDays(rand(0, 5));
                    $paidAmount = $pmt;
                } elseif ($dueDate->isPast()) {
                     $instStatus = 'belum_lunas'; // Overdue
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
