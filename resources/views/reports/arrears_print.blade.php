@extends('layouts.print')

@section('title', 'Laporan Tunggakan Pinjaman')
@section('date', date('d F Y'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="row mb-5 py-4 border-bottom border-top">
            <div class="col-3 border-right">
                <div class="text-muted small mb-1">Total Tunggakan Pokok</div>
                <div class="h4 font-weight-bold mb-0">{{ format_rupiah($totals->total_pokok) }}</div>
            </div>
            <div class="col-3 border-right">
                <div class="text-muted small mb-1">Total Tunggakan Bunga</div>
                <div class="h4 font-weight-bold mb-0">{{ format_rupiah($totals->total_bunga) }}</div>
            </div>
            <div class="col-3 border-right">
                <div class="text-muted small mb-1">Total Tunggakan Admin</div>
                <div class="h4 font-weight-bold mb-0">{{ format_rupiah($totals->total_admin) }}</div>
            </div>
            <div class="col-3">
                <div class="text-muted small mb-1">Total Keseluruhan</div>
                <div class="h4 font-weight-bold mb-0 text-red" style="color: #cd201f !important;">{{ format_rupiah(($totals->total_pokok + $totals->total_bunga + $totals->total_admin + $totals->total_denda)) }}</div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered text-nowrap">
                <thead>
                    <tr>
                        <th>No. Pinjaman</th>
                        <th>Nama Anggota/Nasabah</th>
                        <th class="text-right">Pokok</th>
                        <th class="text-right">Bunga</th>
                        <th class="text-right">Admin</th>
                        <th class="text-right">Denda</th>
                        <th class="text-right font-weight-bold">Total Tunggakan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($loans as $loan)
                    @php
                        $p = $loan->installments->sum('pokok');
                        $b = $loan->installments->sum('bunga');
                        $a = $loan->installments->sum('biaya_admin');
                        $d = $loan->installments->sum('denda');
                        $total = $p + $b + $a + $d;
                    @endphp
                    <tr>
                        <td>{{ $loan->kode_pinjaman }}</td>
                        <td>
                            @if($loan->member)
                                {{ $loan->member->nama }}
                            @elseif($loan->nasabah)
                                {{ $loan->nasabah->nama }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">{{ format_rupiah($p) }}</td>
                        <td class="text-right">{{ format_rupiah($b) }}</td>
                        <td class="text-right">{{ format_rupiah($a) }}</td>
                        <td class="text-right">{{ format_rupiah($d) }}</td>
                        <td class="text-right font-weight-bold">{{ format_rupiah($total) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada tunggakan saat ini.</td>
                    </tr>
                    @endforelse
                    <tr class="font-weight-bold">
                        <td colspan="2" class="text-center">GRAND TOTAL TUNGGAKAN</td>
                        <td class="text-right">{{ format_rupiah($totals->total_pokok) }}</td>
                        <td class="text-right">{{ format_rupiah($totals->total_bunga) }}</td>
                        <td class="text-right">{{ format_rupiah($totals->total_admin) }}</td>
                        <td class="text-right">{{ format_rupiah($totals->total_denda) }}</td>
                        <td class="text-right">{{ format_rupiah(($totals->total_pokok + $totals->total_bunga + $totals->total_admin + $totals->total_denda)) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
