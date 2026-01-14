@extends('layouts.app')

@section('content-app')
<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Laporan Arus Kas</h3>
                <div class="card-options">
                    <form action="" method="GET" class="d-flex align-items-center">
                        <label class="mr-2">Dari:</label>
                        <input type="date" name="start_date" class="form-control mr-2" value="{{ $startDate }}" onchange="this.form.submit()">
                        <label class="mr-2">Sampai:</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" onchange="this.form.submit()">
                    </form>
                    <a href="{{ route('accounting.reports.arus_kas', ['print' => true] + request()->query()) }}" target="_blank" class="btn btn-secondary btn-pill ml-2 text-nowrap">
                        <i class="fa fa-print"></i> PDF/Print
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    Ini adalah laporan arus kas Metode Langsung yang disederhanakan berdasarkan pergerakan akun Kas/Bank.
                </div>

                <h4 class="text-uppercase text-muted font-weight-bold">Penerimaan Kas (Cash In)</h4>
                <table class="table table-sm">
                    @php $totalIn = 0; @endphp
                    @foreach($cashInItems as $item)
                    <tr>
                        <td>{{ $item->description ?? 'Transaksi' }} ({{ $item->ref_number }})</td>
                        <td class="text-right text-success">+ {{ number_format($item->debit, 0, ',', '.') }}</td>
                    </tr>
                    @php $totalIn += $item->debit; @endphp
                    @endforeach
                    <tr class="font-weight-bold bg-light">
                        <td>Total Penerimaan Kas</td>
                        <td class="text-right">{{ number_format($totalIn, 0, ',', '.') }}</td>
                    </tr>
                </table>

                <h4 class="text-uppercase text-muted font-weight-bold mt-4">Pengeluaran Kas (Cash Out)</h4>
                <table class="table table-sm">
                    @php $totalOut = 0; @endphp
                    @foreach($cashOutItems as $item)
                    <tr>
                        <td>{{ $item->description ?? 'Transaksi' }} ({{ $item->ref_number }})</td>
                        <td class="text-right text-danger">- {{ number_format($item->credit, 0, ',', '.') }}</td>
                    </tr>
                    @php $totalOut += $item->credit; @endphp
                    @endforeach
                    <tr class="font-weight-bold bg-light">
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
    </div>
</div>
@endsection
