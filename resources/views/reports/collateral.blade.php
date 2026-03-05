@extends('layouts.app')

@section('page-title')
    Laporan Jaminan
@endsection

@section('content-app')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Daftar Jaminan</h3>
                <div class="card-options">
                    <a href="{{ route('reports.collateral', ['export' => 1]) }}" class="btn btn-success btn-pill mr-2">
                        <i class="fa fa-file-excel-o"></i> Export Excel
                    </a>
                    <a href="{{ route('reports.collateral', ['print' => true] + request()->query()) }}" target="_blank" class="btn btn-secondary btn-pill">
                        <i class="fa fa-print"></i> PDF/Print
                    </a>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row mb-5 py-4 border-bottom">
                    <div class="col-6 col-sm-4 border-right">
                        <div class="text-muted small mb-1">Total Nilai Taksasi Jaminan</div>
                        <div class="h4 font-weight-bold mb-0 text-primary">{{ format_rupiah($totals->total_value) }}</div>
                    </div>
                    <div class="col-6 col-sm-4 border-right">
                        <div class="text-muted small mb-1">Jumlah Jaminan Terdata</div>
                        <div class="h4 font-weight-bold mb-0">{{ number_format($totals->total_count) }} Unit</div>
                    </div>
                    <div class="col-12 col-sm-4 mt-3 mt-sm-0">
                        <div class="text-muted small mb-1">Rincian Per Jenis</div>
                        <div class="d-flex flex-wrap" style="gap: 10px;">
                            @foreach($typeCounts as $type)
                                <span class="badge badge-info">{{ $type->jenis ?: 'N/A' }}: {{ $type->count }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Jenis</th>
                                <th>Nomor</th>
                                <th>Nilai Taksiran</th>
                                <th>Pemilik</th>
                                <th>No. Pinjaman</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($collaterals as $col)
                            <tr>
                                <td>{{ $col->jenis }}</td>
                                <td>{{ $col->nomor }}</td>
                                <td>Rp {{ number_format($col->nilai_taksasi, 0, ',', '.') }}</td>
                                <td>{{ $col->pemilik }}</td>
                                <td>{{ $col->loan ? $col->loan->loan_number : '-' }}</td>
                                <td>
                                    @if($col->status == 'disimpan')
                                        <span class="badge badge-success">Disimpan</span>
                                    @elseif($col->status == 'dikembalikan')
                                        <span class="badge badge-secondary">Dikembalikan</span>
                                    @else
                                        {{ ucfirst($col->status) }}
                                    @endif
                                </td>
                                <td>{{ $col->keterangan }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">Tidak ada data jaminan.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $collaterals->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    @media print {
        .card-header .card-options, .header, .footer, .breadcrumbs, .page-header {
            display: none !important;
        }
    }
</style>
@endsection
