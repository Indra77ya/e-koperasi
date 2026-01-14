@extends('layouts.print')

@section('title', 'Laporan Arus Kas (Metode Langsung)')
@section('date', date('d F Y', strtotime($startDate)) . ' - ' . date('d F Y', strtotime($endDate)))

@section('content')
<div class="row">
    <div class="col-12">
        <h4 class="text-uppercase text-muted font-weight-bold">Penerimaan Kas (Cash In)</h4>
        <table class="table table-sm table-striped">
            @php $totalIn = 0; @endphp
            @foreach($cashInItems as $item)
            <tr>
                <td>{{ $item->description ?? 'Transaksi' }} ({{ $item->ref_number }})</td>
                <td class="text-right text-success">+ {{ number_format($item->debit, 0, ',', '.') }}</td>
            </tr>
            @php $totalIn += $item->debit; @endphp
            @endforeach
            <tr class="font-weight-bold" style="background-color: #f8f9fa;">
                <td>Total Penerimaan Kas</td>
                <td class="text-right">{{ number_format($totalIn, 0, ',', '.') }}</td>
            </tr>
        </table>

        <h4 class="text-uppercase text-muted font-weight-bold mt-4">Pengeluaran Kas (Cash Out)</h4>
        <table class="table table-sm table-striped">
            @php $totalOut = 0; @endphp
            @foreach($cashOutItems as $item)
            <tr>
                <td>{{ $item->description ?? 'Transaksi' }} ({{ $item->ref_number }})</td>
                <td class="text-right text-danger">- {{ number_format($item->credit, 0, ',', '.') }}</td>
            </tr>
            @php $totalOut += $item->credit; @endphp
            @endforeach
            <tr class="font-weight-bold" style="background-color: #f8f9fa;">
                <td>Total Pengeluaran Kas</td>
                <td class="text-right">{{ number_format($totalOut, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="alert alert-{{ ($totalIn - $totalOut) >= 0 ? 'success' : 'warning' }} mt-4">
            <h3 class="m-0">
                Arus Kas Bersih
                <span class="float-right">{{ number_format($totalIn - $totalOut, 0, ',', '.') }}</span>
            </h3>
        </div>
    </div>
</div>
@endsection
