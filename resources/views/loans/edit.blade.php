@extends('layouts.app')

@section('page-title')
    Edit Pinjaman: {{ $loan->kode_pinjaman }}
@endsection

@section('content-app')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Edit Pinjaman</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('loans.update', $loan->id) }}" method="POST" id="loan-form">
                        @csrf
                        @method('PUT')
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Tipe Peminjam</label>
                                    <div>
                                        <label class="radio-inline"><input type="radio" name="tipe_peminjam" value="anggota" {{ $loan->anggota_id ? 'checked' : '' }}> Anggota</label>
                                        <label class="radio-inline ml-3"><input type="radio" name="tipe_peminjam" value="nasabah" {{ $loan->nasabah_id ? 'checked' : '' }}> Nasabah</label>
                                    </div>
                                </div>
                                <div class="form-group" id="anggota-group" style="{{ $loan->nasabah_id ? 'display: none;' : '' }}">
                                    <label>Anggota</label>
                                    <select name="anggota_id" class="form-control select2">
                                        <option value="">Pilih Anggota</option>
                                        @foreach ($members as $member)
                                            <option value="{{ $member->id }}" {{ $loan->anggota_id == $member->id ? 'selected' : '' }}>{{ $member->nik }} - {{ $member->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group" id="nasabah-group" style="{{ $loan->anggota_id ? 'display: none;' : '' }}">
                                    <label>Nasabah</label>
                                    <select name="nasabah_id" class="form-control select2">
                                        <option value="">Pilih Nasabah</option>
                                        @foreach ($nasabahs as $nasabah)
                                            <option value="{{ $nasabah->id }}" {{ $loan->nasabah_id == $nasabah->id ? 'selected' : '' }}>{{ $nasabah->nik }} - {{ $nasabah->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Jenis Pinjaman</label>
                                    <select name="jenis_pinjaman" class="form-control" required>
                                        <option value="produktif" {{ $loan->jenis_pinjaman == 'produktif' ? 'selected' : '' }}>Produktif</option>
                                        <option value="konsumtif" {{ $loan->jenis_pinjaman == 'konsumtif' ? 'selected' : '' }}>Konsumtif</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Jumlah Pinjaman (Rp)</label>
                                    <input type="number" name="jumlah_pinjaman" id="amount" class="form-control" required min="0" value="{{ (int)$loan->jumlah_pinjaman }}" {{ isset($defaults['limit']) && $defaults['limit'] > 0 ? 'max='.$defaults['limit'] : '' }}>
                                    @if(isset($defaults['limit']) && $defaults['limit'] > 0)
                                        <small class="text-muted">Maksimal: Rp {{ number_format($defaults['limit'], 0, ',', '.') }}</small>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <div class="custom-control custom-checkbox mb-2">
                                        <input type="checkbox" class="custom-control-input" id="is_indefinite" name="is_indefinite" value="1" {{ $loan->tenor == 0 ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_indefinite">Pinjaman Tanpa Tenor (Jangka Panjang)</label>
                                    </div>
                                    <label id="label-tenor" class="{{ $loan->tenor == 0 ? 'text-muted' : '' }}">Tenor</label>
                                    <div class="input-group">
                                        <input type="number" name="tenor" id="tenor" class="form-control" required min="1" value="{{ $loan->tenor > 0 ? $loan->tenor : '' }}" {{ $loan->tenor == 0 ? 'disabled' : '' }}>
                                        <div class="input-group-append">
                                            <select name="tempo_angsuran" id="tempo_angsuran" class="form-control">
                                                <option value="bulanan" {{ $loan->tempo_angsuran == 'bulanan' ? 'selected' : '' }}>Bulan</option>
                                                <option value="mingguan" {{ $loan->tempo_angsuran == 'mingguan' ? 'selected' : '' }}>Minggu</option>
                                                <option value="harian" {{ $loan->tempo_angsuran == 'harian' ? 'selected' : '' }}>Hari</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Suku Bunga (%)</label>
                                    <div class="input-group">
                                        <input type="number" name="suku_bunga" id="rate" class="form-control" required step="0.01" value="{{ $loan->suku_bunga }}">
                                        <div class="input-group-append">
                                            <select name="satuan_bunga" id="unit" class="form-control">
                                                <option value="tahun" {{ $loan->satuan_bunga == 'tahun' ? 'selected' : '' }}>Per Tahun</option>
                                                <option value="bulan" {{ $loan->satuan_bunga == 'bulan' ? 'selected' : '' }}>Per Bulan</option>
                                                <option value="hari" {{ $loan->satuan_bunga == 'hari' ? 'selected' : '' }}>Per Hari</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Jenis Bunga</label>
                                    <select name="jenis_bunga" id="type" class="form-control" required>
                                        <option value="flat" {{ $loan->jenis_bunga == 'flat' ? 'selected' : '' }}>Flat</option>
                                        <option value="efektif" {{ $loan->jenis_bunga == 'efektif' ? 'selected' : '' }}>Efektif (Sliding)</option>
                                        <option value="anuitas" {{ $loan->jenis_bunga == 'anuitas' ? 'selected' : '' }}>Anuitas</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    @php
                                        $admin_fee_percent = $loan->jumlah_pinjaman > 0 ? ($loan->biaya_admin / $loan->jumlah_pinjaman) * 100 : 0;
                                    @endphp
                                    <label>Biaya Admin (%)</label>
                                    <div class="input-group">
                                        <input type="number" name="biaya_admin" id="admin_fee_percent" class="form-control" value="{{ round($admin_fee_percent, 2) }}" step="0.01" min="0">
                                        <div class="input-group-append">
                                            <span class="input-group-text" id="admin_fee_amount_display">Rp {{ number_format($loan->biaya_admin, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label>Denda Keterlambatan (Rp)</label>
                                    <input type="number" name="denda_keterlambatan" class="form-control" value="{{ (int)$loan->denda_keterlambatan }}" min="0">
                                </div>
                                <div class="form-group">
                                    <label>Tanggal Pengajuan</label>
                                    <input type="date" name="tanggal_pengajuan" class="form-control" value="{{ $loan->tanggal_pengajuan->format('Y-m-d') }}" required>
                                </div>
                                <div class="form-group">
                                    <label>Status</label>
                                    <select name="status" class="form-control" required>
                                        <option value="diajukan" {{ $loan->status == 'diajukan' ? 'selected' : '' }}>Diajukan</option>
                                        <option value="disetujui" {{ $loan->status == 'disetujui' ? 'selected' : '' }}>Disetujui</option>
                                        <option value="berjalan" {{ $loan->status == 'berjalan' ? 'selected' : '' }}>Berjalan</option>
                                        <option value="lunas" {{ $loan->status == 'lunas' ? 'selected' : '' }}>Lunas</option>
                                        <option value="macet" {{ $loan->status == 'macet' ? 'selected' : '' }}>Macet</option>
                                        <option value="ditolak" {{ $loan->status == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3">{{ $loan->keterangan }}</textarea>
                        </div>

                        <div class="form-group">
                            @if(in_array($loan->status, ['berjalan', 'macet', 'lunas']))
                                <div class="alert alert-warning">
                                    <i class="fa fa-warning mr-2"></i> Perhatian: Mengubah data finansial pada pinjaman yang sudah berjalan akan menghapus riwayat angsuran dan jurnal akuntansi terkait, lalu mengembalikan status menjadi "Disetujui".
                                </div>
                            @endif
                            <button type="button" class="btn btn-info" id="btn-simulate">Simulasi Angsuran</button>
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                            <a href="{{ route('loans.index') }}" class="btn btn-secondary">Batal</a>
                        </div>
                    </form>

                    <div id="simulation-result" style="display: none; margin-top: 20px;">
                        <h5>Simulasi Angsuran</h5>
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Angsuran Ke</th>
                                    <th>Pokok</th>
                                    <th>Bunga</th>
                                    <th>Total Angsuran</th>
                                    <th>Sisa Pinjaman</th>
                                </tr>
                            </thead>
                            <tbody id="simulation-body"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://select2.github.io/select2-bootstrap-theme/css/select2-bootstrap.css">
<script>
    require(['jquery', 'https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js'], function($) {
        $(document).ready(function() {
            if ($('.select2').length) {
                $('.select2').select2({
                    theme: "bootstrap",
                    width: '100%'
                });
            }

            $('input[name="tipe_peminjam"]').change(function() {
                var val = $(this).val();
                if (val == 'anggota') {
                    $('#anggota-group').show();
                    $('#nasabah-group').hide();
                } else {
                    $('#anggota-group').hide();
                    $('#nasabah-group').show();
                }
            });

            $('#is_indefinite').change(function() {
                if ($(this).is(':checked')) {
                    $('#tenor').prop('disabled', true).prop('required', false).val('');
                    $('#label-tenor').addClass('text-muted');
                } else {
                    $('#tenor').prop('disabled', false).prop('required', true);
                    $('#label-tenor').removeClass('text-muted');
                }
            });

            function calculateAdminFee() {
                var amount = parseFloat($('#amount').val()) || 0;
                var percent = parseFloat($('#admin_fee_percent').val()) || 0;
                var fee = Math.round(amount * (percent / 100));
                $('#admin_fee_amount_display').text('Rp ' + fee.toLocaleString('id-ID'));
            }

            $('#amount, #admin_fee_percent').on('input', calculateAdminFee);

            $('#btn-simulate').click(function() {
                var amount = $('#amount').val();
                var tenor = $('#tenor').val();
                var rate = $('#rate').val();
                var unit = $('#unit').val();
                var type = $('#type').val();
                var tempo = $('#tempo_angsuran').val();
                var isIndefinite = $('#is_indefinite').is(':checked');

                if (isIndefinite) {
                    tenor = 0;
                }

                if (!amount || (!isIndefinite && !tenor) || !rate) {
                    alert('Mohon lengkapi jumlah, tenor, dan suku bunga.');
                    return;
                }

                $.ajax({
                    url: '{{ route('loans.calculate') }}',
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        amount: amount,
                        tenor: tenor,
                        rate: rate,
                        unit: unit,
                        type: type,
                        tempo: tempo
                    },
                    success: function(response) {
                        var html = '';
                        $.each(response, function(index, item) {
                            html += '<tr>';
                            html += '<td>' + item.month + '</td>';
                            html += '<td>Rp ' + Math.round(item.principal).toLocaleString('id-ID') + '</td>';
                            html += '<td>Rp ' + Math.round(item.interest).toLocaleString('id-ID') + '</td>';
                            html += '<td>Rp ' + Math.round(item.total).toLocaleString('id-ID') + '</td>';
                            html += '<td>Rp ' + Math.round(item.balance).toLocaleString('id-ID') + '</td>';
                            html += '</tr>';
                        });
                        $('#simulation-body').html(html);
                        $('#simulation-result').show();
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        alert('Terjadi kesalahan saat simulasi.');
                    }
                });
            });
        });
    });
</script>
@endsection
