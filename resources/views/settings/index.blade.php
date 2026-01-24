@extends('layouts.app')

@section('page-title', 'Pengaturan')

@section('content-app')
<div class="row">
    <div class="col-lg-3 mb-4">
        <!-- Vertical Menu -->
        <div class="list-group list-group-transparent mb-0">
            <a href="#general" data-toggle="list" class="list-group-item list-group-item-action active">
                <span class="icon mr-3"><i class="fe fe-home"></i></span>Profil Koperasi
            </a>
            <a href="#loan" data-toggle="list" class="list-group-item list-group-item-action">
                <span class="icon mr-3"><i class="fe fe-dollar-sign"></i></span>Pinjaman
            </a>
            <a href="#collectibility" data-toggle="list" class="list-group-item list-group-item-action">
                <span class="icon mr-3"><i class="fe fe-activity"></i></span>Kolektabilitas
            </a>
            <a href="#saving" data-toggle="list" class="list-group-item list-group-item-action">
                <span class="icon mr-3"><i class="fe fe-briefcase"></i></span>Simpanan
            </a>
            <a href="#accounting" data-toggle="list" class="list-group-item list-group-item-action">
                <span class="icon mr-3"><i class="fe fe-book"></i></span>Akuntansi
            </a>
            <a href="#system" data-toggle="list" class="list-group-item list-group-item-action">
                <span class="icon mr-3"><i class="fe fe-settings"></i></span>Sistem
            </a>
            <a href="#user-guide" data-toggle="list" class="list-group-item list-group-item-action">
                <span class="icon mr-3"><i class="fe fe-help-circle"></i></span>Panduan Pengguna
            </a>
        </div>
    </div>

    <div class="col-lg-9">
        <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="tab-content">
                <!-- General -->
                <div class="tab-pane fade show active" id="general">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Profil Koperasi</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label">Nama Koperasi</label>
                                <input type="text" class="form-control" name="company_name" value="{{ $settings['company_name'] ?? '' }}">
                            </div>
                            <div class="form-group">
                                <label class="form-label">Alamat</label>
                                <textarea class="form-control" name="company_address" rows="3">{{ $settings['company_address'] ?? '' }}</textarea>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Nomor Telepon</label>
                                        <input type="text" class="form-control" name="company_phone" value="{{ $settings['company_phone'] ?? '' }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Email</label>
                                        <input type="email" class="form-control" name="company_email" value="{{ $settings['company_email'] ?? '' }}">
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Logo</label>
                                @if(isset($settings['company_logo']) && $settings['company_logo'])
                                    <div class="mb-2 d-flex align-items-center">
                                        <img src="{{ asset($settings['company_logo']) }}" alt="Logo" style="max-height: 50px;" class="mr-3">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="event.preventDefault(); if(confirm('Hapus logo?')) document.getElementById('delete-logo-form').submit();">
                                            <i class="fe fe-trash"></i> Hapus
                                        </button>
                                    </div>
                                @endif
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="company_logo">
                                    <label class="custom-file-label">Pilih file logo (SVG/PNG/JPG)</label>
                                </div>
                                <small class="text-muted d-block mt-1">Digunakan pada Header, Login, dan Laporan. Disarankan format PNG/SVG transparan. <strong>Ukuran optimal: Tinggi 100px, Lebar menyesuaikan (rasio aspek terjaga).</strong> Maksimal ukuran file 2MB.</small>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Background Halaman Depan</label>
                                @if(isset($settings['front_background']) && $settings['front_background'])
                                    <div class="mb-2 d-flex align-items-center">
                                        <img src="{{ asset($settings['front_background']) }}" alt="Background" style="max-height: 100px; border: 1px solid #ddd; padding: 2px;" class="mr-3">
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="event.preventDefault(); if(confirm('Hapus background?')) document.getElementById('delete-bg-form').submit();">
                                            <i class="fe fe-trash"></i> Hapus
                                        </button>
                                    </div>
                                @endif
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="front_background">
                                    <label class="custom-file-label">Pilih file background (JPG/PNG)</label>
                                </div>
                                <small class="text-muted d-block mt-1">Digunakan sebagai background halaman utama (welcome page). Disarankan gambar resolusi tinggi (1920x1080). Maksimal 1MB.</small>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </div>
                </div>

                <!-- Loan -->
                <div class="tab-pane fade" id="loan">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Pengaturan Pinjaman</h3>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Suku Bunga Default (%)</label>
                                        <input type="number" step="0.01" class="form-control" name="default_interest_rate" value="{{ $settings['default_interest_rate'] ?? 0 }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Biaya Admin Default (%)</label>
                                        <input type="number" step="0.01" class="form-control" name="default_admin_fee" value="{{ $settings['default_admin_fee'] ?? 0 }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Denda Keterlambatan Default (Rp)</label>
                                        <input type="number" class="form-control" name="default_penalty" value="{{ $settings['default_penalty'] ?? 0 }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Plafon Pinjaman (Rp)</label>
                                        <input type="number" class="form-control" name="loan_limit" value="{{ $settings['loan_limit'] ?? 0 }}">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </div>
                </div>

                <!-- Collectibility -->
                <div class="tab-pane fade" id="collectibility">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Pengaturan Kolektabilitas (OJK)</h3>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                Pengaturan batas minimal hari tunggakan untuk klasifikasi kualitas kredit.
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">DPK (Dalam Perhatian Khusus) - Hari</label>
                                        <input type="number" min="0" class="form-control" name="col_dpk_days" value="{{ $settings['col_dpk_days'] ?? 1 }}">
                                        <small class="text-muted">Default: 1 Hari</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Kurang Lancar - Hari</label>
                                        <input type="number" min="0" class="form-control" name="col_kl_days" value="{{ $settings['col_kl_days'] ?? 91 }}">
                                         <small class="text-muted">Default: 91 Hari</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Diragukan - Hari</label>
                                        <input type="number" min="0" class="form-control" name="col_diragukan_days" value="{{ $settings['col_diragukan_days'] ?? 121 }}">
                                         <small class="text-muted">Default: 121 Hari</small>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label class="form-label">Macet - Hari</label>
                                        <input type="number" min="0" class="form-control" name="col_macet_days" value="{{ $settings['col_macet_days'] ?? 181 }}">
                                         <small class="text-muted">Default: 181 Hari</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </div>
                </div>

                <!-- Saving -->
                <div class="tab-pane fade" id="saving">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Pengaturan Simpanan</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label">Suku Bunga Simpanan (%)</label>
                                <input type="number" step="0.01" class="form-control" name="savings_interest_rate" value="{{ $settings['savings_interest_rate'] ?? 0 }}">
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </div>
                </div>

                <!-- Accounting -->
                <div class="tab-pane fade" id="accounting">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Mapping Akuntansi (COA)</h3>
                            <div class="card-options">
                                <button type="button" class="btn btn-secondary btn-sm" onclick="event.preventDefault(); if(confirm('Apakah Anda yakin ingin melakukan seed COA bawaan? Tindakan ini mungkin akan menduplikasi akun jika sudah ada.')) document.getElementById('seed-coa-form').submit();">
                                    <i class="fe fe-refresh-cw"></i> Default COA
                                </button>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="alert alert-info">
                                Pilih akun Chart of Account (COA) yang akan digunakan untuk jurnal otomatis.
                            </div>
                            <div class="form-group">
                                <label class="form-label">Akun Kas (Pencairan & Pembayaran)</label>
                                <select class="form-control custom-select" name="coa_cash">
                                    @foreach($coas as $coa)
                                        <option value="{{ $coa->code }}" {{ ($settings['coa_cash'] ?? '1101') == $coa->code ? 'selected' : '' }}>
                                            {{ $coa->code }} - {{ $coa->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Akun Beban Bunga Simpanan</label>
                                <select class="form-control custom-select" name="coa_interest_expense">
                                    @foreach($coas as $coa)
                                        <option value="{{ $coa->code }}" {{ ($settings['coa_interest_expense'] ?? '') == $coa->code ? 'selected' : '' }}>
                                            {{ $coa->code }} - {{ $coa->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Akun Simpanan Anggota</label>
                                <select class="form-control custom-select" name="coa_savings">
                                    @foreach($coas as $coa)
                                        <option value="{{ $coa->code }}" {{ ($settings['coa_savings'] ?? '2101') == $coa->code ? 'selected' : '' }}>
                                            {{ $coa->code }} - {{ $coa->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Akun Piutang Pinjaman</label>
                                <select class="form-control custom-select" name="coa_receivable">
                                    @foreach($coas as $coa)
                                        <option value="{{ $coa->code }}" {{ ($settings['coa_receivable'] ?? '1103') == $coa->code ? 'selected' : '' }}>
                                            {{ $coa->code }} - {{ $coa->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Akun Pendapatan Bunga</label>
                                <select class="form-control custom-select" name="coa_revenue_interest">
                                    @foreach($coas as $coa)
                                        <option value="{{ $coa->code }}" {{ ($settings['coa_revenue_interest'] ?? '4101') == $coa->code ? 'selected' : '' }}>
                                            {{ $coa->code }} - {{ $coa->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Akun Pendapatan Admin</label>
                                <select class="form-control custom-select" name="coa_revenue_admin">
                                    @foreach($coas as $coa)
                                        <option value="{{ $coa->code }}" {{ ($settings['coa_revenue_admin'] ?? '4102') == $coa->code ? 'selected' : '' }}>
                                            {{ $coa->code }} - {{ $coa->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form-group">
                                <label class="form-label">Akun Pendapatan Denda</label>
                                <select class="form-control custom-select" name="coa_revenue_penalty">
                                    @foreach($coas as $coa)
                                        <option value="{{ $coa->code }}" {{ ($settings['coa_revenue_penalty'] ?? '4103') == $coa->code ? 'selected' : '' }}>
                                            {{ $coa->code }} - {{ $coa->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </div>
                </div>

                <!-- System -->
                <div class="tab-pane fade" id="system">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Pengaturan Sistem</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label">Notifikasi Jatuh Tempo (Hari Sebelum)</label>
                                <input type="number" class="form-control" name="notification_due_date_threshold" value="{{ $settings['notification_due_date_threshold'] ?? 0 }}">
                                <small class="text-muted">Masukkan 0 untuk notifikasi pada hari H.</small>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                        </div>
                    </div>
                </div>

                <!-- User Guide -->
                <div class="tab-pane fade" id="user-guide">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Buku Panduan Pengguna</h3>
                        </div>
                        <div class="card-body" style="max-height: 70vh; overflow-y: auto;">
                            <div class="alert alert-info">
                                <i class="fe fe-info mr-2"></i> Panduan ini membantu Anda memahami fitur-fitur utama aplikasi koperasi.
                            </div>

                            <h4 class="mt-4 text-primary"><i class="fe fe-users mr-2"></i>1. Manajemen Anggota & Nasabah</h4>
                            <ul>
                                <li><strong>Menambah Anggota:</strong> Masuk ke menu Anggota, klik tombol "Tambah Anggota". Isi data lengkap seperti NIK, Nama, Alamat, dan No. HP.</li>
                                <li><strong>Status Anggota:</strong> Anggota aktif dapat melakukan pinjaman dan simpanan. Anggota non-aktif (keluar) datanya tetap tersimpan sebagai arsip.</li>
                                <li><strong>Nasabah:</strong> Nasabah adalah pihak luar (bukan anggota) yang memiliki transaksi dengan koperasi (misal: hanya menabung atau pinjaman khusus).</li>
                            </ul>

                            <h4 class="mt-4 text-primary"><i class="fe fe-briefcase mr-2"></i>2. Simpanan (Tabungan)</h4>
                            <p>Fitur ini mencatat simpanan anggota. Jenis simpanan dapat diatur sesuai kebijakan koperasi.</p>
                            <ul>
                                <li><strong>Setoran Tunai:</strong> Pilih menu Simpanan -> Setoran. Masukkan nama anggota dan jumlah uang.</li>
                                <li><strong>Penarikan Tunai:</strong> Pilih menu Simpanan -> Penarikan. Pastikan saldo anggota mencukupi.</li>
                                <li><strong>Bunga Simpanan:</strong> Bunga dihitung berdasarkan pengaturan di menu Pengaturan -> Simpanan.</li>
                            </ul>

                            <h4 class="mt-4 text-primary"><i class="fe fe-dollar-sign mr-2"></i>3. Pinjaman</h4>
                            <p>Proses pinjaman melalui beberapa tahap:</p>
                            <ol>
                                <li><strong>Pengajuan (Diajukan):</strong> Anggota mengajukan pinjaman. Data dicatat di menu Pinjaman -> Tambah.</li>
                                <li><strong>Persetujuan (Disetujui):</strong> Pengurus menyetujui pinjaman. Status berubah menjadi "Disetujui".</li>
                                <li><strong>Pencairan (Berjalan):</strong> Dana diberikan ke anggota. Klik tombol "Cairkan" pada detail pinjaman. Status berubah menjadi "Berjalan".</li>
                                <li><strong>Angsuran:</strong> Pembayaran cicilan dilakukan melalui menu Pembayaran Angsuran. Saldo pinjaman akan berkurang otomatis.</li>
                                <li><strong>Lunas:</strong> Jika seluruh pokok dan bunga terbayar, status otomatis berubah menjadi "Lunas".</li>
                            </ol>
                            <div class="alert alert-secondary text-dark">
                                <strong>Tips:</strong> Gunakan menu "Kolektabilitas" untuk memantau kredit macet berdasarkan keterlambatan hari.
                            </div>

                            <h4 class="mt-4 text-primary"><i class="fe fe-book mr-2"></i>4. Akuntansi & Laporan</h4>
                            <ul>
                                <li><strong>Jurnal Otomatis:</strong> Transaksi kas, simpanan, dan pinjaman otomatis menjurnal ke buku besar berdasarkan <em>Mapping COA</em> di Pengaturan.</li>
                                <li><strong>Laporan Keuangan:</strong>
                                    <ul>
                                        <li><em>Neraca (Balance Sheet):</em> Menampilkan posisi aset, kewajiban, dan modal.</li>
                                        <li><em>Laba Rugi (Profit & Loss):</em> Menampilkan pendapatan dan biaya dalam periode tertentu.</li>
                                        <li><em>Arus Kas (Cash Flow):</em> Melacak aliran masuk dan keluar uang kas.</li>
                                    </ul>
                                </li>
                                <li><strong>Buku Besar:</strong> Detail transaksi per akun untuk audit lebih mendalam.</li>
                            </ul>

                            <h4 class="mt-4 text-primary"><i class="fe fe-settings mr-2"></i>5. Pengaturan Sistem</h4>
                            <ul>
                                <li><strong>Profil Koperasi:</strong> Ubah nama, alamat, logo, dan background aplikasi di tab "Profil Koperasi".</li>
                                <li><strong>Suku Bunga & Denda:</strong> Atur default bunga pinjaman, biaya admin, dan denda di tab "Pinjaman".</li>
                                <li><strong>Mapping COA:</strong> <strong>PENTING!</strong> Pastikan akun-akun terhubung dengan benar di tab "Akuntansi" agar laporan keuangan valid. Gunakan fitur "Default COA" jika belum ada akun sama sekali.</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <!-- Hidden Forms for Delete Actions (Outside Main Form) -->
        <form id="delete-logo-form" action="{{ route('settings.remove_logo') }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
        <form id="delete-bg-form" action="{{ route('settings.remove_background') }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
        <form id="seed-coa-form" action="{{ route('accounting.coa.seed') }}" method="POST" style="display: none;">
            @csrf
        </form>
    </div>
</div>
@endsection

@section('js')
<script>
    require(['jquery'], function($) {
        $(document).ready(function() {
            // Custom file input label update
            $('.custom-file-input').on('change', function() {
                var fileName = $(this).val().split('\\').pop();
                $(this).next('.custom-file-label').addClass("selected").html(fileName);
            });
        });
    });
</script>
@endsection
