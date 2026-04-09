<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Loan;
use App\Models\Collateral;
use App\Models\JournalItem;
use App\Models\ChartOfAccount;
use DB;
use Carbon\Carbon;

class ReportController extends Controller
{
    public function index()
    {
        return view('reports.index');
    }

    public function outstanding(Request $request)
    {
        \App\Http\Controllers\LoanController::syncAllActiveIndefiniteLoans();
        $query = Loan::with(['member', 'nasabah'])
            ->whereIn('status', ['disetujui', 'dicairkan', 'berjalan']);

        if ($request->has('export')) {
            return $this->exportOutstanding($query->cursor());
        }

        $totals = (clone $query)->select(
            DB::raw('SUM(jumlah_pinjaman) as total_pokok'),
            DB::raw('COUNT(*) as total_count')
        )->first();

        // Summing remaining balance is tricky because it's a dynamic property or requires join
        $totals->total_remaining = DB::table('pinjaman')
            ->join('pinjaman_angsuran', 'pinjaman.id', '=', 'pinjaman_angsuran.pinjaman_id')
            ->whereIn('pinjaman.status', ['disetujui', 'dicairkan', 'berjalan'])
            ->where('pinjaman_angsuran.status', '!=', 'lunas')
            ->sum('pinjaman_angsuran.pokok');

        if ($request->has('print')) {
            $loans = $query->get();
            return view('reports.outstanding_print', compact('loans', 'totals'));
        }

        $loans = $query->paginate(20);

        return view('reports.outstanding', compact('loans', 'totals'));
    }

    public function badDebt(Request $request)
    {
        $query = Loan::with(['member', 'nasabah'])
            ->where('status', 'macet');

        if ($request->has('export')) {
            return $this->exportBadDebt($query->cursor());
        }

        $totals = (clone $query)->select(
            DB::raw('SUM(jumlah_pinjaman) as total_pokok'),
            DB::raw('COUNT(*) as total_count')
        )->first();

        $totals->total_remaining = DB::table('pinjaman')
            ->join('pinjaman_angsuran', 'pinjaman.id', '=', 'pinjaman_angsuran.pinjaman_id')
            ->where('pinjaman.status', 'macet')
            ->where('pinjaman_angsuran.status', '!=', 'lunas')
            ->sum('pinjaman_angsuran.pokok');

        if ($request->has('print')) {
            $loans = $query->get();
            return view('reports.bad_debt_print', compact('loans', 'totals'));
        }

        $loans = $query->paginate(20);

        return view('reports.bad_debt', compact('loans', 'totals'));
    }

    public function collateral(Request $request)
    {
        $query = Collateral::with(['loan.member', 'loan.nasabah'])
            ->where('status', 'disimpan');

        if ($request->has('export')) {
            return $this->exportCollateral($query->cursor());
        }

        $totals = (clone $query)->select(
            DB::raw('SUM(nilai_taksasi) as total_value'),
            DB::raw('COUNT(*) as total_count')
        )->first();

        $typeCounts = (clone $query)->select('jenis', DB::raw('COUNT(*) as count'))
            ->groupBy('jenis')
            ->get();

        $collaterals = $query->orderBy('jenis')->get();

        if ($request->has('print')) {
            return view('reports.collateral_print', compact('collaterals', 'totals', 'typeCounts'));
        }

        return view('reports.collateral', compact('collaterals', 'totals', 'typeCounts'));
    }

    public function cashFlow(Request $request)
    {
        $filter = $request->filter ?? 'daily'; // daily, weekly, monthly
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-d');

        // Reuse logic from AccountingController@arusKas but simplified for display
         $cashAccountIds = ChartOfAccount::where('type', 'ASSET')
             ->where(function($q) { $q->where('name', 'like', '%Kas%')->orWhere('name', 'like', '%Bank%'); })
             ->pluck('id');

        $baseQuery = JournalItem::whereIn('chart_of_account_id', $cashAccountIds)
            ->whereHas('journalEntry', function($q) use ($startDate, $endDate) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
            })
            ->with(['journalEntry', 'account']);

        if ($request->has('export')) {
            return $this->exportCashFlow($baseQuery->cursor());
        }

        // Clone for totals to avoid side effects
        $totalIn = (clone $baseQuery)->where('debit', '>', 0)->sum('debit');
        $totalOut = (clone $baseQuery)->where('credit', '>', 0)->sum('credit');

        if ($request->has('print')) {
            $transactions = $baseQuery->get();
            return view('reports.cash_flow_print', compact('transactions', 'filter', 'startDate', 'endDate', 'totalIn', 'totalOut'));
        }

        $transactions = $baseQuery->paginate(50);

        return view('reports.cash_flow', compact('transactions', 'filter', 'startDate', 'endDate', 'totalIn', 'totalOut'));
    }

    public function arrears(Request $request)
    {
        \App\Http\Controllers\LoanController::syncAllActiveIndefiniteLoans();
        $query = Loan::with(['member', 'nasabah', 'installments' => function($q) {
                $q->where('status', '!=', 'lunas')
                  ->where('tanggal_jatuh_tempo', '<', now());
            }])
            ->whereHas('installments', function($q) {
                $q->where('status', '!=', 'lunas')
                  ->where('tanggal_jatuh_tempo', '<', now());
            });

        if ($request->has('export')) {
            return $this->exportArrears($query->get());
        }

        if ($request->has('print')) {
            $loans = $query->get();
            // Sums for the header
            $totals = DB::table('pinjaman_angsuran')
                ->where('status', '!=', 'lunas')
                ->where('tanggal_jatuh_tempo', '<', now())
                ->select(
                    DB::raw('SUM(pokok) as total_pokok'),
                    DB::raw('SUM(bunga) as total_bunga'),
                    DB::raw('SUM(biaya_admin) as total_admin'),
                    DB::raw('SUM(denda) as total_denda')
                )
                ->first();

            return view('reports.arrears_print', compact('loans', 'totals'));
        }

        $loans = $query->paginate(20);

        // Sums for the header
        $totals = DB::table('pinjaman_angsuran')
            ->where('status', '!=', 'lunas')
            ->where('tanggal_jatuh_tempo', '<', now())
            ->select(
                DB::raw('SUM(pokok) as total_pokok'),
                DB::raw('SUM(bunga) as total_bunga'),
                DB::raw('SUM(biaya_admin) as total_admin'),
                DB::raw('SUM(denda) as total_denda')
            )
            ->first();


        return view('reports.arrears', compact('loans', 'totals'));
    }

    public function revenue(Request $request)
    {
        $startDate = $request->start_date ?? date('Y-m-01');
        $endDate = $request->end_date ?? date('Y-m-d');

        // Revenue accounts (Income from Interest, Penalties)
        $baseQuery = JournalItem::whereHas('account', function($q) {
                $q->where('type', 'REVENUE');
            })
            ->whereHas('journalEntry', function($q) use ($startDate, $endDate) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
            })
            ->with(['journalEntry', 'account']);

        if ($request->has('export')) {
            return $this->exportRevenue($baseQuery->cursor());
        }

        // Clone for summation
        $totalRevenue = (clone $baseQuery)->sum(DB::raw('credit - debit')); // Revenue is Credit normal

        if ($request->has('print')) {
            $revenues = $baseQuery->get();
            return view('reports.revenue_print', compact('revenues', 'startDate', 'endDate', 'totalRevenue'));
        }

        $revenues = $baseQuery->paginate(50);

        return view('reports.revenue', compact('revenues', 'startDate', 'endDate', 'totalRevenue'));
    }

    // Export Helper Methods
    private function streamCsv($filename, $headers, $cursor, $transformer)
    {
        return response()->stream(function() use ($headers, $cursor, $transformer) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            foreach ($cursor as $record) {
                fputcsv($file, $transformer($record));
            }
            fclose($file);
        }, 200, [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ]);
    }

    private function exportOutstanding($cursor)
    {
        return $this->streamCsv('outstanding_loans.csv', ['Nomor Pinjaman', 'Nama', 'Jumlah', 'Sisa', 'Status', 'Tanggal'], $cursor, function($loan) {
            return [
                $loan->kode_pinjaman,
                $loan->member ? $loan->member->name : ($loan->nasabah ? $loan->nasabah->name : '-'),
                $loan->jumlah_pinjaman,
                $loan->remaining_balance ?? 0,
                $loan->status,
                $loan->created_at->format('Y-m-d')
            ];
        });
    }

    private function exportBadDebt($cursor)
    {
        return $this->streamCsv('bad_debts.csv', ['Nomor Pinjaman', 'Nama', 'Jumlah', 'Sisa', 'Status', 'Tanggal Macet'], $cursor, function($loan) {
            return [
                $loan->kode_pinjaman,
                $loan->member ? $loan->member->name : ($loan->nasabah ? $loan->nasabah->name : '-'),
                $loan->jumlah_pinjaman,
                $loan->remaining_balance ?? 0,
                $loan->status,
                $loan->updated_at->format('Y-m-d')
            ];
        });
    }

    private function exportCollateral($cursor)
    {
        return $this->streamCsv('collaterals.csv', ['Jenis', 'Nomor', 'Nilai Taksiran', 'Pemilik', 'No Pinjaman', 'Status', 'Keterangan'], $cursor, function($col) {
            return [
                $col->jenis,
                $col->nomor,
                $col->nilai_taksasi,
                $col->pemilik,
                $col->loan ? $col->loan->kode_pinjaman : '-',
                $col->status,
                $col->keterangan
            ];
        });
    }

    private function exportCashFlow($cursor)
    {
        return $this->streamCsv('cash_flow.csv', ['Tanggal', 'Ref', 'Akun', 'Deskripsi', 'Masuk (Debit)', 'Keluar (Credit)'], $cursor, function($item) {
            return [
                $item->journalEntry->transaction_date,
                $item->journalEntry->reference_number,
                $item->account->name,
                $item->description ?? $item->journalEntry->description,
                $item->debit,
                $item->credit
            ];
        });
    }

    private function exportRevenue($cursor)
    {
        return $this->streamCsv('revenue.csv', ['Tanggal', 'Akun', 'Deskripsi', 'Jumlah'], $cursor, function($item) {
            return [
                $item->journalEntry->transaction_date,
                $item->account->name,
                $item->description ?? $item->journalEntry->description,
                $item->credit - $item->debit
            ];
        });
    }

    private function exportArrears($loans)
    {
        return response()->stream(function() use ($loans) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No Pinjaman', 'Nama', 'Pokok', 'Bunga', 'Admin', 'Denda', 'Total Tunggakan']);
            foreach ($loans as $loan) {
                $pokok = $loan->installments->sum('pokok');
                $bunga = $loan->installments->sum('bunga');
                $admin = $loan->installments->sum('biaya_admin');
                $denda = $loan->installments->sum('denda');
                fputcsv($file, [
                    $loan->kode_pinjaman,
                    $loan->member ? $loan->member->nama : ($loan->nasabah ? $loan->nasabah->nama : '-'),
                    $pokok,
                    $bunga,
                    $admin,
                    $denda,
                    $pokok + $bunga + $admin + $denda
                ]);
            }
            fclose($file);
        }, 200, [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=tunggakan.csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ]);
    }
}
