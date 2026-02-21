@extends('layouts.app')

@section('page-title')
    Laporan Pendapatan (Bunga & Denda)
@endsection

@section('content-app')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Laporan Pendapatan</h3>
                <div class="card-options">
                    <form action="{{ route('reports.revenue') }}" method="GET" class="d-flex align-items-center">
                        <div class="input-group mr-2">
                             <div class="input-group-prepend">
                                <span class="input-group-text">
                                    <i class="fa fa-calendar"></i>
                                </span>
                            </div>
                            <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                            <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                        </div>
                        <button type="submit" class="btn btn-primary btn-pill mr-2 text-nowrap">Filter</button>
                    </form>
                     <a href="{{ route('reports.revenue', array_merge(request()->all(), ['export' => 1])) }}" class="btn btn-success btn-pill mr-2 text-nowrap">
                        <i class="fa fa-file-excel-o"></i> Excel
                    </a>
                    <a href="{{ route('reports.revenue', array_merge(request()->all(), ['print' => true])) }}" target="_blank" class="btn btn-secondary btn-pill text-nowrap">
                        <i class="fa fa-print"></i> PDF
                    </a>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row mb-5 py-4 border-bottom">
                    <div class="col-12 col-sm-6 border-right">
                        <div class="text-muted small mb-1">Periode Laporan</div>
                        <div class="h4 font-weight-bold mb-0 text-info">{{ date('d/m/Y', strtotime($startDate)) }} - {{ date('d/m/Y', strtotime($endDate)) }}</div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="text-muted small mb-1">Total Pendapatan (Bunga & Denda)</div>
                        <div class="h4 font-weight-bold mb-0 text-red">{{ format_rupiah($totalRevenue) }}</div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Akun Pendapatan</th>
                                <th>Deskripsi Transaksi</th>
                                <th>Jumlah (Kredit)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($revenues as $item)
                            <tr>
                                <td>{{ $item->journalEntry->transaction_date }}</td>
                                <td>{{ $item->account->name }}</td>
                                <td>{{ $item->description ?? $item->journalEntry->description }}</td>
                                <td class="text-right">
                                    {{ format_rupiah($item->credit - $item->debit) }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada pendapatan dalam periode ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $revenues->appends(request()->input())->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('css')
<style>
    @media print {
        .card-header .card-options form, .header, .footer, .breadcrumbs, .page-header {
            display: none !important;
        }
        .card-header .card-options a, .card-header .card-options button {
            display: none !important;
        }
        .card-title:after {
            content: " ({{ $startDate }} - {{ $endDate }})";
        }
    }
</style>
@endsection
