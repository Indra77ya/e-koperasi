@extends('layouts.app')

@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://select2.github.io/select2-bootstrap-theme/css/select2-bootstrap.css">
@endsection

@section('content-app')
<div class="row row-cards row-deck">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title" id="withdrawal_desc">Data {{ __('menu.withdrawal') }}</h3>
                <div class="card-options">
                    <a href="{{ route('withdrawals.create') }}" class="btn btn-sm btn-pill btn-primary">{{ __('add') }} {{ __('menu.withdrawal') }}</a>
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

                <!-- Filter Section -->
                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="form-group">
                             <label class="form-label">Tipe</label>
                             <div class="custom-controls-stacked">
                                <label class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" name="type" value="anggota" checked>
                                    <span class="custom-control-label">Anggota</span>
                                </label>
                                <label class="custom-control custom-radio custom-control-inline">
                                    <input type="radio" class="custom-control-input" name="type" value="nasabah">
                                    <span class="custom-control-label">Nasabah</span>
                                </label>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4" id="anggota-group">
                                <div class="form-group">
                                    <label class="form-label">{{ __('menu.member') }}</label>
                                    <select class="form-control select2" name="anggota">
                                        <option value="">-- Semua Anggota --</option>
                                        @foreach ($members as $member)
                                            <option value="{{ $member->id }}">{{ $member->nama }} - {{ $member->nik }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-4" id="nasabah-group" style="display: none;">
                                <div class="form-group">
                                    <label class="form-label">Nasabah</label>
                                    <select class="form-control select2" name="nasabah">
                                        <option value="">-- Semua Nasabah --</option>
                                        @foreach ($nasabahs as $nasabah)
                                            <option value="{{ $nasabah->id }}">{{ $nasabah->nama }} - {{ $nasabah->nik }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Dari Tanggal</label>
                                    <input type="date" name="from_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label class="form-label">Sampai Tanggal</label>
                                    <input type="date" name="to_date" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-2 d-flex align-items-end">
                                <div class="form-group w-100">
                                    <button id="btn-filter" class="btn btn-primary btn-block">{{ __('filter') }}</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- End Filter Section -->

                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap" id="datatable" aria-describedby="withdrawal_desc">
                        <thead>
                            <tr>
                                <th scope="col" class="w-1">No.</th>
                                <th scope="col">{{ __('menu.member') }} / Nasabah</th>
                                <th scope="col">{{ __('amount') }}</th>
                                <th scope="col">{{ __('note') }}</th>
                                <th scope="col">{{ __('date') }}</th>
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
require(['datatables', 'jquery', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js'], function(datatable, $, select2) {

    $('.select2').select2({
        theme: "bootstrap",
        width: '100%'
    });

    $('input[name="type"]').on('change', function() {
        var type = $(this).val();
        if (type == 'anggota') {
            $('#anggota-group').show();
            $('#nasabah-group').hide();
        } else {
            $('#anggota-group').hide();
            $('#nasabah-group').show();
        }
    });

    var table = $('#datatable').DataTable({
        lengthChange: false,
        serverSide: true,
        ajax: {
            url: '{{ url('withdrawals/get-json') }}',
            data: function (d) {
                d.type = $('input[name="type"]:checked').val();
                d.anggota_id = $('select[name="anggota"]').val();
                d.nasabah_id = $('select[name="nasabah"]').val();
                d.from_date = $('input[name="from_date"]').val();
                d.to_date = $('input[name="to_date"]').val();
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'anggota', name: 'anggota', orderable: false },
            { data: 'jumlah', name: 'jumlah' },
            { data: 'keterangan', name: 'keterangan', orderable: false },
            { data: 'tanggal', name: 'tanggal' },
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
                targets: [2],
                className: "text-right"
            },
            {
                targets: [5],
                className: "text-center"
            }
        ]
    });

    $('#btn-filter').click(function(e) {
        e.preventDefault();
        table.draw();
    });
});
</script>
@endsection
