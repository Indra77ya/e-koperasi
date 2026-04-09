@extends('layouts.print')

@section('title', 'Daftar Penagihan DC')
@section('date', date('d F Y'))

@section('content')
<div class="row">
    <div class="col-12">
        @if($status)
            <div class="mb-4">
                <strong>Filter Status:</strong> <span class="badge badge-info">{{ $status }}</span>
            </div>
        @endif

        @forelse($groupedLoans as $area => $loans)
            <div class="card mb-4" style="break-inside: avoid;">
                <div class="card-header bg-light">
                    <h3 class="card-title">Area: {{ $area }}</h3>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered table-vcenter">
                        <thead>
                            <tr>
                                <th style="width: 15%">Kode</th>
                                <th style="width: 25%">Peminjam</th>
                                <th style="width: 30%">Alamat</th>
                                <th style="width: 15%">Terlambat</th>
                                <th style="width: 15%">Total Tagihan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($loans as $loan)
                                <tr>
                                    <td>{{ $loan->kode_pinjaman }}</td>
                                    <td>
                                        <strong>{{ $loan->member ? $loan->member->nama : ($loan->nasabah ? $loan->nasabah->nama : '-') }}</strong><br>
                                        <small>{{ $loan->member ? $loan->member->no_hp : ($loan->nasabah ? $loan->nasabah->no_hp : '-') }}</small>
                                    </td>
                                    <td>{{ $loan->member ? $loan->member->alamat : ($loan->nasabah ? $loan->nasabah->alamat : '-') }}</td>
                                    <td>{{ $loan->days_past_due }} Hari</td>
                                    <td>
                                        @php
                                            $totalTagihan = $loan->installments->where('status', 'belum_lunas')->sum('total_angsuran');
                                        @endphp
                                        Rp {{ number_format($totalTagihan, 0, ',', '.') }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="text-center py-5">
                <p class="text-muted">Tidak ada data penagihan.</p>
            </div>
        @endforelse
    </div>
</div>

<style>
    @media print {
        .card {
            border: 1px solid #dee2e6;
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #f8f9fa !important;
            -webkit-print-color-adjust: exact;
        }
    }
</style>
@endsection
