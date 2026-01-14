@extends('layouts.print')

@section('title', 'Daftar Jaminan')
@section('date', date('d F Y'))

@section('content')
<div class="row">
    <div class="col-12">
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
