@extends('layouts.app')

@section('page-title')
    Edit Jaminan - Pinjaman {{ $loan->kode_pinjaman }}
@endsection

@section('content-app')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Edit Jaminan</h4>
                <div class="card-options">
                    <a href="{{ route('loans.show', $loan->id) }}" class="btn btn-sm btn-secondary">Kembali</a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('loans.collaterals.update', [$loan->id, $collateral->id]) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenis Jaminan</label>
                                <select name="jenis" class="form-control" required>
                                    <option value="BPKB" {{ $collateral->jenis == 'BPKB' ? 'selected' : '' }}>BPKB</option>
                                    <option value="Sertifikat Tanah/Bangunan" {{ $collateral->jenis == 'Sertifikat Tanah/Bangunan' ? 'selected' : '' }}>Sertifikat Tanah/Bangunan</option>
                                    <option value="SK Pegawai" {{ $collateral->jenis == 'SK Pegawai' ? 'selected' : '' }}>SK Pegawai</option>
                                    <option value="Perhiasan" {{ $collateral->jenis == 'Perhiasan' ? 'selected' : '' }}>Perhiasan</option>
                                    <option value="Elektronik" {{ $collateral->jenis == 'Elektronik' ? 'selected' : '' }}>Elektronik</option>
                                    <option value="Kendaraan" {{ $collateral->jenis == 'Kendaraan' ? 'selected' : '' }}>Kendaraan</option>
                                    <option value="Lainnya" {{ $collateral->jenis == 'Lainnya' ? 'selected' : '' }}>Lainnya</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Nomor Identitas Jaminan</label>
                                <input type="text" name="nomor" class="form-control" value="{{ $collateral->nomor }}">
                            </div>
                            <div class="form-group">
                                <label>Nama Pemilik</label>
                                <input type="text" name="pemilik" class="form-control" value="{{ $collateral->pemilik }}">
                            </div>
                            <div class="form-group">
                                <label>Nilai Taksasi (Rp)</label>
                                <input type="number" name="nilai_taksasi" class="form-control" required min="0" value="{{ $collateral->nilai_taksasi }}">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Lokasi Penyimpanan</label>
                                <input type="text" name="lokasi_penyimpanan" class="form-control" value="{{ $collateral->lokasi_penyimpanan }}">
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="disimpan" {{ $collateral->status == 'disimpan' ? 'selected' : '' }}>Disimpan</option>
                                    <option value="dikembalikan" {{ $collateral->status == 'dikembalikan' ? 'selected' : '' }}>Dikembalikan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Foto Jaminan</label>
                                @if($collateral->foto)
                                    <div class="mb-2">
                                        <a href="{{ asset('storage/' . $collateral->foto) }}" target="_blank">Lihat Foto Saat Ini</a>
                                    </div>
                                @endif
                                <input type="file" name="foto" class="form-control-file">
                            </div>
                            <div class="form-group">
                                <label>Dokumen Pendukung</label>
                                @if($collateral->dokumen)
                                    <div class="mb-2">
                                        <a href="{{ asset('storage/' . $collateral->dokumen) }}" target="_blank">Lihat Dokumen Saat Ini</a>
                                    </div>
                                @endif
                                <input type="file" name="dokumen" class="form-control-file">
                            </div>
                        </div>
                    </div>

                    @if($collateral->status == 'dikembalikan')
                    <div class="row">
                        <div class="col-md-6">
                             <div class="form-group">
                                <label>Diserahkan Kepada</label>
                                <input type="text" name="diserahkan_kepada" class="form-control" value="{{ $collateral->diserahkan_kepada }}">
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3">{{ $collateral->keterangan }}</textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Update Jaminan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
