@extends('layouts.app')

@section('css')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://select2.github.io/select2-bootstrap-theme/css/select2-bootstrap.css">
@endsection

@section('content-app')
<div class="row row-cards row-deck">
    <div class="col-10 offset-md-1">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('add') }} {{ __('menu.withdrawal') }}</h3>
                <div class="card-options">
                    <a href="{{ route('withdrawals.index') }}" class="btn btn-sm btn-pill btn-secondary">{{ __('back') }}</a>
                </div>
            </div>
            <form action="{{ route('withdrawals.store') }}" method="POST">
                @csrf
                <div class="card-body">
                    @if (session('message'))
                        <div class="alert alert-icon alert-danger alert-dismissible" role="alert">
                            <i class="fe fe-alert-triangle mr-2" aria-hidden="true"></i>
                            <button type="button" class="close" data-dismiss="alert"></button>
                            {{ session('message') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-icon alert-danger alert-dismissible" role="alert">
                            <i class="fe fe-alert-triangle mr-2" aria-hidden="true"></i>
                            <button type="button" class="close" data-dismiss="alert"></button>
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="form-group">
                        <div class="row align-items-center">
                            <label class="col-sm-2">Tipe Penarik</label>
                            <div class="col-sm-10">
                                <div class="custom-controls-stacked">
                                    <label class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" class="custom-control-input" name="tipe_penarik" value="anggota" checked>
                                        <span class="custom-control-label">Anggota</span>
                                    </label>
                                    <label class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" class="custom-control-input" name="tipe_penarik" value="nasabah">
                                        <span class="custom-control-label">Nasabah</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-group" id="anggota-group">
                        <div class="row align-items-center">
                            <label class="col-sm-2">{{ __('menu.member') }}</label>
                            <div class="col-sm-10">
                                <select class="form-control select2" name="anggota">
                                    <option value="">-- {{ __('menu.member') }} --</option>
                                    @foreach ($members as $member)
                                        <option value="{{ $member->id }}" {{ old('anggota') == $member->id ? 'selected' : '' }}>
                                            {{ $member->nama }} - {{ $member->nik }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('anggota'))
                                    <span class="text-danger small">{{ $errors->first('anggota') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group" id="nasabah-group" style="display: none;">
                        <div class="row align-items-center">
                            <label class="col-sm-2">Nasabah</label>
                            <div class="col-sm-10">
                                <select class="form-control select2" name="nasabah">
                                    <option value="">-- Nasabah --</option>
                                    @foreach ($nasabahs as $nasabah)
                                        <option value="{{ $nasabah->id }}" {{ old('nasabah') == $nasabah->id ? 'selected' : '' }}>
                                            {{ $nasabah->nama }} - {{ $nasabah->nik }}
                                        </option>
                                    @endforeach
                                </select>
                                @if ($errors->has('nasabah'))
                                    <span class="text-danger small">{{ $errors->first('nasabah') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row align-items-center">
                            <label class="col-sm-2">{{ __('amount') }}</label>
                            <div class="col-sm-10">
                                <div class="input-group">
                                    <span class="input-group-prepend">
                                        <span class="input-group-text">Rp</span>
                                    </span>
                                    <input type="number" name="jumlah" autocomplete="off" class="form-control{{ $errors->has('jumlah') ? ' is-invalid' : '' }}" value="{{ old('jumlah') }}">
                                    @if ($errors->has('jumlah'))
                                        <span class="invalid-feedback">{{ $errors->first('jumlah') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row align-items-center">
                            <label class="col-sm-2">{{ __('note') }}</label>
                            <div class="col-sm-10">
                                <input type="text" name="keterangan" autocomplete="off" class="form-control{{ $errors->has('keterangan') ? ' is-invalid' : '' }}" value="{{ old('keterangan') }}">
                                @if ($errors->has('keterangan'))
                                    <span class="invalid-feedback">{{ $errors->first('keterangan') }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button type="submit" class="btn btn-primary">{{ __('add') }} {{ __('menu.withdrawal') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
require(['jquery', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js'], function ($) {
    $(document).ready(function () {
        $('.select2').select2({
            theme: "bootstrap",
            width: '100%'
        });

        $('input[name="tipe_penarik"]').on('change', function() {
            var type = $(this).val();
            if (type == 'anggota') {
                $('#anggota-group').show();
                $('#nasabah-group').hide();
            } else {
                $('#anggota-group').hide();
                $('#nasabah-group').show();
            }
        });

        // Trigger default
        $('input[name="tipe_penarik"]:checked').trigger('change');
    });
});
</script>
@endsection
