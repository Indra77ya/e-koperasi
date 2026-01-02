<?php

use Illuminate\Database\Seeder;
use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Models\PenagihanLog;
use App\Models\PenagihanLapangan;
use App\Models\User;
use Carbon\Carbon;

class CollectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $loans = Loan::all();
        $users = User::all();

        if ($loans->isEmpty() || $users->isEmpty()) {
            return;
        }

        $faker = \Faker\Factory::create('id_ID');

        // To ensure we have reminders, we need to pick a few loans and set their installments to be due soon.
        $loansForReminder = $loans->where('status', '!=', 'lunas')->take(5);
        $daysOffset = 0;

        foreach ($loansForReminder as $loan) {
             // Find first unpaid installment
             $installment = LoanInstallment::where('pinjaman_id', $loan->id)
                ->where('status', 'belum_lunas')
                ->orderBy('angsuran_ke', 'asc')
                ->first();

            if ($installment) {
                // Set due date to today + offset (0, 1, 2 days) to fall within < 3 days reminder
                $installment->tanggal_jatuh_tempo = Carbon::now()->addDays($daysOffset % 3);
                $installment->save();
                $daysOffset++;
            }
        }

        foreach ($loans as $loan) {
            // 1. Randomize Kolektabilitas
            // 50% Lancar, 30% DPK, 20% Macet (increased bad debt for demo purposes)
            $rand = rand(1, 100);
            if ($rand <= 50) {
                $status = 'Lancar';
            } elseif ($rand <= 80) {
                $status = 'DPK';
            } else {
                $status = 'Macet';
            }

            $loan->update(['kolektabilitas' => $status]);

            // 2. Generate History Logs (Timeline)
            // Create 1 to 5 logs per loan to make timeline look populated
            $logCount = rand(1, 5);
            for ($i = 0; $i < $logCount; $i++) {
                PenagihanLog::create([
                    'pinjaman_id' => $loan->id,
                    'user_id' => $users->random()->id,
                    'metode_penagihan' => $faker->randomElement(['Telepon', 'WhatsApp', 'Kunjungan', 'Surat']),
                    'hasil_penagihan' => $faker->randomElement(['Terhubung - Janji Bayar', 'Terhubung - Minta Waktu', 'Tidak Diangkat', 'Nomor Salah', 'Rumah Kosong']),
                    'tanggal_janji_bayar' => $faker->optional(0.5)->dateTimeBetween('now', '+2 weeks'),
                    'catatan' => $faker->sentence,
                    'created_at' => $faker->dateTimeBetween('-6 months', 'now'),
                ]);
            }

            // 3. Generate Field Queue (Antrian Penagihan Lapangan)

            // A. Historical/Completed visits (for all types, mostly DPK/Macet in past)
            if (rand(0, 1) == 1) {
                 PenagihanLapangan::create([
                    'pinjaman_id' => $loan->id,
                    'petugas_id' => $users->random()->id,
                    'tanggal_rencana_kunjungan' => $faker->dateTimeBetween('-2 months', '-1 week'),
                    'status' => 'selesai',
                    'catatan_tugas' => 'Kunjungan sebelumnya selesai. ' . $faker->sentence,
                    'created_at' => $faker->dateTimeBetween('-3 months', '-1 month'),
                ]);
            }

            // B. Active Queue (Future visits)
            // Always create for Macet, High chance for DPK, Low chance for Lancar (e.g. routine check)
            $shouldCreateQueue = false;
            if ($status == 'Macet') {
                $shouldCreateQueue = true;
            } elseif ($status == 'DPK' && rand(1, 10) <= 8) { // 80% chance
                $shouldCreateQueue = true;
            } elseif ($status == 'Lancar' && rand(1, 10) <= 1) { // 10% chance
                $shouldCreateQueue = true;
            }

            if ($shouldCreateQueue) {
                PenagihanLapangan::create([
                    'pinjaman_id' => $loan->id,
                    'petugas_id' => $users->random()->id,
                    'tanggal_rencana_kunjungan' => $faker->dateTimeBetween('now', '+1 week'),
                    'status' => $faker->randomElement(['baru', 'dalam_proses']),
                    'catatan_tugas' => 'Penagihan lapangan prioritas. ' . $faker->sentence,
                    'created_at' => now(),
                ]);
            }
        }
    }
}
