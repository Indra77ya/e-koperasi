@extends('layouts.app')

@section('content')
<div class="container">
    <div class="page-header">
        <h1 class="page-title">
            Detail Pinjaman
        </h1>
        <div class="page-options d-flex">
            <a href="{{ route('nasabah_loans.index') }}" class="btn btn-secondary btn-sm">
                <i class="fe fe-arrow-left"></i> Kembali
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Informasi Pinjaman</h3>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="form-label">Nasabah</label>
                        <div class="form-control-plaintext">{{ $loan->nasabah->nama }}</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jumlah Pinjaman</label>
                        <div class="form-control-plaintext">Rp {{ number_format($loan->amount, 0, ',', '.') }}</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Status</label>
                        <div class="form-control-plaintext">
                            <span class="tag tag-{{ $loan->status == 'approved' ? 'primary' : ($loan->status == 'active' || $loan->status == 'disbursed' || $loan->status == 'paid' ? 'success' : ($loan->status == 'rejected' || $loan->status == 'overdue' ? 'danger' : 'warning')) }}">
                                {{ ucfirst($loan->status) }}
                            </span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Tenor</label>
                        <div class="form-control-plaintext">{{ $loan->tenor }} Bulan</div>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Bunga</label>
                        <div class="form-control-plaintext">{{ $loan->interest_rate }}% ({{ ucfirst($loan->interest_type) }})</div>
                    </div>
                     <div class="form-group">
                        <label class="form-label">Tanggal Pengajuan</label>
                        <div class="form-control-plaintext">{{ $loan->loan_date->format('d M Y') }}</div>
                    </div>
                    @if($loan->disbursed_at)
                    <div class="form-group">
                        <label class="form-label">Tanggal Cair</label>
                        <div class="form-control-plaintext">{{ $loan->disbursed_at->format('d M Y H:i') }}</div>
                    </div>
                    @endif
                </div>
                <div class="card-footer">
                    @if($loan->status == 'pending')
                    <form action="{{ route('nasabah_loans.approve', $loan->id) }}" method="POST" style="display:inline-block">
                        @csrf
                        <button type="submit" class="btn btn-success btn-block mb-2">Setujui Pinjaman</button>
                    </form>
                    <button type="button" class="btn btn-danger btn-block" data-toggle="modal" data-target="#rejectModal">Tolak Pinjaman</button>
                    @endif

                    @if($loan->status == 'approved')
                    <form action="{{ route('nasabah_loans.disburse', $loan->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-block">Cairkan Dana</button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Jadwal Angsuran</h3>
                </div>
                <div class="table-responsive">
                    <table class="table card-table table-striped text-nowrap">
                        <thead>
                            <tr>
                                <th>Ke</th>
                                <th>Jatuh Tempo</th>
                                <th>Pokok</th>
                                <th>Bunga</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loan->installments as $item)
                            <tr>
                                <td>{{ $item->installment_number }}</td>
                                <td>{{ $item->due_date->format('d M Y') }}</td>
                                <td>{{ number_format($item->principal_amount, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->interest_amount, 0, ',', '.') }}</td>
                                <td>{{ number_format($item->total_amount, 0, ',', '.') }}</td>
                                <td>
                                    @if($item->status == 'paid')
                                        <span class="tag tag-success">Lunas</span>
                                        <div class="small text-muted">{{ $item->paid_at ? $item->paid_at->format('d M Y') : '' }}</div>
                                    @else
                                        <span class="tag tag-secondary">Belum Bayar</span>
                                    @endif
                                </td>
                                <td>
                                    @if($item->status != 'paid' && ($loan->status == 'disbursed' || $loan->status == 'active'))
                                    <button class="btn btn-sm btn-success btn-pay"
                                        data-id="{{ $item->id }}"
                                        data-amount="{{ $item->total_amount }}"
                                        data-number="{{ $item->installment_number }}">
                                        Bayar
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center text-muted">Belum ada jadwal angsuran (Pinjaman belum cair)</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div class="modal fade" id="rejectModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{ route('nasabah_loans.reject', $loan->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Pinjaman</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Alasan Penolakan</label>
                        <textarea name="reason" class="form-control" rows="3" required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Tolak</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Payment -->
<div class="modal fade" id="paymentModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="paymentForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Bayar Angsuran Ke-<span id="modalInstallmentNumber"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label class="form-label">Tanggal Bayar</label>
                        <input type="date" name="payment_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jumlah Pembayaran</label>
                        <input type="number" name="amount_paid" id="modalAmount" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Denda (Jika ada)</label>
                        <input type="number" name="penalty" class="form-control" value="0">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Catatan</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success">Simpan Pembayaran</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    require(['jquery'], function($) {
        $('.btn-pay').on('click', function() {
            var id = $(this).data('id');
            var amount = $(this).data('amount');
            var number = $(this).data('number');

            $('#modalInstallmentNumber').text(number);
            $('#modalAmount').val(Math.round(amount)); // Pre-fill with installment amount
            $('#paymentForm').attr('action', '/nasabah_loans/installments/' + id + '/pay');

            $('#paymentModal').modal('show');
        });
    });
</script>
@endsection
