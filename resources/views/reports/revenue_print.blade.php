<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Pendapatan</title>
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
            .alert-info { background-color: #d1ecf1 !important; color: #0c5460 !important; border-color: #bee5eb !important; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container-fluid page-print">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h3>Laporan Pendapatan</h3>
                <p class="text-muted">{{ date('d F Y', strtotime($startDate)) }} - {{ date('d F Y', strtotime($endDate)) }}</p>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-12">
                <div class="alert alert-info">
                    <strong>Total Pendapatan:</strong> Rp {{ number_format($totalRevenue, 0, ',', '.') }}
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead>
                            <tr>
                                <th>Tanggal</th>
                                <th>Akun Pendapatan</th>
                                <th>Deskripsi Transaksi</th>
                                <th>Jumlah (Kredit)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($revenues as $item)
                            <tr>
                                <td>{{ $item->journalEntry->transaction_date }}</td>
                                <td>{{ $item->account->name }}</td>
                                <td>{{ $item->description ?? $item->journalEntry->description }}</td>
                                <td class="text-right">
                                    Rp {{ number_format($item->credit - $item->debit, 0, ',', '.') }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">Tidak ada pendapatan dalam periode ini.</td>
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
