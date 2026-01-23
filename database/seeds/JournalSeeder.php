<?php

use Illuminate\Database\Seeder;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class JournalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();
        DB::table('journal_entries')->truncate();
        DB::table('journal_items')->truncate();
        \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();

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

        // 1. Initial Capital Injection (Cash In) - BIGGER
        $this->createEntry(
            Carbon::now()->subMonths(6)->format('Y-m-d'),
            'REF-001',
            'Setoran Modal Awal (Tunai)',
            [
                ['account_id' => $cash->id, 'debit' => 500000000, 'credit' => 0], // 500 Juta
                ['account_id' => $capital->id, 'debit' => 0, 'credit' => 500000000],
            ]
        );

        // 2. Additional Capital Injection directly to Bank
         $this->createEntry(
            Carbon::now()->subMonths(6)->addDays(1)->format('Y-m-d'),
            'REF-001-B',
            'Setoran Modal Awal (Transfer Bank)',
            [
                ['account_id' => $bank->id, 'debit' => 1000000000, 'credit' => 0], // 1 Milyar
                ['account_id' => $capital->id, 'debit' => 0, 'credit' => 1000000000],
            ]
        );

        // 3. Transfer from Cash to Bank
        $this->createEntry(
            Carbon::now()->subMonths(5)->format('Y-m-d'),
            'REF-002',
            'Setor Tunai ke Bank',
            [
                ['account_id' => $bank->id, 'debit' => 200000000, 'credit' => 0], // 200 Juta
                ['account_id' => $cash->id, 'debit' => 0, 'credit' => 200000000],
            ]
        );

        // 4. Operational Expense (Cash Out)
        if ($expense_ops) {
            $this->createEntry(
                Carbon::now()->subMonths(4)->format('Y-m-d'),
                'REF-003',
                'Bayar Sewa Kantor (Operasional)',
                [
                    ['account_id' => $expense_ops->id, 'debit' => 25000000, 'credit' => 0], // 25 Juta
                    ['account_id' => $bank->id, 'debit' => 0, 'credit' => 25000000],
                ]
            );
        }

        // 5. Revenue (Cash In) - Periodic
        if ($revenue) {
            for ($i=1; $i<=5; $i++) {
                $this->createEntry(
                    Carbon::now()->subMonths(6-$i)->format('Y-m-d'),
                    'REV-' . $i,
                    'Pendapatan Bunga Pinjaman Bulan ke-'.$i,
                    [
                        ['account_id' => $cash->id, 'debit' => 15000000, 'credit' => 0],
                        ['account_id' => $revenue->id, 'debit' => 0, 'credit' => 15000000],
                    ]
                );
            }
        }

        // 6. Salary Expense (Cash Out) - Periodic
        if ($expense_salary) {
             for ($i=1; $i<=5; $i++) {
                $this->createEntry(
                    Carbon::now()->subMonths(6-$i)->endOfMonth()->format('Y-m-d'),
                    'SAL-' . $i,
                    'Bayar Gaji Karyawan Bulan ke-'.$i,
                    [
                        ['account_id' => $expense_salary->id, 'debit' => 35000000, 'credit' => 0],
                        ['account_id' => $bank->id, 'debit' => 0, 'credit' => 35000000],
                    ]
                );
            }
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
