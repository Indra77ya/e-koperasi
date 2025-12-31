@extends('layouts.app')

@section('content-app')
<div class="row row-cards row-deck">
    <div class="col-10 offset-md-1">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Edit Nasabah</h3>
                <div class="card-options">
                    <a href="{{ route('customers.index') }}" class="btn btn-sm btn-pill btn-secondary">Kembali</a>
                </div>
            </div>
            <form action="{{ route('customers.update', $customer->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">NIK</label>
                                <input type="text" name="nik" class="form-control{{ $errors->has('nik') ? ' is-invalid' : '' }}" value="{{ old('nik', $customer->nik) }}">
                                @if ($errors->has('nik'))
                                    <span class="invalid-feedback">{{ $errors->first('nik') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Nama Lengkap</label>
                                <input type="text" name="nama" class="form-control{{ $errors->has('nama') ? ' is-invalid' : '' }}" value="{{ old('nama', $customer->nama) }}">
                                @if ($errors->has('nama'))
                                    <span class="invalid-feedback">{{ $errors->first('nama') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">No. HP</label>
                                <input type="text" name="no_hp" class="form-control{{ $errors->has('no_hp') ? ' is-invalid' : '' }}" value="{{ old('no_hp', $customer->no_hp) }}">
                                @if ($errors->has('no_hp'))
                                    <span class="invalid-feedback">{{ $errors->first('no_hp') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Pekerjaan</label>
                                <input type="text" name="pekerjaan" class="form-control{{ $errors->has('pekerjaan') ? ' is-invalid' : '' }}" value="{{ old('pekerjaan', $customer->pekerjaan) }}">
                                @if ($errors->has('pekerjaan'))
                                    <span class="invalid-feedback">{{ $errors->first('pekerjaan') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Alamat</label>
                        <textarea rows="2" class="form-control{{ $errors->has('alamat') ? ' is-invalid' : '' }}" name="alamat">{{ old('alamat', $customer->alamat) }}</textarea>
                        @if ($errors->has('alamat'))
                            <span class="invalid-feedback">{{ $errors->first('alamat') }}</span>
                        @endif
                    </div>

                    <div class="form-group">
                        <label class="form-label">Info Bisnis/Usaha</label>
                        <textarea rows="2" class="form-control{{ $errors->has('info_bisnis') ? ' is-invalid' : '' }}" name="info_bisnis">{{ old('info_bisnis', $customer->info_bisnis) }}</textarea>
                        @if ($errors->has('info_bisnis'))
                            <span class="invalid-feedback">{{ $errors->first('info_bisnis') }}</span>
                        @endif
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">File KTP</label>
                                <input type="file" name="file_ktp" class="form-control{{ $errors->has('file_ktp') ? ' is-invalid' : '' }}">
                                @if ($customer->file_ktp)
                                    <small><a href="{{ Storage::url($customer->file_ktp) }}" target="_blank">Lihat File Saat Ini</a></small>
                                @endif
                                @if ($errors->has('file_ktp'))
                                    <span class="invalid-feedback">{{ $errors->first('file_ktp') }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">File Jaminan</label>
                                <input type="file" name="file_jaminan" class="form-control{{ $errors->has('file_jaminan') ? ' is-invalid' : '' }}">
                                @if ($customer->file_jaminan)
                                    <small><a href="{{ Storage::url($customer->file_jaminan) }}" target="_blank">Lihat File Saat Ini</a></small>
                                @endif
                                @if ($errors->has('file_jaminan'))
                                    <span class="invalid-feedback">{{ $errors->first('file_jaminan') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Status Risiko</label>
                        <select class="form-control{{ $errors->has('status_risiko') ? ' is-invalid' : '' }}" name="status_risiko">
                            <option value="safe" {{ old('status_risiko', $customer->status_risiko) == 'safe' ? 'selected' : '' }}>Aman</option>
                            <option value="warning" {{ old('status_risiko', $customer->status_risiko) == 'warning' ? 'selected' : '' }}>Peringatan</option>
                            <option value="blacklist" {{ old('status_risiko', $customer->status_risiko) == 'blacklist' ? 'selected' : '' }}>Blacklist</option>
                        </select>
                        @if ($errors->has('status_risiko'))
                            <span class="invalid-feedback">{{ $errors->first('status_risiko') }}</span>
                        @endif
                    </div>

                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">Perbarui Nasabah</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
