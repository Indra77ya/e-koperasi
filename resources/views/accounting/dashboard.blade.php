@extends('layouts.app')

@section('content-app')
<div class="row row-cards">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Accounting Dashboard</h3>
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
                                    <h4 class="m-0"><a href="{{ route('accounting.cash_book') }}">Cash Book</a></h4>
                                    <small class="text-muted">Manage Cash & Bank</small>
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
                                    <h4 class="m-0"><a href="{{ route('accounting.journals') }}">Journals</a></h4>
                                    <small class="text-muted">General Ledger</small>
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
                                    <h4 class="m-0"><a href="{{ route('accounting.coa') }}">Chart of Accounts</a></h4>
                                    <small class="text-muted">Manage Accounts</small>
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
                                    <h4 class="m-0"><a href="{{ route('accounting.reports.neraca') }}">Reports</a></h4>
                                    <small class="text-muted">Financial Statements</small>
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
