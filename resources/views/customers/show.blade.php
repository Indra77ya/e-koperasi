@extends('layouts.app')

@section('content-app')
<div class="row row-cards row-deck">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Detail Nasabah</h3>
                <div class="card-options">
                    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-pill btn-secondary">Kembali</a>
                    <a href="{{ route('customers.edit', $customer->id) }}" class="btn btn-sm btn-pill btn-primary ml-2">Edit</a>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Informasi Pribadi</h4>
                        <table class="table table-striped">
                            <tr>
                                <th>NIK</th>
                                <td>{{ $customer->nik }}</td>
                            </tr>
                            <tr>
                                <th>Nama</th>
                                <td>{{ $customer->nama }}</td>
                            </tr>
                            <tr>
                                <th>Alamat</th>
                                <td>{{ $customer->alamat }}</td>
                            </tr>
                            <tr>
                                <th>No. HP</th>
                                <td>{{ $customer->no_hp }}</td>
                            </tr>
                            <tr>
                                <th>Pekerjaan</th>
                                <td>{{ $customer->pekerjaan }}</td>
                            </tr>
                            <tr>
                                <th>Status Risiko</th>
                                <td>
                                    @if($customer->status_risiko == 'safe')
                                        <span class="badge badge-success">Aman</span>
                                    @elseif($customer->status_risiko == 'warning')
                                        <span class="badge badge-warning">Peringatan</span>
                                    @else
                                        <span class="badge badge-danger">Blacklist</span>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h4>Info Bisnis & Dokumen</h4>
                        <table class="table table-striped">
                            <tr>
                                <th>Info Bisnis</th>
                                <td>{{ $customer->info_bisnis ?? '-' }}</td>
                            </tr>
                            <tr>
                                <th>File KTP</th>
                                <td>
                                    @if ($customer->file_ktp)
                                        <a href="{{ Storage::url($customer->file_ktp) }}" target="_blank" class="btn btn-sm btn-info">Lihat KTP</a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>File Jaminan</th>
                                <td>
                                    @if ($customer->file_jaminan)
                                        <a href="{{ Storage::url($customer->file_jaminan) }}" target="_blank" class="btn btn-sm btn-info">Lihat Jaminan</a>
                                    @else
                                        -
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>

                <hr>

                <h4>Riwayat Pinjaman</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Jumlah</th>
                                <th>Jatuh Tempo</th>
                                <th>Status</th>
                                <th>Keterangan</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($customer->loans as $loan)
                                <tr>
                                    <td>{{ $loan->tanggal_pinjaman }}</td>
                                    <td>{{ number_format($loan->jumlah_pinjaman, 0, ',', '.') }}</td>
                                    <td>{{ $loan->tanggal_jatuh_tempo }}</td>
                                    <td>{{ $loan->status }}</td>
                                    <td>{{ $loan->keterangan }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center">Belum ada riwayat pinjaman.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</div>
@endsection
