@extends('layouts.app')

@section('page-title')
    Detail Pinjaman: {{ $loan->kode_pinjaman }}
@endsection

@section('content-app')
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Informasi Pinjaman</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless">
                        <tr>
                            <th>Peminjam</th>
                            <td>
                                @if($loan->member)
                                    {{ $loan->member->nama }} (Anggota)
                                @elseif($loan->nasabah)
                                    {{ $loan->nasabah->nama }} (Nasabah)
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Jenis</th>
                            <td>{{ ucfirst($loan->jenis_pinjaman) }}</td>
                        </tr>
                        <tr>
                            <th>Jumlah</th>
                            <td>Rp {{ number_format($loan->jumlah_pinjaman, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Tenor</th>
                            <td>{{ $loan->tenor }} Bulan</td>
                        </tr>
                        <tr>
                            <th>Bunga</th>
                            <td>
                                {{ $loan->suku_bunga }}% / {{ $loan->satuan_bunga == 'bulan' ? 'Bulan' : 'Tahun' }}
                                <br><small>({{ ucfirst($loan->jenis_bunga) }})</small>
                            </td>
                        </tr>
                        <tr>
                            <th>Denda Default</th>
                            <td>Rp {{ number_format($loan->denda_keterlambatan, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <th>Keterangan</th>
                            <td>{{ $loan->keterangan ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                @if($loan->status == 'diajukan')
                                    <span class="badge badge-warning">Diajukan</span>
                                @elseif($loan->status == 'disetujui')
                                    <span class="badge badge-info">Disetujui</span>
                                @elseif($loan->status == 'berjalan')
                                    <span class="badge badge-primary">Berjalan</span>
                                @elseif($loan->status == 'lunas')
                                    <span class="badge badge-success">Lunas</span>
                                @elseif($loan->status == 'macet')
                                    <span class="badge badge-danger">Macet / Bermasalah</span>
                                @else
                                    <span class="badge badge-secondary">{{ $loan->status }}</span>
                                @endif
                            </td>
                        </tr>
                    </table>

                    <div class="mt-3">
                        @if($loan->status == 'diajukan')
                            <div class="row">
                                <div class="col-6">
                                    <form action="{{ route('loans.approve', $loan->id) }}" method="POST" onsubmit="return confirm('Setujui pinjaman ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-success btn-block">Setujui</button>
                                    </form>
                                </div>
                                <div class="col-6">
                                    <form action="{{ route('loans.reject', $loan->id) }}" method="POST" onsubmit="return confirm('Tolak pinjaman ini?')">
                                        @csrf
                                        <button type="submit" class="btn btn-danger btn-block">Tolak</button>
                                    </form>
                                </div>
                            </div>
                        @endif

                        @if($loan->status == 'disetujui')
                            <form action="{{ route('loans.disburse', $loan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Cairkan dana dan buat jadwal angsuran?')">
                                @csrf
                                <button type="submit" class="btn btn-primary btn-block mt-2">Cairkan Dana</button>
                            </form>
                        @endif

                        @if($loan->status == 'berjalan')
                            <form action="{{ route('loans.markBadDebt', $loan->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Tandai pinjaman ini sebagai MACET?')">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-block mt-2">Tandai Macet</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title">Jadwal Angsuran</h5>
                </div>
                <div class="card-body">
                    @if($loan->installments->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped table-sm">
                                <thead>
                                    <tr>
                                        <th>Ke</th>
                                        <th>Jatuh Tempo</th>
                                        <th>Total</th>
                                        <th>Pokok</th>
                                        <th>Bunga</th>
                                        <th>Denda</th>
                                        <th>Sisa</th>
                                        <th>Status</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($loan->installments as $inst)
                                        <tr>
                                            <td>{{ $inst->angsuran_ke }}</td>
                                            <td>
                                                {{ $inst->tanggal_jatuh_tempo->format('d-m-Y') }}
                                                @if($inst->status == 'belum_lunas' && now() > $inst->tanggal_jatuh_tempo)
                                                    <br><span class="badge badge-danger">Telat {{ now()->diffInDays($inst->tanggal_jatuh_tempo) }} Hari</span>
                                                @endif
                                            </td>
                                            <td>
                                                Rp {{ number_format($inst->total_angsuran + $inst->denda, 0, ',', '.') }}
                                                @if($inst->denda > 0)
                                                    <small class="d-block text-danger">(+Denda {{ number_format($inst->denda) }})</small>
                                                @endif
                                            </td>
                                            <td>Rp {{ number_format($inst->pokok, 0, ',', '.') }}</td>
                                            <td>Rp {{ number_format($inst->bunga, 0, ',', '.') }}</td>
                                            <td>
                                                Rp {{ number_format($inst->denda, 0, ',', '.') }}
                                            </td>
                                            <td>Rp {{ number_format($inst->sisa_pinjaman, 0, ',', '.') }}</td>
                                            <td>
                                                @if($inst->status == 'lunas')
                                                    <span class="badge badge-success">Lunas</span>
                                                    <br><small>{{ $inst->tanggal_bayar ? $inst->tanggal_bayar->format('d/m/Y') : '' }}</small>
                                                @else
                                                    <span class="badge badge-secondary">Belum Lunas</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($inst->status == 'belum_lunas' && $loan->status != 'macet')
                                                    <div class="btn-group">
                                                        <button type="button"
                                                            class="btn btn-sm btn-success btn-pay"
                                                            data-id="{{ $inst->id }}"
                                                            data-angsuran="{{ $inst->angsuran_ke }}"
                                                            data-total="{{ $inst->total_angsuran }}"
                                                            data-denda="{{ $inst->denda }}"
                                                            data-duedate="{{ $inst->tanggal_jatuh_tempo->format('Y-m-d') }}">
                                                            Bayar
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-warning btn-penalty" data-id="{{ $inst->id }}" data-denda="{{ $inst->denda }}"><i class="fe fe-alert-circle"></i></button>

                                                        @if(now() > $inst->tanggal_jatuh_tempo)
                                                             @php
                                                                $phone = $loan->member ? $loan->member->telepon : ($loan->nasabah ? $loan->nasabah->telepon : '');
                                                                // Basic sanitization for WA
                                                                $phone = preg_replace('/^0/', '62', $phone);
                                                                $msg = "Halo, angsuran ke-" . $inst->angsuran_ke . " Anda jatuh tempo pada " . $inst->tanggal_jatuh_tempo->format('d-m-Y') . ". Mohon segera melakukan pembayaran.";
                                                            @endphp
                                                            @if($phone)
                                                                <a href="https://wa.me/{{ $phone }}?text={{ urlencode($msg) }}" target="_blank" class="btn btn-sm btn-success ml-1"><i class="fe fe-message-circle"></i> WA</a>
                                                            @endif
                                                        @endif
                                                    </div>
                                                @elseif($inst->status == 'lunas')
                                                    <span class="text-muted"><i class="fe fe-check"></i> Terbayar</span>
                                                    <a href="{{ route('loans.installments.print', $inst->id) }}" target="_blank" class="btn btn-sm btn-secondary ml-1"><i class="fe fe-printer"></i></a>
                                                @else
                                                    <span class="text-danger">Macet</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted text-center">Jadwal angsuran belum dibuat (Menunggu pencairan).</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Bayar -->
    <div class="modal fade" id="modal-pay" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Bayar Angsuran <span id="pay-angsuran-ke"></span></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST" id="form-pay">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Tanggal Bayar</label>
                            <input type="date" name="tanggal_bayar" class="form-control" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form-group">
                            <label>Metode Pembayaran</label>
                            <select name="metode_pembayaran" class="form-control" required>
                                <option value="tunai">Tunai / Kas</option>
                                <option value="transfer">Transfer Bank</option>
                                <option value="lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Denda (Rp)</label>
                            <input type="number" name="denda" id="pay-denda" class="form-control" min="0" value="0">
                            <small class="text-muted" id="pay-denda-info"></small>
                        </div>
                         <div class="form-group">
                            <label>Keterangan / Catatan</label>
                            <textarea name="keterangan_pembayaran" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Bayar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Denda -->
    <div class="modal fade" id="modal-penalty" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Atur Denda Angsuran</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" method="POST" id="form-penalty">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Jumlah Denda (Rp)</label>
                            <input type="number" name="denda" class="form-control" min="0" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Collaterals Section --}}
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Data Jaminan</h4>
                    <div class="card-options">
                        <a href="{{ route('loans.collaterals.create', $loan->id) }}" class="btn btn-primary btn-sm btn-pill">Tambah Jaminan</a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered">
                            <thead>
                                <tr>
                                    <th>Jenis</th>
                                    <th>Nomor / Pemilik</th>
                                    <th>Nilai Taksasi</th>
                                    <th>Lokasi</th>
                                    <th>Status</th>
                                    <th>Foto/Dokumen</th>
                                    <th>Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($loan->collaterals as $collateral)
                                <tr>
                                    <td>{{ $collateral->jenis }}</td>
                                    <td>
                                        {{ $collateral->nomor }}<br>
                                        <small class="text-muted">{{ $collateral->pemilik }}</small>
                                    </td>
                                    <td>Rp {{ number_format($collateral->nilai_taksasi, 0, ',', '.') }}</td>
                                    <td>{{ $collateral->lokasi_penyimpanan }}</td>
                                    <td>
                                        @if($collateral->status == 'disimpan')
                                            <span class="badge badge-success">Disimpan</span>
                                            <br><small>{{ $collateral->tanggal_masuk ? \Carbon\Carbon::parse($collateral->tanggal_masuk)->format('d/m/Y') : '-' }}</small>
                                        @else
                                            <span class="badge badge-secondary">Dikembalikan</span>
                                            <br><small>{{ $collateral->tanggal_keluar ? \Carbon\Carbon::parse($collateral->tanggal_keluar)->format('d/m/Y') : '-' }}</small>
                                        @endif
                                    </td>
                                    <td>
                                        @if($collateral->foto)
                                            <a href="{{ asset('storage/' . $collateral->foto) }}" target="_blank" class="btn btn-xs btn-info">Foto</a>
                                        @endif
                                        @if($collateral->dokumen)
                                            <a href="{{ asset('storage/' . $collateral->dokumen) }}" target="_blank" class="btn btn-xs btn-warning">Dokumen</a>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('loans.collaterals.edit', [$loan->id, $collateral->id]) }}" class="btn btn-sm btn-secondary">Edit</a>

                                        @if($collateral->status == 'disimpan' && $loan->status == 'lunas')
                                            <form action="{{ route('loans.collaterals.return', [$loan->id, $collateral->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Apakah Anda yakin ingin mengembalikan jaminan ini?');">
                                                @csrf
                                                <input type="hidden" name="diserahkan_kepada" value="{{ $loan->member->nama ?? ($loan->nasabah->nama ?? '') }}">
                                                <button type="submit" class="btn btn-sm btn-success">Kembalikan</button>
                                            </form>
                                        @endif

                                        <form action="{{ route('loans.collaterals.destroy', [$loan->id, $collateral->id]) }}" method="POST" style="display:inline;" onsubmit="return confirm('Hapus jaminan ini?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger">Hapus</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center">Belum ada data jaminan.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
<script>
    require(['jquery', 'bootstrap'], function($) {
        $(document).ready(function() {
            $('body').on('click', '.btn-penalty', function() {
                var id = $(this).data('id');
                var denda = $(this).data('denda');
                var defaultDenda = {{ $loan->denda_keterlambatan ?? 0 }};

                // If existing denda is 0, suggest the default denda
                if (denda == 0 && defaultDenda > 0) {
                    denda = defaultDenda;
                }

                var action = '{{ url('loans/installments') }}/' + id + '/penalty';

                $('#form-penalty').attr('action', action);
                $('input[name="denda"]').val(denda);
                $('#modal-penalty').modal('show');
            });

            $('body').on('click', '.btn-pay', function() {
                var id = $(this).data('id');
                var angsuranKe = $(this).data('angsuran');
                var denda = $(this).data('denda');
                var duedate = $(this).data('duedate');
                var defaultDenda = {{ $loan->denda_keterlambatan ?? 0 }};

                var today = new Date().toISOString().slice(0, 10);
                var isLate = today > duedate;

                // Auto calculate denda if late and not yet set
                if (isLate && denda == 0 && defaultDenda > 0) {
                    denda = defaultDenda;
                    $('#pay-denda-info').text('Otomatis disarankan berdasarkan denda keterlambatan.');
                } else {
                     $('#pay-denda-info').text('');
                }

                var action = '{{ url('loans/installments') }}/' + id + '/pay';

                $('#pay-angsuran-ke').text('Ke-' + angsuranKe);
                $('#form-pay').attr('action', action);
                $('#pay-denda').val(denda);
                $('#modal-pay').modal('show');
            });
        });
    });
</script>
@endsection
