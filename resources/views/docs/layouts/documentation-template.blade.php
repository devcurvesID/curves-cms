<html lang="{{ app()->getLocale() }}">
<head>
    @include('docs.includes.head')
    @yield('other-style')

    <style>
        div.title-style{
            font-size: 65px;
            margin-bottom: 30px;
            margin-top: 30px;
            text-align: center;

        }

        div.content-style{
            font-size: 20px;
        }
        div.picture-description{
            font-size: 10px;
        }
    </style>

</head>

<body>
    <div class="container">
            <div class="col-md-3">
                @include('docs.includes.left-sidebar')
            </div>
            <div class="col-md-9">
                @yield('content')
            </div>
    </div>
    @yield('other-script')
</body>
</html>