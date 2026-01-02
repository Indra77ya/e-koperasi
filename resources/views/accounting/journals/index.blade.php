@extends('layouts.app')

@section('content-app')
<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Jurnal Umum</h3>
                <div class="card-options">
                    <a href="{{ route('accounting.journals.create') }}" class="btn btn-primary btn-pill">Buat Jurnal Umum</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable" id="journal-table">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>No. Ref</th>
                            <th>Keterangan</th>
                            <th>Detail</th>
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
    $('#journal-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route('accounting.journals.data') }}',
        columns: [
            { data: 'transaction_date', name: 'transaction_date' },
            { data: 'reference_number', name: 'reference_number' },
            { data: 'description', name: 'description' },
            { data: 'items', name: 'items', orderable: false, searchable: false }
        ],
        order: [[ 0, "desc" ]]
    });
});
</script>
@endsection
