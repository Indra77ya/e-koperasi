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
                                        <th>Tanggal Pinjam</th>
                                        <th>Jatuh Tempo</th>
                                        <th>Jumlah</th>
                                        <th>Status</th>
                                        <th>Catatan</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($nasabah->loans as $loan)
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
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Belum ada data pinjaman</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
