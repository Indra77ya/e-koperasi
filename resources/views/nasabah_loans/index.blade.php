@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <h1 class="page-title">
            Daftar Pinjaman Nasabah
        </h1>
        <div class="page-options d-flex">
            <a href="{{ route('nasabah_loans.create') }}" class="btn btn-primary btn-sm">
                <i class="fe fe-plus"></i> Buat Pengajuan Pinjaman
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Data Pinjaman</h3>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap datatable" id="loans-table">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nasabah</th>
                                <th>Jumlah</th>
                                <th>Tgl Pinjam</th>
                                <th>Status</th>
                                <th>Tipe</th>
                                <th>Tenor</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    require(['datatables', 'jquery'], function(datatable, $) {
        $('#loans-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{!! route('nasabah_loans.get-json') !!}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nasabah_name', name: 'nasabah.nama' },
                { data: 'amount', name: 'amount' },
                { data: 'loan_date', name: 'loan_date' },
                { data: 'status', name: 'status' },
                { data: 'loan_type', name: 'loan_type' },
                { data: 'tenor', name: 'tenor' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ]
        });
    });
</script>
@endsection
