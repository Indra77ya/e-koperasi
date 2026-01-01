@extends('layouts.app')

@section('page-title')
    Tambah Jaminan - Pinjaman {{ $loan->kode_pinjaman }}
@endsection

@section('content-app')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h4 class="card-title">Tambah Jaminan</h4>
                <div class="card-options">
                    <a href="{{ route('loans.show', $loan->id) }}" class="btn btn-sm btn-secondary">Kembali</a>
                </div>
            </div>
            <div class="card-body">
                <form action="{{ route('loans.collaterals.store', $loan->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Jenis Jaminan</label>
                                <select name="jenis" class="form-control" required>
                                    <option value="BPKB">BPKB</option>
                                    <option value="Sertifikat Tanah/Bangunan">Sertifikat Tanah/Bangunan</option>
                                    <option value="SK Pegawai">SK Pegawai</option>
                                    <option value="Perhiasan">Perhiasan</option>
                                    <option value="Elektronik">Elektronik</option>
                                    <option value="Kendaraan">Kendaraan</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Nomor Identitas Jaminan (Nomor BPKB/Sertifikat/Seri)</label>
                                <input type="text" name="nomor" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Nama Pemilik</label>
                                <input type="text" name="pemilik" class="form-control">
                            </div>
                            <div class="form-group">
                                <label>Nilai Taksasi (Rp)</label>
                                <input type="number" name="nilai_taksasi" class="form-control" required min="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Lokasi Penyimpanan</label>
                                <input type="text" name="lokasi_penyimpanan" class="form-control" placeholder="Contoh: Brankas A">
                            </div>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status" class="form-control" required>
                                    <option value="disimpan">Disimpan</option>
                                    <option value="dikembalikan">Dikembalikan</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Foto Jaminan</label>
                                <input type="file" name="foto" class="form-control-file">
                                <small class="text-muted">Format: JPG, PNG. Maks 2MB.</small>
                            </div>
                            <div class="form-group">
                                <label>Dokumen Pendukung (PDF/Gambar)</label>
                                <input type="file" name="dokumen" class="form-control-file">
                                <small class="text-muted">Format: PDF, JPG, PNG. Maks 2MB.</small>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Keterangan</label>
                        <textarea name="keterangan" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn btn-primary">Simpan Jaminan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
