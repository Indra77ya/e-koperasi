@extends('layouts.print')

@section('title', 'Laporan Pendapatan')
@section('date', date('d F Y', strtotime($startDate)) . ' - ' . date('d F Y', strtotime($endDate)))

@section('content')
<div class="row mb-3">
    <div class="col-12">
        <div class="alert alert-info">
            <strong>Total Pendapatan:</strong> Rp {{ number_format($totalRevenue, 0, ',', '.') }}
        </div>
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
                            Rp {{ number_format($item->credit - $item->debit, 0, ',', '.') }}
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
