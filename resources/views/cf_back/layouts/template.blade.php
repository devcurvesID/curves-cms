<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>Curves Indonesia</title>

        <!-- Styles -->
        <link href="{{asset('bootstrap-3_3_7/css/bootstrap.min.css')}}" rel="stylesheet">

        <script src="{{asset('js/jquery-3_2_1.min.js')}}"></script>
        <script src="{{asset('bootstrap-3_3_7/js/bootstrap.min.js')}}"></script>
        <script src="{{asset('js/ci-global.js')}}"></script>
    </head>
    <body>
        @yield('main')
    </body>
</html>