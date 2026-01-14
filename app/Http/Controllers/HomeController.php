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

        // Existing mutation logic
        $mutations = SavingHistory::whereDate('created_at', Carbon::today())
                                    ->take(5)
                                    ->latest()
                                    ->get();

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
