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

        .pd-10 {
            padding: 10px;
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

<body class="ROM">
<div style="background: url('{{asset('images/assets/Interface_Program_Item-04.png')}}'); background-size: 60%; background-position: top right; background-repeat: no-repeat">
    <div style="background: url('{{asset('images/assets/Interface_Program_Item-01.png')}}'); background-size: 40%; background-position: top right; background-repeat: no-repeat">
        <div class="container-staff">
            <div class="row">
                <div class="left-sidebar col-md-2">
                    @include('cf_lite.includes.left-sidebar')
                </div>
                <div class="right-sidebar col-md-10">
                    <div class="col-md-12 pd-10">
                        <div class="col-md-12 box-menu">
                            <div class="row">
                                <ul class="nav nav-tabs">
                                    <li class="active left-bar" style="width: 100%;"><a data-toggle="tab" href="#menuutama"><span
                                                    style="font-size: 13px; vertical-align: middle;">❤</span><span
                                                    style="margin: 0 10px; font-weight: 600">@lang($tab_title)</span><span
                                                    style="font-size: 13px; vertical-align: middle;">❤</span></a>
                                    </li>
                                </ul>

                                <div class="col-md-12 tab-content">
                                    <div id="menuutama" class="col-md-12 tab-pane fade in active">
                                        <div class="row">
                                            @yield('content')
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="welcome-member-box">
                    <ul class="list-group" id="welcome-member-list"></ul>
                </div>
                <div id="welcome-member-box-wm">
                    <ul class="list-group" id="welcome-member-list-wm"></ul>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="ba-staff-login-wrap">
    <div class="ba-staff-login popup-ani">
        <div class="table-responsive">
            <table id="table-kehadiran" class="table table-striped">
                <thead>
                <tr>
                    <th>@lang('labels.staff')</th>
                    <th>@lang('labels.hour_sign_in')</th>
                    <th>@lang('labels.hour_sign_out')</th>
                </tr>
                </thead>
                <tbody>
                @if(count($staff_attendance_list) > 0)
                    @foreach($staff_attendance_list as $attendance_staff)
                        <tr>
                            <td>
                                <div style="width: 170px; max-height: 45px; overflow: hidden;">{{$attendance_staff->staffattendance_user->name}}</div>
                            </td>
                            <td>{{date('H:i T', strtotime($attendance_staff->sign_in))}}</td>
                            <td>
                                @if(!empty($attendance_staff->sign_out))
                                    {{date('H:i T', strtotime($attendance_staff->sign_out))}}
                                @else
                                    -
                                @endif
                            </td>
                        </tr>
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
    <div class="ba-staff-login-fab">
        <div class="wrap">
            <div class="img-fab img"
                 style="background-image: url('{{asset('images/assets/peach-clipart-circle-10.png')}}')"></div>
        </div>
    </div>
</div>

<!-- due payments -->
@include('cf_lite.includes.payment-reminder')
<!-- / due payments -->

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

    <script>
        $(".ba-staff-login-fab").click(function () {
            $('.ba-staff-login-fab .wrap').toggleClass("ani");
            $('.ba-staff-login').toggleClass("open");
            $('.ba-staff-login-fab .img-fab.img').toggleClass("close");

            if($('.ba-payment').hasClass("open")){
                $('.ba-payment').removeClass("open")
            };
        });

    </script>

    <script src="{{asset('js/pusher.min.js')}}"></script>
    <script src="{{asset('js/echo.js')}}"></script>
    <script>

        var timeset;
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: "d75412a9bf16ead40a4d",
            cluster: "ap1",
            encrypted: false,
            disableStats: false
        });

        Echo.channel('dengarkan-curhatku-{{Auth::user()->club_id}}').listen('ivanTrigger', function(e) {
            var delay = 10000;
            var attend = '0';
            var welcome = '<li class="list-group-item">' +
                '<div class="row">' +
                '<div class="col-md-4">' +
                '<img src="{{asset('')}}/' + e.photo + '" class="img-circle" style="width: 75px; height: 75px; object-fit: cover;">' +
                '</div>' +
                '<div class="col-md-8">' +
                '<h4>@lang('labels.name'): ' + e.member.name + '</h4>' +
                '<h3>' + e.wo_time + '</h3>' +
                '</div>' +
                '</div>' +
                '</li>';
            $('#welcome-member-list').prepend(welcome);

            $('#welcome-member-box').show();

            function hideMemberBox(){
                $('#welcome-member-box').hide();
                $('#welcome-member-list').html('');
            }

            clearTimeout(timeset);

            timeset = setTimeout(hideMemberBox, delay);

            //Check attendance
            $.each(members, function (index, value) {
                console.log(index + ':' + value)
                if (value == e.member.id) {
                    attend = '1';
                }
            });
            console.log(attend);

            if (attend == '0') {
                var attend_set = '<li>' +
                    '<a role="button" class="attended-member" id="attended-member-' + e.member.id + '">' + e.member.name + '</a>' +
                    '</li>';
                $('.list-staff').prepend(attend_set);
                members.push(e.member.id);
            }

            //Added 14092018 For WM Today Notif
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="_wm_notif_token"]').attr('content')
                }
            });

            var formData = {
                member_id: e.member.id
            };

            var type = 'POST';
            var my_url = '{{url('wm-notif-ajax')}}';

            $.ajax({
                type: type,
                url: my_url,
                data: formData,
                dataType: 'json',
                success: function (data) {
                    console.log(data);

                    if (data.status === 'open') {
                        var wm = '<li class="list-group-item" role="button" title="@lang('labels.close')">' +
                            '<div class="row">' +
                            '<div class="col-md-4">' +
                            '<img src="{{asset('')}}/' + data.member.photo + '" class="img-circle" style="width: 75px; height: 75px; object-fit: cover;">' +
                            '</div>' +
                            '<div class="col-md-8">' +
                            '<div class="close-wm-box">&times;</div>' +
                            '<h4>@lang('labels.name'): ' + data.member.name + '</h4>';

                        if (data.wm_day == '1') {
                            wm+= '<h3>@lang('labels.wm_today')</h3>';
                        }

                        if (data.payment_day == '1') {
                            wm+= '<h3>@lang('labels.not_yet_paid')</h3>';
                        }

                        wm+= '</div>' +
                            '</div>' +
                            '</li>';
                        $('#welcome-member-box-wm').show();
                        $('#welcome-member-list-wm').prepend(wm);
                    }
                },
                error: function (data) {
                    console.log('Error: ', data);
                }
            });
        });

        $('#welcome-member-list-wm').on('click', '.list-group-item', function () {
            $(this).remove();
            if ($('#welcome-member-list-wm li').length === 0) {
                $('#welcome-member-box-wm').hide();
            }
        });
    </script>
</div>
</body>
</html>
