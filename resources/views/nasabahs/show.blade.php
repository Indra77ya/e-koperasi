@extends('layouts.app')

@section('content-app')
<div class="row row-cards row-deck">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detail Nasabah</h3>
                <div class="card-options">
                    <a href="{{ route('nasabahs.index') }}" class="btn btn-sm btn-pill btn-secondary">Kembali</a>
                    <a href="{{ route('nasabahs.edit', $nasabah->id) }}" class="btn btn-sm btn-pill btn-primary ml-2">Edit</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Informasi Pribadi</h4>
                        <table class="table table-striped">
                            <tr>
                                <th>NIK</th>
                                <td>{{ $nasabah->nik }}</td>
                            </tr>
                            <tr>
                                <th>Nama Lengkap</th>
                                <td>{{ $nasabah->nama }}</td>
                            </tr>
                            <tr>
                                <th>No. HP</th>
                                <td>{{ $nasabah->no_hp }}</td>
                            </tr>
                            <tr>
                                <th>Alamat</th>
                                <td>{{ $nasabah->alamat }}</td>
                            </tr>
                            <tr>
                                <th>Status Risiko</th>
                                <td>
                                    @if($nasabah->status == 'aman')
                                        <span class="tag tag-success">Aman</span>
                                    @elseif($nasabah->status == 'berisiko')
                                        <span class="tag tag-warning">Berisiko</span>
                                    @else
                                        <span class="tag tag-danger">Blacklist</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h4>Informasi Pekerjaan</h4>
                        <table class="table table-striped">
                            <tr>
                                <th>Pekerjaan</th>
                                <td>{{ $nasabah->pekerjaan }}</td>
                            </tr>
                            <tr>
                                <th>Usaha</th>
                                <td>{{ $nasabah->usaha }}</td>
                            </tr>
                        </table>

                        <h4 class="mt-4">Dokumen</h4>
                        <table class="table table-striped">
                            <tr>
                                <th>KTP</th>
                                <td>
                                    @if($nasabah->file_ktp)
                                        <a href="{{ asset('storage/' . $nasabah->file_ktp) }}" target="_blank" class="btn btn-sm btn-info">Lihat Dokumen</a>
                                    @else
                                        <span class="text-muted">Tidak ada dokumen</span>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Jaminan</th>
                                <td>
                                    @if($nasabah->file_jaminan)
                                        <a href="{{ asset('storage/' . $nasabah->file_jaminan) }}" target="_blank" class="btn btn-sm btn-info">Lihat Dokumen</a>
                                    @else
                                        <span class="text-muted">Tidak ada dokumen</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <div class="row mt-4">
                    <div class="col-12">
                        <h4>Riwayat Pinjaman & Pembayaran</h4>
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Kode</th>
                                        <th>Tgl Pengajuan</th>
                                        <th>Jumlah</th>
                                        <th>Bunga</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($nasabah->pinjaman as $loan)
                                    <tr>
                                        <td>{{ $loan->kode_pinjaman }}</td>
                                        <td>{{ $loan->tanggal_pengajuan->format('Y-m-d') }}</td>
                                        <td>Rp {{ number_format($loan->jumlah_pinjaman, 0, ',', '.') }}</td>
                                        <td>{{ $loan->suku_bunga }}% ({{ ucfirst($loan->jenis_bunga) }})</td>
                                        <td>
                                            @if($loan->status == 'lunas')
                                                <span class="tag tag-success">Lunas</span>
                                            @elseif($loan->status == 'berjalan')
                                                <span class="tag tag-primary">Berjalan</span>
                                            @elseif($loan->status == 'disetujui')
                                                <span class="tag tag-info">Disetujui</span>
                                            @elseif($loan->status == 'ditolak')
                                                <span class="tag tag-danger">Ditolak</span>
                                            @else
                                                <span class="tag tag-warning">Diajukan</span>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ route('loans.show', $loan->id) }}" class="btn btn-sm btn-secondary">Detail</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted">Belum ada data pinjaman (Modul Baru)</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Legacy Loans if any -->
                        @if($nasabah->loans->count() > 0)
                        <h5 class="mt-4 text-muted">Riwayat Pinjaman (Legacy)</h5>
                        <div class="table-responsive">
                            <table class="table card-table table-vcenter text-nowrap text-muted">
                                <thead>
                                    <tr>
                                        <th>Tanggal Pinjam</th>
                                        <th>Jatuh Tempo</th>
                                        <th>Jumlah</th>
                                        <th>Status</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($nasabah->loans as $loan)
                                    <tr>
                                        <td>{{ $loan->loan_date }}</td>
                                        <td>{{ $loan->due_date }}</td>
                                        <td>{{ number_format($loan->amount, 0, ',', '.') }}</td>
                                        <td>
                                            @if($loan->status == 'paid')
                                                <span class="tag tag-success">Lunas</span>
                                            @elseif($loan->status == 'overdue')
                                                <span class="tag tag-danger">Terlambat</span>
                                            @else
                                                <span class="tag tag-warning">Pending</span>
                                            @endif
                                        </td>
                                        <td>{{ $loan->notes }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
