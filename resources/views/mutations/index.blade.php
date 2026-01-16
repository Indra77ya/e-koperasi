@extends('layouts.app')

@section('content-app')
<div class="row row-cards row-deck">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">{{ __('menu.mutation') }}</h3>
            </div>
            <form>
                <div class="card-body">
                    <div class="form-group">
                        <div class="row align-items-center">
                            <label class="col-sm-2">Tipe</label>
                            <div class="col-sm-10">
                                <div class="custom-controls-stacked">
                                    <label class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" class="custom-control-input" name="type" value="anggota" checked>
                                        <span class="custom-control-label">Anggota</span>
                                    </label>
                                    <label class="custom-control custom-radio custom-control-inline">
                                        <input type="radio" class="custom-control-input" name="type" value="nasabah">
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
                                        <option value="{{ $member->id }}">{{ $member->nama }} - {{ $member->nik }}</option>
                                    @endforeach
                                </select>
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
                                        <option value="{{ $nasabah->id }}">{{ $nasabah->nama }} - {{ $nasabah->nik }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="row align-items-center">
                            <label class="col-sm-2">Periode</label>
                            <div class="col-sm-5">
                                <input type="date" name="from_date" class="form-control" value="{{ date('Y-m-01') }}">
                                <small class="text-muted">Dari Tanggal</small>
                            </div>
                            <div class="col-sm-5">
                                <input type="date" name="to_date" class="form-control" value="{{ date('Y-m-d') }}">
                                <small class="text-muted">Sampai Tanggal</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-footer text-right">
                    <button id="btn-check-mutations" class="btn btn-primary">{{ __('check') }} {{ __('menu.mutation') }}</button>
                </div>
            </form>
        </div>
        <div class="result"></div>
    </div>
</div>
@endsection

@section('js')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://select2.github.io/select2-bootstrap-theme/css/select2-bootstrap.css">

<script>
require(['jquery', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js'], function ($, select2) {
    $(document).ready(function () {
        $('.select2').select2({
            theme: "bootstrap",
            width: '100%'
        });

        // Toggle Member/Nasabah
        $('input[name="type"]').on('change', function() {
            var type = $(this).val();
            if (type == 'anggota') {
                $('#anggota-group').show();
                $('#nasabah-group').hide();
            } else {
                $('#anggota-group').hide();
                $('#nasabah-group').show();
            }
        });

        // Trigger default state on load
        $('input[name="type"]:checked').trigger('change');

        $('#btn-check-mutations').click(function(e) {
            e.preventDefault();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var type = $('input[name="type"]:checked').val();
            var id = (type == 'anggota') ? $('select[name="anggota"]').val() : $('select[name="nasabah"]').val();
            var from_date = $('input[name="from_date"]').val();
            var to_date = $('input[name="to_date"]').val();

            if (!id) {
                alert('Silakan pilih Anggota atau Nasabah terlebih dahulu.');
                return;
            }

            $.ajax({
                type: "GET",
                url: "{{ url('mutations/check-mutations') }}",
                data: {
                    type: type,
                    id: id,
                    from_date: from_date,
                    to_date: to_date
                },
                success: function(data) {
                    $('.result').html(data.html);
                },
                error: function(xhr) {
                    console.log(xhr.responseText);
                    var msg = 'Terjadi kesalahan saat memuat data mutasi.';
                    if (xhr.responseJSON && xhr.responseJSON.error) {
                        msg = xhr.responseJSON.error;
                    } else if (xhr.statusText) {
                        msg += ' (' + xhr.statusText + ')';
                    }
                    alert(msg);
                }
            });
        });
    });
});
</script>
@endsection
