@extends('layouts.print')

@section('title', 'Laporan Arus Kas')
@section('date', date('d F Y', strtotime($startDate)) . ' - ' . date('d F Y', strtotime($endDate)))

@section('content')
<div class="row mb-3">
    <div class="col-6">
        <div class="alert alert-success">
            <strong>Total Masuk:</strong> Rp {{ number_format($totalIn, 0, ',', '.') }}
        </div>
    </div>
    <div class="col-6">
        <div class="alert alert-danger">
            <strong>Total Keluar:</strong> Rp {{ number_format($totalOut, 0, ',', '.') }}
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
    </div>
</div>
@endsection
