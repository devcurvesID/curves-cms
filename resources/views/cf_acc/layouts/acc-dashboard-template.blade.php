<html lang="{{ app()->getLocale() }}">
<head>
    @include('cf_lite.includes.head')
    @yield('other-style')
    <link rel="stylesheet" type="text/css" href="{{asset('custom-css/staff-login-fab.css')}}">

    <!-- Access Favicon -->
    <link rel="shortcut icon" href="{{asset('images/assets/curveslogo.ico')}}">
    <link rel="icon" href="{{asset('images/assets/curveslogo.ico')}}" type="image/x-icon">

    <style>

        html {
            background: url('{{asset('images/assets/Interface_Program_Item-02.png')}}') no-repeat center center fixed;
            background-size: cover;
        }

        body {
            background-color: transparent;
        }

        #table-kehadiran th {
            color: #ec088f;
        }

        #table-kehadiran td,
        #table-kehadiran div {
            color: #77619c;
            font-weight: 700;
            vertical-align: middle;
            /*height: 100px;*/
        }

        .ba-staff-login-fab .close {
            background-image: url('{{asset('images/assets/peach-clipart-circle-15.png')}}') !important;
        }

        .btn-back-to {
            position: absolute;
            left: 0;
            top: 0;
            border-radius: 50px;
            width: 50px;
            height: 50px;
            border-width: 3px;
            border-color: deeppink;
        }

        .btn-back-to:hover {
            background-color: #ff89c9;
            border-color: #ff89c9 !important;
        }

        .btn-back-to:hover > span {
            color: white !important;
        }

    </style>
    <style>
        #welcome-member-box {
            position: absolute;
            background-color: rgba(255, 255, 255, 0.9);
            width: 350px;
            max-height: 485px;
            bottom: 20px;
            left: 25px;
            border-radius: 10px;
            border: 6px solid rgba(118, 43, 144, 0.8);
            overflow-y: hidden;
            display: none;
        }

        #welcome-member-box::-webkit-scrollbar-track {
            display: none!important;
        }

        #welcome-member-list {
            margin-bottom: 0;
        }

        #welcome-member-list .list-group-item {
            background-color: rgba(255, 255, 255, 0.5);
        }
    </style>
    <style>
        #welcome-member-box-wm {
            position: absolute;
            background-color: rgba(255, 240, 147, 0.9);
            width: 350px;
            max-height: 485px;
            bottom: 20px;
            right: 25px;
            border-radius: 10px;
            border: 6px solid rgba(255, 20, 147, 1);
            overflow-y: hidden;
            display: none;
            color: rgba(118, 43, 144, 1);
        }

        #welcome-member-box-wm::-webkit-scrollbar-track {
            display: none!important;
        }

        #welcome-member-list-wm {
            margin-bottom: 0;
        }

        #welcome-member-list-wm .list-group-item {
            background-color: rgba(255, 255, 255, 0.5);
        }

        .close-wm-box {
            position: absolute;
            right: 10px;
            top: -15px;
            font-size: 30px;
            color: deeppink;
        }
    </style>
    <meta name="_wm_notif_token" content="{!! csrf_token() !!}"/>
</head>

<body>
<div style="background: url('{{asset('images/assets/Interface_Program_Item-04.png')}}'); background-size: 60%; background-position: top right; background-repeat: no-repeat">
    <div style="background: url('{{asset('images/assets/Interface_Program_Item-01.png')}}'); background-size: 40%; background-position: top right; background-repeat: no-repeat">
        <div class="container-staff">
            <div class="row">
                <div class="left-sidebar col-md-2">
                    @include('cf_acc.includes.left-sidebar')
                </div>
                <div class="right-sidebar col-md-10">
                    <div class="col-md-12" style="padding: 10px">
                        <div class="col-md-12 box-menu">
                            <div class="row">
                                <ul class="nav nav-tabs">
                                    <li class="active left-bar"><a data-toggle="tab" href="#menuutama"><span
                                                    style="font-size: 13px; vertical-align: middle;">❤</span><span
                                                    style="margin: 0 10px; font-weight: 600">@lang($tab_title)</span><span
                                                    style="font-size: 13px; vertical-align: middle;">❤</span></a>
                                    </li>
                                </ul>

                                <div class="col-md-12 tab-content">
                                    <div id="menuutama" class="col-md-12 tab-pane fade in active">
                                        <div class="row" style="padding: 25px 30px;">
                                            @yield('content')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="tempScript">
    <script>
        $(document).ready(function () {
            $('[data-toggle="tooltip"]').tooltip();
            $(document).on('click', '.btn-hapus', function () {
                var theform = $(this).siblings('div').children('form');
                bootbox.confirm({
                    message: "<?php echo trans('messages.are_you_sure'); ?>",
                    buttons: {
                        confirm: {
                            label: 'Yes',
                            className: 'btn-danger'
                        },
                        cancel: {
                            label: 'No'
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
