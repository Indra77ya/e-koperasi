@extends('layouts.app')

@section('page-title')
    Modul Laporan
@endsection

@section('content-app')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Pusat Laporan</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Report Item -->
                    <div class="col-md-4 mb-4">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h5 class="card-title">Pinjaman Outstanding</h5>
                                <p class="card-text text-muted">Laporan sisa pinjaman yang sedang berjalan.</p>
                                <a href="{{ route('reports.outstanding') }}" class="btn btn-primary btn-block">Lihat Laporan</a>
                            </div>
                        </div>
                    </div>
                    <!-- Report Item -->
                    <div class="col-md-4 mb-4">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h5 class="card-title">Piutang Macet</h5>
                                <p class="card-text text-muted">Laporan pinjaman dengan status macet.</p>
                                <a href="{{ route('reports.bad_debt') }}" class="btn btn-primary btn-block">Lihat Laporan</a>
                            </div>
                        </div>
                    </div>
                    <!-- Report Item -->
                    <div class="col-md-4 mb-4">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h5 class="card-title">Denda & Pendapatan Bunga</h5>
                                <p class="card-text text-muted">Ringkasan pendapatan dari bunga dan denda.</p>
                                <a href="{{ route('reports.revenue') }}" class="btn btn-primary btn-block">Lihat Laporan</a>
                            </div>
                        </div>
                    </div>
                    <!-- Report Item -->
                    <div class="col-md-4 mb-4">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h5 class="card-title">Arus Kas</h5>
                                <p class="card-text text-muted">Laporan kas masuk dan keluar (Harian/Mingguan/Bulanan).</p>
                                <a href="{{ route('reports.cash_flow') }}" class="btn btn-primary btn-block">Lihat Laporan</a>
                            </div>
                        </div>
                    </div>
                    <!-- Report Item -->
                    <div class="col-md-4 mb-4">
                        <div class="card bg-light border-0">
                            <div class="card-body">
                                <h5 class="card-title">Laporan Jaminan</h5>
                                <p class="card-text text-muted">Daftar aset jaminan yang tersimpan.</p>
                                <a href="{{ route('reports.collateral') }}" class="btn btn-primary btn-block">Lihat Laporan</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
