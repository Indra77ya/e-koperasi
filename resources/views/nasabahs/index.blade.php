@extends('layouts.app')

@section('content-app')
<div class="row row-cards row-deck">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data Nasabah</h3>
                <div class="card-options">
                    <a href="{{ route('nasabahs.create') }}" class="btn btn-sm btn-pill btn-primary">Tambah Nasabah</a>
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
                    <table class="table card-table table-vcenter text-nowrap" id="datatable">
                        <thead>
                            <tr>
                                <th scope="col" class="w-1">No.</th>
                                <th scope="col">NIK</th>
                                <th scope="col">Nama Lengkap</th>
                                <th scope="col">Pekerjaan</th>
                                <th scope="col">No. HP</th>
                                <th scope="col">Status</th>
                                <th scope="col"></th>
                            </tr>
                        </thead>
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
    $('#datatable').DataTable({
        lengthChange: false,
        serverSide: true,
        ajax: '{{ route('nasabahs.get-json') }}',
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'nik', name: 'nik' },
            { data: 'nama', name: 'nama' },
            { data: 'pekerjaan', name: 'pekerjaan' },
            { data: 'no_hp', name: 'no_hp' },
            { data: 'status', name: 'status' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ],
        language: {
            "url": '{{ lang_url() }}'
        },
        columnDefs: [
            {
                targets: [0],
                className: "text-center"
            },
            {
                targets: [6],
                className: "text-right"
            }
        ]
    });
});
</script>
@endsection
