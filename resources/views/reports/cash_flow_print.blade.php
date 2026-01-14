<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Arus Kas</title>
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
            .alert-success { background-color: #d4edda !important; color: #155724 !important; border-color: #c3e6cb !important; }
            .alert-danger { background-color: #f8d7da !important; color: #721c24 !important; border-color: #f5c6cb !important; }
            .text-success { color: #28a745 !important; }
            .text-danger { color: #dc3545 !important; }
        }
    </style>
</head>
<body onload="window.print()">
    <div class="container-fluid page-print">
        <div class="row mb-4">
            <div class="col-12 text-center">
                <h3>Laporan Arus Kas</h3>
                <p class="text-muted">{{ date('d F Y', strtotime($startDate)) }} - {{ date('d F Y', strtotime($endDate)) }}</p>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-6">
                <div class="alert alert-success">
                    <strong>Total Masuk:</strong> Rp {{ number_format($totalIn, 0, ',', '.') }}
                </div>
            </div>
            <div class="col-6">
                <div class="alert alert-danger">
                    <strong>Total Keluar:</strong> Rp {{ number_format($totalOut, 0, ',', '.') }}
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
                                <th>Ref</th>
                                <th>Akun Kas/Bank</th>
                                <th>Deskripsi</th>
                                <th>Masuk (Debit)</th>
                                <th>Keluar (Kredit)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($transactions as $item)
                            <tr>
                                <td>{{ $item->journalEntry->transaction_date }}</td>
                                <td>{{ $item->journalEntry->reference_number }}</td>
                                <td>{{ $item->account->name }}</td>
                                <td>{{ $item->description ?? $item->journalEntry->description }}</td>
                                <td class="text-right text-success">
                                    {{ $item->debit > 0 ? 'Rp '.number_format($item->debit, 0, ',', '.') : '-' }}
                                </td>
                                <td class="text-right text-danger">
                                    {{ $item->credit > 0 ? 'Rp '.number_format($item->credit, 0, ',', '.') : '-' }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center">Tidak ada transaksi dalam periode ini.</td>
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
