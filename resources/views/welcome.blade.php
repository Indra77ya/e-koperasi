<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ \App\Models\Setting::get('company_name', __('full_app_name')) }}</title>

        @if($logo = \App\Models\Setting::get('company_logo'))
            <link rel="icon" href="{{ asset($logo) }}" type="image/x-icon"/>
            <link rel="shortcut icon" href="{{ asset($logo) }}" type="image/x-icon"/>
        @else
            <link rel="icon" href="{{ asset('favicon-default.svg') }}" type="image/svg+xml"/>
            <link rel="shortcut icon" href="{{ asset('favicon-default.svg') }}" type="image/svg+xml"/>
        @endif

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,600" rel="stylesheet" type="text/css">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 200;
                height: 100vh;
                margin: 0;
                @if($bg = \App\Models\Setting::get('front_background'))
                background-image: url('{{ asset($bg) }}');
                background-size: cover;
                background-position: center;
                @endif
            }

            .full-height {
                height: 100vh;
                @if(\App\Models\Setting::get('front_background'))
                background-color: rgba(255, 255, 255, 0.8); /* Overlay for readability */
                @endif
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 84px;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 13px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            @if (Route::has('login'))
                <div class="top-right links">
                    @auth
                        <a href="{{ url('/home') }}">{{ __('home') }}</a>
                    @else
                        <a href="{{ route('login') }}">{{ __('menu.login') }}</a>
                    @endauth
                </div>
            @endif

            <div class="content">
                <div class="title m-b-md">
                    {{ \App\Models\Setting::get('company_name', __('full_app_name')) }}
                </div>
            </div>
        </div>
    </body>
</html>
