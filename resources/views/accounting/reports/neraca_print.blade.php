@extends('layouts.print')

@section('title', 'Neraca (Balance Sheet)')
@section('date', 'Per Tanggal: ' . date('d F Y', strtotime($endDate)))

@section('content')
<div class="row">
    <!-- ASSETS -->
    <div class="col-6">
        <h4 class="text-uppercase text-muted font-weight-bold">Aset (Assets)</h4>
        <table class="table table-sm table-striped">
            @foreach($assets as $asset)
                @if(abs($asset->balance) > 0)
                <tr>
                    <td>{{ $asset->code }} - {{ $asset->name }}</td>
                    <td class="text-right">{{ number_format($asset->balance, 0, ',', '.') }}</td>
                </tr>
                @endif
            @endforeach
            <tr class="font-weight-bold" style="background-color: #f8f9fa;">
                <td>Total Aset</td>
                <td class="text-right">{{ number_format($assets->sum('balance'), 0, ',', '.') }}</td>
            </tr>
        </table>
    </div>

    <!-- LIABILITIES & EQUITY -->
    <div class="col-6">
        <h4 class="text-uppercase text-muted font-weight-bold">Liabilitas (Liabilities)</h4>
        <table class="table table-sm table-striped">
            @foreach($liabilities as $liab)
                @if(abs($liab->balance) > 0)
                <tr>
                    <td>{{ $liab->code }} - {{ $liab->name }}</td>
                    <td class="text-right">{{ number_format($liab->balance, 0, ',', '.') }}</td>
                </tr>
                @endif
            @endforeach
            <tr class="font-weight-bold" style="background-color: #f8f9fa;">
                <td>Total Liabilitas</td>
                <td class="text-right">{{ number_format($liabilities->sum('balance'), 0, ',', '.') }}</td>
            </tr>
        </table>

        <h4 class="text-uppercase text-muted font-weight-bold mt-4">Ekuitas (Equity)</h4>
        <table class="table table-sm table-striped">
            @foreach($equities as $equity)
                @if(abs($equity->balance) > 0)
                <tr>
                    <td>{{ $equity->code }} - {{ $equity->name }}</td>
                    <td class="text-right">{{ number_format($equity->balance, 0, ',', '.') }}</td>
                </tr>
                @endif
            @endforeach
            <tr>
                <td>Laba/Rugi Berjalan</td>
                <td class="text-right">{{ number_format($currentEarnings, 0, ',', '.') }}</td>
            </tr>
            <tr class="font-weight-bold" style="background-color: #f8f9fa;">
                <td>Total Ekuitas</td>
                <td class="text-right">{{ number_format($equities->sum('balance') + $currentEarnings, 0, ',', '.') }}</td>
            </tr>
        </table>

        <div class="alert alert-secondary mt-3">
            <strong>Total Liabilitas & Ekuitas:</strong>
            <span class="float-right font-weight-bold">{{ number_format($liabilities->sum('balance') + $equities->sum('balance') + $currentEarnings, 0, ',', '.') }}</span>
        </div>
    </div>
</div>
@endsection
