@extends('layouts.app')

@section('content-app')
<div class="row row-cards row-deck">
    <div class="col-10 offset-md-1">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Nasabah</h3>
                <div class="card-options">
                    <a href="{{ route('nasabahs.index') }}" class="btn btn-sm btn-pill btn-secondary">Kembali</a>
                </div>
            </div>
            <form action="{{ route('nasabahs.update', $nasabah->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">NIK</label>
                        <input type="text" name="nik" class="form-control{{ $errors->has('nik') ? ' is-invalid' : '' }}" value="{{ old('nik', $nasabah->nik) }}">
                        @if ($errors->has('nik'))
                            <span class="invalid-feedback">{{ $errors->first('nik') }}</span>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control{{ $errors->has('nama') ? ' is-invalid' : '' }}" value="{{ old('nama', $nasabah->nama) }}">
                                @if ($errors->has('nama'))
                                    <span class="invalid-feedback">{{ $errors->first('nama') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">No. HP</label>
                                <input type="text" name="no_hp" class="form-control{{ $errors->has('no_hp') ? ' is-invalid' : '' }}" value="{{ old('no_hp', $nasabah->no_hp) }}">
                                @if ($errors->has('no_hp'))
                                    <span class="invalid-feedback">{{ $errors->first('no_hp') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Pekerjaan</label>
                                <input type="text" name="pekerjaan" class="form-control{{ $errors->has('pekerjaan') ? ' is-invalid' : '' }}" value="{{ old('pekerjaan', $nasabah->pekerjaan) }}">
                                @if ($errors->has('pekerjaan'))
                                    <span class="invalid-feedback">{{ $errors->first('pekerjaan') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Usaha (Detail)</label>
                                <input type="text" name="usaha" class="form-control{{ $errors->has('usaha') ? ' is-invalid' : '' }}" value="{{ old('usaha', $nasabah->usaha) }}">
                                @if ($errors->has('usaha'))
                                    <span class="invalid-feedback">{{ $errors->first('usaha') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alamat</label>
                        <textarea rows="2" class="form-control{{ $errors->has('alamat') ? ' is-invalid' : '' }}" name="alamat">{{ old('alamat', $nasabah->alamat) }}</textarea>
                        @if ($errors->has('alamat'))
                            <span class="invalid-feedback">{{ $errors->first('alamat') }}</span>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Status Risiko</label>
                                <select name="status" class="form-control{{ $errors->has('status') ? ' is-invalid' : '' }}">
                                    <option value="aman" {{ old('status', $nasabah->status) == 'aman' ? 'selected' : '' }}>Aman</option>
                                    <option value="berisiko" {{ old('status', $nasabah->status) == 'berisiko' ? 'selected' : '' }}>Berisiko</option>
                                    <option value="blacklist" {{ old('status', $nasabah->status) == 'blacklist' ? 'selected' : '' }}>Blacklist</option>
                                </select>
                                @if ($errors->has('status'))
                                    <span class="invalid-feedback">{{ $errors->first('status') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Upload KTP (Biarkan kosong jika tidak diubah)</label>
                                <input type="file" name="file_ktp" class="form-control-file{{ $errors->has('file_ktp') ? ' is-invalid' : '' }}">
                                @if ($nasabah->file_ktp)
                                    <small class="form-text text-muted"><a href="{{ asset('storage/' . $nasabah->file_ktp) }}" target="_blank">Lihat KTP Saat Ini</a></small>
                                @endif
                                @if ($errors->has('file_ktp'))
                                    <span class="invalid-feedback d-block">{{ $errors->first('file_ktp') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Upload Jaminan (Biarkan kosong jika tidak diubah)</label>
                                <input type="file" name="file_jaminan" class="form-control-file{{ $errors->has('file_jaminan') ? ' is-invalid' : '' }}">
                                @if ($nasabah->file_jaminan)
                                    <small class="form-text text-muted"><a href="{{ asset('storage/' . $nasabah->file_jaminan) }}" target="_blank">Lihat Jaminan Saat Ini</a></small>
                                @endif
                                @if ($errors->has('file_jaminan'))
                                    <span class="invalid-feedback d-block">{{ $errors->first('file_jaminan') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
