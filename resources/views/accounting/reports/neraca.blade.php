@extends('layouts.app')

@section('content-app')
<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Neraca (Balance Sheet)</h3>
                <div class="card-options">
                    <form action="" method="GET" class="d-flex align-items-center">
                        <label class="mr-2">Per Tanggal:</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" onchange="this.form.submit()">
                    </form>
                     <a href="{{ route('accounting.reports.neraca', ['print' => true] + request()->query()) }}" target="_blank" class="btn btn-secondary btn-pill ml-2 text-nowrap">
                        <i class="fa fa-print"></i> PDF/Print
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- ASSETS -->
                    <div class="col-md-6">
                        <h4 class="text-uppercase text-muted font-weight-bold">Aset (Assets)</h4>
                        <table class="table table-sm">
                            @foreach($assets as $asset)
                                @if(abs($asset->balance) > 0)
                                <tr>
                                    <td>{{ $asset->code }} - {{ $asset->name }}</td>
                                    <td class="text-right">{{ number_format($asset->balance, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                            @endforeach
                            <tr class="font-weight-bold bg-light">
                                <td>Total Aset</td>
                                <td class="text-right">{{ number_format($assets->sum('balance'), 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>

                    <!-- LIABILITIES & EQUITY -->
                    <div class="col-md-6">
                        <h4 class="text-uppercase text-muted font-weight-bold">Liabilitas (Liabilities)</h4>
                        <table class="table table-sm">
                            @foreach($liabilities as $liab)
                                @if(abs($liab->balance) > 0)
                                <tr>
                                    <td>{{ $liab->code }} - {{ $liab->name }}</td>
                                    <td class="text-right">{{ number_format($liab->balance, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                            @endforeach
                            <tr class="font-weight-bold bg-light">
                                <td>Total Liabilitas</td>
                                <td class="text-right">{{ number_format($liabilities->sum('balance'), 0, ',', '.') }}</td>
                            </tr>
                        </table>

                        <h4 class="text-uppercase text-muted font-weight-bold mt-4">Ekuitas (Equity)</h4>
                        <table class="table table-sm">
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
                            <tr class="font-weight-bold bg-light">
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
            </div>
        </div>
    </div>
</div>
@endsection
