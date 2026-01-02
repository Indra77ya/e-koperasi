<?php

use Illuminate\Database\Seeder;
use App\Models\Loan;
use App\Models\Member;
use App\Models\Nasabah;
use App\Models\Collateral;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalItem;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Ensure dependent data exists
        if (Member::count() == 0) factory(Member::class, 5)->create();
        if (Nasabah::count() == 0) factory(Nasabah::class, 5)->create();

        $this->seedLoans();
        $this->seedJournals();
    }

    private function seedLoans()
    {
        $members = Member::take(3)->get();
        $nasabahs = Nasabah::take(3)->get();

        // 1. Outstanding Loans (Dicairkan/Berjalan)
        foreach ($members as $index => $member) {
            $loan = Loan::create([
                'loan_number' => 'L-OUT-M-' . ($index + 1),
                'anggota_id' => $member->id,
                'amount' => 10000000 * ($index + 1),
                'remaining_balance' => 8000000 * ($index + 1),
                'tenor' => 12,
                'interest_rate' => 10,
                'status' => 'dicairkan', // or 'berjalan' depending on system convention
                'created_at' => Carbon::now()->subMonths($index + 1),
            ]);

            // Add Collateral
            Collateral::create([
                'pinjaman_id' => $loan->id,
                'jenis' => 'Kendaraan',
                'nomor' => 'BPKB-00' . ($index + 1),
                'pemilik' => $member->name,
                'nilai_taksasi' => 15000000,
                'status' => 'disimpan',
                'keterangan' => 'Diserahkan saat pencairan. Motor Honda ' . ($index + 1),
            ]);
        }

        // 2. Bad Debts (Macet)
        foreach ($nasabahs as $index => $nasabah) {
            Loan::create([
                'loan_number' => 'L-BAD-N-' . ($index + 1),
                'nasabah_id' => $nasabah->id,
                'amount' => 5000000 * ($index + 1),
                'remaining_balance' => 4500000 * ($index + 1), // Mostly unpaid
                'tenor' => 6,
                'interest_rate' => 12,
                'status' => 'macet',
                'created_at' => Carbon::now()->subMonths(6 + $index),
                'updated_at' => Carbon::now()->subDays(10 * ($index + 1)), // Marked macet recently
            ]);
        }
    }

    private function seedJournals()
    {
        $cash = ChartOfAccount::where('type', 'ASSET')->where('name', 'like', '%Kas%')->first();
        $revenue = ChartOfAccount::where('type', 'REVENUE')->first();
        $expense = ChartOfAccount::where('type', 'EXPENSE')->first();

        if (!$cash || !$revenue) return;

        // Generate Daily Cash Flow (Last 10 days)
        for ($i = 0; $i < 10; $i++) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');

            // Income (Revenue)
            $incomeAmount = rand(100000, 1000000);
            $this->createEntry($date, 'INC-'.$i, 'Pendapatan Harian', [
                ['account_id' => $cash->id, 'debit' => $incomeAmount, 'credit' => 0],
                ['account_id' => $revenue->id, 'debit' => 0, 'credit' => $incomeAmount],
            ]);

            // Expense (if exists)
            if ($expense) {
                $expenseAmount = rand(50000, 200000);
                $this->createEntry($date, 'EXP-'.$i, 'Biaya Operasional Harian', [
                    ['account_id' => $expense->id, 'debit' => $expenseAmount, 'credit' => 0],
                    ['account_id' => $cash->id, 'debit' => 0, 'credit' => $expenseAmount],
                ]);
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
