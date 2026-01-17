<?php

use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\Nasabah;
use App\Models\BankInterest;
use App\Models\Saving;
use App\Models\SavingHistory;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class BankInterestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        BankInterest::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $interestRate = Setting::get('savings_interest_rate', 2); // 2% per year default

        $monthsToSeed = 6;
        $startMonth = Carbon::now()->subMonths($monthsToSeed);

        // Members
        $members = Member::all();
        foreach ($members as $member) {
            $this->generateInterest($member, 'anggota', $startMonth, $monthsToSeed, $interestRate);
        }

        // Nasabahs
        $nasabahs = Nasabah::all();
        foreach ($nasabahs as $nasabah) {
            $this->generateInterest($nasabah, 'nasabah', $startMonth, $monthsToSeed, $interestRate);
        }
    }

    private function generateInterest($entity, $type, $startMonth, $count, $rate)
    {
        $idField = ($type == 'anggota') ? 'anggota_id' : 'nasabah_id';

        for ($i = 0; $i < $count; $i++) {
            $date = $startMonth->copy()->addMonths($i);
            $month = $date->month;
            $year = $date->year;

            // Simplified Lowest Balance Calculation
            // Just take current balance * random factor (0.5 - 0.9) to simulate fluctuation
            // In reality, this requires checking daily balances, but this is a dummy seeder.

            $saving = Saving::where($idField, $entity->id)->first();
            $currentBalance = $saving ? $saving->saldo : 0;

            if ($currentBalance <= 0) continue;

            $lowestBalance = $currentBalance * (rand(50, 95) / 100);

            // Calculate Interest: (Lowest Balance * Rate%) / 12
            $interestAmount = ($lowestBalance * ($rate / 100)) / 12;
            $interestAmount = floor($interestAmount); // Round down

            if ($interestAmount > 0) {
                BankInterest::create([
                    $idField => $entity->id,
                    'bulan' => $month,
                    'tahun' => $year,
                    'saldo_terendah' => $lowestBalance,
                    'suku_bunga' => $rate,
                    'nominal_bunga' => $interestAmount,
                    'created_at' => $date->endOfMonth(),
                    'updated_at' => $date->endOfMonth(),
                ]);
            }
        }
    }
}
