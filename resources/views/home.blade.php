@extends('layouts.app')

@section('page-title', 'Dashboard')

@section('content-app')
<div class="row row-cards">
    <!-- Top Stats -->
    <div class="col-6 col-sm-4 col-lg-2">
        <div class="card">
            <div class="card-body p-3 text-center">
                <div class="text-right text-green">
                    &nbsp;
                </div>
                <div class="h1 m-0">{{ $dueToday->count() }}</div>
                <div class="text-muted mb-4">Jatuh Tempo Hari Ini</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-sm-4 col-lg-2">
        <div class="card">
            <div class="card-body p-3 text-center">
                <div class="text-right text-green">
                    &nbsp;
                </div>
                <div class="h1 m-0">{{ count($collectibilityStats) }}</div>
                <div class="text-muted mb-4">Kategori Kolektabilitas</div>
            </div>
        </div>
    </div>
    <div class="col-12 col-sm-4 col-lg-8">
        <div class="card">
            <div class="card-body p-3">
                <div class="d-flex align-items-center">
                    <span class="stamp stamp-md bg-blue mr-3">
                        <i class="fe fe-dollar-sign"></i>
                    </span>
                    <div>
                        <h4 class="m-0"><a href="javascript:void(0)">{{ format_rupiah($totalDisbursed) }}</a></h4>
                        <small class="text-muted">Total Dana Turun (Disbursed)</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Row 2: Charts and Tables -->
    <div class="col-lg-6 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Grafik Pendapatan (6 Bulan Terakhir)</h3>
            </div>
            <div class="card-body">
                <div id="chart-revenue" style="height: 16rem"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-6 col-xl-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Piutang & Kolektabilitas</h3>
                <div class="card-options">
                    <a href="{{ route('collections.index') }}" class="btn btn-sm btn-primary">Lihat Detail</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap">
                    <thead>
                        <tr>
                            <th>Kolektabilitas</th>
                            <th class="text-center">Jumlah Pinjaman</th>
                            <th class="text-right">Total Piutang (Pokok)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($collectibilityStats as $stat)
                            @php
                                $badgeColor = 'bg-success'; // Default Lancar
                                if($stat->kolektabilitas == 'Dalam Perhatian Khusus') $badgeColor = 'bg-warning';
                                if($stat->kolektabilitas == 'Kurang Lancar') $badgeColor = 'bg-orange';
                                if($stat->kolektabilitas == 'Diragukan') $badgeColor = 'bg-danger';
                                if($stat->kolektabilitas == 'Macet') $badgeColor = 'bg-dark';
                            @endphp
                            <tr>
                                <td>
                                    <span class="status-icon {{ $badgeColor }}"></span> {{ $stat->kolektabilitas ?? 'N/A' }}
                                </td>
                                <td class="text-center">{{ $stat->count_loans }}</td>
                                <td class="text-right">{{ format_rupiah($stat->total_outstanding) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted">Tidak ada data piutang</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Row 3: Due Today & Disbursed Chart -->
    <div class="col-md-12">
        <div class="card">
            @if($dueToday->count() > 0)
            <div class="card-status bg-red"></div>
            @endif
            <div class="card-header">
                <h3 class="card-title">Pinjaman Jatuh Tempo Hari Ini ({{ \Carbon\Carbon::today()->format('d M Y') }})</h3>
                <div class="card-options">
                    <span class="tag tag-red">{{ $dueToday->count() }} Tagihan</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap">
                    <thead>
                        <tr>
                            <th>No. Pinjaman</th>
                            <th>Nama</th>
                            <th>Angsuran Ke</th>
                            <th class="text-right">Total Tagihan</th>
                            <th class="text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($dueToday as $installment)
                            <tr>
                                <td>
                                    <a href="{{ route('loans.show', $installment->loan->id) }}">{{ $installment->loan->kode_pinjaman }}</a>
                                </td>
                                <td>
                                    @if($installment->loan->member)
                                        {{ $installment->loan->member->nama }} (Anggota)
                                    @elseif($installment->loan->nasabah)
                                        {{ $installment->loan->nasabah->nama }} (Nasabah)
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>{{ $installment->angsuran_ke }}</td>
                                <td class="text-right">{{ format_rupiah($installment->total_angsuran + $installment->denda) }}</td>
                                <td class="text-right">
                                    <a href="{{ route('loans.show', $installment->loan->id) }}" class="btn btn-secondary btn-sm">Lihat</a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">Tidak ada pinjaman jatuh tempo hari ini.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Row 4: Disbursed Trend -->
     <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title" id="chart-disbursed-title">Grafik Total Dana Turun (6 Bulan Terakhir)</h3>
                <div class="card-options">
                    <div class="dropdown">
                        <button class="btn btn-secondary btn-sm dropdown-toggle" type="button" id="disbursedFilterDropdown" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fe fe-calendar"></i> Filter Waktu
                        </button>
                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="disbursedFilterDropdown">
                            <a class="dropdown-item" href="#" data-filter="today">Hari Ini</a>
                            <a class="dropdown-item" href="#" data-filter="1_month">1 Bulan Terakhir</a>
                            <a class="dropdown-item" href="#" data-filter="6_months">6 Bulan Terakhir</a>
                            <a class="dropdown-item" href="#" data-filter="1_year">1 Tahun Terakhir</a>
                            <a class="dropdown-item" href="#" data-filter="last_year">Tahun Lalu</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#" data-filter="custom">Rentang Waktu</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <div id="custom-range-container" class="mb-3" style="display: none;">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Dari Tanggal</label>
                                <input type="text" class="form-control datepicker" id="custom-start-date" placeholder="YYYY-MM-DD" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">Sampai Tanggal</label>
                                <input type="text" class="form-control datepicker" id="custom-end-date" placeholder="YYYY-MM-DD" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="form-label">&nbsp;</label>
                                <button type="button" class="btn btn-primary btn-block" id="apply-custom-range">Tampilkan</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="chart-disbursed" style="height: 16rem"></div>
            </div>
        </div>
    </div>

    <!-- Existing Mutations -->
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title" id="recent_mutations_today_desc">{{ __('recent_mutations_today') }}</h3>
                <div class="card-options">
                    <a href="{{ route('mutations.feed') }}" class="btn btn-sm btn-primary">Lihat Semua</a>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table card-table table-vcenter text-nowrap datatable" aria-describedby="recent_mutations_today_desc">
                    <thead>
                        <tr>
                            <th scope="col" class="w-1">No.</th>
                            <th scope="col">{{ __('member') }}</th>
                            <th scope="col">{{ __('date') }}</th>
                            <th scope="col">{{ __('note') }}</th>
                            <th scope="col" class="text-right">{{ __('debit') }}</th>
                            <th scope="col" class="text-right">{{ __('credit') }}</th>
                            <th scope="col" class="text-right">{{ __('balance') }}</th>
                            <th scope="col"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($mutations as $mutation)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>
                                    @if($mutation->member)
                                        <div>{{ $mutation->member->nama }} (Anggota)</div>
                                        <div class="small text-muted">
                                            NIK: {{ $mutation->member->nik }}
                                        </div>
                                    @elseif($mutation->nasabah)
                                        <div>{{ $mutation->nasabah->nama }} (Nasabah)</div>
                                        <div class="small text-muted">
                                            NIK: {{ $mutation->nasabah->nik }}
                                        </div>
                                    @else
                                        <div>-</div>
                                    @endif
                                </td>
                                <td>{{ $mutation->date instanceof \Carbon\Carbon ? $mutation->date->format('Y-m-d') : $mutation->date }}</td>
                                <td>{{ $mutation->description }}</td>
                                <td class="text-right">{{ format_rupiah($mutation->debit) }}</td>
                                <td class="text-right">{{ format_rupiah($mutation->credit) }}</td>
                                <td class="text-right">
                                    <div>{{ format_rupiah($mutation->balance) }}</div>
                                    <small class="text-muted">
                                        @if($mutation->balance_type == 'Tabungan')
                                            <span class="text-success">(Tabungan)</span>
                                        @else
                                            <span class="text-warning">(Pinjaman)</span>
                                        @endif
                                    </small>
                                </td>
                                <td class="text-muted">
                                    @if($mutation->date instanceof \Carbon\Carbon)
                                        {{ $mutation->date->diffForHumans() }}
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7">{{ __('data_not_found') }}</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    require(['c3', 'jquery'], function(c3, $) {
        $(document).ready(function(){

            // Revenue Chart
            var revenueData = {
                columns: [
                    ['Bunga', @foreach($revenueStats as $stat) {{ $stat->total_bunga }}, @endforeach],
                    ['Denda', @foreach($revenueStats as $stat) {{ $stat->total_denda }}, @endforeach]
                ],
                type: 'bar',
                groups: [
                    ['Bunga', 'Denda']
                ],
                colors: {
                    'Bunga': '#467fcf',
                    'Denda': '#e74c3c'
                }
            };

            var chartRevenue = c3.generate({
                bindto: '#chart-revenue',
                data: revenueData,
                axis: {
                    x: {
                        type: 'category',
                        categories: [@foreach($revenueStats as $stat) '{{ $stat->month }}', @endforeach]
                    },
                    y: {
                        tick: {
                            format: function (d) { return 'Rp ' + d.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); }
                        }
                    }
                }
            });

            // Disbursed Chart
            var chartDisbursed = c3.generate({
                bindto: '#chart-disbursed',
                data: {
                    columns: [
                        ['Dana Turun', @foreach($disbursedTrend as $stat) {{ $stat->total }}, @endforeach]
                    ],
                    type: 'area', // Area chart for "progress" feel
                    colors: {
                        'Dana Turun': '#5eba00'
                    }
                },
                axis: {
                    x: {
                        type: 'category',
                        categories: [@foreach($disbursedTrend as $stat) '{{ $stat->month }}', @endforeach],
                        tick: {
                            fit: true,
                            culling: {
                                max: 8
                            }
                        }
                    },
                    y: {
                        tick: {
                            format: function (d) { return 'Rp ' + d.toString().replace(/\B(?=(\d{3})+(?!\d))/g, "."); }
                        }
                    }
                }
            });

            // Handle Filter Selection
            $('[data-filter]').on('click', function(e) {
                e.preventDefault();
                var filter = $(this).data('filter');
                var label = $(this).text();

                if (filter === 'custom') {
                    $('#custom-range-container').slideDown();
                } else {
                    $('#custom-range-container').slideUp();
                    loadDisbursedChart(filter);
                    updateChartTitle(label);
                }
            });

            // Initialize Datepickers
            $('.datepicker').datepicker({
                format: 'yyyy-mm-dd',
                autoclose: true,
                todayHighlight: true
            });

            // Handle Custom Range Apply
            $('#apply-custom-range').on('click', function() {
                var start = $('#custom-start-date').val();
                var end = $('#custom-end-date').val();

                if (start && end) {
                    loadDisbursedChart('custom', start, end);
                    updateChartTitle('Rentang Waktu (' + start + ' s/d ' + end + ')');
                } else {
                    alert('Harap isi kedua tanggal.');
                }
            });

            function updateChartTitle(label) {
                $('#chart-disbursed-title').text('Grafik Total Dana Turun (' + label + ')');
            }

            function loadDisbursedChart(filter, startDate = null, endDate = null) {
                $.ajax({
                    url: '{{ route("home.chart.disbursed-trend") }}',
                    method: 'GET',
                    data: {
                        filter: filter,
                        start_date: startDate,
                        end_date: endDate
                    },
                    success: function(response) {
                        chartDisbursed.load({
                            columns: [
                                ['Dana Turun'].concat(response.data)
                            ],
                            categories: response.labels,
                            unload: ['Dana Turun']
                        });
                        // Force redraw to apply tick options potentially
                        setTimeout(function(){ chartDisbursed.flush(); }, 100);
                    },
                    error: function(xhr) {
                        console.error(xhr);
                        alert('Gagal memuat data grafik.');
                    }
                });
            }

        });
    });
</script>
@endsection
