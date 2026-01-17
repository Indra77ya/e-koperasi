<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Models\SavingHistory;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // 1. Total Dana Turun & Trend
        $totalDisbursed = Loan::whereIn('status', ['dicairkan', 'lunas', 'macet'])->sum('jumlah_pinjaman');

        $disbursedTrend = Loan::whereIn('status', ['dicairkan', 'lunas', 'macet'])
            ->select(
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month"),
                DB::raw("SUM(jumlah_pinjaman) as total")
            )
            ->where('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // 2. Pendapatan Bunga & Denda
        $revenueStats = LoanInstallment::where('status', 'lunas')
            ->select(
                DB::raw("DATE_FORMAT(tanggal_bayar, '%Y-%m') as month"),
                DB::raw("SUM(bunga) as total_bunga"),
                DB::raw("SUM(denda) as total_denda")
            )
            ->where('tanggal_bayar', '>=', Carbon::now()->subMonths(6))
            ->groupBy('month')
            ->orderBy('month')
            ->get();

        // 3. Piutang & Kolektabilitas
        $collectibilityStats = DB::table('pinjaman')
            ->join('pinjaman_angsuran', 'pinjaman.id', '=', 'pinjaman_angsuran.pinjaman_id')
            ->whereIn('pinjaman.status', ['dicairkan', 'macet'])
            ->where('pinjaman_angsuran.status', '!=', 'lunas')
            ->select(
                'pinjaman.kolektabilitas',
                DB::raw('COUNT(DISTINCT pinjaman.id) as count_loans'),
                DB::raw('SUM(pinjaman_angsuran.pokok) as total_outstanding')
            )
            ->groupBy('pinjaman.kolektabilitas')
            ->get();

        // 4. Pinjaman Jatuh Tempo Hari Ini
        $dueToday = LoanInstallment::whereDate('tanggal_jatuh_tempo', Carbon::today())
            ->where('status', '!=', 'lunas')
            ->with(['loan.member', 'loan.nasabah'])
            ->get();

        // Unified Mutation Logic (Savings + Loan Disburse + Loan Repay)
        $today = Carbon::today();

        // 1. Savings Mutations
        $savings = SavingHistory::whereDate('created_at', $today)
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

        // 2. Loan Disbursements (Detected by checking if first installment was created today)
        $disbursements = Loan::where('status', 'berjalan')
            ->whereHas('installments', function($q) use ($today) {
                $q->whereDate('created_at', $today);
            })
            ->with(['member', 'nasabah', 'installments' => function($q) {
                $q->orderBy('id', 'asc')->limit(1); // Fetch first installment for timestamp
            }])
            ->get()
            ->map(function ($item) {
                // Use the creation time of the first installment as the disbursement time
                $firstInstallment = $item->installments->first();
                $date = $firstInstallment ? $firstInstallment->created_at : $item->updated_at;

                return (object) [
                    'type' => 'loan_disburse',
                    'member' => $item->member,
                    'nasabah' => $item->nasabah,
                    'date' => $date,
                    'description' => 'Pencairan Pinjaman ' . $item->kode_pinjaman,
                    'debit' => 0,
                    'credit' => $item->jumlah_pinjaman, // User receives money (Credit from Bank view? No, typically "Mutasi" shows money movement relative to account).
                    // In SavingHistory: Kredit = Deposit (Money In to Account). Debit = Withdrawal (Money Out).
                    // Loan Disbursement: Money In to User (Credit?).
                    // Let's stick to the visual: Disbursement increases Loan Balance.
                    // For "Saldo Pinjaman", it starts at X.
                    // Let's leave debit/credit empty or specific for Loan?
                    // In the table: Debet / Kredit columns exist.
                    // If we treat it like Savings:
                    // Disbursement = Money In to User = "Kredit" (like Setoran)?
                    // But it increases Liability (Loan Balance).
                    // Let's put it in Kredit column for consistency with "Money In".
                    'credit' => $item->jumlah_pinjaman,
                    'debit' => 0,
                    'balance' => $item->jumlah_pinjaman, // Initial Balance
                    'balance_type' => 'Pinjaman'
                ];
            });

        // 3. Loan Repayments
        // Filter by updated_at to capture payments made "Today" even if backdated
        $repayments = LoanInstallment::where('status', 'lunas')
            ->whereDate('updated_at', $today)
            ->with(['loan.member', 'loan.nasabah'])
            ->get()
            ->map(function ($item) {
                $loan = $item->loan;
                // Payment = Money Out from User = "Debet" (like Penarikan)?
                // It decreases Loan Balance.
                return (object) [
                    'type' => 'loan_repayment',
                    'member' => $loan->member,
                    'nasabah' => $loan->nasabah,
                    'date' => $item->updated_at, // Use system timestamp
                    'description' => 'Pembayaran Angsuran ' . $loan->kode_pinjaman . ' (Ke-' . $item->angsuran_ke . ')',
                    'debit' => $item->total_angsuran + $item->denda, // Total paid
                    'credit' => 0,
                    'balance' => $item->sisa_pinjaman, // Balance after payment
                    'balance_type' => 'Pinjaman'
                ];
            });

        $mutations = $savings->merge($disbursements)->merge($repayments)->sortByDesc('date')->take(10);

        return view('home', compact(
            'mutations',
            'totalDisbursed',
            'disbursedTrend',
            'revenueStats',
            'collectibilityStats',
            'dueToday'
        ));
    }
}
