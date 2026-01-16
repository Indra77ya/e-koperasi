<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Nasabah;
use App\Models\Saving;
use App\Models\Withdrawal;
use App\Models\Setting;
use App\Models\JournalEntry;
use App\Models\SavingHistory;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreWithdrawal;
use App\Services\AccountingService;

class WithdrawalController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $members = Member::orderBy('nama', 'asc')->get();
        $nasabahs = Nasabah::orderBy('nama', 'asc')->get();

        return view('withdrawals.index', compact('members', 'nasabahs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $members = Member::orderBy('nama', 'asc')->get();
        $nasabahs = Nasabah::orderBy('nama', 'asc')->get();

        return view('withdrawals.create', compact('members', 'nasabahs'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreWithdrawal $request)
    {
        $anggotaId = ($request->tipe_penarik == 'anggota') ? $request->anggota : null;
        $nasabahId = ($request->tipe_penarik == 'nasabah') ? $request->nasabah : null;

        // Check Balance
        if ($anggotaId) {
            $saving = Saving::whereAnggotaId($anggotaId)->first();
        } else {
            $saving = Saving::whereNasabahId($nasabahId)->first();
        }

        if (!$saving || $saving->saldo < $request->jumlah) {
             return redirect()->back()->with('error', 'Saldo tidak mencukupi!')->withInput();
        }

        try {
            DB::transaction(function () use ($request, $anggotaId, $nasabahId, $saving) {
                // Insert Withdrawal
                $withdrawal = new Withdrawal;
                $withdrawal->anggota_id = $anggotaId;
                $withdrawal->nasabah_id = $nasabahId;
                $withdrawal->jumlah = $request->jumlah;
                $withdrawal->keterangan = $request->keterangan;
                $withdrawal->save();

                // Update Saving
                $saving->saldo -= $request->jumlah;
                $saving->save();

                // Create History
                $history = new SavingHistory;
                $history->anggota_id = $anggotaId;
                $history->nasabah_id = $nasabahId;
                $history->tanggal = now()->toDateString();
                $history->keterangan = 'penarikan';
                $history->debet = $request->jumlah;
                $history->saldo = $saving->saldo;
                $history->save();

                // Journal
                $coaCash = Setting::get('coa_cash', '1101');
                $coaSavings = Setting::get('coa_savings', '2101');

                // Debit Savings (Liability decreases), Credit Cash (Asset decreases)
                $journalItems = [
                    ['code' => $coaSavings, 'debit' => $request->jumlah, 'credit' => 0],
                    ['code' => $coaCash, 'debit' => 0, 'credit' => $request->jumlah],
                ];

                $name = $anggotaId ? Member::find($anggotaId)->nama : Nasabah::find($nasabahId)->nama;

                $this->accountingService->createJournal(
                    now(),
                    'WDR-' . $withdrawal->id,
                    'Penarikan Simpanan ' . $name,
                    $journalItems,
                    $withdrawal
                );
            });
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('withdrawals.index')->with('success', 'Data Penarikan berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Withdrawal  $withdrawal
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $withdrawal = Withdrawal::with(['member', 'nasabah'])->findOrFail($id);
        return view('withdrawals.show', ['withdrawal' => $withdrawal]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Withdrawal  $withdrawal
     * @return \Illuminate\Http\Response
     */
    public function destroy(Withdrawal $withdrawal)
    {
        try {
            DB::transaction(function () use ($withdrawal) {
                // 1. Revert Saving Balance
                if ($withdrawal->anggota_id) {
                     $saving = Saving::where('anggota_id', $withdrawal->anggota_id)->first();
                } else {
                     $saving = Saving::where('nasabah_id', $withdrawal->nasabah_id)->first();
                }

                if ($saving) {
                    $saving->saldo += $withdrawal->jumlah; // Add back the amount
                    $saving->save();
                }

                // 2. Add Reversal History
                $history = new SavingHistory;
                $history->anggota_id = $withdrawal->anggota_id;
                $history->nasabah_id = $withdrawal->nasabah_id;
                $history->tanggal = now()->toDateString();
                $history->keterangan = 'koreksi penarikan (hapus)';
                $history->kredit = $withdrawal->jumlah; // Credit to increase balance
                $history->saldo = $saving ? $saving->saldo : 0;
                $history->save();

                // 3. Delete Journal Entry
                $journal = JournalEntry::where('ref_type', Withdrawal::class)
                                       ->where('ref_id', $withdrawal->id)
                                       ->first();
                if ($journal) {
                    $journal->items()->delete();
                    $journal->delete();
                }

                // 4. Delete Withdrawal Record
                $withdrawal->delete();
            });

            return redirect()->route('withdrawals.index')->with('success', 'Data penarikan berhasil dihapus (dibatalkan).');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus penarikan: ' . $e->getMessage());
        }
    }

    public function jsonWithdrawals(Request $request)
    {
        $withdrawals = Withdrawal::with(['member', 'nasabah'])->select('penarikan.*');

        if ($request->filled('from_date') && $request->filled('to_date')) {
            $withdrawals->whereBetween('created_at', [$request->from_date . ' 00:00:00', $request->to_date . ' 23:59:59']);
        }

        if ($request->filled('type')) {
            if ($request->type == 'anggota') {
                $withdrawals->whereNotNull('anggota_id');
                if ($request->filled('anggota_id')) {
                    $withdrawals->where('anggota_id', $request->anggota_id);
                }
            } elseif ($request->type == 'nasabah') {
                $withdrawals->whereNotNull('nasabah_id');
                if ($request->filled('nasabah_id')) {
                    $withdrawals->where('nasabah_id', $request->nasabah_id);
                }
            }
        }

        return DataTables::of($withdrawals)
            ->addIndexColumn()
            ->addColumn('action', function($withdrawal) {
                return view('withdrawals.datatables.action', compact('withdrawal'))->render();
            })
            ->addColumn('anggota', function($withdrawal) {
                if ($withdrawal->member) {
                    return $withdrawal->member->nama . ' (Anggota)';
                } elseif ($withdrawal->nasabah) {
                    return $withdrawal->nasabah->nama . ' (Nasabah)';
                }
                return '-';
            })
            ->addColumn('tanggal', function($withdrawal) {
                return $withdrawal->created_at->format('d/m/Y H:i');
            })
            ->orderColumn('tanggal', function ($query, $order) {
                $query->orderBy('created_at', $order);
            })
            ->editColumn('jumlah', function($withdrawal) {
                return format_rupiah($withdrawal->jumlah);
            })
            ->editColumn('keterangan', function($withdrawal) {
                return $withdrawal->keterangan ?? '-';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
