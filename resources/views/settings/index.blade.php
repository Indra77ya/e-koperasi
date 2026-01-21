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
            <a href="#saving" data-toggle="list" class="list-group-item list-group-item-action">
                <span class="icon mr-3"><i class="fe fe-briefcase"></i></span>Simpanan
            </a>
            <a href="#accounting" data-toggle="list" class="list-group-item list-group-item-action">
                <span class="icon mr-3"><i class="fe fe-book"></i></span>Akuntansi
            </a>
            <a href="#system" data-toggle="list" class="list-group-item list-group-item-action">
                <span class="icon mr-3"><i class="fe fe-settings"></i></span>Sistem
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
                                <form action="{{ route('accounting.coa.seed') }}" method="POST" class="d-inline-block" onsubmit="return confirm('Apakah Anda yakin ingin melakukan seed COA bawaan? Tindakan ini mungkin akan menduplikasi akun jika sudah ada.');">
                                    @csrf
                                    <button type="submit" class="btn btn-secondary btn-sm">
                                        <i class="fe fe-refresh-cw"></i> Isi COA
                                    </button>
                                </form>
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
