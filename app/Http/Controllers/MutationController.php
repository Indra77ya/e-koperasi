<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Nasabah;
use Illuminate\Http\Request;
use App\Models\SavingHistory;
use App\Models\Saving;
use Illuminate\Support\Facades\Log;

class MutationController extends Controller
{
    public function index()
    {
        $members = Member::orderBy('nama', 'asc')->get();
        $nasabahs = Nasabah::orderBy('nama', 'asc')->get();

        return view('mutations.index', compact('members', 'nasabahs'));
    }

    public function check_mutations(Request $request)
    {
        try {
            $type = $request->query('type');
            $id = $request->query('id');
            $fromDate = $request->query('from_date');
            $toDate = $request->query('to_date');

            if (!$type || !$id) {
                return response()->json(['error' => 'Tipe dan ID harus dipilih.'], 400);
            }

            $queryHistory = SavingHistory::query();
            $queryBalance = Saving::query();

            if ($type == 'anggota') {
                $queryHistory->where('anggota_id', $id);
                $queryBalance->where('anggota_id', $id);
            } else {
                $queryHistory->where('nasabah_id', $id);
                $queryBalance->where('nasabah_id', $id);
            }

            if ($fromDate && $toDate) {
                $queryHistory->whereBetween('tanggal', [$fromDate, $toDate]);
            }

            $savings_history = $queryHistory->orderBy('id', 'asc')->get();
            $total_credit = $savings_history->sum('kredit');
            $total_debet = $savings_history->sum('debet');
            $balance = $queryBalance->first();

            // Prevent error if no balance record exists yet
            $currentBalance = $balance ? $balance->saldo : 0;

            $view = view('mutations.check_mutations', compact('savings_history', 'currentBalance', 'total_credit', 'total_debet'))->render();

            return response()->json([
                'html'=> $view,
            ]);
        } catch (\Exception $e) {
            Log::error($e);
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
