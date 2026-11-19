<html>
<head>
    @include('cf_back.includes.head')
    @yield('other-style')
    <style>
        .top_nav .navbar-right li .info-number{
            margin-top: 10px;
        }
    </style>
</head>
<body class="nav-md">
<div class="container body">
    <div class="main_container">
        <div class="col-md-3 left_col">
            @include('cf_back.includes.sidebar')
        </div>
        <div class="top_nav">
            @include('cf_back.includes.header')
        </div>

        <div class="right_col" role="main">
            @yield('content')
        </div>

        <footer>
            @include('cf_back.includes.footer')
        </footer>
    </div>
</div>
<div class="tempScript">
    <script>
        $(document).ready(function () {
            $(document).on('click', '.btn-hapus', function () {
                var theform = $(this).siblings('div').children('form');
                bootbox.confirm({
                    message: "<?php echo trans('messages.are_you_sure'); ?>",
                    buttons: {
                        confirm: {
                            label: '@lang('labels.yes')',
                            className: 'btn-danger'
                        },
                        cancel: {
                            label: '@lang('labels.no')',
                        }
                    },
                    callback: function (result) {
                        if (result == false) {
                        } else {
                            theform.submit();
                        }
                    }
                });
            });
        });
    </script>
    @yield('other-script')
</div>
</body>
</html>