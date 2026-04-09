@extends('layouts.app')

@section('page-title')
    Penagihan & Kolektabilitas
@endsection

@section('content-app')
<div class="row row-cards">
    <div class="col-sm-6 col-lg-4">
        <div class="card p-3 card-status-link" data-status="Kurang Lancar" style="cursor: pointer;">
            <div class="d-flex align-items-center">
                <span class="stamp stamp-md bg-orange mr-3">
                    <i class="fe fe-alert-triangle"></i>
                </span>
                <div>
                    <h4 class="m-0">{{ $countKL }}</h4>
                    <small class="text-muted">Kurang Lancar</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card p-3 card-status-link" data-status="Diragukan" style="cursor: pointer;">
            <div class="d-flex align-items-center">
                <span class="stamp stamp-md bg-pink mr-3">
                    <i class="fe fe-slash"></i>
                </span>
                <div>
                    <h4 class="m-0">{{ $countDiragukan }}</h4>
                    <small class="text-muted">Diragukan</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card p-3 card-status-link" data-status="Lancar" style="cursor: pointer;">
            <div class="d-flex align-items-center">
                <span class="stamp stamp-md bg-green mr-3">
                    <i class="fe fe-check"></i>
                </span>
                <div>
                    <h4 class="m-0">{{ $countLancar }}</h4>
                    <small class="text-muted">Kredit Lancar</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card p-3 card-status-link" data-status="DPK" style="cursor: pointer;">
            <div class="d-flex align-items-center">
                <span class="stamp stamp-md bg-yellow mr-3">
                    <i class="fe fe-alert-circle"></i>
                </span>
                <div>
                    <h4 class="m-0">{{ $countDPK }}</h4>
                    <small class="text-muted">Dalam Perhatian Khusus</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-4">
        <div class="card p-3 card-status-link" data-status="Macet" style="cursor: pointer;">
            <div class="d-flex align-items-center">
                <span class="stamp stamp-md bg-red mr-3">
                    <i class="fe fe-x-circle"></i>
                </span>
                <div>
                    <h4 class="m-0">{{ $countMacet }}</h4>
                    <small class="text-muted">Kredit Macet</small>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Reminder Penagihan (Jatuh Tempo < 3 Hari)</h3>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="reminders-table" style="width: 100%;">
                        <thead>
                            <tr>
                                <th>Peminjam</th>
                                <th style="min-width: 150px;">Kode Pinjaman</th>
                                <th>Jatuh Tempo</th>
                                <th>Sisa Tagihan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Pinjaman Berdasarkan Kolektabilitas</h3>
                <div class="card-options">
                    <button type="button" id="btn-print-dc" class="btn btn-sm btn-pill btn-primary mr-2"><i class="fe fe-printer"></i> Print Data DC</button>
                    <form action="{{ route('collections.refresh') }}" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-sm btn-pill btn-secondary"><i class="fe fe-refresh-cw"></i> Refresh Status</button>
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <select id="filter-status" class="form-control">
                            <option value="">Semua Status</option>
                            <option value="Lancar">Lancar</option>
                            <option value="DPK">DPK</option>
                            <option value="Kurang Lancar">Kurang Lancar</option>
                            <option value="Diragukan">Diragukan</option>
                            <option value="Macet">Macet</option>
                        </select>
                    </div>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered" id="collection-table">
                        <thead>
                            <tr>
                                <th>Kode</th>
                                <th>Peminjam</th>
                                <th>Alamat</th>
                                <th>Area/Desa</th>
                                <th>Status Pinjaman</th>
                                <th>Kolektabilitas</th>
                                <th>Terlambat (Hari)</th>
                                <th>Aksi</th>
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
    require(['jquery', 'datatables'], function($) {
        var tableReminders = $('#reminders-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('collections.reminders-data') }}",
            columns: [
                { data: 'borrower', name: 'borrower', orderable: false, searchable: false },
                { data: 'kode_pinjaman', name: 'kode_pinjaman', className: 'text-nowrap' },
                { data: 'due_dates', name: 'due_dates', orderable: false, searchable: false },
                { data: 'remaining_bill', name: 'remaining_bill', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
            }
        });

        var table = $('#collection-table').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "{{ route('collections.data') }}",
                data: function (d) {
                    d.kolektabilitas = $('#filter-status').val();
                }
            },
            columns: [
                { data: 'kode_pinjaman', name: 'kode_pinjaman' },
                { data: 'borrower', name: 'borrower', orderable: false, searchable: false },
                { data: 'address', name: 'address', orderable: false, searchable: false },
                { data: 'area', name: 'area', name: 'area' },
                { data: 'status', name: 'status' },
                { data: 'kolektabilitas', name: 'kolektabilitas' },
                { data: 'overdue_days', name: 'overdue_days', orderable: false, searchable: false },
                { data: 'action', name: 'action', orderable: false, searchable: false }
            ],
            order: [[3, 'asc']],
            drawCallback: function (settings) {
                var api = this.api();
                var rows = api.rows({ page: 'current' }).nodes();
                var last = null;

                api.column(3, { page: 'current' }).data().each(function (group, i) {
                    if (last !== group) {
                        $(rows).eq(i).before(
                            '<tr class="group bg-light font-weight-bold"><td colspan="8">Area: ' + group + '</td></tr>'
                        );
                        last = group;
                    }
                });
            }
        });

        $('#filter-status').change(function() {
            table.draw();
        });

        $('#btn-print-dc').click(function() {
            var status = $('#filter-status').val();
            var url = "{{ route('collections.print') }}";
            if (status) {
                url += "?kolektabilitas=" + status;
            }
            window.open(url, '_blank');
        });

        $('.card-status-link').click(function() {
            var status = $(this).data('status');
            $('#filter-status').val(status).change();

            // Scroll to table
            $('html, body').animate({
                scrollTop: $("#collection-table").offset().top - 100
            }, 500);
        });
    });
</script>
@endsection
