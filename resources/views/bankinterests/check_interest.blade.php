@if ($greater_than_periode)
    <div class="alert alert-danger mt-3 mb-0" role="alert">
        <i class="fe fe-alert-triangle mr-2"></i>Periode melebihi waktu sekarang.
    </div>
@else
    <div class="card mt-3 mb-0 border-info" style="border-left-width: 4px;">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <h5 class="text-muted mb-1 text-uppercase small font-weight-bold">Hasil Perhitungan</h5>
                    <div class="h2 font-weight-bold text-info mb-0">{{ format_rupiah($format_calculate_interest) }}</div>
                    <small class="text-muted">Bunga yang didapat</small>
                </div>
                @if ($count_interest == 1)
                    <span class="badge badge-success"><i class="fe fe-check mr-1"></i>Sudah Disimpan</span>
                @endif
            </div>

            <div class="row mt-4 pt-3 border-top">
                <div class="col-6">
                    <small class="text-muted d-block">Periode</small>
                    <strong>{{ $periode }}</strong>
                </div>
                <div class="col-6 text-right">
                    <small class="text-muted d-block">{{ __('interest_rate') }}</small>
                    <strong>{{ $interest_rate }}%</strong>
                </div>
                <div class="col-12 mt-2">
                    <small class="text-muted d-block">{{ __('lowest_balance') }}</small>
                    <strong>{{ format_rupiah($lowest_balance) }}</strong>
                </div>
            </div>

            @if ($count_interest == 0)
                <div class="mt-4">
                    <button id="btn-add-to-balance" class="btn btn-success btn-block">
                        <i class="fe fe-save mr-2"></i>Simpan & Tambah Saldo
                    </button>
                </div>
            @endif
        </div>
    </div>
@endif

<script>
require(['jquery'], function ($) {
    $(document).ready(function () {
        $('#btn-add-to-balance').click(function(e) {
            var id = '{{ $id }}';
            var type = '{{ $type }}';
            var month = '{{ $month }}';
            var year = '{{ $year }}';
            var lowest_balance = '{{ $lowest_balance }}';
            var interest_rate = '{{ $interest_rate }}';
            var calculate_interest = '{{ $format_calculate_interest }}';

            $(this).addClass('btn-loading');

            $.ajax({
                type: "POST",
                url: "{{ url('bankinterests/') }}",
                data: {
                    id: id,
                    type: type,
                    month: month,
                    year: year,
                    lowest_balance: lowest_balance,
                    interest_rate: interest_rate,
                    calculate_interest: calculate_interest
                },
                success: function(data) {
                    if (data.status == true) {
                        window.location = data.url
                    } else {
                        alert("Terjadi kesalahan");
                        $('#btn-add-to-balance').removeClass('btn-loading');
                    }
                },
                error: function() {
                    alert("Terjadi kesalahan server");
                    $('#btn-add-to-balance').removeClass('btn-loading');
                }
            });
        });
    });
});
</script>
