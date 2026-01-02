@extends('layouts.app')

@section('content-app')
<div class="row row-cards">
    <div class="col-12">
        <form action="{{ route('accounting.coa.update', $account->id) }}" method="POST" class="card">
            @csrf
            @method('PUT')
            <div class="card-header">
                <h3 class="card-title">Edit Account</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Account Code</label>
                            <input type="text" class="form-control" name="code" value="{{ $account->code }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Account Name</label>
                            <input type="text" class="form-control" name="name" value="{{ $account->name }}" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Type</label>
                            <select class="form-control" name="type" required>
                                <option value="ASSET" {{ $account->type == 'ASSET' ? 'selected' : '' }}>Asset</option>
                                <option value="LIABILITY" {{ $account->type == 'LIABILITY' ? 'selected' : '' }}>Liability</option>
                                <option value="EQUITY" {{ $account->type == 'EQUITY' ? 'selected' : '' }}>Equity</option>
                                <option value="REVENUE" {{ $account->type == 'REVENUE' ? 'selected' : '' }}>Revenue</option>
                                <option value="EXPENSE" {{ $account->type == 'EXPENSE' ? 'selected' : '' }}>Expense</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Normal Balance</label>
                            <select class="form-control" name="normal_balance" required>
                                <option value="DEBIT" {{ $account->normal_balance == 'DEBIT' ? 'selected' : '' }}>Debit</option>
                                <option value="CREDIT" {{ $account->normal_balance == 'CREDIT' ? 'selected' : '' }}>Credit</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description">{{ $account->description }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">Update Account</button>
            </div>
        </form>
    </div>
</div>
@endsection
