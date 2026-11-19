<html>
<head>
    @include('includes.member.head')
    @yield('other-style')
</head>
<body class="nav-md">
<?php $template = 0; ?>
<div class="container body">
    <div class="main_container">
        @if($template == 0)
            <div class="col-xs-12">
                <div class="layar">
                    <div class="col-xs-8">
                        <header>
                            @include('includes.member.header')
                        </header>
                        <div class="the_content">
                            @yield('content')
                        </div>
                    </div>
                    <div class="col-xs-4">
                        <div class="the-side">
                            @include('includes.member.sidebar')
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-message">
                @include('includes.member.footer')
            </div>
        @endif
    </div>
</div>
<div class="tempScript">
    @yield('other-script')
</div>
</body>
</html>