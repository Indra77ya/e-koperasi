<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class NasabahController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('nasabah.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('nasabah.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|unique:nasabah,nik',
            'nama' => 'required',
            'status' => 'required',
            'file_ktp' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'file_jaminan' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $data = $request->all();

        if ($request->filled('tanggal_lahir')) {
            $data['tanggal_lahir'] = Carbon::createFromFormat('d/m/Y', $request->tanggal_lahir)->format('Y-m-d');
        }

        if ($request->hasFile('file_ktp')) {
            $data['file_ktp'] = $request->file('file_ktp')->store('nasabah/ktp', 'public');
        }

        if ($request->hasFile('file_jaminan')) {
            $data['file_jaminan'] = $request->file('file_jaminan')->store('nasabah/jaminan', 'public');
        }

        Nasabah::create($data);

        return redirect()->route('nasabah.index')->with('success', 'Data Nasabah berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Nasabah  $nasabah
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $nasabah = Nasabah::findOrFail($id);
        return view('nasabah.show', compact('nasabah'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Nasabah  $nasabah
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $nasabah = Nasabah::findOrFail($id);
        return view('nasabah.edit', compact('nasabah'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Nasabah  $nasabah
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'nik' => 'required|unique:nasabah,nik,' . $id,
            'nama' => 'required',
            'status' => 'required',
            'file_ktp' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
            'file_jaminan' => 'nullable|file|mimes:jpeg,png,jpg,pdf|max:2048',
        ]);

        $nasabah = Nasabah::findOrFail($id);
        $data = $request->all();

        if ($request->filled('tanggal_lahir')) {
            $data['tanggal_lahir'] = Carbon::createFromFormat('d/m/Y', $request->tanggal_lahir)->format('Y-m-d');
        }

        if ($request->hasFile('file_ktp')) {
            if ($nasabah->file_ktp) {
                Storage::disk('public')->delete($nasabah->file_ktp);
            }
            $data['file_ktp'] = $request->file('file_ktp')->store('nasabah/ktp', 'public');
        }

        if ($request->hasFile('file_jaminan')) {
            if ($nasabah->file_jaminan) {
                Storage::disk('public')->delete($nasabah->file_jaminan);
            }
            $data['file_jaminan'] = $request->file('file_jaminan')->store('nasabah/jaminan', 'public');
        }

        $nasabah->update($data);

        return redirect()->route('nasabah.index')->with('success', 'Data Nasabah berhasil diubah.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Nasabah  $nasabah
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $nasabah = Nasabah::findOrFail($id);
        if ($nasabah->file_ktp) {
            Storage::disk('public')->delete($nasabah->file_ktp);
        }
        if ($nasabah->file_jaminan) {
            Storage::disk('public')->delete($nasabah->file_jaminan);
        }
        $nasabah->delete();

        return redirect()->route('nasabah.index')->with('success', 'Data Nasabah berhasil dihapus.');
    }

    public function jsonNasabah()
    {
        $nasabah = Nasabah::orderBy('id', 'desc')->get();
        return DataTables::of($nasabah)
            ->addIndexColumn()
            ->addColumn('action', function($nasabah) {
                return view('nasabah.datatables.action', compact('nasabah'))->render();
            })
            ->editColumn('status', function($nasabah) {
                $badges = [
                    'aktif' => 'success',
                    'non-aktif' => 'secondary',
                    'blacklist' => 'danger',
                    'berisiko' => 'warning',
                ];
                $color = $badges[$nasabah->status] ?? 'secondary';
                return '<span class="badge badge-' . $color . '">' . ucfirst($nasabah->status) . '</span>';
            })
            ->rawColumns(['action', 'status'])
            ->toJson();
    }
}
