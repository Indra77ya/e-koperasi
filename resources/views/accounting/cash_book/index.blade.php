@extends('layouts.app')

@section('content-app')
<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Buku Kas & Bank</h3>
                <div class="card-options">
                    <form action="" method="GET" class="d-flex align-items-center">
                        <select name="account_id" class="form-control mr-2" onchange="this.form.submit()">
                            @foreach($cashAccounts as $acc)
                                <option value="{{ $acc->id }}" {{ $selectedAccount->id == $acc->id ? 'selected' : '' }}>{{ $acc->name }}</option>
                            @endforeach
                        </select>
                    </form>
                    <a href="{{ route('accounting.journals.create') }}" class="btn btn-primary btn-pill">Input Transaksi</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card p-3">
                            <div class="d-flex align-items-center">
                                <span class="stamp stamp-md bg-blue mr-3">
                                    <i class="fe fe-dollar-sign"></i>
                                </span>
                                <div>
                                    <h4 class="m-0"><a href="javascript:void(0)">{{ number_format($currentBalance, 2) }}</a></h4>
                                    <small class="text-muted">Saldo Saat Ini</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable" id="cash-book-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Ref No</th>
                                <th>Description</th>
                                <th>Debit (Masuk)</th>
                                <th>Credit (Keluar)</th>
                                <th>Balance</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
require(['jquery', 'datatables'], function($, datatable) {
    $('#cash-book-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route('accounting.cash_book.data') }}',
            data: function(d) {
                d.account_id = '{{ $selectedAccount->id }}';
            }
        },
        columns: [
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'reference_number', name: 'reference_number' },
            { data: 'description', name: 'description' },
            { data: 'debit', name: 'debit', render: $.fn.dataTable.render.number(',', '.', 2) },
            { data: 'credit', name: 'credit', render: $.fn.dataTable.render.number(',', '.', 2) },
            { data: 'balance', name: 'balance', render: $.fn.dataTable.render.number(',', '.', 2) }
        ],
        order: [[ 0, "desc" ]]
    });
});
</script>
@endsection
