@extends('layouts.app')

@section('content-app')
<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Statement of Cash Flows (Arus Kas)</h3>
                <div class="card-options">
                    <form action="" method="GET" class="d-flex align-items-center">
                        <label class="mr-2">From:</label>
                        <input type="date" name="start_date" class="form-control mr-2" value="{{ $startDate }}" onchange="this.form.submit()">
                        <label class="mr-2">To:</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" onchange="this.form.submit()">
                    </form>
                </div>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    This is a simplified Direct Method cash flow statement based on Cash/Bank account movements.
                </div>

                <h4 class="text-uppercase text-muted font-weight-bold">Cash In (Penerimaan Kas)</h4>
                <table class="table table-sm">
                    @php $totalIn = 0; @endphp
                    @foreach($cashInItems as $item)
                    <tr>
                        <td>{{ $item->description ?? 'Transaction' }} ({{ $item->ref_number }})</td>
                        <td class="text-right text-success">+ {{ number_format($item->debit, 2) }}</td>
                    </tr>
                    @php $totalIn += $item->debit; @endphp
                    @endforeach
                    <tr class="font-weight-bold bg-light">
                        <td>Total Cash In</td>
                        <td class="text-right">{{ number_format($totalIn, 2) }}</td>
                    </tr>
                </table>

                <h4 class="text-uppercase text-muted font-weight-bold mt-4">Cash Out (Pengeluaran Kas)</h4>
                <table class="table table-sm">
                    @php $totalOut = 0; @endphp
                    @foreach($cashOutItems as $item)
                    <tr>
                        <td>{{ $item->description ?? 'Transaction' }} ({{ $item->ref_number }})</td>
                        <td class="text-right text-danger">- {{ number_format($item->credit, 2) }}</td>
                    </tr>
                    @php $totalOut += $item->credit; @endphp
                    @endforeach
                    <tr class="font-weight-bold bg-light">
                        <td>Total Cash Out</td>
                        <td class="text-right">{{ number_format($totalOut, 2) }}</td>
                    </tr>
                </table>

                <div class="alert alert-{{ ($totalIn - $totalOut) >= 0 ? 'success' : 'warning' }} mt-4">
                    <h3 class="m-0">
                        Net Cash Flow
                        <span class="float-right">{{ number_format($totalIn - $totalOut, 2) }}</span>
                    </h3>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
