@extends('layouts.print')

@section('title', 'Daftar Jaminan')
@section('date', date('d F Y'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="row mb-5 py-4 border-bottom border-top">
            <div class="col-4 border-right">
                <div class="text-muted small mb-1">Total Nilai Taksasi Jaminan</div>
                <div class="h4 font-weight-bold mb-0 text-primary">{{ format_rupiah($totals->total_value) }}</div>
            </div>
            <div class="col-4 border-right">
                <div class="text-muted small mb-1">Jumlah Jaminan Terdata</div>
                <div class="h4 font-weight-bold mb-0">{{ number_format($totals->total_count) }} Unit</div>
            </div>
            <div class="col-4">
                <div class="text-muted small mb-1">Rincian Per Jenis</div>
                <div class="font-weight-bold">
                    @foreach($typeCounts as $type)
                        {{ $type->jenis ?: 'N/A' }}: {{ $type->count }}{{ !$loop->last ? ',' : '' }}
                    @endforeach
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th>Jenis</th>
                        <th>Nomor</th>
                        <th>Nilai Taksiran</th>
                        <th>Pemilik</th>
                        <th>No. Pinjaman</th>
                        <th>Status</th>
                        <th>Keterangan</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($collaterals as $col)
                    <tr>
                        <td>{{ $col->jenis }}</td>
                        <td>{{ $col->nomor }}</td>
                        <td>Rp {{ number_format($col->nilai_taksasi, 0, ',', '.') }}</td>
                        <td>{{ $col->pemilik }}</td>
                        <td>{{ $col->loan ? $col->loan->loan_number : '-' }}</td>
                        <td>
                            @if($col->status == 'disimpan')
                                <span class="badge badge-success">Disimpan</span>
                            @elseif($col->status == 'dikembalikan')
                                <span class="badge badge-secondary">Dikembalikan</span>
                            @else
                                {{ ucfirst($col->status) }}
                            @endif
                        </td>
                        <td>{{ $col->keterangan }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">Tidak ada data jaminan.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
