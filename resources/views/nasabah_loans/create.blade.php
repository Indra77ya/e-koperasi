@extends('layouts.app')

@section('page-title', 'Pengajuan Pinjaman Baru')

@section('content-app')
<div class="row">
    <div class="col-lg-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Formulir Pengajuan</h3>
            </div>
            <div class="card-body">
                <form action="{{ route('nasabah_loans.store') }}" method="POST" id="loan-form">
                    @csrf
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Nasabah</label>
                                <select name="nasabah_id" class="form-control custom-select">
                                    <option value="">Pilih Nasabah</option>
                                    @foreach($nasabahs as $nasabah)
                                        <option value="{{ $nasabah->id }}">{{ $nasabah->nama }} - {{ $nasabah->nik }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Jumlah Pinjaman (Rp)</label>
                                <input type="number" name="amount" id="amount" class="form-control" placeholder="Contoh: 10000000">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tanggal Pengajuan</label>
                                <input type="date" name="loan_date" class="form-control" value="{{ date('Y-m-d') }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Jenis Pinjaman</label>
                                <select name="loan_type" class="form-control custom-select">
                                    <option value="consumptive">Konsumtif</option>
                                    <option value="productive">Produktif</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="form-label">Tenor (Bulan)</label>
                                <input type="number" name="tenor" id="tenor" class="form-control" placeholder="Contoh: 12">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Tipe Bunga</label>
                                <select name="interest_type" id="interest_type" class="form-control custom-select">
                                    <option value="flat">Flat</option>
                                    <option value="effective">Efektif (Menurun)</option>
                                    <option value="annuity">Anuitas</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Suku Bunga (% per tahun)</label>
                                <input type="number" name="interest_rate" id="interest_rate" class="form-control" placeholder="Contoh: 12">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Metode Pencairan</label>
                                <select name="disbursement_method" class="form-control custom-select">
                                    <option value="cash">Tunai</option>
                                    <option value="transfer">Transfer</option>
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Biaya Admin</label>
                                <input type="number" name="admin_fee" class="form-control" value="0">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label class="form-label">Catatan</label>
                                <textarea name="notes" class="form-control" rows="3"></textarea>
                            </div>
                        </div>
                    </div>

                    <div class="form-footer">
                        <button type="button" class="btn btn-secondary" id="btn-simulate">Simulasi Angsuran</button>
                        <button type="submit" class="btn btn-primary ml-auto">Simpan Pengajuan</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card" id="simulation-result" style="display: none;">
            <div class="card-header">
                <h3 class="card-title">Hasil Simulasi Angsuran</h3>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-striped text-nowrap">
                    <thead>
                        <tr>
                            <th>Bulan Ke</th>
                            <th>Pokok</th>
                            <th>Bunga</th>
                            <th>Total Angsuran</th>
                            <th>Sisa Pinjaman</th>
                        </tr>
                    </thead>
                    <tbody id="simulation-body">
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    require(['jquery'], function($) {
        $('#btn-simulate').on('click', function() {
            var amount = $('#amount').val();
            var tenor = $('#tenor').val();
            var rate = $('#interest_rate').val();
            var type = $('#interest_type').val();

            if(!amount || !tenor || !rate) {
                alert('Mohon lengkapi jumlah, tenor, dan bunga.');
                return;
            }

            $.ajax({
                url: '{{ route("nasabah_loans.simulate") }}',
                method: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    amount: amount,
                    tenor: tenor,
                    interest_rate: rate,
                    interest_type: type
                },
                success: function(response) {
                    var html = '';
                    response.schedule.forEach(function(item) {
                        html += '<tr>';
                        html += '<td>' + item.number + '</td>';
                        html += '<td>' + new Intl.NumberFormat('id-ID').format(item.principal) + '</td>';
                        html += '<td>' + new Intl.NumberFormat('id-ID').format(item.interest) + '</td>';
                        html += '<td>' + new Intl.NumberFormat('id-ID').format(item.total) + '</td>';
                        html += '<td>' + new Intl.NumberFormat('id-ID').format(item.balance) + '</td>';
                        html += '</tr>';
                    });
                    $('#simulation-body').html(html);
                    $('#simulation-result').show();
                },
                error: function(xhr) {
                    alert('Gagal melakukan simulasi: ' + xhr.responseJSON.message);
                }
            });
        });
    });
</script>
@endsection
