@extends('layouts.app')

@section('page-title')
    Laporan Pinjaman Outstanding
@endsection

@section('content-app')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Pinjaman Outstanding</h3>
                <div class="card-options">
                    <a href="{{ route('reports.outstanding', ['export' => 1]) }}" class="btn btn-success btn-pill mr-2">
                        <i class="fa fa-file-excel-o"></i> Export Excel
                    </a>
                    <a href="{{ route('reports.outstanding', ['print' => true] + request()->query()) }}" target="_blank" class="btn btn-secondary btn-pill">
                        <i class="fa fa-print"></i> PDF/Print
                    </a>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row mb-5 py-4 border-bottom">
                    <div class="col-6 col-sm-4 border-right">
                        <div class="text-muted small mb-1">Total Plafon Pinjaman</div>
                        <div class="h4 font-weight-bold mb-0">{{ format_rupiah($totals->total_pokok) }}</div>
                    </div>
                    <div class="col-6 col-sm-4 border-right">
                        <div class="text-muted small mb-1">Total Sisa Pinjaman (Outstanding)</div>
                        <div class="h4 font-weight-bold mb-0 text-red">{{ format_rupiah($totals->total_remaining) }}</div>
                    </div>
                    <div class="col-6 col-sm-4">
                        <div class="text-muted small mb-1">Jumlah Pinjaman Aktif</div>
                        <div class="h4 font-weight-bold mb-0">{{ number_format($totals->total_count) }} Rekening</div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No. Pinjaman</th>
                                <th>Nama Anggota/Nasabah</th>
                                <th>Jumlah Pinjaman</th>
                                <th>Sisa Pinjaman</th>
                                <th>Status</th>
                                <th>Tanggal Cair</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loans as $loan)
                            <tr>
                                <td>{{ $loan->kode_pinjaman }}</td>
                                <td>
                                    @if($loan->member)
                                        <span class="badge badge-info">Anggota</span> {{ $loan->member->nama }}
                                    @elseif($loan->nasabah)
                                        <span class="badge badge-warning">Nasabah</span> {{ $loan->nasabah->nama }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-right">{{ format_rupiah($loan->jumlah_pinjaman) }}</td>
                                <td class="text-right">{{ format_rupiah($loan->remaining_balance ?? 0) }}</td>
                                <td>
                                    <span class="badge badge-primary">{{ ucfirst($loan->status) }}</span>
                                </td>
                                <td>{{ $loan->created_at->format('d-m-Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $loans->links() }}
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
        .card {
            border: none !important;
            box-shadow: none !important;
        }
    }
</style>
@endsection
