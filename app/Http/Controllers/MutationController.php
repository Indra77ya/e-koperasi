<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Nasabah;
use Illuminate\Http\Request;
use App\Models\SavingHistory;
use App\Models\Saving;
use App\Models\Loan;
use App\Models\LoanInstallment;
use Illuminate\Support\Facades\Log;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class MutationController extends Controller
{
    public function index()
    {
        $members = Member::orderBy('nama', 'asc')->get();
        $nasabahs = Nasabah::orderBy('nama', 'asc')->get();

        return view('mutations.index', compact('members', 'nasabahs'));
    }

    public function feed(Request $request)
    {
        $date = $request->input('date', Carbon::today()->format('Y-m-d'));
        $searchDate = Carbon::parse($date);

        // 1. Savings Mutations
        $savings = SavingHistory::whereDate('created_at', $searchDate)
            ->with(['member', 'nasabah'])
            ->get()
            ->map(function ($item) {
                return (object) [
                    'type' => 'saving',
                    'member' => $item->member,
                    'nasabah' => $item->nasabah,
                    'date' => $item->created_at,
                    'description' => $item->keterangan,
                    'debit' => $item->debet,
                    'credit' => $item->kredit,
                    'balance' => $item->saldo,
                    'balance_type' => 'Tabungan'
                ];
            });

        $savings = collect($savings);

        // 2. Loan Disbursements
        $disbursements = Loan::whereIn('status', ['berjalan', 'lunas', 'macet', 'dicairkan'])
            ->whereHas('installments', function($q) use ($searchDate) {
                $q->whereDate('created_at', $searchDate);
            })
            ->with(['member', 'nasabah', 'installments' => function($q) {
                $q->orderBy('id', 'asc')->limit(1);
            }])
            ->get()
            ->map(function ($item) {
                $firstInstallment = $item->installments->first();
                $date = $firstInstallment ? $firstInstallment->created_at : $item->updated_at;

                return (object) [
                    'type' => 'loan_disburse',
                    'member' => $item->member,
                    'nasabah' => $item->nasabah,
                    'date' => $date,
                    'description' => 'Pencairan Pinjaman ' . $item->kode_pinjaman,
                    'debit' => 0,
                    'credit' => $item->jumlah_pinjaman,
                    'balance' => $item->jumlah_pinjaman,
                    'balance_type' => 'Pinjaman'
                ];
            });

        // 3. Loan Repayments
        $repayments = LoanInstallment::where('status', 'lunas')
            ->whereDate('updated_at', $searchDate)
            ->with(['loan.member', 'loan.nasabah'])
            ->get()
            ->map(function ($item) {
                $loan = $item->loan;
                return (object) [
                    'type' => 'loan_repayment',
                    'member' => $loan->member,
                    'nasabah' => $loan->nasabah,
                    'date' => $item->updated_at,
                    'description' => 'Pembayaran Angsuran ' . $loan->kode_pinjaman . ' (Ke-' . $item->angsuran_ke . ')',
                    'debit' => $item->total_angsuran + $item->denda,
                    'credit' => 0,
                    'balance' => $item->sisa_pinjaman,
                    'balance_type' => 'Pinjaman'
                ];
            });

        // Merge and Sort
        $mutations = $savings->merge($disbursements)->merge($repayments)->sortByDesc('date');

        // Paginate
        $currentPage = LengthAwarePaginator::resolveCurrentPage();
        $perPage = 20;
        $currentItems = $mutations->slice(($currentPage - 1) * $perPage, $perPage)->all();
        $paginatedItems = new LengthAwarePaginator($currentItems, count($mutations), $perPage);
        $paginatedItems->setPath($request->url());
        $paginatedItems->appends(['date' => $date]);

        return view('mutations.feed', compact('paginatedItems', 'date'));
    }

    public function check_mutations(Request $request)
    {
        try {
            $type = $request->query('type');
            $id = $request->query('id');
            $fromDate = $request->query('from_date');
            $toDate = $request->query('to_date');

            if (!$type || !$id) {
                return response()->json(['error' => 'Tipe dan ID harus dipilih.'], 400);
            }

            $queryHistory = SavingHistory::query();
            $queryBalance = Saving::query();

            if ($type == 'anggota') {
                $queryHistory->where('anggota_id', $id);
                $queryBalance->where('anggota_id', $id);
            } else {
                $queryHistory->where('nasabah_id', $id);
                $queryBalance->where('nasabah_id', $id);
            }

            if ($fromDate && $toDate) {
                $queryHistory->whereBetween('tanggal', [$fromDate, $toDate]);
            }

            $savings_history = $queryHistory->orderBy('id', 'asc')->get();
            $total_credit = $savings_history->sum('kredit');
            $total_debet = $savings_history->sum('debet');
            $balance = $queryBalance->first();

            // Prevent error if no balance record exists yet
            $currentBalance = $balance ? $balance->saldo : 0;

            $view = view('mutations.check_mutations', compact('savings_history', 'currentBalance', 'total_credit', 'total_debet'))->render();

            return response()->json([
                'html'=> $view,
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
