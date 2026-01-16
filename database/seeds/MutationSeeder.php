<?php

use Illuminate\Database\Seeder;
use App\Models\Member;
use App\Models\Nasabah;
use App\Models\Saving;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\SavingHistory;
use App\Models\Setting;
use App\Services\AccountingService;
use Faker\Factory as Faker;
use Illuminate\Support\Facades\DB;

class MutationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Truncate tables
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Deposit::truncate();
        Withdrawal::truncate();
        Saving::truncate();
        SavingHistory::truncate();
        DB::table('journal_entries')->truncate();
        DB::table('journal_items')->truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        // Dependencies
        $accountingService = new AccountingService();
        $faker = Faker::create('id_ID');

        // Settings
        $coaCash = Setting::get('coa_cash', '1101');
        $coaSavings = Setting::get('coa_savings', '2101');

        // 1. Process Members
        $members = Member::all();
        foreach ($members as $member) {
            $this->generateTransactions($member, 'anggota', $faker, $accountingService, $coaCash, $coaSavings);
        }

        // 2. Process Nasabahs
        $nasabahs = Nasabah::all();
        foreach ($nasabahs as $nasabah) {
            $this->generateTransactions($nasabah, 'nasabah', $faker, $accountingService, $coaCash, $coaSavings);
        }
    }

    private function generateTransactions($entity, $type, $faker, $accountingService, $coaCash, $coaSavings)
    {
        $idField = ($type == 'anggota') ? 'anggota_id' : 'nasabah_id';

        // Initial Balance (if exists) or 0
        $saving = Saving::firstOrNew([$idField => $entity->id]);
        $currentBalance = $saving->saldo ?? 0;

        // Generate 10-25 transactions per user
        $numTransactions = rand(10, 25);
        $startDate = \Carbon\Carbon::now()->subMonths(6); // 6 months history

        for ($i = 0; $i < $numTransactions; $i++) {
            $date = $startDate->copy()->addDays(rand(2, 7)); // Spread out
            $startDate = $date; // Advance date

            // Higher chance of deposit (income) than withdrawal to build balance
            $isDeposit = (rand(0, 100) > 30); // 70% deposit
            $amount = rand(50000, 2000000);
            $amount = ceil($amount / 50000) * 50000; // Round to 50k

            if ($isDeposit) {
                // Deposit
                $deposit = new Deposit();
                $deposit->{$idField} = $entity->id;
                $deposit->jumlah = $amount;
                $deposit->keterangan = 'Setoran via Seeder ' . $faker->word;
                $deposit->created_at = $date;
                $deposit->updated_at = $date;
                $deposit->save();

                $currentBalance += $amount;

                // History
                $history = new SavingHistory();
                $history->{$idField} = $entity->id;
                $history->tanggal = $date->toDateString();
                $history->keterangan = 'setoran';
                $history->kredit = $amount;
                $history->saldo = $currentBalance;
                $history->created_at = $date;
                $history->updated_at = $date;
                $history->save();

                // Journal
                $journalItems = [
                    ['code' => $coaCash, 'debit' => $amount, 'credit' => 0],
                    ['code' => $coaSavings, 'debit' => 0, 'credit' => $amount],
                ];
                try {
                     $accountingService->createJournal(
                        $date, 'DEP-SEED-' . $deposit->id, 'Setoran ' . $entity->nama, $journalItems, $deposit
                    );
                } catch (\Exception $e) { }

            } else {
                // Withdrawal
                if ($currentBalance < $amount) continue; // Skip if insufficient funds

                $withdrawal = new Withdrawal();
                $withdrawal->{$idField} = $entity->id;
                $withdrawal->jumlah = $amount;
                $withdrawal->keterangan = 'Penarikan via Seeder ' . $faker->word;
                $withdrawal->created_at = $date;
                $withdrawal->updated_at = $date;
                $withdrawal->save();

                $currentBalance -= $amount;

                // History
                $history = new SavingHistory();
                $history->{$idField} = $entity->id;
                $history->tanggal = $date->toDateString();
                $history->keterangan = 'penarikan';
                $history->debet = $amount;
                $history->saldo = $currentBalance;
                $history->created_at = $date;
                $history->updated_at = $date;
                $history->save();

                 // Journal
                $journalItems = [
                    ['code' => $coaSavings, 'debit' => $amount, 'credit' => 0],
                    ['code' => $coaCash, 'debit' => 0, 'credit' => $amount],
                ];
                try {
                     $accountingService->createJournal(
                        $date, 'WDR-SEED-' . $withdrawal->id, 'Penarikan ' . $entity->nama, $journalItems, $withdrawal
                    );
                } catch (\Exception $e) { }
            }
        }

        // Save Final Balance
        $saving->saldo = $currentBalance;
        $saving->save();
    }
}
