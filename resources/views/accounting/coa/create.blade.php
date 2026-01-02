@extends('layouts.app')

@section('content-app')
<div class="row row-cards">
    <div class="col-12">
        <form action="{{ route('accounting.coa.store') }}" method="POST" class="card">
            @csrf
            <div class="card-header">
                <h3 class="card-title">Tambah Akun</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Kode Akun</label>
                            <input type="text" class="form-control" name="code" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Nama Akun</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Tipe</label>
                            <select class="form-control" name="type" required>
                                <option value="ASSET">Aset (Asset)</option>
                                <option value="LIABILITY">Liabilitas (Liability)</option>
                                <option value="EQUITY">Ekuitas (Equity)</option>
                                <option value="REVENUE">Pendapatan (Revenue)</option>
                                <option value="EXPENSE">Beban (Expense)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Saldo Normal</label>
                            <select class="form-control" name="normal_balance" required>
                                <option value="DEBIT">Debit</option>
                                <option value="CREDIT">Kredit</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" name="description"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">Simpan Akun</button>
            </div>
        </form>
    </div>
</div>
@endsection
