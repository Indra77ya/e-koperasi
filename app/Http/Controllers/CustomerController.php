<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Storage;

class CustomerController extends Controller
{
    public function index()
    {
        return view('customers.index');
    }

    public function create()
    {
        return view('customers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nik' => 'required|unique:nasabah,nik',
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
            'pekerjaan' => 'required',
            'file_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_jaminan' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'status_risiko' => 'required',
        ]);

        $data = $request->except(['file_ktp', 'file_jaminan']);

        if ($request->hasFile('file_ktp')) {
            $data['file_ktp'] = $request->file('file_ktp')->store('uploads/customers', 'public');
        }

        if ($request->hasFile('file_jaminan')) {
            $data['file_jaminan'] = $request->file('file_jaminan')->store('uploads/customers', 'public');
        }

        Customer::create($data);

        return redirect()->route('customers.index')->with('success', 'Data Nasabah berhasil disimpan.');
    }

    public function show($id)
    {
        $customer = Customer::with(['loans.payments'])->findOrFail($id);
        return view('customers.show', compact('customer'));
    }

    public function edit($id)
    {
        $customer = Customer::findOrFail($id);
        return view('customers.edit', compact('customer'));
    }

    public function update(Request $request, $id)
    {
        $customer = Customer::findOrFail($id);

        $request->validate([
            'nik' => 'required|unique:nasabah,nik,' . $id,
            'nama' => 'required',
            'alamat' => 'required',
            'no_hp' => 'required',
            'pekerjaan' => 'required',
            'file_ktp' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'file_jaminan' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:2048',
            'status_risiko' => 'required',
        ]);

        $data = $request->except(['file_ktp', 'file_jaminan']);

        if ($request->hasFile('file_ktp')) {
            if ($customer->file_ktp) {
                Storage::disk('public')->delete($customer->file_ktp);
            }
            $data['file_ktp'] = $request->file('file_ktp')->store('uploads/customers', 'public');
        }

        if ($request->hasFile('file_jaminan')) {
            if ($customer->file_jaminan) {
                Storage::disk('public')->delete($customer->file_jaminan);
            }
            $data['file_jaminan'] = $request->file('file_jaminan')->store('uploads/customers', 'public');
        }

        $customer->update($data);

        return redirect()->route('customers.index')->with('success', 'Data Nasabah berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $customer = Customer::findOrFail($id);
        if ($customer->file_ktp) {
            Storage::disk('public')->delete($customer->file_ktp);
        }
        if ($customer->file_jaminan) {
            Storage::disk('public')->delete($customer->file_jaminan);
        }
        $customer->delete();

        return redirect()->route('customers.index')->with('success', 'Data Nasabah berhasil dihapus.');
    }

    public function jsonCustomers()
    {
        $customers = Customer::orderBy('id', 'desc')->get();
        return DataTables::of($customers)
            ->addIndexColumn()
            ->addColumn('action', function($customer) {
                return view('customers.datatables.action', compact('customer'))->render();
            })
            ->rawColumns(['action'])
            ->toJson();
    }
}
