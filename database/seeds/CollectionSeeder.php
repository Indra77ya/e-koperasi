<?php

use Illuminate\Database\Seeder;
use App\Models\Loan;
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

        foreach ($loans as $loan) {
            // 1. Randomize Kolektabilitas
            // 60% Lancar, 25% DPK, 15% Macet
            $rand = rand(1, 100);
            if ($rand <= 60) {
                $status = 'Lancar';
            } elseif ($rand <= 85) {
                $status = 'DPK';
            } else {
                $status = 'Macet';
            }

            $loan->update(['kolektabilitas' => $status]);

            // 2. Generate History Logs (Timeline)
            // Create 0 to 3 logs per loan
            $logCount = rand(0, 3);
            for ($i = 0; $i < $logCount; $i++) {
                PenagihanLog::create([
                    'pinjaman_id' => $loan->id,
                    'user_id' => $users->random()->id,
                    'metode_penagihan' => $faker->randomElement(['Telepon', 'WhatsApp', 'Kunjungan', 'Surat']),
                    'hasil_penagihan' => $faker->randomElement(['Terhubung - Janji Bayar', 'Terhubung - Minta Waktu', 'Tidak Diangkat', 'Nomor Salah']),
                    'tanggal_janji_bayar' => $faker->optional(0.5)->dateTimeBetween('now', '+1 week'),
                    'catatan' => $faker->sentence,
                    'created_at' => $faker->dateTimeBetween('-3 months', 'now'),
                ]);
            }

            // 3. Generate Field Queue
            // Only for DPK or Macet, small chance to have active queue
            if ($status != 'Lancar' && rand(0, 1) == 1) {
                PenagihanLapangan::create([
                    'pinjaman_id' => $loan->id,
                    'petugas_id' => $users->random()->id,
                    'tanggal_rencana_kunjungan' => $faker->dateTimeBetween('now', '+1 week'),
                    'status' => $faker->randomElement(['baru', 'dalam_proses', 'selesai', 'batal']),
                    'catatan_tugas' => 'Kunjungan rutin untuk penagihan ' . $status,
                ]);
            }
        }
    }
}
