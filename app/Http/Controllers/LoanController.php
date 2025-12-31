<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Models\Member;
use App\Models\Nasabah;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LoanController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $loans = Loan::with(['member', 'nasabah'])->select('pinjaman.*');
            return DataTables::of($loans)
                ->editColumn('jumlah_pinjaman', function ($loan) {
                    return 'Rp ' . number_format($loan->jumlah_pinjaman, 0, ',', '.');
                })
                ->editColumn('suku_bunga', function ($loan) {
                    return $loan->suku_bunga . '% (' . ucfirst($loan->jenis_bunga) . ')';
                })
                ->addColumn('member_name', function ($loan) {
                    if ($loan->member) {
                        return $loan->member->nama . ' (Anggota)';
                    } elseif ($loan->nasabah) {
                        return $loan->nasabah->nama . ' (Nasabah)';
                    }
                    return '-';
                })
                ->addColumn('action', function ($loan) {
                    $btn = '<a href="' . route('loans.show', $loan->id) . '" class="btn btn-sm btn-info">Detail</a>';
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('loans.index');
    }

    public function create()
    {
        $members = Member::all();
        $nasabahs = Nasabah::all();
        return view('loans.create', compact('members', 'nasabahs'));
    }

    public function calculate(Request $request)
    {
        $amount = $request->amount;
        $tenor = $request->tenor; // months
        $rate = $request->rate; // yearly percentage
        $type = $request->type; // flat, efektif, anuitas

        $schedule = $this->generateSchedule($amount, $tenor, $rate, $type);

        return response()->json($schedule);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tipe_peminjam' => 'required|in:anggota,nasabah',
            'anggota_id' => 'required_if:tipe_peminjam,anggota|nullable|exists:anggota,id',
            'nasabah_id' => 'required_if:tipe_peminjam,nasabah|nullable|exists:nasabahs,id',
            'jenis_pinjaman' => 'required',
            'jumlah_pinjaman' => 'required|numeric|min:0',
            'tenor' => 'required|integer|min:1',
            'suku_bunga' => 'required|numeric|min:0',
            'jenis_bunga' => 'required|in:flat,efektif,anuitas',
            'tanggal_pengajuan' => 'required|date',
        ]);

        $loan = Loan::create([
            'kode_pinjaman' => 'P-' . date('Ymd') . '-' . rand(100, 999),
            'anggota_id' => $request->tipe_peminjam == 'anggota' ? $request->anggota_id : null,
            'nasabah_id' => $request->tipe_peminjam == 'nasabah' ? $request->nasabah_id : null,
            'jenis_pinjaman' => $request->jenis_pinjaman,
            'jumlah_pinjaman' => $request->jumlah_pinjaman,
            'tenor' => $request->tenor,
            'suku_bunga' => $request->suku_bunga,
            'jenis_bunga' => $request->jenis_bunga,
            'biaya_admin' => $request->biaya_admin ?? 0,
            'tanggal_pengajuan' => $request->tanggal_pengajuan,
            'status' => 'diajukan',
            'keterangan' => $request->keterangan,
        ]);

        return redirect()->route('loans.index')->with('success', 'Pinjaman berhasil diajukan.');
    }

    public function show($id)
    {
        $loan = Loan::with(['member', 'nasabah', 'installments'])->findOrFail($id);
        return view('loans.show', compact('loan'));
    }

    public function approve($id)
    {
        $loan = Loan::findOrFail($id);
        if ($loan->status == 'diajukan') {
            $loan->update([
                'status' => 'disetujui',
                'tanggal_persetujuan' => now(),
            ]);
        }
        return redirect()->back()->with('success', 'Pinjaman disetujui.');
    }

    public function disburse(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);
        if ($loan->status == 'disetujui') {
            DB::transaction(function () use ($loan) {
                $loan->update(['status' => 'berjalan']);

                $schedule = $this->generateSchedule($loan->jumlah_pinjaman, $loan->tenor, $loan->suku_bunga, $loan->jenis_bunga);

                foreach ($schedule as $inst) {
                    LoanInstallment::create([
                        'pinjaman_id' => $loan->id,
                        'angsuran_ke' => $inst['month'],
                        'tanggal_jatuh_tempo' => now()->addMonths($inst['month']), // Simplifying due date to +1 month from now
                        'total_angsuran' => $inst['total'],
                        'pokok' => $inst['principal'],
                        'bunga' => $inst['interest'],
                        'sisa_pinjaman' => $inst['balance'],
                        'status' => 'belum_lunas',
                    ]);
                }
            });
        }
        return redirect()->back()->with('success', 'Dana dicairkan, jadwal angsuran dibuat.');
    }

    private function generateSchedule($amount, $tenor, $rate, $type)
    {
        $schedule = [];
        $balance = $amount;
        $ratePerMonth = ($rate / 100) / 12;

        if ($type == 'flat') {
            $principal = $amount / $tenor;
            $interest = ($amount * ($rate / 100)) / 12; // Flat interest based on original amount
             // Or annual rate / 12 * principal? Usually Flat is (P * R * T) / N.
             // If Rate is 12% p.a., monthly is 1%.
             // Monthly Interest = Principal * (Rate/12).
            $total = $principal + $interest;

            for ($i = 1; $i <= $tenor; $i++) {
                $balance -= $principal;
                if ($i == $tenor && $balance != 0) {
                     // Adjust last
                     // But for simple flat, balance just goes down.
                }
                $schedule[] = [
                    'month' => $i,
                    'principal' => round($principal, 2),
                    'interest' => round($interest, 2),
                    'total' => round($total, 2),
                    'balance' => max(0, round($balance, 2))
                ];
            }

        } elseif ($type == 'efektif') {
             // Sliding: Interest on remaining balance. Principal is constant?
             // Usually Principal is constant = Amount / Tenor.
            $principal = $amount / $tenor;

            for ($i = 1; $i <= $tenor; $i++) {
                $interest = $balance * $ratePerMonth;
                $total = $principal + $interest;
                $balance -= $principal;

                $schedule[] = [
                    'month' => $i,
                    'principal' => round($principal, 2),
                    'interest' => round($interest, 2),
                    'total' => round($total, 2),
                    'balance' => max(0, round($balance, 2))
                ];
            }

        } elseif ($type == 'anuitas') {
             // PMT = P * r * (1+r)^n / ((1+r)^n - 1)
            $pmt = ($amount * $ratePerMonth) / (1 - pow(1 + $ratePerMonth, -$tenor));

            for ($i = 1; $i <= $tenor; $i++) {
                $interest = $balance * $ratePerMonth;
                $principal = $pmt - $interest;
                $balance -= $principal;

                $schedule[] = [
                    'month' => $i,
                    'principal' => round($principal, 2),
                    'interest' => round($interest, 2),
                    'total' => round($pmt, 2),
                    'balance' => max(0, round($balance, 2))
                ];
            }
        }

        return $schedule;
    }
}
