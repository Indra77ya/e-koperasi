@extends('layouts.print')

@section('title', 'Laporan Pendapatan')
@section('date', date('d F Y', strtotime($startDate)) . ' - ' . date('d F Y', strtotime($endDate)))

@section('content')
<div class="row mb-5 py-4 border-bottom border-top">
    <div class="col-6 border-right">
        <div class="text-muted small mb-1">Periode Laporan</div>
        <div class="h4 font-weight-bold mb-0 text-info">{{ date('d/m/Y', strtotime($startDate)) }} - {{ date('d/m/Y', strtotime($endDate)) }}</div>
    </div>
    <div class="col-6">
        <div class="text-muted small mb-1">Total Pendapatan (Bunga & Denda)</div>
        <div class="h4 font-weight-bold mb-0 text-red" style="color: #cd201f !important;">{{ format_rupiah($totalRevenue) }}</div>
    </div>
</div>

<div class="row">
    <div class="col-12">
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
    </div>
</div>
@endsection
