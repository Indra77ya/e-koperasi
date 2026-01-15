<?php

namespace App\Http\Controllers;

use App\Models\Nasabah;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;

class NasabahController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('nasabahs.index');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('nasabahs.create');
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
            'nik' => 'required|unique:nasabahs,nik',
            'nama' => 'required',
            'file_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_jaminan' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = $request->except(['file_ktp', 'file_jaminan']);

        if ($request->hasFile('file_ktp')) {
            $data['file_ktp'] = $request->file('file_ktp')->store('nasabah_files', 'public');
        }

        if ($request->hasFile('file_jaminan')) {
            $data['file_jaminan'] = $request->file('file_jaminan')->store('nasabah_files', 'public');
        }

        Nasabah::create($data);

        return redirect()->route('nasabahs.index')->with('success', 'Data Nasabah berhasil disimpan.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $nasabah = Nasabah::findOrFail($id);
        return view('nasabahs.show', compact('nasabah'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $nasabah = Nasabah::findOrFail($id);
        return view('nasabahs.edit', compact('nasabah'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $nasabah = Nasabah::findOrFail($id);

        $request->validate([
            'nik' => 'required|unique:nasabahs,nik,' . $id,
            'nama' => 'required',
            'file_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_jaminan' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ]);

        $data = $request->except(['file_ktp', 'file_jaminan']);

        if ($request->hasFile('file_ktp')) {
            if ($nasabah->file_ktp) {
                Storage::disk('public')->delete($nasabah->file_ktp);
            }
            $data['file_ktp'] = $request->file('file_ktp')->store('nasabah_files', 'public');
        }

        if ($request->hasFile('file_jaminan')) {
            if ($nasabah->file_jaminan) {
                Storage::disk('public')->delete($nasabah->file_jaminan);
            }
            $data['file_jaminan'] = $request->file('file_jaminan')->store('nasabah_files', 'public');
        }

        $nasabah->update($data);

        return redirect()->route('nasabahs.index')->with('success', 'Data Nasabah berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
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

        return redirect()->route('nasabahs.index')->with('success', 'Data Nasabah berhasil dihapus.');
    }

    public function jsonNasabah()
    {
        $nasabahs = Nasabah::select('nasabahs.*')->orderBy('id', 'desc');
        return DataTables::of($nasabahs)
            ->addIndexColumn()
            ->addColumn('action', function($nasabah) {
                return view('nasabahs.datatables.action', compact('nasabah'))->render();
            })
            ->editColumn('status', function($nasabah) {
                $badges = [
                    'aman' => 'success',
                    'blacklist' => 'danger',
                    'berisiko' => 'warning',
                ];
                $color = $badges[$nasabah->status] ?? 'secondary';
                return '<span class="tag tag-' . $color . '">' . ucfirst($nasabah->status) . '</span>';
            })
            ->rawColumns(['action', 'status'])
            ->toJson();
    }
}
