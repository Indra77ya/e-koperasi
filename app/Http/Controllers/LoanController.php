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
use App\Services\AccountingService;

class LoanController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $loans = Loan::with(['member', 'nasabah'])->select('pinjaman.*');
            return DataTables::of($loans)
                ->editColumn('jumlah_pinjaman', function ($loan) {
                    return 'Rp ' . number_format($loan->jumlah_pinjaman, 0, ',', '.');
                })
                ->editColumn('suku_bunga', function ($loan) {
                    if ($loan->satuan_bunga == 'hari') {
                         $unit = 'Hari';
                    } elseif ($loan->satuan_bunga == 'bulan') {
                         $unit = 'Bulan';
                    } else {
                         $unit = 'Tahun';
                    }
                    return $loan->suku_bunga . '% / ' . $unit . ' (' . ucfirst($loan->jenis_bunga) . ')';
                })
                ->editColumn('tenor', function ($loan) {
                    if ($loan->tempo_angsuran == 'harian') {
                        return $loan->tenor . ' Hari';
                    } elseif ($loan->tempo_angsuran == 'mingguan') {
                        return $loan->tenor . ' Minggu';
                    }
                    return $loan->tenor . ' Bulan';
                })
                ->editColumn('status', function ($loan) {
                    if ($loan->status == 'diajukan') {
                        return '<span class="badge badge-warning">Diajukan</span>';
                    } elseif ($loan->status == 'disetujui') {
                        return '<span class="badge badge-info">Disetujui</span>';
                    } elseif ($loan->status == 'berjalan') {
                        return '<span class="badge badge-primary">Berjalan</span>';
                    } elseif ($loan->status == 'lunas') {
                        return '<span class="badge badge-success">Lunas</span>';
                    } elseif ($loan->status == 'macet') {
                        return '<span class="badge badge-danger">Macet</span>';
                    } elseif ($loan->status == 'ditolak') {
                        return '<span class="badge badge-secondary">Ditolak</span>';
                    }
                    return '<span class="badge badge-secondary">' . $loan->status . '</span>';
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
                ->rawColumns(['status', 'action'])
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
        $rate = $request->rate; // percentage
        $type = $request->type; // flat, efektif, anuitas
        $unit = $request->unit ?? 'tahun'; // tahun, bulan
        $tempo = $request->tempo ?? 'bulanan'; // harian, mingguan, bulanan

        $schedule = $this->generateSchedule($amount, $tenor, $rate, $type, $unit, $tempo);

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
            'satuan_bunga' => 'required|in:tahun,bulan,hari',
            'tempo_angsuran' => 'required|in:harian,mingguan,bulanan',
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
            'satuan_bunga' => $request->satuan_bunga,
            'tempo_angsuran' => $request->tempo_angsuran,
            'jenis_bunga' => $request->jenis_bunga,
            'biaya_admin' => $request->jumlah_pinjaman * (($request->biaya_admin ?? 0) / 100),
            'denda_keterlambatan' => $request->denda_keterlambatan ?? 0,
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

    public function reject($id)
    {
        $loan = Loan::findOrFail($id);
        if ($loan->status == 'diajukan') {
            $loan->update([
                'status' => 'ditolak',
            ]);
        }
        return redirect()->back()->with('success', 'Pinjaman ditolak.');
    }

    public function disburse(Request $request, $id)
    {
        $loan = Loan::findOrFail($id);
        if ($loan->status == 'disetujui') {
            DB::transaction(function () use ($loan) {
                $loan->update(['status' => 'berjalan']);

                $schedule = $this->generateSchedule($loan->jumlah_pinjaman, $loan->tenor, $loan->suku_bunga, $loan->jenis_bunga, $loan->satuan_bunga, $loan->tempo_angsuran);

                foreach ($schedule as $inst) {
                    $dueDate = now();
                    if ($loan->tempo_angsuran == 'harian') {
                        $dueDate->addDays($inst['month']);
                    } elseif ($loan->tempo_angsuran == 'mingguan') {
                        $dueDate->addWeeks($inst['month']);
                    } else {
                        // Default bulanan
                        $dueDate->addMonths($inst['month']);
                    }

                    LoanInstallment::create([
                        'pinjaman_id' => $loan->id,
                        'angsuran_ke' => $inst['month'],
                        'tanggal_jatuh_tempo' => $dueDate,
                        'total_angsuran' => $inst['total'],
                        'pokok' => $inst['principal'],
                        'bunga' => $inst['interest'],
                        'sisa_pinjaman' => $inst['balance'],
                        'status' => 'belum_lunas',
                    ]);
                }

                // Create Journal: Dr Piutang Pinjaman, Cr Kas, Cr Pendapatan Admin
                // Codes based on Seeder: Piutang Pinjaman (1103), Kas (1101), Pendapatan Admin (4102)
                $adminFee = $loan->biaya_admin ?? 0;
                $disbursedAmount = $loan->jumlah_pinjaman - $adminFee;

                $journalItems = [
                    ['code' => '1103', 'debit' => $loan->jumlah_pinjaman, 'credit' => 0], // Piutang
                    ['code' => '1101', 'debit' => 0, 'credit' => $disbursedAmount], // Kas
                ];

                if ($adminFee > 0) {
                    $journalItems[] = ['code' => '4102', 'debit' => 0, 'credit' => $adminFee]; // Pendapatan Admin
                }

                $this->accountingService->createJournal(
                    now(),
                    $loan->kode_pinjaman,
                    'Pencairan Pinjaman ' . ($loan->member ? $loan->member->nama : $loan->nasabah->nama),
                    $journalItems,
                    $loan
                );
            });
        }
        return redirect()->back()->with('success', 'Dana dicairkan, jadwal angsuran dibuat.');
    }

    public function markBadDebt($id)
    {
        $loan = Loan::findOrFail($id);
        if ($loan->status == 'berjalan') {
            $loan->update(['status' => 'macet']);
        }
        return redirect()->back()->with('success', 'Pinjaman ditandai sebagai macet.');
    }

    public function addPenalty(Request $request, $id)
    {
        $request->validate([
            'denda' => 'required|numeric|min:0'
        ]);

        $installment = LoanInstallment::findOrFail($id);
        $installment->update(['denda' => $request->denda]);

        return redirect()->back()->with('success', 'Denda berhasil ditambahkan.');
    }

    public function payInstallment(Request $request, $id)
    {
        $installment = LoanInstallment::findOrFail($id);

        if ($installment->status == 'belum_lunas') {
            $request->validate([
                'tanggal_bayar' => 'required|date',
                'metode_pembayaran' => 'required',
                'denda' => 'nullable|numeric|min:0',
                'keterangan_pembayaran' => 'nullable|string',
            ]);

            DB::transaction(function () use ($installment, $request) {
                $installment->update([
                    'status' => 'lunas',
                    'tanggal_bayar' => $request->tanggal_bayar,
                    'metode_pembayaran' => $request->metode_pembayaran,
                    'keterangan_pembayaran' => $request->keterangan_pembayaran,
                    'denda' => $request->denda ?? $installment->denda, // Update denda if provided
                ]);

                // Check if all installments are paid
                $loan = $installment->loan;
                $remaining = $loan->installments()->where('status', 'belum_lunas')->count();

                if ($remaining == 0) {
                    $loan->update(['status' => 'lunas']);
                }

                // Create Journal: Dr Kas, Cr Piutang (Pokok), Cr Pendapatan Bunga (Bunga), Cr Pendapatan Denda (Denda)
                $denda = $installment->denda ?? 0;
                $totalMasuk = $installment->pokok + $installment->bunga + $denda;

                $items = [
                    ['code' => '1101', 'debit' => $totalMasuk, 'credit' => 0], // Kas
                    ['code' => '1103', 'debit' => 0, 'credit' => $installment->pokok], // Piutang
                    ['code' => '4101', 'debit' => 0, 'credit' => $installment->bunga], // Pendapatan Bunga
                ];

                if ($denda > 0) {
                    $items[] = ['code' => '4103', 'debit' => 0, 'credit' => $denda]; // Pendapatan Denda
                }

                $this->accountingService->createJournal(
                    $request->tanggal_bayar,
                    $loan->kode_pinjaman . '-' . $installment->angsuran_ke,
                    'Pembayaran Angsuran ' . ($loan->member ? $loan->member->nama : $loan->nasabah->nama),
                    $items,
                    $installment
                );
            });

            return redirect()->back()->with('success', 'Angsuran berhasil dibayar.');
        }

        return redirect()->back()->with('error', 'Angsuran sudah lunas atau tidak valid.');
    }

    public function printReceipt($id)
    {
        $installment = LoanInstallment::with(['loan.member', 'loan.nasabah'])->findOrFail($id);
        if ($installment->status != 'lunas') {
            return redirect()->back()->with('error', 'Angsuran belum lunas.');
        }
        return view('loans.installments.print', compact('installment'));
    }

    private function generateSchedule($amount, $tenor, $rate, $type, $unit = 'tahun', $tempo = 'bulanan')
    {
        $schedule = [];
        $balance = $amount;
        $ratePercent = $rate / 100;

        // 1. Normalize to Yearly Rate
        if ($unit == 'hari') {
            $yearlyRate = $ratePercent * 365;
        } elseif ($unit == 'bulan') {
            $yearlyRate = $ratePercent * 12;
        } else { // tahun
            $yearlyRate = $ratePercent;
        }

        // 2. Calculate Rate Per Period
        if ($tempo == 'harian') {
            $ratePerPeriod = $yearlyRate / 365;
        } elseif ($tempo == 'mingguan') {
            $ratePerPeriod = $yearlyRate / 52;
        } else { // bulanan
            $ratePerPeriod = $yearlyRate / 12;
        }

        if ($type == 'flat') {
            $principal = $amount / $tenor;
            $interest = $amount * $ratePerPeriod;
            $total = $principal + $interest;

            for ($i = 1; $i <= $tenor; $i++) {
                $balance -= $principal;
                if ($i == $tenor && $balance != 0) {
                     // Adjust last? For now simple flat.
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
            $principal = $amount / $tenor;

            for ($i = 1; $i <= $tenor; $i++) {
                $interest = $balance * $ratePerPeriod;
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
            if ($ratePerPeriod > 0) {
                $pmt = ($amount * $ratePerPeriod) / (1 - pow(1 + $ratePerPeriod, -$tenor));
            } else {
                $pmt = $amount / $tenor;
            }

            for ($i = 1; $i <= $tenor; $i++) {
                $interest = $balance * $ratePerPeriod;
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
