@extends('layouts.app')

@section('content-app')
<div class="row row-cards row-deck">
    <div class="col-10 offset-md-1">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('add') }} {{ __('menu.deposit') }}</h3>
                <div class="card-options">
                    <a href="{{ route('deposits.index') }}" class="btn btn-sm btn-pill btn-secondary">{{ __('back') }}</a>
                </div>
            </div>
            <form action="{{ route('deposits.store') }}" method="POST">
                @csrf
                <div class="card-body">

                    <div class="form-group">
                        <div class="row align-items-center">
                            <label class="col-sm-2">Tipe Penyetor</label>
                            <div class="col-sm-10">
                                <div class="custom-controls-stacked">
                                    <label class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" class="custom-control-input" name="tipe_penyetor" value="anggota" checked>
                                        <span class="custom-control-label">Anggota</span>
                                    </label>
                                    <label class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" class="custom-control-input" name="tipe_penyetor" value="nasabah">
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
                    <button type="submit" class="btn btn-primary">{{ __('add') }} {{ __('menu.deposit') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
require(['jquery', 'select2'], function ($) {
    $(document).ready(function () {
        $('.select2').select2({
            theme: "bootstrap",
            width: '100%'
        });

        $('input[name="tipe_penyetor"]').on('change', function() {
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
        $('input[name="tipe_penyetor"]:checked').trigger('change');
    });
});
</script>
@endsection
