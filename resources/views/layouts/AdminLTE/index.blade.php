<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @include('layouts.AdminLTE._includes._head')
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/css/bootstrap-notify.min.css">
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-notify/0.2.0/js/bootstrap-notify.min.js"></script>

    </head>

        @impersonating($guard = null)
            <body class="hold-transition skin-red {{ \App\Models\Config::find(1)->layout }} sidebar-mini">
        @else
            <body class="hold-transition skin-{{ \App\Models\Config::find(1)->skin }} {{ \App\Models\Config::find(1)->layout }} sidebar-mini">
        @endImpersonating

        <div class="wrapper">

            @if(Auth::user())
                @include('layouts.AdminLTE._includes._menu_superior')
                @include('layouts.AdminLTE._includes._menu_lateral')
            @endif

            <div class="content-wrapper">
                <nav class="navbar navbar-static-top" id="menu_sup_corpo" style="background-color:#d2d6de; margin-bottom:0; padding-bottom:0;navbar-header.a:color:#fff;">
                    <div class="navbar-header">
                        <a href="" class="navbar-brand" id="" style="color:#222d32;'"><i class="fa fa-@yield('icon_page')"></i> @yield('title')</a>
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navbar-collapse-2" aria-expanded="false">
                            <i class="fa fa-bars"></i>
                        </button>
                    </div>
                    <div class="navbar-collapse collapse" id="navbar-collapse-2" aria-expanded="false" style="height: 1px;">
                        <ul class="nav navbar-nav">

                            @yield('menu_pagina')

                        </ul>
                        <ul class="nav navbar-nav pull-right">

                            @yield('menu_pagina_direita')

                        </ul>
                    </div>
                </nav>

                @if(Session::has('flash_message'))

                    <div class="{{ Session::get('flash_message')['class'] }} flash-message" id="flash_message">
                        <div style="color: #fff; display: inline-block; margin-right: 10px; font-weight: bold;">
                            {!! Session::get('flash_message')['msg'] !!}
                        </div>
                    </div>

                @endif

                <section class="content">

                    @impersonating($guard = null)

                        <div class="row">
                            <div class="col-md-12">
                                <div class="alert alert-warning alert-dismissible">
                                <h4><i class="icon fa fa-info"></i> Attention!</h4>
                                Impersonated access. <a href="{{ route('impersonate.leave') }}">Exit</a>
                                </div>
                            </div>
                        </div>

                    @endImpersonating

                    <div class="row">
                        <div class="col-md-12">

                            @yield('content')

                        </div>
                    </div>

                </section>

            </div>

            @include('layouts.AdminLTE._includes._footer')

        </div>

        <div class='notifications top-right'></div>
        @include('layouts.AdminLTE._includes._script_footer')
            <script>

                @if(Session::has('success'))

                $('.top-right').notify({
                    message: { text: "{{ Session::get('success') }}" }
                }).show();
                @php
                    Session::forget('success');
                @endphp
                @endif
                @if(Session::has('info'))
                $('.top-right').notify({
                    message: { text: "{{ Session::get('info') }}" },
                    type:'info'
                }).show();
                @php
                    Session::forget('info');
                @endphp
                @endif
                @if(Session::has('warning'))
                $('.top-right').notify({
                    message: { text: "{{ Session::get('warning') }}" },
                    type:'warning'
                }).show();
                @php
                    Session::forget('warning');
                @endphp
                @endif
                @if(Session::has('error'))
                $('.top-right').notify({
                    message: { text: "{{ Session::get('error') }}" },
                    type:'danger'
                }).show();
                @php
                    Session::forget('error');
                @endphp
                @endif
            </script>
            @yield('scripts')
            </body>
</html>
