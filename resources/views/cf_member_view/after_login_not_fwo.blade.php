<!DOCTYPE html>
<html>
<head>
    <title>Curves ID - Member View</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap -->
    <link href={{asset('vendors/bootstrap/dist/css/bootstrap.min.css')}} rel="stylesheet">
    <!-- Font Awesome -->
    <link href={{asset('vendors/font-awesome/css/font-awesome.min.css')}} rel="stylesheet">
    <!-- Animate.css -->
    <link href={{asset("vendors/animate.css/animate.min.css")}} rel="stylesheet">

    <link rel="stylesheet" href="{{asset('vendors/flipclock/css/flipclock.css')}}">

    <!-- Custom Theme Style -->
    <link href={{asset("custom-css/my-global-css.css")}} rel="stylesheet">
    <style>
        .top-section {
            height: 200px;
            width: 100%;
            background-image: url({{asset('images/bg3.png')}});
            background-size: cover;
            background-position: 385px;
            background-repeat: no-repeat;
        }

        .top-section-filtering {
            /*width: 100%;*/
            /*height: 100%;*/
            /*background: #440951;*/
            /*background: -moz-linear-gradient(top, rgba(176, 50, 203, 0.5), rgba(198, 181, 206, 0.5));*/
            /*background: -webkit-gradient(linear, left top, left bottom, from(rgba(176, 50, 203, 0.5)), to(rgba(198, 181, 206, 0.5)));*/
            /*background: linear-gradient(top, rgba(176, 50, 203, 0.5), rgba(198, 181, 206, 0.5));*/

            padding-top: 20px;
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

        .img-profile {
            border-radius: 50%;
            width: 150px;
            height: 150px;

            background-size: cover;
            background-position: center center;
            display: inline-block;
            box-shadow: 5px 5px 8px #888;
        }

        .flip-clock-wrapper ul {
            width: 45px;
            height: 70px;
        }

        .flip-clock-wrapper ul li {
            line-height: 75px;
        }

        .flip-clock-wrapper ul li a div div.inn {
            font-size: 50px;
        }

        .flip-clock-divider .flip-clock-label {
            top: 8em;
        }

        .flip-clock-divider .flip-clock-label {
            right: -74px;
        }

        .flip-clock-divider.minutes .flip-clock-label {
            right: -78px;
        }

        .flip-clock-divider.seconds .flip-clock-label {
            right: -80px;
        }

        .your-clock {
            float: left;
            width: 460px;
            margin-top: -60px;
            margin-left: 550px;
        }

        .welcome {
            display: inline-block;
            position: absolute;
            top: 60px;
            margin-left: 200px;
        }

        .knob {
            width: 50px !important;
            margin-left: -80px !important;
        }

        .knob-container > div {
            position: relative;
        }

        .my_card {
            background: #f4d5e4;
            /*background: -moz-linear-gradient(top, rgba(176, 50, 203, 0.5), rgba(198, 181, 206, 0.5));*/
            /*background: -webkit-gradient(linear, left top, left bottom, from(rgba(176, 50, 203, 0.5)), to(rgba(198, 181, 206, 0.5)));*/
            /*background: linear-gradient(top, rgba(176, 50, 203, 0.5), rgba(198, 181, 206, 0.5));*/
            padding: 13px;

            margin: 3px -7px;
            /*border-radius: 15px;*/
        }

        .my_cardpromosi {
            background: #cbc1d9;
            padding: 13px;

            margin: 3px -7px;
        }

        .bg-brown-yellow {
            background: linear-gradient(-90deg, #800000 0%, #ffeb7f 35%, #ffffff 40%, #ffeb7f 45%, #800000 100%);
            padding: 1px 12px;
        }

        .boxjadwalwnm {
            font-size: 20px;
            margin-top: 20px;
        }

        .boxjadwalwnm ul {

        }

        .flip-clock-wrapper .flip {
            -webkit-box-shadow: 0 2px 5px rgba(0, 0, 192, 0.7);
            -moz-box-shadow: 0 2px 5px rgba(0, 0, 192, 0.7);
            box-shadow: 0 2px 5px rgba(0, 0, 192, 0.7);
        }

        ul {
            list-style: none;
        }

        ul li:before {
            content: "\2665 \0020";
            margin-left: -26px;
            font-size: 30px;
        }

        ul.flip li:before {
            content: normal;
            margin-left: 0;
            font-size: 0;
        }

        #keytag_box:focus {
            outline: none;
        }

        .blink-text {
            animation: blinker 1.5s linear infinite;
        }

        @keyframes blinker {
            50% {opacity: 0.3;}
        }
    </style>
</head>
<body>
<div class="bg-al-2">
    <div class="bg-al-4">
        <div class="bg-al-1">

            <div class="container" style="font-family: Raleway-Regular;">
                <div class="top-section">
                    <div class="top-section-filtering">
                        <div style="background: url('{{asset('images/assets/Interface Program Item-26.png')}}'); height: 170px; width: 170px; background-size: 170px;">
                            @if(!empty($user->user_memberpersonal->photo))
                                <div class="img-profile"
                                     style="background-image: url('{{asset('images/member_photos/'.$user->user_memberpersonal->photo)}}'); margin-top: 10px; margin-left: 10px;">
                                </div>
                            @else
                                <div class="img-profile"
                                     style="background-image: url('{{asset('images/img.png')}}'); margin-top: 10px; margin-left: 10px;">
                                </div>
                            @endif
                        </div>
                        <h2 class="welcome">
                            @if(date('d-m', strtotime(\Carbon\Carbon::parse($user->user_memberpersonal->birth))) == date('d-m', strtotime(\Carbon\Carbon::today())))
                                <span class="blink-text" style="font-family: Allura; font-size: 40px; line-height: 40px; color: rebeccapurple; font-weight: 600;">@lang('labels.happy_birthday') {{$user->name}} !!</span> <br/>
                            @else
                                <span style="font-family: Allura; font-size: 36px;">Selamat Anda Hadir Hari Ini!</span> <br/>
                            @endif
                            <img src="{{asset('images/assets/Interface_Program_Item-07.png')}}" width="400px"><br/>
                            <small style="color: #414042; font-size: 30px; font-weight: 600;">{{$user->name}}</small>
                            <br/>
                            <span style="font-size: 17px; font-weight: 600; color: #714b9f;">@lang('labels.joined_date') : {{date('d F Y', strtotime($user->user_memberpersonal->joined))}}</span>
                        </h2>
                    </div>
                </div>
                <div class="your-clock"></div>

                <div class="col-xs-12" style="padding-top: 50px;">
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="bg-brown-yellow">
                                        <div class="my_card" style="height: auto; padding-bottom: 40px; padding-top: 40px;padding-left: 40px; padding-right: 40px;">
                                            <div class="card_title">
                                                <h3 style="margin-top: 0px; font-weight: bold; color: #ec068c; font-size: 50px;">
                                                    <center>@lang('labels.message')</center>
                                                </h3>
                                                <hr style="margin-top: 0px; margin-bottom: 20px; border-top: 2px solid #79538f;"/>
                                            </div>
                                            <div style="font-size: 50px; text-align: center;">
                                                @lang('messages.contact_for_fwo')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {!! Form::open(['method' => 'POST', 'action' => ['ViewController@member_login', session('the_club')], 'id' => 'form-member-scanner']) !!}
                {!! Form::text('keytag', null, ['id' => 'keytag_box', 'autofocus', 'style' => 'border: none; background-color: transparent; color: transparent;']) !!}
                {!! Form::close() !!}
            </div>

        </div>
    </div>
</div>

<script src="{{asset('vendors/Chart.js/dist/Chart.min.js')}}"></script>
<script src="{{asset('vendors/jquery/dist/jquery.min.js')}}"></script>
<script src="{{asset('vendors/bootstrap/dist/js/bootstrap.min.js')}}"></script>

<script src={{asset('vendors/jquery-knob/dist/jquery.knob.min.js')}}></script>
<script src="{{asset('vendors/flipclock/js/flipclock.js')}}"></script>
<script src={{asset('build/js/custom.min.js')}}></script>
<script>
    var clock = $('.your-clock').FlipClock({{(date('H') * 3600) + (date('i') * 60) + (date('s'))}});
</script>
</body>


<script>
    $(document).ready(function () {
        init_charts();

        $('.hours').children('.flip-clock-label').text('jam');
        $('.minutes').children('.flip-clock-label').text('menit');
        $('.seconds').children('.flip-clock-label').text('detik');
    });
</script>

<script type="text/javascript">
    inactivityTimeout = false;
    resetTimeout();

    function onUserInactivity() {
        window.location.href = "{{url('member-scan/'.session('the_club'))}}";
    }

    function resetTimeout() {
        clearTimeout(inactivityTimeout);
        inactivityTimeout = setTimeout(onUserInactivity, 1000 * 20);
    }
    window.onmousemove = resetTimeout;
</script>
