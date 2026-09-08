<!doctype html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="Content-Language" content="en" />
    <meta name="msapplication-TileColor" content="#2d89ef">
    <meta name="theme-color" content="#4188c9">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="HandheldFriendly" content="True">
    <meta name="MobileOptimized" content="320">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    @if($logo = \App\Models\Setting::get('company_logo'))
        <link rel="icon" href="{{ asset($logo) }}" type="image/x-icon"/>
        <link rel="shortcut icon" href="{{ asset($logo) }}" type="image/x-icon"/>
    @else
        <link rel="icon" href="{{ asset('images/logo-default.png') }}" type="image/png"/>
        <link rel="shortcut icon" href="{{ asset('images/logo-default.png') }}" type="image/png"/>
    @endif

    <title>{{ \App\Models\Setting::get('company_name', 'Koperasi Tabungan Sukarela') }}</title>

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,300i,400,400i,500,500i,600,600i,700,700i&amp;subset=latin-ext">
    <script src="{{ asset('js/require.min.js') }}"></script>
    <script>
        requirejs.config({
            baseUrl: "{{ URL::to('/') }}"
        });
    </script>
    <!-- Dashboard Core -->
    <link href="{{ asset('css/dashboard.css') }}" rel="stylesheet" />
    <script src="{{ asset('js/dashboard.js') }}"></script>
    <!-- c3.js') }} Charts Plugin -->
    <link href="{{ asset('plugins/charts-c3/plugin.css') }}" rel="stylesheet" />
    <script src="{{ asset('plugins/charts-c3/plugin.js') }}"></script>
    <!-- Google Maps Plugin -->
    <link href="{{ asset('plugins/maps-google/plugin.css') }}" rel="stylesheet" />
    <script src="{{ asset('plugins/maps-google/plugin.js') }}"></script>
    <!-- Input Mask Plugin -->
    <script src="{{ asset('plugins/input-mask/plugin.js') }}"></script>
    <!-- Datatables Plugin -->
    <script src="{{ asset('plugins/datatables/plugin.js') }}"></script>
    <!-- Datepicker Plugin -->
    <link href="{{ asset('plugins/bootstrap-datepicker/css/bootstrap-datepicker.min.css') }}" rel="stylesheet" />
    <script src="{{ asset('plugins/bootstrap-datepicker/plugin.js') }}"></script>

    <!-- Custom DataTables & Mobile Responsive Styles -->
    <style>
        /* Base DataTables Search Styling */
        .dataTables_wrapper .dataTables_filter {
            float: right;
            text-align: right;
            margin-bottom: 0.75rem;
        }
        .dataTables_wrapper .dataTables_filter label {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 0;
            font-size: 0.875rem;
            font-weight: 600;
            color: #495057;
        }
        .dataTables_wrapper .dataTables_filter input {
            margin-left: 0.5rem;
            font-size: 0.875rem;
            padding: 0.375rem 0.75rem;
            border-radius: 6px;
            border: 1px solid rgba(0, 40, 100, 0.12);
            box-sizing: border-box;
            display: inline-block;
            transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
            background-color: #ffffff;
            width: 200px;
            max-width: 220px;
        }
        .dataTables_wrapper .dataTables_filter input:focus {
            border-color: #206bc4;
            outline: 0;
            box-shadow: 0 0 0 2px rgba(32, 107, 196, 0.25);
        }

        @media (max-width: 767.98px) {
            /* Mobile Header Enhancements */
            .header .container {
                padding-left: 12px;
                padding-right: 12px;
            }
            .header-brand {
                max-width: 55%;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
                font-size: 1rem;
            }
            .header-brand-img {
                height: 1.75rem !important;
                margin-right: 0.3rem;
            }
            .header-toggler {
                width: 2.25rem;
                height: 2.25rem;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                border-radius: 4px;
                border: 1px solid #e9ecef;
                background-color: #f8f9fa;
            }

            /* Nav collapse menu on mobile */
            #headerMenuCollapse {
                background: #fff;
                border-bottom: 1px solid rgba(0, 40, 100, 0.12);
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }
            #headerMenuCollapse .nav-tabs {
                border-bottom: none !important;
                padding: 0.5rem 0.75rem !important;
                gap: 0.25rem !important;
            }
            #headerMenuCollapse .nav-item {
                width: 100% !important;
                margin-bottom: 0.25rem !important;
            }
            #headerMenuCollapse .nav-link {
                padding: 0.65rem 0.85rem !important;
                border-radius: 8px !important;
                border: none !important;
                font-weight: 500 !important;
                font-size: 0.925rem !important;
                color: #495057 !important;
                display: flex !important;
                align-items: center !important;
                transition: all 0.2s ease !important;
            }
            #headerMenuCollapse .nav-link i {
                font-size: 1.1rem !important;
                margin-right: 0.65rem !important;
                width: 1.25rem !important;
                text-align: center !important;
                color: #6c757d !important;
            }
            #headerMenuCollapse .nav-link:hover {
                background-color: #f1f5f9 !important;
                color: #206bc4 !important;
            }
            #headerMenuCollapse .nav-link.active {
                background-color: #e8f1fd !important;
                color: #206bc4 !important;
                font-weight: 600 !important;
                border: none !important;
                box-shadow: none !important;
            }
            #headerMenuCollapse .nav-link.active i {
                color: #206bc4 !important;
            }
            #headerMenuCollapse .dropdown-menu {
                border: none !important;
                background-color: #f8fafc !important;
                box-shadow: none !important;
                margin: 0.25rem 0 0.5rem 0 !important;
                padding: 0.35rem 0 0.35rem 0.85rem !important;
                border-left: 3px solid #206bc4 !important;
                border-radius: 0 8px 8px 0 !important;
            }
            #headerMenuCollapse .dropdown-item {
                padding: 0.5rem 0.85rem !important;
                font-size: 0.875rem !important;
                font-weight: 500 !important;
                color: #475569 !important;
                border-radius: 6px !important;
                transition: background-color 0.15s ease !important;
            }
            #headerMenuCollapse .dropdown-item:hover {
                background-color: #e2e8f0 !important;
                color: #1e293b !important;
            }
            #headerMenuCollapse .dropdown-item.active,
            #headerMenuCollapse .dropdown-item:active {
                background-color: #206bc4 !important;
                color: #ffffff !important;
                font-weight: 600 !important;
            }
            #headerMenuCollapse .dropdown-divider {
                margin: 0.35rem 0 !important;
                border-top: 1px solid #e2e8f0 !important;
            }

            /* Responsive Cards & Padding */
            .page-header {
                margin-bottom: 0.75rem;
            }
            .page-title {
                font-size: 1.25rem;
            }
            .card {
                margin-bottom: 0.75rem;
            }
            .card-body {
                padding: 0.85rem;
            }
            .card-header {
                padding: 0.75rem 0.85rem;
                flex-direction: column;
                align-items: flex-start !important;
                gap: 0.5rem;
            }
            .card-title {
                font-size: 1rem;
                width: 100%;
                margin-bottom: 0.25rem;
            }
            .card-options {
                margin-left: 0 !important;
                width: 100%;
                display: flex;
                flex-wrap: wrap;
                align-items: stretch;
                justify-content: flex-start;
                gap: 0.5rem;
            }
            .card-options form {
                width: 100% !important;
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 0.35rem;
            }
            .card-options form label {
                margin-right: 0 !important;
                margin-bottom: 0.15rem !important;
                font-weight: 600;
                font-size: 0.85rem;
            }
            .card-options form input,
            .card-options form select {
                width: 100% !important;
                margin-right: 0 !important;
                margin-bottom: 0.35rem !important;
            }
            .card-options .btn {
                font-size: 0.8rem;
                padding: 0.35rem 0.75rem;
                width: 100% !important;
                margin-left: 0 !important;
                text-align: center;
                justify-content: center;
            }

            /* Touch-friendly DataTables and Tables */
            .table-responsive {
                -webkit-overflow-scrolling: touch;
                margin-bottom: 0;
                border-radius: 6px;
                overflow-x: auto !important;
            }
            .table {
                width: 100% !important;
            }
            .table th, .table td {
                padding: 0.5rem 0.65rem;
                font-size: 0.825rem;
                vertical-align: middle;
            }
            .table th {
                white-space: nowrap;
                font-size: 0.775rem;
                letter-spacing: 0.02em;
            }
            .table td .badge, .table td .tag, .table .btn-group, .table .btn-sm {
                white-space: nowrap;
            }
            .dataTables_wrapper {
                padding: 0.75rem 0.85rem;
            }
            .dataTables_wrapper .dataTables_length {
                display: none !important;
            }
            .dataTables_wrapper .dataTables_filter {
                float: none !important;
                text-align: left !important;
                margin-bottom: 0.75rem !important;
                width: 100% !important;
            }
            .dataTables_wrapper .dataTables_filter label {
                width: 100% !important;
                display: flex !important;
                flex-direction: column !important;
                align-items: flex-start !important;
                gap: 0.35rem !important;
                margin-bottom: 0 !important;
                font-size: 0.875rem !important;
                font-weight: 600 !important;
                color: #495057 !important;
            }
            .dataTables_wrapper .dataTables_filter input {
                width: 100% !important;
                max-width: 240px !important;
                margin-left: 0 !important;
                font-size: 0.875rem !important;
                padding: 0.45rem 0.75rem !important;
                border-radius: 6px !important;
                border: 1px solid rgba(0, 40, 100, 0.12) !important;
                box-sizing: border-box !important;
                display: block !important;
            }
            .dataTables_wrapper .dataTables_paginate {
                float: none !important;
                display: flex !important;
                justify-content: center !important;
                flex-wrap: wrap !important;
                gap: 2px !important;
                margin-top: 0.75rem !important;
            }
            .dataTables_wrapper .dataTables_paginate .paginate_button {
                padding: 0.25rem 0.5rem !important;
                font-size: 0.8rem !important;
                margin: 0 !important;
            }
            .dataTables_wrapper .dataTables_info {
                float: none !important;
                text-align: center !important;
                margin-bottom: 0.5rem !important;
                font-size: 0.8rem !important;
            }

            /* Mobile Modals */
            .modal-dialog {
                margin: 0.5rem;
                max-width: calc(100% - 1rem);
            }
            .modal-content {
                border-radius: 8px;
            }
            .modal-header {
                padding: 0.75rem 1rem;
            }
            .modal-body {
                padding: 0.85rem;
                max-height: calc(100vh - 160px);
                overflow-y: auto;
            }
            .modal-footer {
                padding: 0.75rem 1rem;
                flex-wrap: wrap;
                gap: 0.5rem;
            }
            .modal-footer .btn {
                flex: 1 1 auto;
            }

            /* Forms & Form Groups on Mobile */
            .form-group {
                margin-bottom: 0.75rem;
            }
            .form-control, .custom-select {
                font-size: 0.9rem;
                height: auto;
                padding: 0.45rem 0.65rem;
            }

            /* Stamp card fixes */
            .stamp {
                width: 2.25rem;
                height: 2.25rem;
                line-height: 2.25rem;
                font-size: 0.9rem;
            }

            /* Utility helpers for mobile stack */
            .mobile-stack {
                flex-direction: column !important;
                align-items: stretch !important;
            }
            .mobile-stack > * {
                width: 100% !important;
                margin-bottom: 0.5rem;
            }
        }
    </style>

    @yield('css')
</head>
<body class="">
    @yield('content')
</body>
@yield('js')
<script>
(function() {
    function setupDtDomRestructuring($) {
        if (!$) return;
        $(document).on('init.dt', function(e, settings) {
            var api = new $.fn.dataTable.Api(settings);
            var $table = $(api.table().node());
            var $wrapper = $(api.table().container());
            var $parentResponsive = $wrapper.parent('.table-responsive');

            // If table is not already wrapped in .table-responsive inside wrapper
            if ($table.parent('.table-responsive').length === 0) {
                $table.wrap('<div class="table-responsive"></div>');
            }

            // Unwrap wrapper if it was nested inside an outer .table-responsive
            if ($parentResponsive.length) {
                $wrapper.unwrap();
            }
        });
    }

    if (typeof window.jQuery !== 'undefined') {
        setupDtDomRestructuring(window.jQuery);
    } else if (typeof require !== 'undefined') {
        require(['jquery'], function($) {
            setupDtDomRestructuring($);
        });
    } else {
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof window.jQuery !== 'undefined') {
                setupDtDomRestructuring(window.jQuery);
            }
        });
    }
})();
</script>
</html>
