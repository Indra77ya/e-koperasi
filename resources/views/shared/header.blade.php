<div class="header py-4">
    <div class="container">
        <div class="d-flex">
            <a class="header-brand" href="{{ route('home') }}">
                @if($logo = \App\Models\Setting::get('company_logo'))
                    <img src="{{ asset($logo) }}" class="header-brand-img" alt="logo" style="height: 2rem;">
                @else
                    <img src="{{ asset('images/logo-default.png') }}" class="header-brand-img" alt="logo" style="height: 2rem;">
                @endif
                {{ \App\Models\Setting::get('company_name', __('app_name')) }}
            </a>
            <div class="d-flex order-lg-2 ml-auto">
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
                        <a class="dropdown-item" href="{{ route('settings.index') }}">
                            <i class="dropdown-icon fe fe-settings" aria-hidden="true"></i> {{ __('menu.settings') }}
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="{{ route('logout') }}" onclick="event.preventDefault();document.getElementById('logout-form-profile').submit();">
                            <i class="dropdown-icon fe fe-log-out" aria-hidden="true"></i> {{ __('menu.logout') }}
                        </a>
                        <form id="logout-form-profile" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
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
                    <!-- Beranda -->
                    <li class="nav-item">
                        <a href="{{ route('home') }}" class="nav-link {{ Request::is('home') ? 'active' : '' }}">
                            <i class="fe fe-home" aria-hidden="true"></i> {{ __('menu.home') }}
                        </a>
                    </li>

                    <!-- Anggota -->
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link {{ (Request::is('members*') || Request::is('nasabahs*')) ? 'active' : '' }}" data-toggle="dropdown">
                            <i class="fe fe-users" aria-hidden="true"></i> Anggota
                        </a>
                        <div class="dropdown-menu dropdown-menu-arrow">
                            <a href="{{ route('members.index') }}" class="dropdown-item {{ Request::is('members*') ? 'active' : '' }}">
                                {{ __('menu.member') }}
                            </a>
                            <a href="{{ route('nasabahs.index') }}" class="dropdown-item {{ Request::is('nasabahs*') ? 'active' : '' }}">
                                Nasabah
                            </a>
                        </div>
                    </li>

                    <!-- Pinjaman -->
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link {{ (Request::is('loans*') || Request::is('collections*')) ? 'active' : '' }}" data-toggle="dropdown">
                            <i class="fe fe-activity" aria-hidden="true"></i> Pinjaman
                        </a>
                        <div class="dropdown-menu dropdown-menu-arrow">
                            <a href="{{ route('loans.index') }}" class="dropdown-item {{ Request::is('loans*') ? 'active' : '' }}">
                                Data Pinjaman
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('collections.index') }}" class="dropdown-item {{ (Request::is('collections') || Request::is('collections/data')) ? 'active' : '' }}">
                                Dashboard Penagihan
                            </a>
                            <a href="{{ route('collections.queue') }}" class="dropdown-item {{ Request::is('collections/queue*') ? 'active' : '' }}">
                                Antrian Lapangan
                            </a>
                        </div>
                    </li>

                    <!-- Transaksi -->
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link {{ (Request::is('deposits*') || Request::is('withdrawals*') || Request::is('mutations*') || Request::is('bankinterests*')) ? 'active' : '' }}" data-toggle="dropdown">
                            <i class="fe fe-dollar-sign" aria-hidden="true"></i> Transaksi
                        </a>
                        <div class="dropdown-menu dropdown-menu-arrow">
                            <a href="{{ route('deposits.index') }}" class="dropdown-item {{ Request::is('deposits*') ? 'active' : '' }}">
                                {{ __('menu.deposit') }}
                            </a>
                            <a href="{{ route('withdrawals.index') }}" class="dropdown-item {{ Request::is('withdrawals*') ? 'active' : '' }}">
                                {{ __('menu.withdrawal') }}
                            </a>
                            <a href="{{ url('mutations') }}" class="dropdown-item {{ Request::is('mutations*') ? 'active' : '' }}">
                                {{ __('menu.mutation') }}
                            </a>
                            <a href="{{ url('bankinterests') }}" class="dropdown-item {{ Request::is('bankinterests*') ? 'active' : '' }}">
                                {{ __('menu.interest') }}
                            </a>
                        </div>
                    </li>

                    <!-- Akuntansi -->
                    <li class="nav-item dropdown">
                        <a href="#" class="nav-link {{ Request::is('accounting*') ? 'active' : '' }}" data-toggle="dropdown">
                            <i class="fe fe-book" aria-hidden="true"></i> Akuntansi
                        </a>
                        <div class="dropdown-menu dropdown-menu-arrow">
                            <a href="{{ route('accounting.cash_book') }}" class="dropdown-item {{ Request::is('accounting/cash-book*') ? 'active' : '' }}">
                                Buku Kas & Bank
                            </a>
                            <a href="{{ route('accounting.journals') }}" class="dropdown-item {{ Request::is('accounting/journals*') ? 'active' : '' }}">
                                Jurnal Umum
                            </a>
                            <a href="{{ route('accounting.coa') }}" class="dropdown-item {{ Request::is('accounting/coa*') ? 'active' : '' }}">
                                Chart of Accounts
                            </a>
                            <div class="dropdown-divider"></div>
                            <a href="{{ route('accounting.reports.neraca') }}" class="dropdown-item {{ Request::is('accounting/reports/neraca*') ? 'active' : '' }}">
                                Neraca
                            </a>
                            <a href="{{ route('accounting.reports.laba_rugi') }}" class="dropdown-item {{ Request::is('accounting/reports/laba-rugi*') ? 'active' : '' }}">
                                Laba Rugi
                            </a>
                            <a href="{{ route('accounting.reports.arus_kas') }}" class="dropdown-item {{ Request::is('accounting/reports/arus-kas*') ? 'active' : '' }}">
                                Arus Kas
                            </a>
                        </div>
                    </li>

                    <!-- Laporan -->
                    <li class="nav-item">
                        <a href="{{ route('reports.index') }}" class="nav-link {{ Request::is('reports*') ? 'active' : '' }}">
                            <i class="fe fe-file-text" aria-hidden="true"></i> Laporan
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>
