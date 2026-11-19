<html lang="{{ app()->getLocale() }}">
<head>
    @include('cf_lite.includes.head')
    @yield('other-style')
    <link rel="stylesheet" type="text/css" href="{{asset('custom-css/staff-login-fab.css')}}">

    <style>

        td {
            color: #414042;
        }

        html {
            background: url('{{asset('images/assets/Interface_Program_Item-02.png')}}') no-repeat center center fixed;
            background-size: cover;
        }

        body {
            background-color: transparent;
        }

        .bg-al-4 {
            background: url('{{asset('images/assets/Interface_Program_Item-04.png')}}');
            background-size: 30%;
            background-position: top right;
            background-repeat: no-repeat;
        }

        .bg-al-1 {
            background: url('{{asset('images/assets/Interface_Program_Item-01.png')}}');
            background-size: 25%;
            background-position: top right;
            background-repeat: no-repeat;
        }

        .wnmpp {
            margin: 10px 77px;
        }

        .wmimgprint img,
        .wnmpp img,
        img.img-circle{
            width: 150px;
            height: 150px;
            object-fit: cover;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }

        #menuutama {
            background-color: rgba(255, 255, 255, 0.8);
            margin: 25px 10px;
            border-radius: 20px;
        }

        .margincontent {
            padding: 25px 30px;
        }

        .table > tbody > tr > td {
            border-top: 0px;
        }

        input[name='wm_date'],
        input[name='age'],
        input[name='height'],
        input[name='bp_high'],
        input[name='bp_low'],
        input[name='rhr'] {
            font-size: 18px;
            border-color: #dc3c91;
        }

        input[name='bp_low'] {
            width: 37%;
        }

        input[name='rhr'] {
            width: 50%;
        }

        .form-horizontal .control-label {
            text-align: left;
            font-size: 17px;
        }

        .panel-info {
            border: 2px solid #eb9ec6;
            background-color: transparent;
        }

        .panel-default,
        .panel-warning {
            border: 2px solid #eb9ec6;
            background-color: transparent;
        }

        .panel-selisih {
            border: 2px solid #eb9ec6;
            background-color: transparent;
            height: 850px;
        }

        .panel-primary {
            border: 2px solid #eb9ec6;
            background-color: transparent;
            height: 233px;
        }

        .panel-nasihat {
            border: 2px solid #eb9ec6;
            background-color: transparent;
            height: 300px;
            max-height: 300px;
            overflow: hidden;
        }

        .panel-success {
            border: 2px solid #b19ecf;
            background-color: transparent;
        }

        .panel-info > .panel-heading,
        .panel-warning > .panel-heading,
        .panel-default > .panel-heading,
        .panel-success > .panel-heading,
        .panel-nasihat > .panel-heading,
        .panel-primary > .panel-heading {
            font-size: 24px;
            font-weight: 600;
            color: #dc3c91;
            background-color: transparent;
            border-color: transparent;
        }

        .wmdataprintout .panel-info > .panel-heading,
        .wmdataprintout .panel-default > .panel-heading,
        .wmdataprintout .panel-success > .panel-heading,
        .wmdataprintout .panel-selisih > .panel-heading {
            line-height: 40px;
            font-size: 40px;
            font-weight: 600;
            color: #6752a5;
            text-align: center;
            background-color: transparent;
            border-color: transparent;
        }

        .wmdataprintout .panel-selisih > .panel-heading2 {
            line-height: 40px;
            font-size: 25px;
            font-weight: 600;
            color: #6752a5;
            text-align: center;
            background-color: transparent;
            border-color: transparent;
        }

        .datecurpre {
            font-size: 30px;
            font-weight: 600;
            text-align: center;
            background-color: transparent;
            border-color: transparent;
        }

        .checkbox-inline, .radio-inline {
            padding-left: 30px;
        }

        .radio-inline + .radio-inline {
            margin-left: 40px;
        }

        .frame {
            padding: 15px;
            position: relative;
        }

        .frame:after,
        .frame:before {
            border: 10px solid #414042;
            bottom: 0;
            content: '';
            position: absolute;
            top: 0;
            width: 10%;
        }

        .frame:after {
            border-left: none;
            right: 0;
        }

        .frame:before {
            border-right: none;
            left: 0;
        }

        #table-kehadiran th{
            color: #ec088f;
        }

        #table-kehadiran td,
        #table-kehadiran div{
            color: #77619c;
            font-weight: 700;
            vertical-align: middle;
            /*height: 100px;*/
        }

        .ba-staff-login-fab .close {
            transition: 1s;
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

        #menuutama {
            padding-left: 30px;
            padding-right: 30px;
        }
    </style>
</head>

<body>
<div style="background: url('{{asset('images/assets/Interface_Program_Item-04.png')}}'); background-size: 60%; background-position: top right; background-repeat: no-repeat">
    <div style="background: url('{{asset('images/assets/Interface_Program_Item-01.png')}}'); background-size: 40%; background-position: top right; background-repeat: no-repeat">
        <div class="container-staff">
            <div class="row">
                <div class="left-sidebar col-md-2">
                    @include('cf_wm.layout.left-sidebar')
                </div>
                <div class="right-sidebar col-md-10">
                    <div class="col-md-12" style="padding: 10px">
                        <div class="col-md-12 box-menu">
                            <div class="row">
                                <ul class="nav nav-tabs">
                                    <li class="active left-bar"><a data-toggle="tab" href="#menuutama"><span
                                                    style="font-size: 13px; vertical-align: middle;">❤</span><span
                                                    style="margin: 0 10px; font-weight: 600">@lang('labels.weigh_n_measure')</span><span
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
        {{--var ch = '{{Auth::user()->club_id}}';--}}

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
        });
    </script>
</div>
</body>
</html>
