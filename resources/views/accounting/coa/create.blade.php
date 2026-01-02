@extends('layouts.app')

@section('content-app')
<div class="row row-cards">
    <div class="col-12">
        <form action="{{ route('accounting.coa.store') }}" method="POST" class="card">
            @csrf
            <div class="card-header">
                <h3 class="card-title">Add Account</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Account Code</label>
                            <input type="text" class="form-control" name="code" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Account Name</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Type</label>
                            <select class="form-control" name="type" required>
                                <option value="ASSET">Asset</option>
                                <option value="LIABILITY">Liability</option>
                                <option value="EQUITY">Equity</option>
                                <option value="REVENUE">Revenue</option>
                                <option value="EXPENSE">Expense</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Normal Balance</label>
                            <select class="form-control" name="normal_balance" required>
                                <option value="DEBIT">Debit</option>
                                <option value="CREDIT">Credit</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description"></textarea>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer text-right">
                <button type="submit" class="btn btn-primary">Save Account</button>
            </div>
        </form>
    </div>
</div>
@endsection
