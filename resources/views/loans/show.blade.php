@extends('layouts.app')

@section('page-title')
    Detail Pinjaman: {{ $loan->kode_pinjaman }}
@endsection

@section('content-app')
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Informasi Pinjaman</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th>Peminjam</th>
                            <td>
                                @if($loan->member)
                                    {{ $loan->member->nama }} (Anggota)
                                @elseif($loan->nasabah)
                                    {{ $loan->nasabah->nama }} (Nasabah)
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Jenis</th>
                            <td>{{ ucfirst($loan->jenis_pinjaman) }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <td>Rp {{ number_format($loan->jumlah_pinjaman, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Tenor</th>
                            <td>{{ $loan->tenor }} Bulan</td>
                        </tr>
                        <tr>
                            <th>Bunga</th>
                            <td>{{ $loan->suku_bunga }}% / thn ({{ ucfirst($loan->jenis_bunga) }})</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($loan->status == 'diajukan')
                                    <span class="badge badge-warning">Diajukan</span>
                                @elseif($loan->status == 'disetujui')
                                    <span class="badge badge-info">Disetujui</span>
                                @elseif($loan->status == 'berjalan')
                                    <span class="badge badge-primary">Berjalan</span>
                                @elseif($loan->status == 'lunas')
                                    <span class="badge badge-success">Lunas</span>
                                @else
                                    <span class="badge badge-secondary">{{ $loan->status }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>

                    <div class="mt-3">
                        @if($loan->status == 'diajukan')
                            <div class="row">
                                <div class="col-6">
                                    <form action="{{ route('loans.approve', $loan->id) }}" method="POST" onsubmit="return confirm('Setujui pinjaman ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-block">Setujui</button>
                                    </form>
                                </div>
                                <div class="col-6">
                                    <form action="{{ route('loans.reject', $loan->id) }}" method="POST" onsubmit="return confirm('Tolak pinjaman ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-block">Tolak</button>
                                    </form>
                                </div>
                            </div>
                        @endif

                        @if($loan->status == 'disetujui')
                            <form action="{{ route('loans.disburse', $loan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Cairkan dana dan buat jadwal angsuran?')">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-block mt-2">Cairkan Dana</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Jadwal Angsuran</h5>
                </div>
                <div class="card-body">
                    @if($loan->installments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Ke</th>
                                        <th>Jatuh Tempo</th>
                                        <th>Total</th>
                                        <th>Pokok</th>
                                        <th>Bunga</th>
                                        <th>Sisa</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($loan->installments as $inst)
                                        <tr>
                                            <td>{{ $inst->angsuran_ke }}</td>
                                            <td>{{ $inst->tanggal_jatuh_tempo->format('d-m-Y') }}</td>
                                            <td>Rp {{ number_format($inst->total_angsuran, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($inst->pokok, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($inst->bunga, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($inst->sisa_pinjaman, 0, ',', '.') }}</td>
                                            <td>
                                                @if($inst->status == 'lunas')
                                                    <span class="badge badge-success">Lunas</span>
                                                @else
                                                    <span class="badge badge-secondary">Belum Lunas</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($inst->status == 'belum_lunas')
                                                    <form action="{{ route('loans.installments.pay', $inst->id) }}" method="POST" onsubmit="return confirm('Konfirmasi pembayaran angsuran ke-{{ $inst->angsuran_ke }}?')">
                                                        @csrf
                                                        <button type="submit" class="btn btn-sm btn-success">Bayar</button>
                                                    </form>
                                                @else
                                                    <span class="text-muted"><i class="fe fe-check"></i> Terbayar</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">Jadwal angsuran belum dibuat (Menunggu pencairan).</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
