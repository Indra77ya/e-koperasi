@extends('layouts.app')

@section('content-app')
<div class="row row-cards">
    <div class="col-12">
        <form action="{{ route('accounting.coa.update', $account->id) }}" method="POST" class="card">
            @csrf
            @method('PUT')
            <div class="card-header">
                <h3 class="card-title">Edit Akun</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Kode Akun</label>
                            <input type="text" class="form-control" name="code" value="{{ $account->code }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Nama Akun</label>
                            <input type="text" class="form-control" name="name" value="{{ $account->name }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Tipe</label>
                            <select class="form-control" name="type" required>
                                <option value="ASSET" {{ $account->type == 'ASSET' ? 'selected' : '' }}>Aset (Asset)</option>
                                <option value="LIABILITY" {{ $account->type == 'LIABILITY' ? 'selected' : '' }}>Liabilitas (Liability)</option>
                                <option value="EQUITY" {{ $account->type == 'EQUITY' ? 'selected' : '' }}>Ekuitas (Equity)</option>
                                <option value="REVENUE" {{ $account->type == 'REVENUE' ? 'selected' : '' }}>Pendapatan (Revenue)</option>
                                <option value="EXPENSE" {{ $account->type == 'EXPENSE' ? 'selected' : '' }}>Beban (Expense)</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Saldo Normal</label>
                            <select class="form-control" name="normal_balance" required>
                                <option value="DEBIT" {{ $account->normal_balance == 'DEBIT' ? 'selected' : '' }}>Debit</option>
                                <option value="CREDIT" {{ $account->normal_balance == 'CREDIT' ? 'selected' : '' }}>Kredit</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">Keterangan</label>
                            <textarea class="form-control" name="description">{{ $account->description }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">Update Akun</button>
            </div>
        </form>
    </div>
</div>
@endsection
