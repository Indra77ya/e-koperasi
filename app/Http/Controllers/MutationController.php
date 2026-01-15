<?php

namespace App\Http\Controllers;

use App\Models\Member;
use App\Models\Nasabah;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use App\Models\SavingHistory;
use App\Models\Saving;

class MutationController extends Controller
{
    public function index()
    {
        $members = Member::orderBy('nama', 'asc')->get();
        $nasabahs = Nasabah::orderBy('nama', 'asc')->get();

        return view('mutations.index', compact('members', 'nasabahs'));
    }

    public function check_mutations()
    {
        $type = Input::get('type');
        $id = Input::get('id');

        $queryHistory = SavingHistory::query();
        $queryBalance = Saving::query();

        if ($type == 'anggota') {
            $queryHistory->where('anggota_id', $id);
            $queryBalance->where('anggota_id', $id);
        } else {
            $queryHistory->where('nasabah_id', $id);
            $queryBalance->where('nasabah_id', $id);
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
    }
}
