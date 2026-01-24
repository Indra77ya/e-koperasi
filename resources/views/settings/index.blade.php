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
            <a href="#about" data-toggle="list" class="list-group-item list-group-item-action">
                <span class="icon mr-3"><i class="fe fe-info"></i></span>Tentang Aplikasi
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
                                <i class="fe fe-info mr-2"></i> Silakan klik kategori di bawah ini untuk melihat panduan detail setiap fitur.
                            </div>

                            <div id="guideAccordion">
                                <!-- 1. Keanggotaan & Nasabah -->
                                <div class="card mb-2">
                                    <div class="card-header" id="headingOne" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne" style="cursor: pointer;">
                                        <h5 class="mb-0 text-primary"><i class="fe fe-users mr-2"></i> 1. Keanggotaan & Nasabah</h5>
                                    </div>
                                    <div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#guideAccordion">
                                        <div class="card-body">
                                            <ul>
                                                <li><strong>Anggota:</strong>
                                                    <ul>
                                                        <li>Data anggota mencakup informasi pribadi (NIK, Nama, Alamat) dan status keanggotaan.</li>
                                                        <li>Hanya anggota <strong>Aktif</strong> yang dapat melakukan pengajuan pinjaman.</li>
                                                        <li>Anggota dapat melihat riwayat simpanan dan pinjaman mereka.</li>
                                                    </ul>
                                                </li>
                                                <li><strong>Nasabah:</strong>
                                                    <ul>
                                                        <li>Pihak eksternal (bukan anggota koperasi) yang memiliki hubungan transaksi, misalnya hanya menabung atau pinjaman khusus.</li>
                                                        <li>Data nasabah dipisahkan dari anggota untuk memudahkan pelaporan.</li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- 2. Simpanan & Bunga -->
                                <div class="card mb-2">
                                    <div class="card-header" id="headingTwo" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo" style="cursor: pointer;">
                                        <h5 class="mb-0 text-primary"><i class="fe fe-briefcase mr-2"></i> 2. Transaksi Simpanan & Bunga</h5>
                                    </div>
                                    <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#guideAccordion">
                                        <div class="card-body">
                                            <ul>
                                                <li><strong>Setoran (Deposit):</strong> Transaksi penambahan saldo simpanan. Mengkredit akun Simpanan dan Mendebit akun Kas.</li>
                                                <li><strong>Penarikan (Withdrawal):</strong> Pengambilan dana simpanan. Sistem akan memvalidasi saldo yang tersedia.</li>
                                                <li><strong>Daftar Mutasi:</strong> Riwayat lengkap keluar-masuk dana per anggota/nasabah.</li>
                                                <li><strong>Hitung Bunga (Interest Calculation):</strong>
                                                    <ul>
                                                        <li>Bunga dihitung otomatis berdasarkan <strong>Saldo Terendah</strong> (Lowest Balance) dalam bulan tersebut.</li>
                                                        <li>Rumus: <code>(Saldo Terendah x Suku Bunga% x Hari Bulan) / 365</code>.</li>
                                                        <li>Saat bunga diposting, sistem otomatis membuat Jurnal Akuntansi (Debit Beban Bunga, Kredit Simpanan).</li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- 3. Pinjaman & Penagihan -->
                                <div class="card mb-2">
                                    <div class="card-header" id="headingThree" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree" style="cursor: pointer;">
                                        <h5 class="mb-0 text-primary"><i class="fe fe-dollar-sign mr-2"></i> 3. Pinjaman & Penagihan (Collection)</h5>
                                    </div>
                                    <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#guideAccordion">
                                        <div class="card-body">
                                            <h6 class="font-weight-bold">A. Siklus Data Pinjaman</h6>
                                            <ol>
                                                <li><strong>Diajukan:</strong> Draft awal pinjaman. Belum aktif.</li>
                                                <li><strong>Disetujui:</strong> Telah diverifikasi, siap dicairkan.</li>
                                                <li><strong>Berjalan (Cair):</strong> Dana diserahkan. Angsuran mulai berjalan. Status ini yang dihitung dalam portofolio aktif.</li>
                                                <li><strong>Lunas:</strong> Seluruh kewajiban pokok dan bunga selesai dibayar.</li>
                                            </ol>

                                            <h6 class="font-weight-bold mt-3">B. Dashboard Penagihan & Kolektabilitas</h6>
                                            <p>Sistem mengklasifikasikan kualitas kredit (Kolektabilitas) berdasarkan hari keterlambatan (Overdue Days):</p>
                                            <ul>
                                                <li><span class="badge badge-success">Lancar</span>: 0 Hari (Tepat waktu)</li>
                                                <li><span class="badge badge-info">DPK (Dalam Perhatian Khusus)</span>: > 1 Hari</li>
                                                <li><span class="badge badge-warning">Kurang Lancar</span>: > 91 Hari</li>
                                                <li><span class="badge badge-danger">Diragukan</span>: > 121 Hari</li>
                                                <li><span class="badge badge-dark">Macet</span>: > 181 Hari</li>
                                            </ul>
                                            <p><small class="text-muted">*Batas hari dapat diubah di menu Pengaturan -> Kolektabilitas.</small></p>

                                            <h6 class="font-weight-bold mt-3">C. Antrian Lapangan (Field Queue)</h6>
                                            <ul>
                                                <li>Fitur untuk menugaskan kolektor mengunjungi peminjam bermasalah.</li>
                                                <li><strong>Tambah Tugas:</strong> Dari detail pinjaman, pilih "Tambah ke Antrian Lapangan".</li>
                                                <li><strong>Update Status:</strong> Petugas melaporkan hasil (Janji Bayar, Bayar Sebagian, Gagal) dan status tugas (Selesai/Dalam Proses).</li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- 4. Akuntansi -->
                                <div class="card mb-2">
                                    <div class="card-header" id="headingFour" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour" style="cursor: pointer;">
                                        <h5 class="mb-0 text-primary"><i class="fe fe-book mr-2"></i> 4. Akuntansi & Laporan Keuangan</h5>
                                    </div>
                                    <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#guideAccordion">
                                        <div class="card-body">
                                            <ul>
                                                <li><strong>COA (Chart of Accounts):</strong>
                                                    Daftar akun buku besar. Terdiri dari Aset (1xxx), Kewajiban (2xxx), Modal (3xxx), Pendapatan (4xxx), dan Beban (5xxx).
                                                    <br><em>Gunakan tombol "Default COA" di Pengaturan untuk membuat akun standar otomatis.</em>
                                                </li>
                                                <li><strong>Jurnal Umum (General Journal):</strong>
                                                    Tempat mencatat transaksi manual non-tunai atau penyesuaian. Total Debit harus sama dengan Kredit (Balance).
                                                </li>
                                                <li><strong>Buku Kas (Cash Book):</strong>
                                                    Melihat mutasi spesifik per akun Kas/Bank untuk rekonsiliasi.
                                                </li>
                                                <li><strong>Laporan (Reports):</strong>
                                                    <ul>
                                                        <li><strong>Neraca (Balance Sheet):</strong> Posisi keuangan per tanggal tertentu. (Aset = Kewajiban + Modal).</li>
                                                        <li><strong>Laba Rugi (Profit & Loss):</strong> Kinerja operasional (Pendapatan - Beban) dalam periode waktu.</li>
                                                        <li><strong>Arus Kas (Cash Flow):</strong> Laporan penerimaan dan pengeluaran kas riil.</li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <!-- 5. Pengaturan -->
                                <div class="card mb-2">
                                    <div class="card-header" id="headingFive" data-toggle="collapse" data-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive" style="cursor: pointer;">
                                        <h5 class="mb-0 text-primary"><i class="fe fe-settings mr-2"></i> 5. Pengaturan Sistem</h5>
                                    </div>
                                    <div id="collapseFive" class="collapse" aria-labelledby="headingFive" data-parent="#guideAccordion">
                                        <div class="card-body">
                                            <ul>
                                                <li><strong>Mapping Akuntansi:</strong>
                                                    Menghubungkan fitur aplikasi dengan Akun COA agar jurnal terbentuk otomatis.
                                                    <ul>
                                                        <li><em>Akun Kas:</em> Untuk pencairan/pembayaran tunai.</li>
                                                        <li><em>Akun Simpanan:</em> Untuk menampung dana anggota.</li>
                                                        <li><em>Akun Pendapatan:</em> Untuk bunga pinjaman dan denda.</li>
                                                    </ul>
                                                </li>
                                                <li><strong>Konfigurasi Pinjaman:</strong>
                                                    Mengatur default suku bunga, denda harian, dan biaya admin.
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- About -->
                <div class="tab-pane fade" id="about">
                    <div class="card">
                        <div class="card-header">
                            <h3 class="card-title">Tentang Aplikasi</h3>
                        </div>
                        <div class="card-body">
                            <div class="text-center mb-5">
                                @if(isset($settings['company_logo']) && $settings['company_logo'])
                                    <img src="{{ asset($settings['company_logo']) }}" alt="Logo" style="max-height: 80px;" class="mb-3">
                                @endif
                                <h2 class="mb-1">E-Koperasi</h2>
                                <p class="text-muted">Versi 1.0.0</p>
                            </div>

                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <p class="lead text-center">
                                        Aplikasi pengelolaan data anggota, simpanan, pinjaman, dan akuntansi keuangan berbasis web untuk membantu operasional koperasi secara efisien dan transparan.
                                    </p>

                                    <hr class="my-5">

                                    <div class="row">
                                        <div class="col-md-6">
                                            <h5><i class="fe fe-check-circle text-success mr-2"></i>Fitur Utama</h5>
                                            <ul class="list-unstyled">
                                                <li class="mb-2">Manajemen Anggota & Nasabah</li>
                                                <li class="mb-2">Simpanan & Perhitungan Bunga Otomatis</li>
                                                <li class="mb-2">Pinjaman, Angsuran & Kolektabilitas</li>
                                                <li class="mb-2">Akuntansi & Laporan Keuangan</li>
                                                <li class="mb-2">Manajemen Pengguna (Role-based)</li>
                                            </ul>
                                        </div>
                                        <div class="col-md-6">
                                            <h5><i class="fe fe-code text-info mr-2"></i>Informasi Teknis</h5>
                                            <ul class="list-unstyled">
                                                <li class="mb-2"><strong>Framework:</strong> Laravel 5.8</li>
                                                <li class="mb-2"><strong>Database:</strong> MySQL</li>
                                                <li class="mb-2"><strong>Frontend:</strong> Bootstrap 4 & Stisla</li>
                                                <li class="mb-2"><strong>Pengembang:</strong> Indra N. Utomo</li>
                                            </ul>
                                        </div>
                                    </div>

                                    <div class="text-center mt-5">
                                        <p class="text-muted mb-0">
                                            &copy; {{ date('Y') }} Indra N. Utomo. All rights reserved.
                                        </p>
                                    </div>
                                </div>
                            </div>
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
