@extends('layouts.app')

@section('content-app')
<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Laba Rugi (Profit & Loss)</h3>
                <div class="card-options">
                    <form action="" method="GET" class="d-flex align-items-center">
                        <label class="mr-2">Dari:</label>
                        <input type="date" name="start_date" class="form-control mr-2" value="{{ $startDate }}" onchange="this.form.submit()">
                        <label class="mr-2">Sampai:</label>
                        <input type="date" name="end_date" class="form-control" value="{{ $endDate }}" onchange="this.form.submit()">
                    </form>
                    <a href="{{ route('accounting.reports.laba_rugi', ['print' => true] + request()->query()) }}" target="_blank" class="btn btn-primary btn-pill ml-2 text-nowrap">
                        <i class="fa fa-print"></i> PDF/Print
                    </a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <!-- REVENUE -->
                        <h4 class="text-uppercase text-muted font-weight-bold">Pendapatan (Revenue)</h4>
                        <table class="table table-sm">
                            @foreach($revenues as $rev)
                                @if(abs($rev->balance) > 0)
                                <tr>
                                    <td>{{ $rev->code }} - {{ $rev->name }}</td>
                                    <td class="text-right">{{ number_format($rev->balance, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                            @endforeach
                            <tr class="font-weight-bold bg-light">
                                <td>Total Pendapatan</td>
                                <td class="text-right">{{ number_format($revenues->sum('balance'), 0, ',', '.') }}</td>
                            </tr>
                        </table>

                        <!-- EXPENSES -->
                        <h4 class="text-uppercase text-muted font-weight-bold mt-4">Beban (Expenses)</h4>
                        <table class="table table-sm">
                            @foreach($expenses as $exp)
                                @if(abs($exp->balance) > 0)
                                <tr>
                                    <td>{{ $exp->code }} - {{ $exp->name }}</td>
                                    <td class="text-right">{{ number_format($exp->balance, 0, ',', '.') }}</td>
                                </tr>
                                @endif
                            @endforeach
                            <tr class="font-weight-bold bg-light">
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
            </div>
        </div>
    </div>
</div>
@endsection
