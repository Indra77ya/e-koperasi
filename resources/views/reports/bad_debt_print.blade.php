@extends('layouts.print')

@section('title', 'Laporan Piutang Macet')
@section('date', date('d F Y'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="row mb-5 py-4 border-bottom border-top">
            <div class="col-4 border-right">
                <div class="text-muted small mb-1">Total Plafon Pinjaman Macet</div>
                <div class="h4 font-weight-bold mb-0">{{ format_rupiah($totals->total_pokok) }}</div>
            </div>
            <div class="col-4 border-right">
                <div class="text-muted small mb-1">Total Saldo Macet</div>
                <div class="h4 font-weight-bold mb-0 text-red" style="color: #cd201f !important;">{{ format_rupiah($totals->total_remaining) }}</div>
            </div>
            <div class="col-4">
                <div class="text-muted small mb-1">Jumlah Pinjaman Macet</div>
                <div class="h4 font-weight-bold mb-0">{{ number_format($totals->total_count) }} Rekening</div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>No. Pinjaman</th>
                        <th>Nama Anggota/Nasabah</th>
                        <th>Jumlah Pinjaman</th>
                        <th>Sisa Pinjaman</th>
                        <th>Tanggal Macet</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                    <tr>
                        <td>{{ $loan->kode_pinjaman }}</td>
                        <td>
                            @if($loan->member)
                                <span class="badge badge-info">Anggota</span> {{ $loan->member->nama }}
                            @elseif($loan->nasabah)
                                <span class="badge badge-warning">Nasabah</span> {{ $loan->nasabah->nama }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">{{ format_rupiah($loan->jumlah_pinjaman) }}</td>
                        <td class="text-right">{{ format_rupiah($loan->remaining_balance ?? 0) }}</td>
                        <td>{{ $loan->updated_at->format('d-m-Y') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center">Tidak ada data piutang macet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
