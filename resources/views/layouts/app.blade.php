<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'GCTF') }}</title>

    <!-- Styles -->
    <link href="{{ asset('css/semantic.min.css') }}" rel="stylesheet">
    <style>
        html {height: 100%;}
        body {min-height: 100%;display: flex;flex-direction: column;}
        #app {flex: 1;}
    </style>
    @stack('stylesheets')
</head>
<body>
    @yield('navigation')
    <div id="app">
        @yield('content')
    </div>
    @yield('footer')
    <!--script src="//cdn.bootcss.com/underscore.js/1.9.0/underscore-min.js"></script-->
    <script src="{{ asset('js/arttemplate.js') }}"></script>
    <!--script src="{{ asset('js/app.js') }}"></script-->
    <script src="{{ asset('js/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('js/semantic.min.js') }}"></script>
    @stack('scripts')
</body>
</html>
