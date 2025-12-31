@extends('layouts.app')

@section('page-title')
    Detail Nasabah: {{ $nasabah->nama }}
@endsection

@section('content-app')
<div class="row row-cards">
    <div class="col-lg-4">
        <div class="card">
            <div class="card-body">
                <div class="media">
                    <span class="avatar avatar-xxl mr-5" style="background-image: url({{ asset('demo/faces/male/21.jpg') }})"></span>
                    <div class="media-body">
                        <h4 class="m-0">{{ $nasabah->nama }}</h4>
                        <p class="text-muted mb-0">{{ $nasabah->pekerjaan ?? 'Belum ada pekerjaan' }}</p>
                        <ul class="social-links list-inline mb-0 mt-2">
                            <li class="list-inline-item">
                                @php
                                    $badges = [
                                        'aktif' => 'success',
                                        'non-aktif' => 'secondary',
                                        'blacklist' => 'danger',
                                        'berisiko' => 'warning',
                                    ];
                                    $color = $badges[$nasabah->status] ?? 'secondary';
                                @endphp
                                <span class="badge badge-{{ $color }}">{{ ucfirst($nasabah->status) }}</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data Pribadi</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">NIK</label>
                    <div class="form-control-plaintext">{{ $nasabah->nik }}</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Tempat, Tanggal Lahir</label>
                    <div class="form-control-plaintext">{{ $nasabah->tempat_lahir ?? '-' }}, {{ optional($nasabah->tanggal_lahir)->format('d F Y') ?? '-' }}</div>
                </div>
                <div class="form-group">
                    <label class="form-label">No. HP</label>
                    <div class="form-control-plaintext">{{ $nasabah->no_hp ?? '-' }}</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Alamat</label>
                    <div class="form-control-plaintext">{{ $nasabah->alamat ?? '-' }}</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Data Pekerjaan & Usaha</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Pekerjaan</label>
                    <div class="form-control-plaintext">{{ $nasabah->pekerjaan ?? '-' }}</div>
                </div>
                <div class="form-group">
                    <label class="form-label">Detail Usaha</label>
                    <div class="form-control-plaintext">{{ $nasabah->detail_usaha ?? '-' }}</div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Dokumen</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label">File KTP</label>
                            @if($nasabah->file_ktp)
                                <a href="{{ asset('storage/' . $nasabah->file_ktp) }}" target="_blank" class="btn btn-outline-primary"><i class="fe fe-download"></i> Unduh / Lihat KTP</a>
                            @else
                                <span class="text-muted">Tidak ada file</span>
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group">
                            <label class="form-label">File Jaminan</label>
                            @if($nasabah->file_jaminan)
                                <a href="{{ asset('storage/' . $nasabah->file_jaminan) }}" target="_blank" class="btn btn-outline-primary"><i class="fe fe-download"></i> Unduh / Lihat Jaminan</a>
                            @else
                                <span class="text-muted">Tidak ada file</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Riwayat Pinjaman & Pembayaran</h3>
            </div>
            <div class="card-body">
                <p class="text-muted">Fitur Riwayat Pinjaman & Pembayaran belum tersedia (placeholder).</p>
                {{-- Placeholder table for future implementation --}}
                <div class="table-responsive">
                    <table class="table card-table table-vcenter text-nowrap">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jenis</th>
                                <th>Jumlah</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td colspan="4" class="text-center">Belum ada data</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="card-footer text-right">
            <a href="{{ route('nasabah.edit', $nasabah->id) }}" class="btn btn-primary"><i class="fe fe-edit"></i> Edit Nasabah</a>
        </div>
    </div>
</div>
@endsection
