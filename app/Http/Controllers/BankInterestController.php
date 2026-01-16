<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Nasabah;
use App\Models\Saving;
use App\Models\BankInterest;
use App\Models\Setting;
use Illuminate\Http\Request;
use App\Models\SavingHistory;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\DB;
use App\Services\AccountingService;

class BankInterestController extends Controller
{
    protected $accountingService;

    public function __construct(AccountingService $accountingService)
    {
        $this->accountingService = $accountingService;
    }

    public function index()
    {
        return view('bankinterests.index');
    }

    public function calculate(Request $request, $id)
    {
        $type = $request->query('type', 'anggota');
        $data = null;

        if ($type == 'nasabah') {
            $data = Nasabah::with('balance')->findOrFail($id);
        } else {
            $data = Member::with('balance')->findOrFail($id);
        }

        return view('bankinterests.calculate', compact('data', 'type'));
    }

    private function calculateInterestAmount($id, $type, $month, $year)
    {
        $interest_rate = Setting::get('savings_interest_rate', 0);

        // Calculate Lowest Balance
        // 1. Get balance before the month starts
        $start_date = "$year-$month-01";
        $end_date = \Carbon\Carbon::parse($start_date)->endOfMonth()->toDateString();

        $historyQuery = SavingHistory::where('tanggal', '<', $start_date)->orderBy('tanggal', 'desc')->orderBy('id', 'desc');
        if ($type == 'nasabah') {
            $historyQuery->where('nasabah_id', $id);
        } else {
            $historyQuery->where('anggota_id', $id);
        }
        $last_history = $historyQuery->first();
        $start_balance = $last_history ? $last_history->saldo : 0;

        // 2. Get min balance from transactions during the month
        $monthQuery = SavingHistory::whereBetween('tanggal', [$start_date, $end_date]);
        if ($type == 'nasabah') {
            $monthQuery->where('nasabah_id', $id);
        } else {
            $monthQuery->where('anggota_id', $id);
        }
        $min_during_month = $monthQuery->min('saldo');

        $lowest_balance = is_null($min_during_month) ? $start_balance : min($start_balance, $min_during_month);

        $days_in_month = \Carbon\Carbon::createFromDate($year, $month)->daysInMonth;
        $day_of_year = \Carbon\Carbon::createFromDate($year)->format('L') + 365;

        $calculate_interest = 0;
        if ($lowest_balance > 0 && $interest_rate > 0) {
             $calculate_interest = ($lowest_balance * ($interest_rate / 100) * $days_in_month) / $day_of_year;
        }

        return [
            'lowest_balance' => $lowest_balance,
            'interest_amount' => $calculate_interest,
            'interest_rate' => $interest_rate
        ];
    }

    public function check_interest(Request $request)
    {
        $id = $request->id;
        $type = $request->type;
        $month = $request->month;
        $year = $request->year;
        $periode = month_id($month) . ' ' . $year;

        // validation of months and years
        $time_now = \Carbon\Carbon::createFromDate(date("Y"), date("m"));
        $time_input = \Carbon\Carbon::createFromDate($year, $month);
        $greater_than_periode = $time_input->greaterThan($time_now);

        // check whether the savings interest has been added
        $query = BankInterest::where('bulan', $month)->where('tahun', $year);
        if ($type == 'nasabah') {
            $query->where('nasabah_id', $id);
        } else {
            $query->where('anggota_id', $id);
        }
        $count_interest = $query->count();

        // Calculate Interest
        $calc = $this->calculateInterestAmount($id, $type, $month, $year);
        $lowest_balance = $calc['lowest_balance'];
        $interest_rate = $calc['interest_rate'];
        $calculate_interest = $calc['interest_amount'];

        $format_calculate_interest = number_format($calculate_interest, 0, '', '');

        $view = view('bankinterests.check_interest', compact('id', 'type', 'interest_rate', 'format_calculate_interest', 'lowest_balance', 'periode', 'count_interest', 'greater_than_periode', 'month', 'year'))->render();

        return response()->json([
            'html'=> $view,
        ]);
    }

    public function store(Request $request)
    {
        $status = false;
        $url = '';

        try {
            DB::beginTransaction();

            $type = $request->type;
            $id = $request->id;
            $month = $request->month;
            $year = $request->year;

            // Recalculate interest server-side for security
            $calc = $this->calculateInterestAmount($id, $type, $month, $year);
            $lowest_balance = $calc['lowest_balance'];
            $interest_rate = $calc['interest_rate'];
            $calculate_interest = $calc['interest_amount']; // This is float/double

            // Insert into bank interest
            $interest = new BankInterest;
            if ($type == 'nasabah') {
                $interest->nasabah_id = $id;
            } else {
                $interest->anggota_id = $id;
            }
            $interest->bulan = $month;
            $interest->tahun = $year;
            $interest->saldo_terendah = $lowest_balance;
            $interest->suku_bunga = $interest_rate;
            $interest->nominal_bunga = $calculate_interest;
            $interest->save();

            // Add to balance
            if ($type == 'nasabah') {
                $saving = Saving::firstOrNew(['nasabah_id' => $id]);
            } else {
                $saving = Saving::firstOrNew(['anggota_id' => $id]);
            }

            $saving->saldo = ($saving->saldo ?? 0) + $calculate_interest;
            $saving->save();

            // Insert into history saving
            $saving_history = new SavingHistory;
            if ($type == 'nasabah') {
                $saving_history->nasabah_id = $id;
            } else {
                $saving_history->anggota_id = $id;
            }
            $saving_history->tanggal = \Carbon\Carbon::today()->toDateString();
            $saving_history->keterangan = 'bunga tabungan';
            $saving_history->kredit = $calculate_interest;
            $saving_history->saldo = $saving->saldo;
            $saving_history->save();

            // Accounting Journal
            $coaSavings = Setting::get('coa_savings', '2101');
            $coaInterestExpense = Setting::get('coa_interest_expense'); // Beban Bunga

            if ($coaInterestExpense && $calculate_interest > 0) {
                $journalItems = [
                    ['code' => $coaInterestExpense, 'debit' => $calculate_interest, 'credit' => 0],
                    ['code' => $coaSavings, 'debit' => 0, 'credit' => $calculate_interest],
                ];

                $name = ($type == 'nasabah') ? Nasabah::find($id)->nama : Member::find($id)->nama;

                $this->accountingService->createJournal(
                    now(),
                    'INT-' . $interest->id,
                    'Bunga Tabungan ' . $name . ' ' . $interest->bulan . '/' . $interest->tahun,
                    $journalItems,
                    $interest
                );
            }

            DB::commit();
            $status = true;
            $url = url("/bankinterests/calculate/" . $id . "?type=" . $type);

        } catch (\Exception $e) {
            DB::rollBack();
            // Log error?
        }

        return response()->json([
            'status' => $status,
            'url' => $url,
        ]);
    }

    public function jsonList(Request $request)
    {
        $type = $request->query('type', 'anggota');

        if ($type == 'nasabah') {
            $query = Nasabah::with('balance')->select('nasabahs.*');
        } else {
            $query = Member::with('balance')->select('anggota.*');
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->addColumn('action', function($row) use ($type) {
                return view('bankinterests.datatables.action', ['data' => $row, 'type' => $type])->render();
            })
            ->addColumn('saldo', function($row) {
                return $row->balance ? format_rupiah($row->balance->saldo) : '0';
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    public function jsonHistoryInterests(Request $request, $id)
    {
        $type = $request->query('type', 'anggota');
        $query = BankInterest::query();

        if ($type == 'nasabah') {
            $query->where('nasabah_id', $id);
        } else {
            $query->where('anggota_id', $id);
        }

        $interests = $query->orderBy('id', 'desc');

        return DataTables::of($interests)
            ->addIndexColumn()
            ->addColumn('periode', function($interest) {
                return month_id($interest->bulan) . ' ' . $interest->tahun;
            })
            ->orderColumn('periode', function ($query, $order) {
                $query->orderBy('tahun', $order)->orderBy('bulan', $order);
            })
            ->editColumn('nominal_bunga', function($interest) {
                return format_rupiah($interest->nominal_bunga);
            })
            ->editColumn('saldo_terendah', function($interest) {
                return format_rupiah($interest->saldo_terendah);
            })
            ->toJson();
    }
}
