@extends('layouts.print')

@section('title', 'Laporan Piutang Macet')
@section('date', date('d F Y'))

@section('content')
<div class="row">
    <div class="col-12">
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
                                {{ $loan->member->name }} <span class="badge badge-info">Anggota</span>
                            @elseif($loan->nasabah)
                                {{ $loan->nasabah->name }} <span class="badge badge-warning">Nasabah</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>Rp {{ number_format($loan->jumlah_pinjaman, 0, ',', '.') }}</td>
                        <td>Rp {{ number_format($loan->remaining_balance ?? 0, 0, ',', '.') }}</td>
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
