<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use App\Models\NasabahLoan;
use App\Models\NasabahLoanInstallment;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use DB;

class NasabahLoanController extends Controller
{
    public function index()
    {
        return view('nasabah_loans.index');
    }

    public function jsonLoans()
    {
        $loans = NasabahLoan::with('nasabah')->orderBy('created_at', 'desc')->get();
        return DataTables::of($loans)
            ->addIndexColumn()
            ->addColumn('nasabah_name', function($loan) {
                return $loan->nasabah->nama ?? '-';
            })
            ->addColumn('action', function($loan) {
                $btn = '<a href="'.route('nasabah_loans.show', $loan->id).'" class="btn btn-sm btn-info">Detail</a>';
                return $btn;
            })
            ->editColumn('amount', function($loan) {
                return number_format($loan->amount, 2);
            })
            ->editColumn('status', function($loan) {
                $colors = [
                    'pending' => 'warning',
                    'approved' => 'primary',
                    'rejected' => 'danger',
                    'disbursed' => 'success',
                    'active' => 'success',
                    'closed' => 'secondary',
                    'cancelled' => 'gray',
                    'paid' => 'success',
                    'overdue' => 'danger'
                ];
                $color = $colors[$loan->status] ?? 'secondary';
                return '<span class="tag tag-' . $color . '">' . ucfirst($loan->status) . '</span>';
            })
            ->rawColumns(['action', 'status'])
            ->toJson();
    }

    public function create()
    {
        $nasabahs = Nasabah::all();
        return view('nasabah_loans.create', compact('nasabahs'));
    }

    public function calculateSchedule($amount, $rate, $tenor, $type)
    {
        $schedule = [];
        $balance = $amount;
        $monthlyRate = ($rate / 100) / 12; // Assuming rate is yearly percentage

        $totalPrincipalPaid = 0;

        for ($i = 1; $i <= $tenor; $i++) {
            $installment = [];
            $installment['number'] = $i;

            // Standard calculations
            if ($type == 'flat') {
                $principal = $amount / $tenor;
                $interest = ($amount * ($rate/100)) / 12;
                $total = $principal + $interest;
            } elseif ($type == 'effective') {
                $principal = $amount / $tenor;
                $interest = $balance * $monthlyRate;
                $total = $principal + $interest;
            } elseif ($type == 'annuity') {
                if ($monthlyRate > 0) {
                    $total = $amount * ($monthlyRate * pow(1 + $monthlyRate, $tenor)) / (pow(1 + $monthlyRate, $tenor) - 1);
                } else {
                    $total = $amount / $tenor;
                }
                $interest = $balance * $monthlyRate;
                $principal = $total - $interest;
            }

            // Adjust last installment for rounding errors
            if ($i == $tenor) {
                $principal = $amount - $totalPrincipalPaid;
                if ($type == 'flat' || $type == 'effective') {
                    $total = $principal + $interest;
                } elseif ($type == 'annuity') {
                    // For annuity, if we adjust principal, total changes too
                    $total = $principal + $interest;
                }
                $balance = 0;
            } else {
                $balance -= $principal;
                if ($balance < 0) $balance = 0;
            }

            $installment['principal'] = $principal;
            $installment['interest'] = $interest;
            $installment['total'] = $total;
            $installment['balance'] = $balance;

            $schedule[] = $installment;

            $totalPrincipalPaid += $principal;
        }

        return $schedule;
    }

    public function simulate(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|min:0',
            'interest_rate' => 'required|numeric|min:0',
            'tenor' => 'required|integer|min:1',
            'interest_type' => 'required|in:flat,effective,annuity',
        ]);

        $schedule = $this->calculateSchedule(
            $request->amount,
            $request->interest_rate,
            $request->tenor,
            $request->interest_type
        );

        return response()->json([
            'schedule' => $schedule
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nasabah_id' => 'required|exists:nasabahs,id',
            'amount' => 'required|numeric|min:1000',
            'loan_date' => 'required|date',
            'loan_type' => 'required|in:productive,consumptive',
            'interest_type' => 'required|in:flat,effective,annuity',
            'interest_rate' => 'required|numeric|min:0',
            'tenor' => 'required|integer|min:1',
            'disbursement_method' => 'required|in:cash,transfer',
        ]);

        // Calculate due date based on loan date + tenor months
        $loanDate = Carbon::parse($request->loan_date);
        $dueDate = $loanDate->copy()->addMonths($request->tenor);

        NasabahLoan::create([
            'nasabah_id' => $request->nasabah_id,
            'amount' => $request->amount,
            'loan_date' => $request->loan_date,
            'due_date' => $dueDate,
            'status' => 'pending',
            'loan_type' => $request->loan_type,
            'interest_type' => $request->interest_type,
            'interest_rate' => $request->interest_rate,
            'tenor' => $request->tenor,
            'admin_fee' => $request->admin_fee ?? 0,
            'disbursement_method' => $request->disbursement_method,
            'notes' => $request->notes,
        ]);

        return redirect()->route('nasabah_loans.index')->with('success', 'Pengajuan pinjaman berhasil dibuat.');
    }

    public function show($id)
    {
        $loan = NasabahLoan::with(['nasabah', 'installments', 'approver'])->findOrFail($id);
        return view('nasabah_loans.show', compact('loan'));
    }

    public function approve(Request $request, $id)
    {
        $loan = NasabahLoan::findOrFail($id);
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Hanya pinjaman pending yang bisa disetujui.');
        }

        $loan->status = 'approved';
        $loan->approved_by = Auth::id();
        $loan->approved_at = now();
        $loan->save();

        return back()->with('success', 'Pinjaman disetujui.');
    }

    public function reject(Request $request, $id)
    {
        $loan = NasabahLoan::findOrFail($id);
        if ($loan->status !== 'pending') {
            return back()->with('error', 'Hanya pinjaman pending yang bisa ditolak.');
        }

        $loan->status = 'rejected';
        $loan->rejected_at = now();
        $loan->rejection_reason = $request->input('reason');
        $loan->save();

        return back()->with('success', 'Pinjaman ditolak.');
    }

    public function disburse(Request $request, $id)
    {
        $loan = NasabahLoan::findOrFail($id);
        if ($loan->status !== 'approved') {
            return back()->with('error', 'Hanya pinjaman disetujui yang bisa dicairkan.');
        }

        DB::transaction(function () use ($loan) {
            $loan->status = 'disbursed'; // Or 'active'
            $loan->disbursed_at = now();
            $loan->save();

            // Generate Installments
            $schedule = $this->calculateSchedule(
                $loan->amount,
                $loan->interest_rate,
                $loan->tenor,
                $loan->interest_type
            );

            $startDate = Carbon::parse($loan->disbursed_at);

            foreach ($schedule as $item) {
                NasabahLoanInstallment::create([
                    'nasabah_loan_id' => $loan->id,
                    'installment_number' => $item['number'],
                    'due_date' => $startDate->copy()->addMonths($item['number']),
                    'principal_amount' => $item['principal'],
                    'interest_amount' => $item['interest'],
                    'total_amount' => $item['total'],
                    'remaining_balance' => $item['balance'],
                    'status' => 'unpaid'
                ]);
            }
        });

        return back()->with('success', 'Dana pinjaman dicairkan dan jadwal angsuran dibuat.');
    }

    public function payInstallment(Request $request, $id)
    {
        $installment = NasabahLoanInstallment::with('loan')->findOrFail($id);

        if ($installment->status == 'paid') {
            return back()->with('error', 'Angsuran ini sudah lunas.');
        }

        $request->validate([
            'payment_date' => 'required|date',
            'amount_paid' => 'required|numeric|min:' . floor($installment->total_amount),
            'penalty' => 'nullable|numeric|min:0'
        ]);

        DB::transaction(function () use ($installment, $request) {
            $installment->status = 'paid';
            $installment->paid_at = $request->payment_date;
            $installment->amount_paid = $request->amount_paid;
            $installment->penalty_amount = $request->penalty ?? 0;
            $installment->notes = $request->notes;
            $installment->save();

            // Check if all installments are paid
            $unpaidCount = NasabahLoanInstallment::where('nasabah_loan_id', $installment->nasabah_loan_id)
                ->where('status', '!=', 'paid')
                ->count();

            if ($unpaidCount == 0) {
                $loan = $installment->loan;
                $loan->status = 'paid';
                $loan->save();
            }
        });

        return back()->with('success', 'Pembayaran angsuran berhasil disimpan.');
    }
}
