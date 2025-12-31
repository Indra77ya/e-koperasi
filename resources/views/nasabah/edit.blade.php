@extends('layouts.app')

@section('page-title')
    Edit Data Nasabah
@endsection

@section('content-app')
<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Form Edit Nasabah</h3>
                <div class="card-options">
                    <a href="{{ route('nasabah.index') }}" class="btn btn-sm btn-secondary"><i class="fe fe-arrow-left"></i> Kembali</a>
                </div>
            </div>
            <div class="card-body">
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('nasabah.update', $nasabah->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="card-title">Data Pribadi</h4>
                            <div class="form-group">
                                <label class="form-label">NIK <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nik" value="{{ old('nik', $nasabah->nik) }}" placeholder="Masukkan NIK" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama" value="{{ old('nama', $nasabah->nama) }}" placeholder="Masukkan Nama Lengkap" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" class="form-control" name="tempat_lahir" value="{{ old('tempat_lahir', $nasabah->tempat_lahir) }}" placeholder="Masukkan Tempat Lahir">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="text" class="form-control" name="tanggal_lahir" id="datepicker" autocomplete="off" value="{{ old('tanggal_lahir', optional($nasabah->tanggal_lahir)->format('d/m/Y')) }}" placeholder="dd/mm/yyyy">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nomor HP</label>
                                <input type="text" class="form-control" name="no_hp" value="{{ old('no_hp', $nasabah->no_hp) }}" placeholder="Masukkan Nomor HP">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Alamat Lengkap</label>
                                <textarea class="form-control" name="alamat" rows="3" placeholder="Masukkan Alamat Lengkap">{{ old('alamat', $nasabah->alamat) }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4 class="card-title">Data Pekerjaan & Usaha</h4>
                            <div class="form-group">
                                <label class="form-label">Pekerjaan</label>
                                <input type="text" class="form-control" name="pekerjaan" value="{{ old('pekerjaan', $nasabah->pekerjaan) }}" placeholder="Masukkan Pekerjaan">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Detail Usaha</label>
                                <textarea class="form-control" name="detail_usaha" rows="3" placeholder="Deskripsi usaha, lokasi, dll">{{ old('detail_usaha', $nasabah->detail_usaha) }}</textarea>
                            </div>

                            <h4 class="card-title mt-4">Dokumen & Status</h4>
                            <div class="form-group">
                                <label class="form-label">Upload KTP</label>
                                @if($nasabah->file_ktp)
                                    <div class="mb-2">
                                        <a href="{{ asset('storage/' . $nasabah->file_ktp) }}" target="_blank" class="btn btn-sm btn-info">Lihat File Saat Ini</a>
                                    </div>
                                @endif
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="file_ktp">
                                    <label class="custom-file-label">Ganti file (Opsional)</label>
                                </div>
                                <small class="form-text text-muted">Format: jpg, png, pdf. Max: 2MB</small>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Upload Jaminan</label>
                                @if($nasabah->file_jaminan)
                                    <div class="mb-2">
                                        <a href="{{ asset('storage/' . $nasabah->file_jaminan) }}" target="_blank" class="btn btn-sm btn-info">Lihat File Saat Ini</a>
                                    </div>
                                @endif
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="file_jaminan">
                                    <label class="custom-file-label">Ganti file (Opsional)</label>
                                </div>
                                <small class="form-text text-muted">Format: jpg, png, pdf. Max: 2MB</small>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control custom-select" name="status" required>
                                    <option value="aktif" {{ old('status', $nasabah->status) == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="non-aktif" {{ old('status', $nasabah->status) == 'non-aktif' ? 'selected' : '' }}>Non-Aktif</option>
                                    <option value="blacklist" {{ old('status', $nasabah->status) == 'blacklist' ? 'selected' : '' }}>Blacklist</option>
                                    <option value="berisiko" {{ old('status', $nasabah->status) == 'berisiko' ? 'selected' : '' }}>Berisiko</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    require(['jquery', 'datepicker'], function($) {
        $('.custom-file-input').on('change', function() {
            let fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').addClass("selected").html(fileName);
        });

        $('#datepicker').datepicker({
            format: 'dd/mm/yyyy',
            autoclose: true
        });
    });
</script>
@endsection
