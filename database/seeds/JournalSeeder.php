<?php

use Illuminate\Database\Seeder;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalItem;
use Carbon\Carbon;

class JournalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Only run if we have accounts
        // Matches codes in CoaSeeder.php
        $cash = ChartOfAccount::where('code', '1101')->first(); // Kas
        $bank = ChartOfAccount::where('code', '1102')->first(); // Bank
        $capital = ChartOfAccount::where('code', '3101')->first(); // Modal Awal
        $revenue = ChartOfAccount::where('code', '4101')->first(); // Pendapatan Bunga Pinjaman
        $expense_salary = ChartOfAccount::where('code', '5101')->first(); // Beban Gaji
        $expense_ops = ChartOfAccount::where('code', '5102')->first(); // Beban Operasional

        if (!$cash || !$capital) {
            $this->command->info('COA not found. Please run CoaSeeder first.');
            return;
        }

        // 1. Initial Capital Injection (Cash In)
        $this->createEntry(
            Carbon::now()->subMonths(2)->format('Y-m-d'),
            'REF-001',
            'Setoran Modal Awal',
            [
                ['account_id' => $cash->id, 'debit' => 50000000, 'credit' => 0],
                ['account_id' => $capital->id, 'debit' => 0, 'credit' => 50000000],
            ]
        );

        // 2. Transfer to Bank
        $this->createEntry(
            Carbon::now()->subMonths(2)->addDay()->format('Y-m-d'),
            'REF-002',
            'Setor Tunai ke Bank',
            [
                ['account_id' => $bank->id, 'debit' => 20000000, 'credit' => 0],
                ['account_id' => $cash->id, 'debit' => 0, 'credit' => 20000000],
            ]
        );

        // 3. Operational Expense (Cash Out)
        if ($expense_ops) {
            $this->createEntry(
                Carbon::now()->subMonth()->format('Y-m-d'),
                'REF-003',
                'Bayar Sewa Kantor (Operasional)',
                [
                    ['account_id' => $expense_ops->id, 'debit' => 5000000, 'credit' => 0],
                    ['account_id' => $bank->id, 'debit' => 0, 'credit' => 5000000],
                ]
            );
        }

        // 4. Revenue (Cash In)
        if ($revenue) {
            $this->createEntry(
                Carbon::now()->subDays(15)->format('Y-m-d'),
                'REF-004',
                'Pendapatan Bunga Pinjaman',
                [
                    ['account_id' => $cash->id, 'debit' => 2500000, 'credit' => 0],
                    ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 2500000],
                ]
            );
        }

        // 5. Salary Expense (Cash Out)
        if ($expense_salary) {
            $this->createEntry(
                Carbon::now()->subDays(2)->format('Y-m-d'),
                'REF-005',
                'Bayar Gaji Karyawan',
                [
                    ['account_id' => $expense_salary->id, 'debit' => 15000000, 'credit' => 0],
                    ['account_id' => $bank->id, 'debit' => 0, 'credit' => 15000000],
                ]
            );
        }
    }

    private function createEntry($date, $ref, $desc, $items)
    {
        $journal = JournalEntry::create([
            'transaction_date' => $date,
            'reference_number' => $ref,
            'description' => $desc,
        ]);

        foreach ($items as $item) {
            JournalItem::create([
                'journal_entry_id' => $journal->id,
                'chart_of_account_id' => $item['account_id'],
                'debit' => $item['debit'],
                'credit' => $item['credit'],
            ]);
        }
    }
}
