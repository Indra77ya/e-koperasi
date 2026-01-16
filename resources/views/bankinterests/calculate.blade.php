@extends('layouts.app')

@section('content-app')
<div class="row">
    <div class="col-lg-5">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('calculate') }} {{ __('savings_interest') }}</h3>
                <div class="card-options">
                    <a href="{{ url('/bankinterests') }}" class="btn btn-sm btn-outline-secondary"><i class="fe fe-arrow-left mr-1"></i>{{ __('back') }}</a>
                </div>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="text-muted">{{ __('nin') }}</span>
                        <span class="font-weight-bold">{{ $data->nik }}</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="text-muted">{{ __('full_name') }}</span>
                        <span class="font-weight-bold">{{ $data->nama }}</span>
                    </div>
                    <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                        <span class="text-muted">{{ __('balance') }}</span>
                        <span class="font-weight-bold text-success">{{ $data->balance ? format_rupiah($data->balance->saldo) : '0' }}</span>
                    </div>
                </div>

                <form id="form-check-interest">
                    <label class="form-label">Periode Perhitungan</label>
                    <div class="form-row">
                        <div class="col-6">
                            <input type="number" id="onlmonth" name="month" autocomplete="off" class="form-control" required placeholder="Bulan" min="1" max="12">
                        </div>
                        <div class="col-6">
                            <input type="number" id="onlyear" name="year" autocomplete="off" class="form-control" required placeholder="Tahun" min="2000">
                        </div>
                    </div>
                    <button id="btn-check-interest" class="btn btn-primary btn-block mt-3">
                        <i class="fe fe-calculator mr-2"></i>{{ __('calculate') }}
                    </button>
                </form>

                {{-- Result Container (Hidden Initially) --}}
                <div class="result-interest mt-4"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-7">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('history') }} {{ __('savings_interest') }}</h3>
                <div class="card-options">
                    <a href="javascript:void(0)" id="reload-table" class="btn btn-sm btn-outline-secondary"><i class="fe fe-refresh-cw mr-1"></i>{{ __('refresh') }}</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter table-striped text-nowrap" id="datatable">
                    <thead>
                        <tr>
                            <th class="w-1 text-center">No.</th>
                            <th>Periode</th>
                            <th class="text-right">{{ __('lowest_balance') }}</th>
                            <th class="text-right">{{ __('interest') }}</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
require(['datatables', 'jquery'], function(datatable, $) {
    $(document).ready(function () {

        $('#btn-check-interest').click(function(e) {
            var form = $('#form-check-interest')[0];
            if (form.checkValidity()) {
                e.preventDefault();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var id = '{{ $data->id }}';
                var type = '{{ $type }}';
                var month = $('input[name="month"]').val();
                var year = $('input[name="year"]').val();

                $.ajax({
                    type: "GET",
                    url: "{{ url('bankinterests/check-interest') }}",
                    data: {id: id, type: type, month: month, year: year},
                    success: function(data) {
                        $('.result-interest').html(data.html);
                    }
                });
            } else {
                form.reportValidity();
            }
        });
    });

    var oTable = $('#datatable').DataTable({
        lengthChange: false,
        serverSide: true,
        ajax: {
            url: '{{ url('bankinterests/get-history-interests/' . $data->id) }}',
            data: function(d) {
                d.type = '{{ $type }}';
            }
        },
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'periode', name: 'periode' },
            { data: 'saldo_terendah', name: 'saldo_terendah', className: 'text-right' },
            { data: 'nominal_bunga', name: 'nominal_bunga', className: 'text-right' },
        ],
        language: {
            "url": '{{ lang_url() }}'
        },
        columnDefs: [
            { targets: 0, className: "text-center" }
        ]
    });
    $("#reload-table").click(function() {
        oTable.ajax.reload(null, false);
    });
});
</script>
@endsection
