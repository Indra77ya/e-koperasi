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
                    <button onclick="window.print()" class="btn btn-secondary btn-pill">
                        <i class="fa fa-print"></i> PDF/Print
                    </button>
                </div>
            </div>
            <div class="card-body">
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
