@extends('layouts.app')

@section('page-title')
    Laporan Tunggakan Pinjaman
@endsection

@section('content-app')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h3 class="card-title">Tunggakan Pinjaman (P+B+A+D)</h3>
                <div class="card-options">
                    <a href="{{ route('reports.arrears', ['export' => 1]) }}" class="btn btn-success btn-pill mr-2">
                        <i class="fa fa-file-excel-o"></i> Export Excel
                    </a>
                    <a href="{{ route('reports.arrears', ['print' => true] + request()->query()) }}" target="_blank" class="btn btn-secondary btn-pill">
                        <i class="fa fa-print"></i> PDF/Print
                    </a>
                </div>
            </div>
            <div class="card-body pt-0">
                <div class="row mb-5 py-4 border-bottom">
                    <div class="col-6 col-sm-3 border-right">
                        <div class="text-muted small mb-1">Total Tunggakan Pokok</div>
                        <div class="h4 font-weight-bold mb-0">{{ format_rupiah($totals->total_pokok) }}</div>
                    </div>
                    <div class="col-6 col-sm-3 border-right">
                        <div class="text-muted small mb-1">Total Tunggakan Bunga</div>
                        <div class="h4 font-weight-bold mb-0">{{ format_rupiah($totals->total_bunga) }}</div>
                    </div>
                    <div class="col-6 col-sm-3 border-right">
                        <div class="text-muted small mb-1">Total Tunggakan Admin</div>
                        <div class="h4 font-weight-bold mb-0">{{ format_rupiah($totals->total_admin) }}</div>
                    </div>
                    <div class="col-6 col-sm-3">
                        <div class="text-muted small mb-1">Total Keseluruhan</div>
                        <div class="h4 font-weight-bold mb-0 text-red">{{ format_rupiah(($totals->total_pokok + $totals->total_bunga + $totals->total_admin + $totals->total_denda)) }}</div>
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
                                <th class="text-right">Admin (6 Bln)</th>
                                <th class="text-right">Denda</th>
                                <th class="text-right font-weight-bold">Total Tunggakan</th>
                                <th class="text-center">Aksi</th>
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
                                        <span class="badge badge-info">Anggota</span> {{ $loan->member->nama }}
                                    @elseif($loan->nasabah)
                                        <span class="badge badge-warning">Nasabah</span> {{ $loan->nasabah->nama }}
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="text-right">{{ format_rupiah($p) }}</td>
                                <td class="text-right">{{ format_rupiah($b) }}</td>
                                <td class="text-right">{{ format_rupiah($a) }}</td>
                                <td class="text-right">{{ format_rupiah($d) }}</td>
                                <td class="text-right font-weight-bold text-red">{{ format_rupiah($total) }}</td>
                                <td class="text-center">
                                    <a href="{{ route('loans.show', $loan->id) }}" class="btn btn-info btn-sm">Detail</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center">Tidak ada tunggakan saat ini.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $loans->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
