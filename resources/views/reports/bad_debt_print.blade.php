<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Piutang Macet</title>
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet" />
    <style>
        body {
            background-color: white;
            font-family: "Source Sans Pro", -apple-system, BlinkMacSystemFont, "Segoe UI", "Helvetica Neue", Arial, sans-serif;
        }
        .page-print {
            padding: 20px;
        }
        @media print {
            body {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .no-print {
                display: none;
            }
            .card {
                box-shadow: none;
                border: none;
            }
            /* Force badge colors */
            .badge {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
                color: white !important;
            }
            .badge-info { background-color: #17a2b8 !important; }
            .badge-warning { background-color: #ffc107 !important; color: #212529 !important; }
            .badge-primary { background-color: #007bff !important; }
            .badge-success { background-color: #28a745 !important; }
            .badge-danger { background-color: #dc3545 !important; }
            .badge-secondary { background-color: #6c757d !important; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container-fluid page-print">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h3>Laporan Piutang Macet</h3>
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
                                <th>Tanggal Macet</th>
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
                                <td>{{ $loan->updated_at->format('d-m-Y') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data piutang macet.</td>
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
