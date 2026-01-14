<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>@yield('title', 'Laporan')</title>
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet" />
    <style>
        body {
            background-color: white;
            font-family: "Source Sans Pro", -apple-system, BlinkMacSystemFont, "Segoe UI", "Helvetica Neue", Arial, sans-serif;
            color: #333;
        }
        .page-print {
            padding: 20px;
        }
        .report-header {
            margin-bottom: 30px;
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 20px;
        }
        .company-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 5px;
            text-transform: uppercase;
        }
        .report-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
        }
        .report-date {
            font-size: 14px;
            color: #666;
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
                border: 1px solid #000 !important;
            }
            .badge-info { background-color: #17a2b8 !important; }
            .badge-warning { background-color: #ffc107 !important; color: #212529 !important; border-color: #d39e00 !important; }
            .badge-primary { background-color: #007bff !important; }
            .badge-success { background-color: #28a745 !important; }
            .badge-danger { background-color: #dc3545 !important; }
            .badge-secondary { background-color: #6c757d !important; }

            .alert-success { background-color: #d4edda !important; color: #155724 !important; border-color: #c3e6cb !important; }
            .alert-danger { background-color: #f8d7da !important; color: #721c24 !important; border-color: #f5c6cb !important; }
            .alert-info { background-color: #d1ecf1 !important; color: #0c5460 !important; border-color: #bee5eb !important; }
            .text-success { color: #28a745 !important; }
            .text-danger { color: #dc3545 !important; }
        }
    </style>
    @yield('css')
</head>
<body onload="window.print()">
    <div class="container-fluid page-print">
        <div class="report-header">
            @if($logo = \App\Models\Setting::get('company_logo'))
                <img src="{{ asset($logo) }}" alt="Logo" style="max-height: 60px; margin-bottom: 10px;">
            @endif
            <div class="company-name">{{ \App\Models\Setting::get('company_name', 'Koperasi Tabungan Sukarela') }}</div>
            <div style="font-size: 14px; margin-bottom: 5px;">
                {{ \App\Models\Setting::get('company_address') }}
                @if(\App\Models\Setting::get('company_phone'))
                    <br>Telp: {{ \App\Models\Setting::get('company_phone') }}
                @endif
                @if(\App\Models\Setting::get('company_email'))
                    | Email: {{ \App\Models\Setting::get('company_email') }}
                @endif
            </div>
            <hr style="border-top: 2px solid #333;">
            <div class="report-title">@yield('title')</div>
            <div class="report-date">@yield('date')</div>
        </div>

        @yield('content')

        <div class="row mt-4 no-print">
            <div class="col-12 text-center">
                <button class="btn btn-secondary" onclick="window.print()">Print Again</button>
                <button class="btn btn-outline-danger" onclick="window.close()">Close</button>
            </div>
        </div>
    </div>
</body>
</html>
