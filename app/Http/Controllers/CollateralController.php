<?php

namespace App\Http\Controllers;

use App\Models\Collateral;
use App\Models\Loan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class CollateralController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Loan $loan)
    {
        $request->validate([
            'jenis' => 'required|string|max:255',
            'nilai_taksasi' => 'required|numeric|min:0',
            'foto' => 'nullable|file|image|max:2048',
            'dokumen' => 'nullable|file|max:2048',
            'status' => 'required|string',
        ]);

        $data = $request->except(['foto', 'dokumen']);
        $data['pinjaman_id'] = $loan->id;
        $data['tanggal_masuk'] = now();
        $data['diterima_oleh'] = auth()->user()->name ?? 'System';

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('collaterals/photos', 'public');
            $data['foto'] = $path;
        }

        if ($request->hasFile('dokumen')) {
            $path = $request->file('dokumen')->store('collaterals/documents', 'public');
            $data['dokumen'] = $path;
        }

        Collateral::create($data);

        return redirect()->route('loans.show', $loan->id)->with('success', 'Jaminan berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Collateral  $collateral
     * @return \Illuminate\Http\Response
     */
    public function edit(Loan $loan, Collateral $collateral)
    {
        return view('loans.collaterals.edit', compact('loan', 'collateral'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Collateral  $collateral
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Loan $loan, Collateral $collateral)
    {
        $request->validate([
            'jenis' => 'required|string|max:255',
            'nilai_taksasi' => 'required|numeric|min:0',
            'foto' => 'nullable|file|image|max:2048',
            'dokumen' => 'nullable|file|max:2048',
            'status' => 'required|string',
        ]);

        // Prevent mass assignment of crucial IDs
        $data = $request->except(['foto', 'dokumen', 'pinjaman_id', 'id']);

        // Check if user is trying to return the collateral
        if ($data['status'] == 'dikembalikan' && $collateral->status != 'dikembalikan') {
            if ($loan->status != 'lunas') {
                return back()->withErrors(['status' => 'Jaminan hanya dapat dikembalikan jika status pinjaman sudah lunas.']);
            }
            $data['tanggal_keluar'] = now();
        } elseif ($data['status'] == 'disimpan') {
            $data['tanggal_keluar'] = null;
        }

        if ($request->hasFile('foto')) {
            // Delete old photo
            if ($collateral->foto) {
                Storage::disk('public')->delete($collateral->foto);
            }
            $path = $request->file('foto')->store('collaterals/photos', 'public');
            $data['foto'] = $path;
        }

        if ($request->hasFile('dokumen')) {
            // Delete old doc
            if ($collateral->dokumen) {
                Storage::disk('public')->delete($collateral->dokumen);
            }
            $path = $request->file('dokumen')->store('collaterals/documents', 'public');
            $data['dokumen'] = $path;
        }

        $collateral->update($data);

        return redirect()->route('loans.show', $loan->id)->with('success', 'Jaminan berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Collateral  $collateral
     * @return \Illuminate\Http\Response
     */
    public function destroy(Loan $loan, Collateral $collateral)
    {
        if ($collateral->foto) {
            Storage::disk('public')->delete($collateral->foto);
        }
        if ($collateral->dokumen) {
            Storage::disk('public')->delete($collateral->dokumen);
        }
        $collateral->delete();

        return redirect()->route('loans.show', $loan->id)->with('success', 'Jaminan berhasil dihapus.');
    }

    public function create(Loan $loan)
    {
        return view('loans.collaterals.create', compact('loan'));
    }

    public function returnCollateral(Request $request, Loan $loan, Collateral $collateral)
    {
        if ($loan->status != 'lunas') {
            return back()->with('error', 'Pinjaman belum lunas. Jaminan tidak dapat dikembalikan.');
        }

        $collateral->update([
            'status' => 'dikembalikan',
            'tanggal_keluar' => now(),
            'diserahkan_kepada' => $request->diserahkan_kepada,
            'keterangan' => $request->keterangan ?? $collateral->keterangan
        ]);

        return redirect()->route('loans.show', $loan->id)->with('success', 'Jaminan berhasil dikembalikan.');
    }
}
