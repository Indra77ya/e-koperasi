<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Pinjaman Outstanding</title>
    <!-- Use dashboard.css for consistent table styling -->
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet" />
    <style>
        body {
            background-color: white; /* Ensure white background for printing */
            font-family: "Source Sans Pro", -apple-system, BlinkMacSystemFont, "Segoe UI", "Helvetica Neue", Arial, sans-serif;
        }
        .page-print {
            padding: 20px;
        }
        @media print {
            .no-print {
                display: none;
            }
            /* Remove shadows and borders for print */
            .card {
                box-shadow: none;
                border: none;
            }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container-fluid page-print">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h3>Laporan Pinjaman Outstanding</h3>
                <p class="text-muted">{{ date('d F Y') }}</p>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>No. Pinjaman</th>
                                <th>Nama Anggota/Nasabah</th>
                                <th>Jumlah Pinjaman</th>
                                <th>Sisa Pinjaman</th>
                                <th>Status</th>
                                <th>Tanggal Cair</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($loans as $loan)
                            <tr>
                                <td>{{ $loan->kode_pinjaman }}</td>
                                <td>
                                    @if($loan->member)
                                        {{ $loan->member->name }} <span class="badge badge-info">Anggota</span>
                                    @elseif($loan->nasabah)
                                        {{ $loan->nasabah->name }} <span class="badge badge-warning">Nasabah</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td>Rp {{ number_format($loan->jumlah_pinjaman, 0, ',', '.') }}</td>
                                <td>Rp {{ number_format($loan->remaining_balance ?? 0, 0, ',', '.') }}</td>
                                <td>
                                    <span class="badge badge-primary">{{ ucfirst($loan->status) }}</span>
                                </td>
                                <td>{{ $loan->created_at->format('d-m-Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada data.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row mt-4 no-print">
            <div class="col-12 text-center">
                <button class="btn btn-secondary" onclick="window.print()">Print Again</button>
                <button class="btn btn-outline-danger" onclick="window.close()">Close</button>
            </div>
        </div>
    </div>
</body>
</html>
