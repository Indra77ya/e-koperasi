<div class="header py-4">
    <div class="container">
        <div class="d-flex">
            <a class="header-brand" href="{{ route('home') }}">
                {{ __('app_name') }}
            </a>
            <div class="d-flex order-lg-2 ml-auto">
                @include('shared.lang')
                <div class="dropdown d-none d-md-flex">
                    <a class="nav-link icon" data-toggle="dropdown">
                        <i class="fe fe-bell"></i>
                        @if(isset($headerAlerts) && $headerAlerts['total_alerts'] > 0)
                            <span class="nav-unread"></span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-right dropdown-menu-arrow">
                        @if(isset($headerAlerts))
                            <!-- Pending Loans -->
                            @if($headerAlerts['pending_loans'] > 0)
                            <a href="{{ route('loans.index', ['status' => 'diajukan']) }}" class="dropdown-item d-flex">
                                <span class="avatar mr-3 align-self-center bg-orange-lightest text-orange">
                                    <i class="fe fe-file-text"></i>
                                </span>
                                <div>
                                    <strong>{{ $headerAlerts['pending_loans'] }} Pinjaman</strong> perlu persetujuan.
                                    <div class="small text-muted">Lihat daftar pinjaman</div>
                                </div>
                            </a>
                            @endif

                            <!-- Due Today -->
                            @if($headerAlerts['due_today'] > 0)
                            <a href="{{ route('collections.index') }}" class="dropdown-item d-flex">
                                <span class="avatar mr-3 align-self-center bg-red-lightest text-red">
                                    <i class="fe fe-clock"></i>
                                </span>
                                <div>
                                    <strong>{{ $headerAlerts['due_today'] }} Tagihan</strong> jatuh tempo hari ini.
                                    <div class="small text-muted">Cek dashboard penagihan</div>
                                </div>
                            </a>
                            @endif

                            <!-- Notifications -->
                            @foreach($headerAlerts['notifications'] as $notification)
                                <a href="#" class="dropdown-item d-flex">
                                    <span class="avatar mr-3 align-self-center bg-blue-lightest text-blue">
                                        <i class="fe fe-info"></i>
                                    </span>
                                    <div>
                                        {{ $notification->data['message'] ?? 'Notification' }}
                                        <div class="small text-muted">{{ $notification->created_at->diffForHumans() }}</div>
                                    </div>
                                </a>
                            @endforeach

                            @if($headerAlerts['total_alerts'] == 0)
                                <div class="dropdown-item text-center text-muted">
                                    Tidak ada notifikasi baru.
                                </div>
                            @endif

                            @if($headerAlerts['unread_notifications_count'] > 0)
                                <div class="dropdown-divider"></div>
                                <a href="{{ route('notifications.readAll') }}" class="dropdown-item text-center text-muted-dark">Tandai semua sudah dibaca</a>
                            @endif
                        @else
                             <div class="dropdown-item text-center text-muted">
                                Loading...
                            </div>
                        @endif
                    </div>
                </div>
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
