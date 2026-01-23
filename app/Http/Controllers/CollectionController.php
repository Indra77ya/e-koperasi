<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\Setting;
use App\Models\PenagihanLog;
use App\Models\PenagihanLapangan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\DataTables;
use Carbon\Carbon;

class CollectionController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the collection dashboard.
     */
    public function index()
    {
        // Counts
        $countLancar = Loan::where('status', '!=', 'lunas')->where('kolektabilitas', 'Lancar')->count();
        $countDPK = Loan::where('status', '!=', 'lunas')->where('kolektabilitas', 'DPK')->count();
        $countKL = Loan::where('status', '!=', 'lunas')->where('kolektabilitas', 'Kurang Lancar')->count();
        $countDiragukan = Loan::where('status', '!=', 'lunas')->where('kolektabilitas', 'Diragukan')->count();
        $countMacet = Loan::where('status', '!=', 'lunas')->where('kolektabilitas', 'Macet')->count();

        // Reminders: Loans with installment due in next 3 days OR overdue but marked as Lancar
        $reminders = Loan::where('status', '!=', 'lunas')
            ->whereHas('installments', function ($q) {
                $q->where('status', 'belum_lunas')
                  ->whereBetween('tanggal_jatuh_tempo', [today(), today()->addDays(3)]);
            })
            ->with(['member', 'nasabah'])
            ->limit(10)
            ->get();

        return view('collections.index', compact('countLancar', 'countDPK', 'countKL', 'countDiragukan', 'countMacet', 'reminders'));
    }

    /**
     * DataTables for Loan List by Status
     */
    public function data(Request $request)
    {
        $status = $request->input('kolektabilitas');

        $query = Loan::with(['member', 'nasabah', 'installments'])
                     ->where('status', '!=', 'lunas');

        if ($status) {
            $query->where('kolektabilitas', $status);
        }

        return DataTables::of($query)
            ->addColumn('borrower', function ($loan) {
                return $loan->member ? $loan->member->nama : ($loan->nasabah ? $loan->nasabah->nama : '-');
            })
            ->addColumn('overdue_days', function ($loan) {
                return $loan->days_past_due . ' Hari';
            })
            ->addColumn('action', function ($loan) {
                return '<a href="'.route('loans.show', $loan->id).'" class="btn btn-sm btn-primary">Detail</a>';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * Store a collection log.
     */
    public function storeLog(Request $request)
    {
        $request->validate([
            'pinjaman_id' => 'required|exists:pinjaman,id',
            'metode_penagihan' => 'required',
            'hasil_penagihan' => 'required',
            'catatan' => 'nullable|string',
            'tanggal_janji_bayar' => 'nullable|date',
        ]);

        PenagihanLog::create([
            'pinjaman_id' => $request->pinjaman_id,
            'user_id' => Auth::id(),
            'metode_penagihan' => $request->metode_penagihan,
            'hasil_penagihan' => $request->hasil_penagihan,
            'catatan' => $request->catatan,
            'tanggal_janji_bayar' => $request->tanggal_janji_bayar,
        ]);

        return back()->with('success', 'Riwayat penagihan berhasil ditambahkan.');
    }

    /**
     * Add to Field Collection Queue.
     */
    public function addToFieldQueue(Request $request)
    {
        $request->validate([
            'pinjaman_id' => 'required|exists:pinjaman,id',
            'petugas_id' => 'nullable|exists:users,id',
            'tanggal_rencana_kunjungan' => 'required|date',
            'catatan_tugas' => 'nullable|string',
        ]);

        PenagihanLapangan::create([
            'pinjaman_id' => $request->pinjaman_id,
            'petugas_id' => $request->petugas_id,
            'tanggal_rencana_kunjungan' => $request->tanggal_rencana_kunjungan,
            'status' => 'baru',
            'catatan_tugas' => $request->catatan_tugas,
        ]);

        return back()->with('success', 'Berhasil ditambahkan ke antrian lapangan.');
    }

    /**
     * View Field Queue
     */
    public function fieldQueue()
    {
        $queue = PenagihanLapangan::with(['loan.member', 'loan.nasabah', 'petugas'])
            ->where('status', '!=', 'selesai')
            ->orderBy('tanggal_rencana_kunjungan', 'asc')
            ->get();

        return view('collections.field_queue', compact('queue'));
    }

    /**
     * Update Field Queue Status
     */
    public function updateFieldQueueStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:selesai,batal,dalam_proses',
        ]);

        $task = PenagihanLapangan::findOrFail($id);
        $task->status = $request->status;
        $task->save();

        // Sync with Collection History (Timeline)
        if ($request->status == 'selesai' || $request->status == 'batal') {
            PenagihanLog::create([
                'pinjaman_id' => $task->pinjaman_id,
                'user_id' => Auth::id(),
                'metode_penagihan' => 'Kunjungan Lapangan',
                'hasil_penagihan' => $request->status == 'selesai' ? 'Tugas Selesai' : 'Tugas Dibatalkan',
                'catatan' => "Status antrian lapangan diperbarui menjadi " . $request->status . ". \nCatatan Tugas: " . $task->catatan_tugas,
                'tanggal_janji_bayar' => null,
            ]);
        }

        return back()->with('success', 'Status tugas berhasil diperbarui.');
    }

    /**
     * Refresh Collectibility Statuses
     * Loops through active loans and updates their status based on overdue days.
     */
    public function refreshCollectibility()
    {
        $loans = Loan::where('status', '!=', 'lunas')->get();
        $countUpdated = 0;

        // Fetch thresholds from settings
        $dpkDays = Setting::get('col_dpk_days', 1);
        $klDays = Setting::get('col_kl_days', 91);
        $diragukanDays = Setting::get('col_diragukan_days', 121);
        $macetDays = Setting::get('col_macet_days', 181);

        foreach ($loans as $loan) {
            $dpd = $loan->days_past_due;
            $newStatus = 'Lancar';

            if ($dpd < $dpkDays) {
                $newStatus = 'Lancar';
            } elseif ($dpd < $klDays) {
                $newStatus = 'DPK';
            } elseif ($dpd < $diragukanDays) {
                $newStatus = 'Kurang Lancar';
            } elseif ($dpd < $macetDays) {
                $newStatus = 'Diragukan';
            } else {
                $newStatus = 'Macet';
            }

            if ($loan->kolektabilitas !== $newStatus) {
                $loan->kolektabilitas = $newStatus;
                $loan->save();
                $countUpdated++;
            }
        }

        return back()->with('success', "Status kolektabilitas diperbarui. $countUpdated data berubah.");
    }
}
