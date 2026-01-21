<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ChartOfAccount;
use App\Models\JournalEntry;
use App\Models\JournalItem;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Artisan;
use DB;

class AccountingController extends Controller
{
    public function index()
    {
        return view('accounting.dashboard');
    }

    // COA Management
    public function coa()
    {
        return view('accounting.coa.index');
    }

    public function coaData()
    {
        $accounts = ChartOfAccount::query();
        return DataTables::of($accounts)
            ->addColumn('action', function ($account) {
                return '<a href="'.route('accounting.coa.edit', $account->id).'" class="btn btn-sm btn-primary">Edit</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function coaCreate()
    {
        return view('accounting.coa.create');
    }

    public function coaStore(Request $request)
    {
        $request->validate([
            'code' => 'required|unique:chart_of_accounts',
            'name' => 'required',
            'type' => 'required',
            'normal_balance' => 'required',
        ]);

        ChartOfAccount::create($request->all());

        return redirect()->route('accounting.coa')->with('success', 'Account created successfully');
    }

    public function coaEdit($id)
    {
        $account = ChartOfAccount::findOrFail($id);
        return view('accounting.coa.edit', compact('account'));
    }

    public function coaUpdate(Request $request, $id)
    {
        $request->validate([
            'code' => 'required|unique:chart_of_accounts,code,'.$id,
            'name' => 'required',
            'type' => 'required',
            'normal_balance' => 'required',
        ]);

        $account = ChartOfAccount::findOrFail($id);
        $account->update($request->all());

        return redirect()->route('accounting.coa')->with('success', 'Account updated successfully');
    }

    public function seedCoa()
    {
        try {
            require_once base_path('database/seeds/CoaSeeder.php');
            Artisan::call('db:seed', [
                '--class' => 'CoaSeeder',
                '--force' => true
            ]);
            return redirect()->route('accounting.coa')->with('success', 'COA berhasil di-seed!');
        } catch (\Exception $e) {
            return redirect()->route('accounting.coa')->with('error', 'Seeding gagal: ' . $e->getMessage());
        }
    }

    // Cash Book
    public function cashBook(Request $request)
    {
        $cashAccounts = ChartOfAccount::where('type', 'ASSET')
            ->where(function($q) {
                $q->where('name', 'like', '%Kas%')
                  ->orWhere('name', 'like', '%Bank%');
            })->get();

        $selectedId = $request->account_id ?? ($cashAccounts->first()->id ?? 0);
        $selectedAccount = ChartOfAccount::find($selectedId);

        if (!$selectedAccount) {
             return redirect()->route('accounting.coa')->with('error', 'Please create a Cash/Bank account first.');
        }

        // Calculate current balance
        $debit = JournalItem::where('chart_of_account_id', $selectedId)->sum('debit');
        $credit = JournalItem::where('chart_of_account_id', $selectedId)->sum('credit');
        $currentBalance = $debit - $credit; // Assuming Asset is Debit normal

        return view('accounting.cash_book.index', compact('cashAccounts', 'selectedAccount', 'currentBalance'));
    }

    public function cashBookData(Request $request)
    {
        $accountId = $request->account_id;

        $query = JournalItem::where('chart_of_account_id', $accountId)
            ->join('journal_entries', 'journal_items.journal_entry_id', '=', 'journal_entries.id')
            ->select('journal_items.*', 'journal_entries.transaction_date', 'journal_entries.reference_number', 'journal_entries.description as journal_desc');

        return DataTables::of($query)
            ->addColumn('description', function($row) {
                return $row->description ?: $row->journal_desc;
            })
            ->addColumn('balance', function($row) use ($accountId) {
                 return 0; // Placeholder
            })
            ->make(true);
    }

    // Journal Management
    public function journals()
    {
        return view('accounting.journals.index');
    }

    // Reports
    public function neraca(Request $request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date ?? date('Y-m-d');

        // Helper for efficient sum (Laravel 6 compatible)
        $getAccountsWithBalance = function($type, $normalBalance) use ($endDate) {
            return ChartOfAccount::where('type', $type)
                ->with(['journalItems' => function($q) use ($endDate) {
                    $q->whereHas('journalEntry', function($je) use ($endDate) {
                        $je->where('transaction_date', '<=', $endDate);
                    });
                }])
                ->get()
                ->map(function($account) use ($normalBalance) {
                    $debit = $account->journalItems->sum('debit');
                    $credit = $account->journalItems->sum('credit');

                    $account->balance = $normalBalance == 'DEBIT'
                        ? ($debit - $credit)
                        : ($credit - $debit);
                    return $account;
                });
        };

        $assets = $getAccountsWithBalance('ASSET', 'DEBIT');
        $liabilities = $getAccountsWithBalance('LIABILITY', 'CREDIT');
        $equities = $getAccountsWithBalance('EQUITY', 'CREDIT');

        // Calculate Current Earnings (Revenue - Expenses) for Retained Earnings
        $revenue = JournalItem::whereHas('account', function($q) { $q->where('type', 'REVENUE'); })
            ->whereHas('journalEntry', function($q) use ($endDate) { $q->where('transaction_date', '<=', $endDate); })
            ->sum(DB::raw('credit - debit'));

        $expense = JournalItem::whereHas('account', function($q) { $q->where('type', 'EXPENSE'); })
            ->whereHas('journalEntry', function($q) use ($endDate) { $q->where('transaction_date', '<=', $endDate); })
            ->sum(DB::raw('debit - credit'));

        $currentEarnings = $revenue - $expense;

        if ($request->has('print')) {
            return view('accounting.reports.neraca_print', compact('assets', 'liabilities', 'equities', 'currentEarnings', 'endDate'));
        }

        return view('accounting.reports.neraca', compact('assets', 'liabilities', 'equities', 'currentEarnings', 'endDate'));
    }

    public function labaRugi(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-d');

        $getAccountsWithBalance = function($type, $normalBalance) use ($startDate, $endDate) {
            return ChartOfAccount::where('type', $type)
                ->with(['journalItems' => function($q) use ($startDate, $endDate) {
                    $q->whereHas('journalEntry', function($je) use ($startDate, $endDate) {
                        $je->whereBetween('transaction_date', [$startDate, $endDate]);
                    });
                }])
                ->get()
                ->map(function($account) use ($normalBalance) {
                     $debit = $account->journalItems->sum('debit');
                     $credit = $account->journalItems->sum('credit');

                    $account->balance = $normalBalance == 'DEBIT'
                        ? ($debit - $credit)
                        : ($credit - $debit);
                    return $account;
                });
        };

        $revenues = $getAccountsWithBalance('REVENUE', 'CREDIT');
        $expenses = $getAccountsWithBalance('EXPENSE', 'DEBIT');

        if ($request->has('print')) {
            return view('accounting.reports.laba_rugi_print', compact('revenues', 'expenses', 'startDate', 'endDate'));
        }

        return view('accounting.reports.laba_rugi', compact('revenues', 'expenses', 'startDate', 'endDate'));
    }

    public function arusKas(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-d');

        // Identify Cash Accounts (usually 1101, 1102 etc or type Asset + name like Cash/Bank)
        $cashAccountIds = ChartOfAccount::where('type', 'ASSET')
             ->where(function($q) { $q->where('name', 'like', '%Kas%')->orWhere('name', 'like', '%Bank%'); })
             ->pluck('id');

        // Cash In: Debit on Cash Account
        $cashInItems = JournalItem::whereIn('chart_of_account_id', $cashAccountIds)
            ->where('debit', '>', 0)
            ->whereHas('journalEntry', function($q) use ($startDate, $endDate) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
            })
            ->join('journal_entries', 'journal_items.journal_entry_id', '=', 'journal_entries.id')
            ->select('journal_items.*', 'journal_entries.reference_number as ref_number', 'journal_entries.description as description')
            ->get();

        // Cash Out: Credit on Cash Account
        $cashOutItems = JournalItem::whereIn('chart_of_account_id', $cashAccountIds)
            ->where('credit', '>', 0)
            ->whereHas('journalEntry', function($q) use ($startDate, $endDate) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
            })
            ->join('journal_entries', 'journal_items.journal_entry_id', '=', 'journal_entries.id')
            ->select('journal_items.*', 'journal_entries.reference_number as ref_number', 'journal_entries.description as description')
            ->get();

        if ($request->has('print')) {
            return view('accounting.reports.arus_kas_print', compact('cashInItems', 'cashOutItems', 'startDate', 'endDate'));
        }

        return view('accounting.reports.arus_kas', compact('cashInItems', 'cashOutItems', 'startDate', 'endDate'));
    }

    public function journalsData()
    {
        $journals = JournalEntry::with('items.account')->select('journal_entries.*');
        return DataTables::of($journals)
            ->addColumn('items', function ($journal) {
                $html = '<ul>';
                foreach ($journal->items as $item) {
                    $html .= '<li>' . $item->account->name . ': ' .
                             ($item->debit > 0 ? 'Dr '.number_format($item->debit) : 'Cr '.number_format($item->credit)) .
                             '</li>';
                }
                $html .= '</ul>';
                return $html;
            })
            ->addColumn('action', function ($journal) {
                return ''; // Read-only for now, maybe add View/Edit later
            })
            ->rawColumns(['items', 'action'])
            ->make(true);
    }

    public function createJournal()
    {
        $accounts = ChartOfAccount::all();
        return view('accounting.journals.create', compact('accounts'));
    }

    public function storeJournal(Request $request)
    {
        $request->validate([
            'transaction_date' => 'required|date',
            'description' => 'required',
            'items' => 'required|array',
            'items.*.chart_of_account_id' => 'required|exists:chart_of_accounts,id',
            'items.*.debit' => 'nullable|numeric',
            'items.*.credit' => 'nullable|numeric',
        ]);

        // Verify balance
        $totalDebit = collect($request->items)->sum('debit');
        $totalCredit = collect($request->items)->sum('credit');

        if (abs($totalDebit - $totalCredit) > 0.01) {
            return back()->withErrors(['message' => 'Journal must be balanced. Debit: '.$totalDebit.', Credit: '.$totalCredit]);
        }

        DB::transaction(function () use ($request) {
            $journal = JournalEntry::create([
                'transaction_date' => $request->transaction_date,
                'reference_number' => $request->reference_number,
                'description' => $request->description,
            ]);

            foreach ($request->items as $item) {
                JournalItem::create([
                    'journal_entry_id' => $journal->id,
                    'chart_of_account_id' => $item['chart_of_account_id'],
                    'debit' => $item['debit'] ?? 0,
                    'credit' => $item['credit'] ?? 0,
                    'description' => $item['description'] ?? null,
                ]);
            }
        });

        return redirect()->route('accounting.journals')->with('success', 'Journal entry created successfully');
    }
}
