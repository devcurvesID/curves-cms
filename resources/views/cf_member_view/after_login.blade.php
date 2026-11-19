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
            /*box-shadow: 5px 5px 8px #888;*/
            /*transform: scale(1.1);*/
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

        #lineChart {
            height: 250px !important;
        }

        .knob-container span {
            display: none;
        }

        .boxjadwalwnm {
            font-size: 20px;
            margin-top: 20px;
        }

        .membership-expire-soon {
            background-color: rebeccapurple;
            position: absolute;
            margin-left: 684px;
            width: 444px;
            height: 222px;
            display: inline-block;
            z-index: 886;
            border-radius: 10px;
        }

        .membership-expire-soon-text {
            padding: 10px;
            margin: 10px;
            color: white;
            border-radius: 10px;
            border: 5px solid white;
            z-index: 887;
            font-weight: 12;
            font-size: 30px;
            width: 424px;
            height: 202px;
            text-align: center;
            vertical-align: middle;
        }

        .due-payment {
            background-color: rebeccapurple;
            position: absolute;
            width: 100vw;
            height: 100vh;
            display: inline-block;
            z-index: 888;
            border-radius: 20px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .due-payment-text {
            color: white;
            border-radius: 10px;
            border: 5px solid white;
            z-index: 889;
            font-weight: 12;
            font-size: 72px;
            text-align: center;
            margin: auto;
            padding: 50px;
        }

        .blink{
            animation:blink 1200ms infinite alternate;
        }

        @keyframes blink {
            from { background-color: rebeccapurple; }
            to { background-color: deeppink; }
        };

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
            @if($debtcount > 0)
                @if(count($renewals) == 0)
                <div class="due-payment blink">
                    <div class="due-payment-text">
                        @lang('messages.please_finish_the_payment')
                    </div>
                </div>
                @endif
            @endif
            <div class="container" style="font-family: Raleway-Regular;">
                <div class="top-section">
                    <div class="top-section-filtering">
                        @if(count($renewals) == 0)
                            @if(Carbon\Carbon::parse($member_status->thru_date) < Carbon\Carbon::now()->addMonths(1))
                                <div class="membership-expire-soon blink">
                                    <div class="membership-expire-soon-text">
                                        @lang('messages.membership_will_end_soon')
                                        {{Carbon\Carbon::parse($member_status->thru_date)->format('d-m-Y')}}
                                    </div>
                                </div>
                            @endif
                        @endif

                        <div style="background: url('{{asset('images/assets/Interface Program Item-26.png')}}'); height: 170px; width: 170px; background-size: 170px; transform: scale(1.2); margin-top: 30px;">
                            @if(!empty($user->user_memberpersonal->photo))
                                <div style="margin-top: 10px; margin-left: 10px; width: 150px; height: 150px; overflow: hidden; display: inline-block; border-radius: 50%;">
                                    <div class="img-profile"
                                         style="background-image: url('{{asset('images/member_photos/'.$user->user_memberpersonal->photo)}}'); box-shadow: 5px 5px 8px #888; transform: scale(1.1);">
                                    </div>
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
                        <div class="col-xs-4">
                            <div class="row">
                                <div class="col-xs-12" style="margin-bottom: 15px;">
                                    <div class="bg-brown-yellow">
                                        <div class="my_card">
                                            <div class="card_title">
                                                <h3 style="margin-top: 0px;">
                                                    <span style="float:left; font-size: 27px; font-weight: bold; color: #ec068c;">@lang('labels.workout_total')</span><br/>
                                                    <span style="text-align:right;float: right; font-size: 23px; font-weight: bold; color: #ec068c;">@lang('labels.since_become_member')</span>
                                                </h3>
                                                <hr style="margin-top: 37px; margin-bottom: 0px; border-top: 2px solid #79538f;"/>
                                            </div>
                                            <div style="display: inline-block; width: 100%;">
                                        <span style="font-size: 54px">
                                            @php
                                                if (empty($total_workout)):
                                                    $total_workout = 0;
                                                endif;

                                                if (empty($total_workout_this_month)):
                                                    $total_workout_this_month = 0;
                                                endif;
                                            @endphp
                                            <span style="font-size: 60px;">{{$total_workout}}</span><span>x</span>
                                        </span><b
                                                        style="font-size: 17px; color: #754e9f; "> {{strtolower(trans('labels.workout').' '.trans('labels.since_become_member'))}}</b>
                                                <hr style="margin: 0px; border-top: 2px dashed #79538f;"/>
                                            </div>
                                            <div style="display: inline-block; width: 80%; margin-left: 28px; margin-bottom: 50px; margin-top: 17px;">
                                                <?php
                                                    if($challenges && isset($challenges->variable_target)):
                                                ?>
                                                <span style="font-size: 54px; color: #ec068c; line-height: 36px;">{{$challenges->variable_target - $total_workout}}x</span><b
                                                        style="font-size: 17px; color: #754e9f; "> @lang('labels.workout_left') {{strtolower(trans('labels.for'))}}<br/><span
                                                            style="text-align: left !important;">{{strtolower(trans('labels.achieve'))}}</span></b>
                                                <span style="font-size: 77px; font-weight: 600; color: #ec068c; position: absolute; bottom: 0px; right: 60px;">{{$challenges->variable_target}} !</span>

                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="bg-brown-yellow">
                                        <div class="my_cardpromosi" style="height: 209px;">
                                            <div class="card_title">
                                                <h3 style="margin-top: 0px; font-weight: bold; color: #ec068c; font-size: 20px;">
                                                    <center>Promosi Mitra Curves</center>
                                                </h3>
                                                <hr style="margin-top: 0px; margin-bottom: 20px; border-top: 2px solid #79538f;"/>
                                            </div>
                                            <div style="font-size: 16px">
                                                <ul>
                                                    @php($counter = 0)
                                                    @foreach($cp_message as $cpm)
                                                        @if($counter%3==0)
                                                            <li style="color: #3ca59d;">{{$cpm->message}}</li>
                                                        @elseif($counter%2==0)
                                                            <li style="color: #774493;">{{$cpm->message}}</li>
                                                        @else
                                                            <li style="color: #da308a;">{{$cpm->message}}</li>
                                                        @endif
                                                        @php($counter += 1)
                                                    @endforeach
                                                </ul>

                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-4" style="width: 30%;">
                            <div class="row">
                                <div class="col-xs-12" style="margin-bottom: 26px;">
                                    <div class="bg-brown-yellow">
                                        <div class="my_card">
                                            <div class="card_title">
                                                <h3 style="margin-top: 0px; font-weight: bold; color: #ec068c; font-size: 20px;">
                                                    <center>@lang('labels.total_workout_this_month')</center>
                                                </h3>
                                                <hr style="margin-top: 0px; margin-bottom: 0px; border-top: 2px solid #79538f;"/>
                                            </div>
                                            <div class="knob-container"
                                                 style="display: inline-block; margin-top: 40px; margin-bottom: 30px;">
                                                @if($total_workout_this_month <= 12)
                                                    <input class="knob" data-width="104" data-height="120"
                                                           data-displayPrevious=true
                                                           data-fgColor="#ec068c"
                                                           data-skin="tron" data-thickness=".2" data-max="100" rel="{{($total_workout_this_month * 100) / 12}}" readonly>
                                                @else
                                                    <input class="knob" data-width="104" data-height="120"
                                                           data-displayPrevious=true
                                                           data-fgColor="rebeccapurple"
                                                           data-skin="tron" data-thickness=".2" data-max="200" rel="{{($total_workout_this_month * 200) / 24}}" readonly>
                                                @endif
                                            </div>
                                            <div style="font-family: Adlery-Pro;display: inline-block; position: absolute; top: 58px; right:40px; font-size: 50px; color: #ec068c; font-style: italic;">
                                                @lang('labels.come_on') !
                                            </div>
                                            <div style="display: inline-block; position: absolute; top: 117px; right:50px; font-size: 25px; color: #ec068c; font-weight: 600; line-height: 27px; text-transform: lowercase;">
                                                @lang('labels.reach2') 12x<br/>@lang('labels.this_month')!
                                            </div>
                                            <div style="display: inline-block; position: absolute; top: 180px; right:55px; font-size: 15px; color: #79538f; font-weight: 600; line-height: 20px; text-transform: lowercase;">
                                                @if($total_workout_this_month < 12)
                                                    @lang('labels.just_need')<br/>@lang('labels.minimum_workout')<br/>{{12-$total_workout_this_month}} @lang('labels.times_to_go') !
                                                @else
                                                    @lang('labels.this_month_workout')<br/>{{$total_workout_this_month}} @lang('labels.times') !
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xs-12">
                                    <div class="bg-brown-yellow">
                                        <div class="my_card" style="min-height: 261px;">
                                            <div class="card_title">
                                                <h3 style="margin-top: 0px; font-weight: bold; color: #ec068c; font-size: 20px;">
                                                    <span style="float:left; font-size: 35px; font-weight: 600; color: #ec068c; margin-top: 7px;">@lang('labels.schedule')</span><br/>
                                                    <span style="text-align:right;float: right; font-size: 22px; font-weight: 600; color: #ec068c; position: absolute; top: 18px; right: 37px;">@lang('labels.weigh') <br/>& @lang('labels.measurement')</span>
                                                </h3>
                                                <hr style="margin-top: 37px; margin-bottom: 0px; border-top: 2px solid #79538f;"/>
                                            </div>
                                            <div class="boxjadwalwnm">
                                                <ul>
                                                    @if(count($member_appointments) > 0)
                                                        @foreach($member_appointments as $wm)
                                                            <li style="color: #da308a;">{{date('d F Y', strtotime($wm->app_date))}}</li>
                                                        @endforeach
                                                    @else
                                                        @if(count($wms) > 0)
                                                            @foreach($wms as $wm)
                                                                <li style="color: #da308a;">{{date('d F Y', strtotime($wm->wm_date))}}</li>
                                                            @endforeach
                                                        @else
                                                            <li style="color: #da308a;">@lang('labels.not_yet_scheduled')</li>
                                                        @endif
                                                    @endif
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xs-4" style="width: 36.666666666%;">
                            <div class="bg-brown-yellow">
                                <div class="my_card" style="height: 550px;">
                                    <div class="card_title">
                                        <h3 style="margin-top: 0px; font-weight: bold; color: #ec068c; font-size: 20px;">
                                            <center>@lang('labels.message')</center>
                                        </h3>
                                        <hr style="margin-top: 0px; margin-bottom: 20px; border-top: 2px solid #79538f;"/>
                                    </div>
                                    <div style="font-size: 20px">
                                        @php($total_message = 0)
                                        @if(count($global_messages) > 0)
                                            @foreach($global_messages as $global_message)
                                                {!! nl2br(e($global_message->message)) !!}
                                                @php($total_message++)
                                            @endforeach
                                        @endif

                                        @if(!empty($user->user_membermessagenote))
                                            @if((strtotime($user->user_membermessagenote->from_date) <= strtotime(\Carbon\Carbon::now())) && (strtotime($user->user_membermessagenote->thru_date) >= strtotime(\Carbon\Carbon::now())))
                                                @if(!empty($user->user_membermessagenote->predefined_message))
                                                    <h4>{{$user->user_membermessagenote->membermessagenote_predefinedmessage->message}}</h4>
                                                @endif
                                                <p style="font-size: 16px;">{{$user->user_membermessagenote->notes}}</p>
                                                @php($total_message++)
                                            @endif
                                        @endif

                                        @if($total_message == 0)
                                            @lang('labels.there_is_no_message')
                                        @endif
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
    $('.knob').each(function () {

        var $this = $(this);
        var myVal = $this.attr("rel");
        // alert(myVal);
        $this.knob({
            draw: function () {
                // "tron" case
                if (this.$.data('skin') == 'tron') {

                    this.cursorExt = 0.3;

                    var a = this.arc(this.cv) // Arc
                        ,
                        pa // Previous arc
                        , r = 1;

                    this.g.lineWidth = this.lineWidth;

                    if (this.o.displayPrevious) {
                        pa = this.arc(this.v);
                        this.g.beginPath();
                        this.g.strokeStyle = this.pColor;
                        this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, pa.s, pa.e, pa.d);
                        this.g.stroke();
                    }

                    this.g.beginPath();
                    this.g.strokeStyle = r ? this.o.fgColor : this.fgColor;
                    this.g.arc(this.xy, this.xy, this.radius - this.lineWidth, a.s, a.e, a.d);
                    this.g.stroke();

                    this.g.lineWidth = 2;
                    this.g.beginPath();
                    this.g.strokeStyle = this.o.fgColor;
                    this.g.arc(this.xy, this.xy, this.radius - this.lineWidth + 1 + this.lineWidth * 2 / 3, 0, 2 * Math.PI, false);
                    this.g.stroke();

                    return false;
                }
            },
            'format' : function (value) {
                return value + '%';
            },
        });
        $this.after('<span style="position: absolute;left: 67px;top: -62.5px; color: white">%</span>');

        $({
            value: 0
        }).animate({

            value: myVal
        }, {
            duration: 2000,
            easing: 'swing',
            step: function () {
                $this.val(Math.ceil(this.value)).trigger('change');

            }
        })
    });


    function init_charts() {
        if (typeof (Chart) === 'undefined') {
            return;
        }
        Chart.defaults.global.legend = {
            enabled: false
        };
        if ($('#lineChart').length) {

            var ctx = document.getElementById("lineChart");
            var lineChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Des"],
                    datasets: [{
                        label: "Your Workout",
                        backgroundColor: "rgba(175, 50, 203, 0.7)",
                        borderColor: "rgba(198, 181, 195, 0.7)",
                        pointBorderColor: "rgba(175, 50, 203, 0.7)",
                        pointBackgroundColor: "rgba(198, 181, 195, 0.7)",
                        pointHoverBackgroundColor: "#fff",
                        pointHoverBorderColor: "rgba(175, 50, 203, 0.7)",
                        pointBorderWidth: 1,
                        data: [, 5, 10, 5, 5, 12, 0, 3, 0, 25, 5, 5]
                    }]
                },
            });

        }
    }

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
