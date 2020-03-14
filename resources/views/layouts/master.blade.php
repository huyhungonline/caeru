<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title') | CAERRU管理画面</title>
        <link href= "{{ asset('/css/all.css') }}" rel="stylesheet" type="text/css">
        <link rel="icon" href="{{ asset('/images/favicon.ico') }}">
        <script src="{{ asset('/js/bootstrap.js') }}" type="text/javascript"></script>
        @stack('scripts')
    </head>
    <body>
        @yield('header')
        @yield('content')
    </body>
    @include('layouts.vue_declaration')
</html>