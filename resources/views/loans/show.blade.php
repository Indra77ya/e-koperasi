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
                            <td>
                                {{ $loan->tenor }}
                                @if($loan->tempo_angsuran == 'harian')
                                    Hari
                                @elseif($loan->tempo_angsuran == 'mingguan')
                                    Minggu
                                @else
                                    Bulan
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Bunga</th>
                            <td>
                                {{ $loan->suku_bunga }}% /
                                @if($loan->satuan_bunga == 'hari')
                                    Hari
                                @elseif($loan->satuan_bunga == 'bulan')
                                    Bulan
                                @else
                                    Tahun
                                @endif
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

                                @if($loan->kolektabilitas)
                                    <br><span class="badge badge-{{ $loan->kolektabilitas == 'Lancar' ? 'success' : ($loan->kolektabilitas == 'DPK' ? 'warning' : 'danger') }} mt-1">Kolek: {{ $loan->kolektabilitas }}</span>
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
                                            <td class="text-nowrap">
                                                @if($inst->status == 'belum_lunas' && $loan->status != 'macet')
                                                    <button type="button"
                                                        class="btn btn-xs btn-success btn-pay mr-1"
                                                        data-id="{{ $inst->id }}"
                                                        data-angsuran="{{ $inst->angsuran_ke }}"
                                                        data-total="{{ $inst->total_angsuran }}"
                                                        data-bunga="{{ $inst->bunga }}"
                                                        data-sisa="{{ $inst->sisa_pinjaman }}"
                                                        data-denda="{{ $inst->denda }}"
                                                        data-tenor="{{ $loan->tenor }}"
                                                        data-duedate="{{ $inst->tanggal_jatuh_tempo->format('Y-m-d') }}">
                                                        Bayar
                                                    </button>
                                                    <button type="button" class="btn btn-xs btn-warning btn-penalty mr-1" data-id="{{ $inst->id }}" data-denda="{{ $inst->denda }}"><i class="fe fe-alert-circle"></i></button>

                                                    @if(now() > $inst->tanggal_jatuh_tempo)
                                                            @php
                                                            $phone = $loan->member ? $loan->member->no_hp : ($loan->nasabah ? $loan->nasabah->no_hp : '');
                                                            // Basic sanitization for WA
                                                            $phone = preg_replace('/^0/', '62', $phone);
                                                            $msg = "Halo, angsuran ke-" . $inst->angsuran_ke . " Anda jatuh tempo pada " . $inst->tanggal_jatuh_tempo->format('d-m-Y') . ". Mohon segera melakukan pembayaran.";
                                                        @endphp
                                                        @if($phone)
                                                            <a href="https://wa.me/{{ $phone }}?text={{ urlencode($msg) }}" target="_blank" class="btn btn-xs btn-success"><i class="fe fe-message-circle"></i> WA</a>
                                                        @endif
                                                    @endif
                                                @elseif($inst->status == 'lunas')
                                                    <span class="text-muted"><i class="fe fe-check"></i> Terbayar</span>
                                                    <a href="{{ route('loans.installments.print', $inst->id) }}" target="_blank" class="btn btn-xs btn-secondary ml-1"><i class="fe fe-printer"></i></a>
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
                            <label>Tagihan Bunga (Rp)</label>
                            <input type="text" id="pay-bunga-display" class="form-control" readonly>
                        </div>
                        <div class="form-group">
                            <label id="label-pay-amount">Jumlah Bayar (Rp)</label>
                            <input type="number" name="jumlah_bayar" id="pay-amount" class="form-control" required min="0">
                            <small class="text-muted" id="pay-amount-help">Minimal sebesar tagihan bunga (untuk pinjaman jangka panjang) atau total angsuran.</small>
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

                        <div class="alert alert-secondary bg-light">
                            <strong>Simulasi Pembayaran:</strong>
                            <ul class="mb-0 pl-3 small">
                                <li>Tagihan Bunga + Denda: <span id="calc-bill" class="font-weight-bold">Rp 0</span></li>
                                <li>Alokasi Pokok: <span id="calc-principal" class="font-weight-bold text-success">Rp 0</span></li>
                                <li>Sisa Pinjaman Akhir: <span id="calc-balance" class="font-weight-bold text-danger">Rp 0</span></li>
                            </ul>
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

    {{-- Collection Logs & Field Queue --}}
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Histori Penagihan (Timeline)</h4>
                    <div class="card-options">
                        <button class="btn btn-primary btn-sm btn-pill mr-2" data-toggle="modal" data-target="#modal-log">Catat Penagihan</button>
                        <button class="btn btn-secondary btn-sm btn-pill" data-toggle="modal" data-target="#modal-queue">Antrian Lapangan</button>
                    </div>
                </div>
                <div class="card-body p-0">
                     <ul class="timeline mt-3 mb-3 ml-3">
                        @forelse($loan->penagihanLogs()->latest()->get() as $log)
                            <li class="timeline-item">
                                <div class="timeline-badge bg-primary"></div>
                                <div>
                                    <strong>{{ $log->metode_penagihan }}</strong> oleh {{ $log->user->name ?? '-' }}
                                    <div class="text-muted small">{{ $log->created_at->format('d/m/Y H:i') }}</div>
                                    <div class="mt-1">
                                        Status: <strong>{{ $log->hasil_penagihan }}</strong><br>
                                        @if($log->tanggal_janji_bayar)
                                            <span class="text-danger">Janji Bayar: {{ \Carbon\Carbon::parse($log->tanggal_janji_bayar)->format('d/m/Y') }}</span><br>
                                        @endif
                                        Catatan: {{ $log->catatan }}
                                    </div>
                                </div>
                            </li>
                        @empty
                            <li class="timeline-item">
                                <div class="timeline-badge"></div>
                                <div>Belum ada riwayat penagihan.</div>
                            </li>
                        @endforelse
                    </ul>
                </div>
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

    <!-- Modal Log Penagihan -->
    <div class="modal fade" id="modal-log" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Catat Riwayat Penagihan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('collections.log.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="pinjaman_id" value="{{ $loan->id }}">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Metode Penagihan</label>
                            <select name="metode_penagihan" class="form-control">
                                <option value="Telepon">Telepon</option>
                                <option value="WhatsApp">WhatsApp</option>
                                <option value="Kunjungan">Kunjungan</option>
                                <option value="Surat">Surat</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Hasil</label>
                            <select name="hasil_penagihan" class="form-control">
                                <option value="Terhubung - Janji Bayar">Terhubung - Janji Bayar</option>
                                <option value="Terhubung - Minta Waktu">Terhubung - Minta Waktu</option>
                                <option value="Tidak Diangkat">Tidak Diangkat</option>
                                <option value="Nomor Salah">Nomor Salah</option>
                                <option value="Rumah Kosong">Rumah Kosong</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Janji Bayar (Opsional)</label>
                            <input type="date" name="tanggal_janji_bayar" class="form-control">
                        </div>
                        <div class="form-group">
                            <label>Catatan</label>
                            <textarea name="catatan" class="form-control" rows="3"></textarea>
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

    <!-- Modal Antrian Lapangan -->
    <div class="modal fade" id="modal-queue" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Masukan ke Antrian Lapangan</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('collections.queue.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="pinjaman_id" value="{{ $loan->id }}">
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Petugas (Opsional)</label>
                            <select name="petugas_id" class="form-control">
                                <option value="">-- Pilih Petugas --</option>
                                @foreach(\App\Models\User::all() as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Rencana Kunjungan</label>
                            <input type="date" name="tanggal_rencana_kunjungan" class="form-control" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="form-group">
                            <label>Catatan Tugas</label>
                            <textarea name="catatan_tugas" class="form-control" rows="3" placeholder="Instruksi khusus untuk petugas..."></textarea>
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
                $('input[name="denda"]').val(Math.round(denda));
                $('#modal-penalty').modal('show');
            });

            $('body').on('click', '.btn-pay', function() {
                var id = $(this).data('id');
                var angsuranKe = $(this).data('angsuran');
                var total = parseFloat($(this).data('total')) || 0;
                var bunga = parseFloat($(this).data('bunga')) || 0;
                var currentSisa = parseFloat($(this).data('sisa')) || 0;
                var denda = parseFloat($(this).data('denda')) || 0;
                var tenor = parseInt($(this).data('tenor')) || 0;
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

                // Store base values for calculation
                $('#modal-pay').data('bunga', bunga);
                $('#modal-pay').data('sisa', currentSisa);
                $('#modal-pay').data('tenor', tenor);

                var action = '{{ url('loans/installments') }}/' + id + '/pay';

                $('#pay-angsuran-ke').text('Ke-' + angsuranKe);
                $('#form-pay').attr('action', action);
                $('#pay-denda').val(Math.round(denda));
                $('#pay-bunga-display').val(Math.round(bunga).toLocaleString('id-ID')); // Show formatted bunga

                if (tenor == 0) {
                    // Indefinite: Input is PRINCIPAL ONLY
                    $('#label-pay-amount').text('Bayar Pokok (Rp)');
                    $('#pay-amount-help').text('Masukkan jumlah pokok yang ingin dibayar. Total bayar otomatis ditambah bunga & denda.');
                    $('#pay-amount').val(0); // Default to paying 0 principal (just interest)
                } else {
                    // Fixed: Input is TOTAL
                    $('#label-pay-amount').text('Jumlah Bayar (Rp)');
                    $('#pay-amount-help').text('Masukkan total nominal uang yang dibayarkan.');
                    $('#pay-amount').val(Math.round(total + (denda - $(this).data('denda'))));
                }

                // Trigger calculation
                calculateSimulation();

                $('#modal-pay').modal('show');
            });

            $('#pay-amount, #pay-denda').on('input', function() {
                calculateSimulation();
            });

            function calculateSimulation() {
                var inputAmount = parseFloat($('#pay-amount').val()) || 0;
                var inputDenda = parseFloat($('#pay-denda').val()) || 0;
                var baseBunga = parseFloat($('#modal-pay').data('bunga')) || 0;
                var currentSisa = parseFloat($('#modal-pay').data('sisa')) || 0;
                var tenor = parseInt($('#modal-pay').data('tenor')) || 0;

                var totalBill = baseBunga + inputDenda;
                var principalPaid = 0;

                if (tenor == 0) {
                    // Indefinite: Input is Principal
                    principalPaid = inputAmount;
                    totalBill = totalBill + principalPaid; // Total Cash needed

                    $('#calc-bill').html('Rp ' + Math.round(baseBunga + inputDenda).toLocaleString('id-ID') + ' <span class="text-muted">(Bunga+Denda)</span> <br>+ Rp ' + Math.round(principalPaid).toLocaleString('id-ID') + ' <span class="text-muted">(Pokok)</span><br><strong>Total: Rp ' + Math.round(totalBill).toLocaleString('id-ID') + '</strong>');
                } else {
                    // Fixed: Input is Total
                    if (inputAmount > totalBill) {
                        principalPaid = inputAmount - totalBill;
                    }
                    $('#calc-bill').text('Rp ' + Math.round(totalBill).toLocaleString('id-ID'));
                }

                var finalBalance = currentSisa - principalPaid;
                if (finalBalance < 0) finalBalance = 0;

                $('#calc-principal').text('Rp ' + Math.round(principalPaid).toLocaleString('id-ID'));
                $('#calc-balance').text('Rp ' + Math.round(finalBalance).toLocaleString('id-ID'));
            }
        });
    });
</script>
@endsection
