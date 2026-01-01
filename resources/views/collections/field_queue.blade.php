@extends('layouts.app')

@section('page-title')
    Antrian Penagihan Lapangan
@endsection

@section('content-app')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Daftar Kunjungan Lapangan</h3>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap">
                    <thead>
                        <tr>
                            <th>Tanggal Rencana</th>
                            <th>Peminjam</th>
                            <th>Petugas</th>
                            <th>Status</th>
                            <th>Catatan Tugas</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($queue as $item)
                            <tr>
                                <td>{{ $item->tanggal_rencana_kunjungan->format('d/m/Y') }}</td>
                                <td>
                                    @if($item->loan->member)
                                        {{ $item->loan->member->nama }} (Anggota)
                                    @elseif($item->loan->nasabah)
                                        {{ $item->loan->nasabah->nama }} (Nasabah)
                                    @endif
                                    <br>
                                    <small class="text-muted">{{ $item->loan->kode_pinjaman }}</small>
                                </td>
                                <td>{{ $item->petugas ? $item->petugas->name : '-' }}</td>
                                <td>
                                    <span class="badge badge-{{ $item->status == 'selesai' ? 'success' : ($item->status == 'batal' ? 'secondary' : 'warning') }}">
                                        {{ ucfirst($item->status) }}
                                    </span>
                                </td>
                                <td>{{ $item->catatan_tugas }}</td>
                                <td>
                                    <a href="{{ route('loans.show', $item->pinjaman_id) }}" class="btn btn-sm btn-primary">Lihat Pinjaman</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">Belum ada antrian kunjungan.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
