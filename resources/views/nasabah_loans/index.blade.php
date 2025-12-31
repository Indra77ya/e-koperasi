@extends('layouts.app')

@section('page-title', 'Daftar Pinjaman Nasabah')

@section('content-app')
<div class="row row-cards row-deck">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data Pinjaman</h3>
                <div class="card-options">
                    <a href="{{ route('nasabah_loans.create') }}" class="btn btn-sm btn-pill btn-primary">
                        <i class="fe fe-plus"></i> Buat Pengajuan Pinjaman
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if (session('success'))
                    <div class="alert alert-icon alert-success alert-dismissible" role="alert">
                        <i class="fe fe-check mr-2" aria-hidden="true"></i>
                        <button type="button" class="close" data-dismiss="alert"></button>
                        {{ session('success') }}
                    </div>
                @endif

                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap" id="loans-table">
                        <thead>
                            <tr>
                                <th class="w-1">No</th>
                                <th>Nasabah</th>
                                <th>Jumlah</th>
                                <th>Tgl Pinjam</th>
                                <th>Status</th>
                                <th>Tipe</th>
                                <th>Tenor</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    require(['datatables', 'jquery'], function(datatable, $) {
        $('#loans-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: '{{ route('nasabah_loans.get-json') }}',
            columns: [
                { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'nasabah_name', name: 'nasabah.nama' },
                { data: 'amount', name: 'amount' },
                { data: 'loan_date', name: 'loan_date' },
                { data: 'status', name: 'status' },
                { data: 'loan_type', name: 'loan_type' },
                { data: 'tenor', name: 'tenor' },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            language: {
                "url": '{{ lang_url() }}'
            }
        });
    });
</script>
@endsection
