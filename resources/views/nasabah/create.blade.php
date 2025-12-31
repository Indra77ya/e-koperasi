@extends('layouts.app')

@section('page-title')
    Tambah Data Nasabah
@endsection

@section('content-app')
<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Form Tambah Nasabah</h3>
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

                <form action="{{ route('nasabah.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <h4 class="card-title">Data Pribadi</h4>
                            <div class="form-group">
                                <label class="form-label">NIK <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nik" value="{{ old('nik') }}" placeholder="Masukkan NIK" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="nama" value="{{ old('nama') }}" placeholder="Masukkan Nama Lengkap" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tempat Lahir</label>
                                <input type="text" class="form-control" name="tempat_lahir" value="{{ old('tempat_lahir') }}" placeholder="Masukkan Tempat Lahir">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tanggal Lahir</label>
                                <input type="text" class="form-control" name="tanggal_lahir" id="datepicker" autocomplete="off" value="{{ old('tanggal_lahir') }}" placeholder="dd/mm/yyyy">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Nomor HP</label>
                                <input type="text" class="form-control" name="no_hp" value="{{ old('no_hp') }}" placeholder="Masukkan Nomor HP">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Alamat Lengkap</label>
                                <textarea class="form-control" name="alamat" rows="3" placeholder="Masukkan Alamat Lengkap">{{ old('alamat') }}</textarea>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <h4 class="card-title">Data Pekerjaan & Usaha</h4>
                            <div class="form-group">
                                <label class="form-label">Pekerjaan</label>
                                <input type="text" class="form-control" name="pekerjaan" value="{{ old('pekerjaan') }}" placeholder="Masukkan Pekerjaan">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Detail Usaha</label>
                                <textarea class="form-control" name="detail_usaha" rows="3" placeholder="Deskripsi usaha, lokasi, dll">{{ old('detail_usaha') }}</textarea>
                            </div>

                            <h4 class="card-title mt-4">Dokumen & Status</h4>
                            <div class="form-group">
                                <label class="form-label">Upload KTP</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="file_ktp">
                                    <label class="custom-file-label">Pilih file</label>
                                </div>
                                <small class="form-text text-muted">Format: jpg, png, pdf. Max: 2MB</small>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Upload Jaminan</label>
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="file_jaminan">
                                    <label class="custom-file-label">Pilih file</label>
                                </div>
                                <small class="form-text text-muted">Format: jpg, png, pdf. Max: 2MB</small>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Status <span class="text-danger">*</span></label>
                                <select class="form-control custom-select" name="status" required>
                                    <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="non-aktif" {{ old('status') == 'non-aktif' ? 'selected' : '' }}>Non-Aktif</option>
                                    <option value="blacklist" {{ old('status') == 'blacklist' ? 'selected' : '' }}>Blacklist</option>
                                    <option value="berisiko" {{ old('status') == 'berisiko' ? 'selected' : '' }}>Berisiko</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer text-right">
                        <button type="submit" class="btn btn-primary">Simpan Data</button>
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
