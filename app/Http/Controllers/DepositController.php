<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Saving;
use App\Models\Deposit;
use App\Models\Setting;
use App\Models\JournalEntry;
use App\Models\SavingHistory;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\StoreDeposit;
use App\Services\AccountingService;

class DepositController extends Controller
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
        return view('deposits.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $members = Member::orderBy('nama', 'asc')->get();

        return view('deposits.create', ['members' => $members]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreDeposit $request)
    {
        DB::transaction(function () use ($request) {
            // Insert into deposit
            $deposit = new Deposit;
            $deposit->anggota_id = $request->anggota;
            $deposit->jumlah = $request->jumlah;
            $deposit->keterangan = $request->keterangan;
            $deposit->save();

            // Create or Update Saving
            $balance = Saving::firstOrNew(['anggota_id' => $request->anggota]);
            $balance->saldo = ($balance->saldo ?? 0) + $request->jumlah;
            $balance->save();

            // Insert into history saving
            $saving_history = new SavingHistory;
            $saving_history->anggota_id = $request->anggota;
            $saving_history->tanggal = \Carbon\Carbon::today()->toDateString();
            $saving_history->keterangan = 'setoran';
            $saving_history->kredit = $request->jumlah;
            $saving_history->saldo = $balance->saldo;
            $saving_history->save();

            // Accounting Journal
            $coaCash = Setting::get('coa_cash', '1101');
            $coaSavings = Setting::get('coa_savings', '2101');

            $journalItems = [
                ['code' => $coaCash, 'debit' => $request->jumlah, 'credit' => 0], // Debit Kas
                ['code' => $coaSavings, 'debit' => 0, 'credit' => $request->jumlah], // Credit Simpanan
            ];

            $this->accountingService->createJournal(
                now(),
                'DEP-' . $deposit->id,
                'Setoran Simpanan ' . $deposit->member->nama,
                $journalItems,
                $deposit
            );

        });

        return redirect()->route('deposits.index')->with('success', 'Data Setoran berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Deposit  $deposit
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $deposit = Deposit::with('member')->findOrFail($id);
        return view('deposits.show', ['deposit' => $deposit]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Deposit  $deposit
     * @return \Illuminate\Http\Response
     */
    public function destroy(Deposit $deposit)
    {
        try {
            DB::transaction(function () use ($deposit) {
                // 1. Revert Saving Balance
                $saving = Saving::where('anggota_id', $deposit->anggota_id)->first();
                if ($saving) {
                    $saving->saldo -= $deposit->jumlah;
                    $saving->save();
                }

                // 2. Add Reversal History
                $saving_history = new SavingHistory;
                $saving_history->anggota_id = $deposit->anggota_id;
                $saving_history->tanggal = \Carbon\Carbon::today()->toDateString();
                $saving_history->keterangan = 'koreksi setoran (hapus)';
                $saving_history->debit = $deposit->jumlah; // Debit to reduce balance
                $saving_history->saldo = $saving ? $saving->saldo : 0;
                $saving_history->save();

                // 3. Delete Journal Entry
                $journal = JournalEntry::where('ref_type', Deposit::class)
                                       ->where('ref_id', $deposit->id)
                                       ->first();
                if ($journal) {
                    $journal->items()->delete();
                    $journal->delete();
                }

                // 4. Delete Deposit Record
                $deposit->delete();
            });

            return redirect()->route('deposits.index')->with('success', 'Data setoran berhasil dihapus (dibatalkan).');

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Gagal menghapus setoran: ' . $e->getMessage());
        }
    }

    public function jsonDeposits()
    {
        $deposits = Deposit::with('member')->select('setoran.*');

        return DataTables::of($deposits)
            ->addIndexColumn()
            ->addColumn('action', function($deposit) {
                return view('deposits.datatables.action', compact('deposit'))->render();
            })
            ->addColumn('anggota', function($deposit) {
                return $deposit->member->nama;
            })
            ->addColumn('tanggal', function($deposit) {
                return $deposit->created_at->format('d/m/Y H:i');
            })
            ->orderColumn('tanggal', function ($query, $order) {
                $query->orderBy('created_at', $order);
            })
            ->editColumn('jumlah', function($deposit) {
                return 'Rp ' . number_format($deposit->jumlah, 0, ',', '.');
            })
            ->editColumn('keterangan', function($deposit) {
                return $deposit->keterangan ?? '-';
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
