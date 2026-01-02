@extends('layouts.app')

@section('page-title')
    Laporan Arus Kas
@endsection

@section('content-app')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Laporan Arus Kas</h3>
                <div class="card-options">
                    <form action="{{ route('reports.cash_flow') }}" method="GET" class="d-flex align-items-center">
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
                     <a href="{{ route('reports.cash_flow', array_merge(request()->all(), ['export' => 1])) }}" class="btn btn-success btn-pill mr-2 text-nowrap">
                        <i class="fa fa-file-excel-o"></i> Excel
                    </a>
                    <button onclick="window.print()" class="btn btn-secondary btn-pill text-nowrap">
                        <i class="fa fa-print"></i> PDF
                    </button>
                </div>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="alert alert-success">
                            <strong>Total Masuk:</strong> Rp {{ number_format($totalIn, 0, ',', '.') }}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-danger">
                            <strong>Total Keluar:</strong> Rp {{ number_format($totalOut, 0, ',', '.') }}
                        </div>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Ref</th>
                                <th>Akun Kas/Bank</th>
                                <th>Deskripsi</th>
                                <th>Masuk (Debit)</th>
                                <th>Keluar (Kredit)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $item)
                            <tr>
                                <td>{{ $item->journalEntry->transaction_date }}</td>
                                <td>{{ $item->journalEntry->reference_number }}</td>
                                <td>{{ $item->account->name }}</td>
                                <td>{{ $item->description ?? $item->journalEntry->description }}</td>
                                <td class="text-right text-success">
                                    {{ $item->debit > 0 ? 'Rp '.number_format($item->debit, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-right text-danger">
                                    {{ $item->credit > 0 ? 'Rp '.number_format($item->credit, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada transaksi dalam periode ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $transactions->appends(request()->input())->links() }}
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
