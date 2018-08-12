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
    <link href="{{ asset('css') }}/semantic.min.css" rel="stylesheet">
    <link href="{{ asset('css') }}/g2uc.challenge.css" rel="stylesheet">
    @stack('stylesheets')
</head>
<body>
    <div id="app">
        @yield('navigation')
        @yield('content')
        @yield('footer')
    </div>
    <!--script src="//cdn.bootcss.com/underscore.js/1.9.0/underscore-min.js"></script-->
    <script src="{{ asset('js') }}/arttemplate.js"></script>
    <!--script src="{{ asset('js') }}/app.js"></script-->
    <script src="{{ asset('js') }}/jquery.min.js"></script>
    <script src="{{ asset('js') }}/semantic.min.js"></script>
    @stack('scripts')
</body>
</html>
