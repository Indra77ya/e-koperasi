<div class="header py-4">
    <div class="container">
        <div class="d-flex">
            <a class="header-brand" href="{{ route('home') }}">
                {{ __('app_name') }}
            </a>
            <div class="d-flex order-lg-2 ml-auto">
                @include('shared.lang')
                <div class="dropdown">
                    <a href="#" class="nav-link pr-0 leading-none" data-toggle="dropdown">
                    <span class="avatar" style="background-image: url(https://randomuser.me/api/portraits/men/43.jpg)"></span>
                    <span class="ml-2 d-none d-lg-block">
                        <span class="text-default">{{ Auth::user()->name }}</span>
                        <small class="text-muted d-block mt-1">Administrator</small>
                    </span>
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                        <a class="dropdown-item" href="{{ url('profile') }}">
                            <i class="dropdown-icon fe fe-user" aria-hidden="true"></i> {{ __('menu.profile') }}
                        </a>
                    </div>
                </div>
            </div>
            <a href="#" class="header-toggler d-lg-none ml-3 ml-lg-0" data-toggle="collapse" data-target="#headerMenuCollapse">
                <span class="header-toggler-icon"></span>
            </a>
        </div>
    </div>
</div>

<div class="header collapse d-lg-flex p-0" id="headerMenuCollapse">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-3 ml-auto">
            </div>
            <div class="col-lg order-lg-first">
                <ul class="nav nav-tabs border-0 flex-column flex-lg-row">
                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link"><i class="fe fe-home" aria-hidden="true"></i> {{ __('menu.home') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('members.index') }}" class="nav-link"><i class="fe fe-users" aria-hidden="true"></i> {{ __('menu.member') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('nasabahs.index') }}" class="nav-link"><i class="fe fe-user-check" aria-hidden="true"></i> Nasabah</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('loans.index') }}" class="nav-link"><i class="fe fe-activity" aria-hidden="true"></i> Pinjaman</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link" data-toggle="dropdown"><i class="fe fe-book" aria-hidden="true"></i> Akuntansi</a>
                        <div class="dropdown-menu dropdown-menu-arrow">
                            <a href="{{ route('accounting.cash_book') }}" class="dropdown-item">Buku Kas & Bank</a>
                            <a href="{{ route('accounting.journals') }}" class="dropdown-item">Jurnal Umum</a>
                            <a href="{{ route('accounting.coa') }}" class="dropdown-item">Chart of Accounts</a>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('accounting.reports.neraca') }}" class="dropdown-item">Neraca</a>
                            <a href="{{ route('accounting.reports.laba_rugi') }}" class="dropdown-item">Laba Rugi</a>
                            <a href="{{ route('accounting.reports.arus_kas') }}" class="dropdown-item">Arus Kas</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('reports.index') }}" class="nav-link"><i class="fe fe-file-text" aria-hidden="true"></i> Laporan</a>
                    </li>
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link" data-toggle="dropdown"><i class="fe fe-bell" aria-hidden="true"></i> Penagihan</a>
                        <div class="dropdown-menu dropdown-menu-arrow">
                            <a href="{{ route('collections.index') }}" class="dropdown-item">Dashboard Penagihan</a>
                            <a href="{{ route('collections.queue') }}" class="dropdown-item">Antrian Lapangan</a>
                        </div>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('deposits.index') }}" class="nav-link"><i class="fe fe-dollar-sign" aria-hidden="true"></i> {{ __('menu.deposit') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('withdrawals.index') }}" class="nav-link"><i class="fe fe-hash" aria-hidden="true"></i> {{ __('menu.withdrawal') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('mutations') }}" class="nav-link"><i class="fe fe-printer" aria-hidden="true"></i> {{ __('menu.mutation') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('bankinterests') }}" class="nav-link"><i class="fe fe-box" aria-hidden="true"></i> {{ __('menu.interest') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ url('profile') }}" class="nav-link"><i class="fe fe-user" aria-hidden="true"></i> {{ __('menu.profile') }}</a>
                    </li>
                    <li class="nav-item">
                        <a href="{{ route('logout') }}" class="nav-link" onclick="event.preventDefault();document.getElementById('logout-form').submit();"><i class="fe fe-log-out" aria-hidden="true"></i> {{ __('menu.logout') }}</a>

                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
