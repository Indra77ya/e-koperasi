@extends('layouts.print')

@section('title', 'Laba Rugi (Profit & Loss)')
@section('date', date('d F Y', strtotime($startDate)) . ' - ' . date('d F Y', strtotime($endDate)))

@section('content')
<div class="row">
    <div class="col-12">
        <!-- REVENUE -->
        <h4 class="text-uppercase text-muted font-weight-bold">Pendapatan (Revenue)</h4>
        <table class="table table-sm table-striped">
            @foreach($revenues as $rev)
                @if(abs($rev->balance) > 0)
                <tr>
                    <td>{{ $rev->code }} - {{ $rev->name }}</td>
                    <td class="text-right">{{ number_format($rev->balance, 0, ',', '.') }}</td>
                </tr>
                @endif
            @endforeach
            <tr class="font-weight-bold" style="background-color: #f8f9fa;">
                <td>Total Pendapatan</td>
                <td class="text-right">{{ number_format($revenues->sum('balance'), 0, ',', '.') }}</td>
            </tr>
        </table>

        <!-- EXPENSES -->
        <h4 class="text-uppercase text-muted font-weight-bold mt-4">Beban (Expenses)</h4>
        <table class="table table-sm table-striped">
            @foreach($expenses as $exp)
                @if(abs($exp->balance) > 0)
                <tr>
                    <td>{{ $exp->code }} - {{ $exp->name }}</td>
                    <td class="text-right">{{ number_format($exp->balance, 0, ',', '.') }}</td>
                </tr>
                @endif
            @endforeach
            <tr class="font-weight-bold" style="background-color: #f8f9fa;">
                <td>Total Beban</td>
                <td class="text-right">{{ number_format($expenses->sum('balance'), 0, ',', '.') }}</td>
            </tr>
        </table>

        <!-- NET INCOME -->
        <div class="alert alert-{{ ($revenues->sum('balance') - $expenses->sum('balance')) >= 0 ? 'success' : 'danger' }} mt-4">
            <h3 class="m-0">
                Laba/Rugi Bersih
                <span class="float-right">{{ number_format($revenues->sum('balance') - $expenses->sum('balance'), 0, ',', '.') }}</span>
            </h3>
        </div>
    </div>
</div>
@endsection
