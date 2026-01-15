@extends('layouts.app')

@section('content-app')
<div class="row row-cards row-deck">
    <div class="col-10 offset-md-1">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('detail') }} {{ __('menu.deposit') }}</h3>
                <div class="card-options">
                    <a href="{{ route('deposits.index') }}" class="btn btn-sm btn-pill btn-secondary">{{ __('back') }}</a>
                </div>
            </div>
            <div class="card-body">
                <table class="table table-striped">
                    <tbody>
                        <tr>
                            <td class="w-25">Penyetor</td>
                            <td>
                                @if($deposit->member)
                                    {{ $deposit->member->nama }} (Anggota)
                                @elseif($deposit->nasabah)
                                    {{ $deposit->nasabah->nama }} (Nasabah)
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>{{ __('amount') }}</td>
                            <td>Rp {{ number_format($deposit->jumlah, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('note') }}</td>
                            <td>{{ $deposit->keterangan }}</td>
                        </tr>
                        <tr>
                            <td>{{ __('date') }}</td>
                            <td>{{ $deposit->created_at->format('d F Y H:i') }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
