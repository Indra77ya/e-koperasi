@extends('layouts.app')

@section('page-title', 'Pengaturan')

@section('content-app')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <ul class="nav nav-tabs card-header-tabs" id="setting-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="general-tab" data-toggle="tab" href="#general" role="tab">Profil Koperasi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="loan-tab" data-toggle="tab" href="#loan" role="tab">Pinjaman</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="accounting-tab" data-toggle="tab" href="#accounting" role="tab">Akuntansi</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="system-tab" data-toggle="tab" href="#system" role="tab">Sistem</a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <form action="{{ route('settings.update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="tab-content" id="setting-tabs-content">
                        <!-- General -->
                        <div class="tab-pane fade show active" id="general" role="tabpanel">
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
                                    <div class="mb-2">
                                        <img src="{{ asset($settings['company_logo']) }}" alt="Logo" style="max-height: 50px;">
                                    </div>
                                @endif
                                <div class="custom-file">
                                    <input type="file" class="custom-file-input" name="company_logo">
                                    <label class="custom-file-label">Pilih file logo (SVG/PNG/JPG)</label>
                                </div>
                             </div>
                        </div>

                        <!-- Loan -->
                        <div class="tab-pane fade" id="loan" role="tabpanel">
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

                        <!-- Accounting -->
                        <div class="tab-pane fade" id="accounting" role="tabpanel">
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

                        <!-- System -->
                        <div class="tab-pane fade" id="system" role="tabpanel">
                            <div class="form-group">
                                <label class="form-label">Notifikasi Jatuh Tempo (Hari Sebelum)</label>
                                <input type="number" class="form-control" name="notification_due_date_threshold" value="{{ $settings['notification_due_date_threshold'] ?? 0 }}">
                                <small class="text-muted">Masukkan 0 untuk notifikasi pada hari H.</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 text-right">
                        <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
