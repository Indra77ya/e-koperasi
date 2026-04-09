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
                <div class="card-options">
                    <form action="{{ route('collections.index') }}" method="GET" class="d-flex">
                        <div class="input-group">
                            <input type="text" name="search" class="form-control form-control-sm" placeholder="Cari nama/kode..." value="{{ request('search') }}">
                            <span class="input-group-append">
                                <button class="btn btn-sm btn-primary" type="submit">
                                    <i class="fe fe-search"></i>
                                </button>
                                @if(request('search'))
                                    <a href="{{ route('collections.index') }}" class="btn btn-sm btn-secondary">
                                        <i class="fe fe-x"></i>
                                    </a>
                                @endif
                            </span>
                        </div>
                    </form>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap">
                    <thead>
                        <tr>
                            <th>Peminjam</th>
                            <th>Kode Pinjaman</th>
                            <th>Jatuh Tempo</th>
                            <th>Sisa Tagihan</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reminders as $loan)
                            <tr>
                                <td>{{ $loan->member ? $loan->member->nama : ($loan->nasabah ? $loan->nasabah->nama : '-') }}</td>
                                <td>{{ $loan->kode_pinjaman }}</td>
                                <td>
                                    @foreach($loan->installments->where('status', 'belum_lunas')->where('tanggal_jatuh_tempo', '<=', now()->addDays(3)) as $ins)
                                        <span class="badge badge-warning">{{ $ins->tanggal_jatuh_tempo->format('d/m/Y') }}</span>
                                    @endforeach
                                </td>
                                <td>Rp {{ number_format($loan->installments->where('status', 'belum_lunas')->sum('total_angsuran'), 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('loans.show', $loan->id) }}" class="btn btn-sm btn-primary">Detail</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada tagihan mendekati jatuh tempo.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($reminders->hasPages())
            <div class="card-footer">
                {{ $reminders->appends(['search' => request('search')])->links() }}
            </div>
            @endif
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
