<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Models\SavingHistory;
use Illuminate\Http\Request;
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
        // 1. Total Dana Turun (Disbursed)
        // User clarified this should be "Total Channeled Funds" (Cumulative Disbursed), not Outstanding.
        // Include 'berjalan' (active), 'lunas' (paid), 'macet' (bad debt), 'dicairkan' (legacy/synonym).
        $totalDisbursed = Loan::whereIn('status', ['berjalan', 'dicairkan', 'lunas', 'macet'])
            ->sum('jumlah_pinjaman');

        // Generate last 6 months keys
        $months = collect([]);
        for ($i = 5; $i >= 0; $i--) {
            $months->push(Carbon::now()->subMonths($i)->format('Y-m'));
        }

        // Trend Chart: Use tanggal_persetujuan (Approval Date) as proxy for Disbursement Date
        // updated_at is unreliable as it changes on payment/status update.
        $disbursedTrendRaw = Loan::whereIn('status', ['berjalan', 'dicairkan', 'lunas', 'macet'])
            ->where('tanggal_persetujuan', '>=', Carbon::now()->subMonths(6)->startOfMonth())
            ->get()
            ->groupBy(function ($item) {
                return $item->tanggal_persetujuan ? Carbon::parse($item->tanggal_persetujuan)->format('Y-m') : 'N/A';
            });

        $disbursedTrend = $months->map(function ($month) use ($disbursedTrendRaw) {
            $group = $disbursedTrendRaw->get($month);
            return (object) [
                'month' => $month,
                'total' => $group ? $group->sum('jumlah_pinjaman') : 0
            ];
        });

        // 2. Pendapatan Bunga & Denda
        $revenueStatsRaw = LoanInstallment::where('status', 'lunas')
            ->where('tanggal_bayar', '>=', Carbon::now()->subMonths(6)->startOfMonth())
            ->get()
            ->groupBy(function ($item) {
                return $item->tanggal_bayar ? $item->tanggal_bayar->format('Y-m') : 'N/A';
            });

        $revenueStats = $months->map(function ($month) use ($revenueStatsRaw) {
            $group = $revenueStatsRaw->get($month);
            return (object) [
                'month' => $month,
                'total_bunga' => $group ? $group->sum('bunga') : 0,
                'total_denda' => $group ? $group->sum('denda') : 0
            ];
        });

        // 3. Piutang & Kolektabilitas
        $collectibilityStats = DB::table('pinjaman')
            ->join('pinjaman_angsuran', 'pinjaman.id', '=', 'pinjaman_angsuran.pinjaman_id')
            ->whereIn('pinjaman.status', ['berjalan', 'dicairkan', 'macet'])
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

        // Convert to Base Collection to allow merging with other collections of stdClass
        $savings = collect($savings);

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

    public function getDisbursedTrend(Request $request)
    {
        $filter = $request->get('filter', '6_months');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Default query
        $query = Loan::whereIn('status', ['berjalan', 'dicairkan', 'lunas', 'macet']);

        $labels = collect([]);
        $format = 'Y-m'; // Default format

        if ($filter == 'today') {
            $start = Carbon::today();
            $format = 'Y-m-d';
            $labels->push($start->format($format));
            $query->whereDate('tanggal_persetujuan', $start);

        } elseif ($filter == '1_month') {
            // Last 30 days
            $start = Carbon::today()->subDays(29);
            $end = Carbon::today();
            $format = 'd M';
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $labels->push($date->format($format));
            }
            $query->whereBetween('tanggal_persetujuan', [$start, $end]);

        } elseif ($filter == '3_months') {
            // Last 3 months (Daily resolution)
            $start = Carbon::today()->subMonths(3);
            $end = Carbon::today();
            $format = 'd M';
            for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                $labels->push($date->format($format));
            }
            $query->whereBetween('tanggal_persetujuan', [$start, $end]);

        } elseif ($filter == '6_months') {
            $start = Carbon::today()->subMonths(5)->startOfMonth();
            $end = Carbon::today()->endOfMonth();
            $format = 'Y-m';
            for ($date = $start->copy(); $date->lte($end); $date->addMonth()) {
                $labels->push($date->format($format));
            }
            $query->whereBetween('tanggal_persetujuan', [$start, $end]);

        } elseif ($filter == '1_year') {
            $start = Carbon::today()->subMonths(11)->startOfMonth();
            $end = Carbon::today()->endOfMonth();
            $format = 'Y-m';
            for ($date = $start->copy(); $date->lte($end); $date->addMonth()) {
                $labels->push($date->format($format));
            }
            $query->whereBetween('tanggal_persetujuan', [$start, $end]);

        } elseif ($filter == 'last_year') {
            $start = Carbon::today()->subYear()->startOfYear();
            $end = Carbon::today()->subYear()->endOfYear();
            $format = 'Y-m';
            for ($date = $start->copy(); $date->lte($end); $date->addMonth()) {
                $labels->push($date->format($format));
            }
            $query->whereBetween('tanggal_persetujuan', [$start, $end]);

        } elseif ($filter == 'custom') {
            if ($startDate && $endDate) {
                $start = Carbon::parse($startDate);
                $end = Carbon::parse($endDate);
                $diffDays = $start->diffInDays($end);

                // If > 60 days, use Monthly resolution, otherwise Daily
                if ($diffDays > 60) {
                    $format = 'Y-m';
                    $iterStart = $start->copy()->startOfMonth();
                    $iterEnd = $end->copy()->endOfMonth();
                    for ($date = $iterStart; $date->lte($iterEnd); $date->addMonth()) {
                        $labels->push($date->format($format));
                    }
                } else {
                    $format = 'Y-m-d';
                    for ($date = $start->copy(); $date->lte($end); $date->addDay()) {
                        $labels->push($date->format($format));
                    }
                }
                $query->whereBetween('tanggal_persetujuan', [$start, $end]);
            }
        }

        // Execute Query
        $results = $query->get()->groupBy(function($item) use ($format) {
            return $item->tanggal_persetujuan ? Carbon::parse($item->tanggal_persetujuan)->format($format) : 'N/A';
        });

        // Map to labels
        $chartData = $labels->map(function($label) use ($results) {
            $group = $results->get($label);
            return $group ? $group->sum('jumlah_pinjaman') : 0;
        });

        return response()->json([
            'labels' => $labels,
            'data' => $chartData,
            'format' => $format // Helpful for frontend formatting
        ]);
    }
}
