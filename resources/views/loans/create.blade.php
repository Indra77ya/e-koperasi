@extends('layouts.app')

@section('page-title')
    Ajukan Pinjaman
@endsection

@section('content-app')
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Form Pengajuan Pinjaman</h4>
                </div>
                <div class="card-body">
                    <form action="{{ route('loans.store') }}" method="POST" id="loan-form">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Anggota</label>
                                    <select name="anggota_id" class="form-control select2" required>
                                        <option value="">Pilih Anggota</option>
                                        @foreach ($members as $member)
                                            <option value="{{ $member->id }}">{{ $member->nik }} - {{ $member->nama }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Jenis Pinjaman</label>
                                    <select name="jenis_pinjaman" class="form-control" required>
                                        <option value="produktif">Produktif</option>
                                        <option value="konsumtif">Konsumtif</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Jumlah Pinjaman (Rp)</label>
                                    <input type="number" name="jumlah_pinjaman" id="amount" class="form-control" required min="0">
                                </div>
                                <div class="form-group">
                                    <label>Tenor (Bulan)</label>
                                    <input type="number" name="tenor" id="tenor" class="form-control" required min="1">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Suku Bunga (% per tahun)</label>
                                    <input type="number" name="suku_bunga" id="rate" class="form-control" required step="0.01">
                                </div>
                                <div class="form-group">
                                    <label>Jenis Bunga</label>
                                    <select name="jenis_bunga" id="type" class="form-control" required>
                                        <option value="flat">Flat</option>
                                        <option value="efektif">Efektif (Sliding)</option>
                                        <option value="anuitas">Anuitas</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label>Biaya Admin (Rp)</label>
                                    <input type="number" name="biaya_admin" class="form-control" value="0">
                                </div>
                                <div class="form-group">
                                    <label>Tanggal Pengajuan</label>
                                    <input type="date" name="tanggal_pengajuan" class="form-control" value="{{ date('Y-m-d') }}" required>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="3"></textarea>
                        </div>

                        <div class="form-group">
                            <button type="button" class="btn btn-info" id="btn-simulate">Simulasi Angsuran</button>
                            <button type="submit" class="btn btn-primary">Simpan Pengajuan</button>
                        </div>
                    </form>

                    <div id="simulation-result" style="display: none; margin-top: 20px;">
                        <h5>Simulasi Angsuran</h5>
                        <table class="table table-bordered table-sm">
                            <thead>
                                <tr>
                                    <th>Bulan Ke</th>
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
                    theme: "bootstrap"
                });
            }

            $('#btn-simulate').click(function() {
                var amount = $('#amount').val();
                var tenor = $('#tenor').val();
                var rate = $('#rate').val();
                var type = $('#type').val();

                if (!amount || !tenor || !rate) {
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
                        type: type
                    },
                    success: function(response) {
                        var html = '';
                        $.each(response, function(index, item) {
                            html += '<tr>';
                            html += '<td>' + item.month + '</td>';
                            html += '<td>Rp ' + item.principal.toLocaleString('id-ID') + '</td>';
                            html += '<td>Rp ' + item.interest.toLocaleString('id-ID') + '</td>';
                            html += '<td>Rp ' + item.total.toLocaleString('id-ID') + '</td>';
                            html += '<td>Rp ' + item.balance.toLocaleString('id-ID') + '</td>';
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
