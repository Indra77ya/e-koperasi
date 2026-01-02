<?php

namespace App\Services;

use App\Models\JournalEntry;
use App\Models\JournalItem;
use App\Models\ChartOfAccount;
use DB;

class AccountingService
{
    /**
     * Create a journal entry automatically.
     *
     * @param string $date
     * @param string $refNumber
     * @param string $description
     * @param array $items  [['code' => '1101', 'debit' => 1000, 'credit' => 0], ...]
     * @param object|null $refObject (The model causing this transaction)
     */
    public function createJournal($date, $refNumber, $description, $items, $refObject = null)
    {
        // 1. Validate Balance
        $totalDebit = collect($items)->sum('debit');
        $totalCredit = collect($items)->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            throw new \Exception("Journal Entry must be balanced. Debit: $totalDebit, Credit: $totalCredit");
        }

        // 2. Resolve COA IDs
        $processedItems = [];
        foreach ($items as $item) {
            $account = ChartOfAccount::where('code', $item['code'])->first();
            if (!$account) {
                // Optionally create or throw error. For now, we assume seeders exist.
                // Fallback or error? Let's log error but maybe fail gracefully?
                // Throwing exception is safer for data integrity.
                throw new \Exception("Chart of Account with code {$item['code']} not found.");
            }
            $processedItems[] = [
                'chart_of_account_id' => $account->id,
                'debit' => $item['debit'] ?? 0,
                'credit' => $item['credit'] ?? 0,
                'description' => $item['description'] ?? null,
            ];
        }

        // 3. Create Entry
        $entry = new JournalEntry([
            'transaction_date' => $date,
            'reference_number' => $refNumber,
            'description' => $description,
        ]);

        if ($refObject) {
            $entry->ref()->associate($refObject);
        }

        $entry->save();

        // 4. Create Items
        foreach ($processedItems as $pItem) {
            $pItem['journal_entry_id'] = $entry->id;
            JournalItem::create($pItem);
        }

        return $entry;
    }
}
