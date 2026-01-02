@extends('layouts.app')

@section('content-app')
<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Chart of Accounts (COA)</h3>
                <div class="card-options">
                    <a href="{{ route('accounting.coa.create') }}" class="btn btn-primary btn-pill btn-sm">Tambah Akun</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable" id="coa-table">
                    <thead>
                        <tr>
                            <th>Kode Akun</th>
                            <th>Nama Akun</th>
                            <th>Tipe</th>
                            <th>Saldo Normal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
require(['jquery', 'datatables'], function($, datatable) {
    $('#coa-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('accounting.coa.data') }}',
        columns: [
            { data: 'code', name: 'code' },
            { data: 'name', name: 'name' },
            { data: 'type', name: 'type' },
            { data: 'normal_balance', name: 'normal_balance' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ]
    });
});
</script>
@endsection
