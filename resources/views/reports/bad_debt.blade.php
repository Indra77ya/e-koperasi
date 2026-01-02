@extends('layouts.app')

@section('page-title')
    Laporan Piutang Macet
@endsection

@section('content-app')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Piutang Macet</h3>
                <div class="card-options">
                    <a href="{{ route('reports.bad_debt', ['export' => 1]) }}" class="btn btn-success btn-pill mr-2">
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
                                <th>No. Pinjaman</th>
                                <th>Nama Anggota/Nasabah</th>
                                <th>Jumlah Pinjaman</th>
                                <th>Sisa Pinjaman</th>
                                <th>Tanggal Macet</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loans as $loan)
                            <tr>
                                <td>{{ $loan->kode_pinjaman }}</td>
                                <td>
                                    @if($loan->member)
                                        {{ $loan->member->name }} <span class="badge badge-info">Anggota</span>
                                    @elseif($loan->nasabah)
                                        {{ $loan->nasabah->name }} <span class="badge badge-warning">Nasabah</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>Rp {{ number_format($loan->jumlah_pinjaman, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($loan->remaining_balance ?? 0, 0, ',', '.') }}</td>
                                <td>{{ $loan->updated_at->format('d-m-Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data piutang macet.</td>
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
    }
</style>
@endsection
