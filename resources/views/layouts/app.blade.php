@extends('layouts.template')

@section('content')
<div class="page">
    <div class="flex-fill">
        @include('shared.header')
        <div class="my-3 my-md-5">
            <div class="container">
                <div class="page-header">
                    <h1 class="page-title">@yield('page-title')</h1>
                </div>

                @if(session('success'))
                    <div class="alert alert-icon alert-success" role="alert">
                        <i class="fe fe-check mr-2" aria-hidden="true"></i> {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert alert-icon alert-danger" role="alert">
                        <i class="fe fe-alert-triangle mr-2" aria-hidden="true"></i> {{ session('error') }}
                    </div>
                @endif

                @yield('content-app')
            </div>
        </div>
    </div>
    @include('shared.footer')
</div>
<script>
    require(['jquery'], function($) {
        $(document).ready(function() {
            // Auto hide alerts after 5 seconds
            window.setTimeout(function() {
                $(".alert").fadeTo(500, 0).slideUp(500, function(){
                    $(this).remove();
                });
            }, 5000);
        });
    });
</script>
@endsection
