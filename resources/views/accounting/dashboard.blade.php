@extends('layouts.app')

@section('content-app')
<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Dashboard Akuntansi</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-6 col-lg-3">
                        <div class="card p-3">
                            <div class="d-flex align-items-center">
                                <span class="stamp stamp-md bg-blue mr-3">
                                    <i class="fe fe-dollar-sign"></i>
                                </span>
                                <div>
                                    <h4 class="m-0"><a href="{{ route('accounting.cash_book') }}">Buku Kas</a></h4>
                                    <small class="text-muted">Kelola Kas & Bank</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                         <div class="card p-3">
                            <div class="d-flex align-items-center">
                                <span class="stamp stamp-md bg-green mr-3">
                                    <i class="fe fe-book-open"></i>
                                </span>
                                <div>
                                    <h4 class="m-0"><a href="{{ route('accounting.journals') }}">Jurnal</a></h4>
                                    <small class="text-muted">Jurnal Umum</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                         <div class="card p-3">
                            <div class="d-flex align-items-center">
                                <span class="stamp stamp-md bg-orange mr-3">
                                    <i class="fe fe-list"></i>
                                </span>
                                <div>
                                    <h4 class="m-0"><a href="{{ route('accounting.coa') }}">Kode Akun (COA)</a></h4>
                                    <small class="text-muted">Kelola Akun</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-lg-3">
                         <div class="card p-3">
                            <div class="d-flex align-items-center">
                                <span class="stamp stamp-md bg-red mr-3">
                                    <i class="fe fe-bar-chart"></i>
                                </span>
                                <div>
                                    <h4 class="m-0"><a href="{{ route('accounting.reports.neraca') }}">Laporan</a></h4>
                                    <small class="text-muted">Laporan Keuangan</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
