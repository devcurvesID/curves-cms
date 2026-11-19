<!DOCTYPE html>
<html>
<head>
    <title>Curves ID - Member Login</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- Bootstrap -->
    <link href={{asset('vendors/bootstrap/dist/css/bootstrap.min.css')}} rel="stylesheet">
    <!-- Font Awesome -->
    <link href={{asset('vendors/font-awesome/css/font-awesome.min.css')}} rel="stylesheet">
    <!-- Animate.css -->
    <link href={{asset("vendors/animate.css/animate.min.css")}} rel="stylesheet">

    <!-- Custom Theme Style -->
    <link href="{{asset("build/css/custom.min.css")}}" rel="stylesheet">
    <link href={{asset("custom-css/my-global-css.css")}} rel="stylesheet">
    <link href={{asset("vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css")}} rel="stylesheet">

    <!-- PNotify -->
    <link href={{asset("vendors/pnotify/dist/pnotify.css")}} rel="stylesheet">
    <link href={{asset("vendors/pnotify/dist/pnotify.buttons.css")}} rel="stylesheet">
    <link href={{asset("vendors/pnotify/dist/pnotify.nonblock.css")}} rel="stylesheet">

    <style media="all">
        html, body {
            height: 100%;
        }

        body {
            background: url('{{asset('images/assets/Interface Program Item-02.png')}}');
        }

        body, p {
            margin: 0;
            padding: 0
        }

        .the-side {
            padding: 0px;
            height: 100%;
        }

        .my-input {
            background-color: transparent;
            height: 50px;
            border: 0px;
            border-bottom: 3px groove white;
            margin-top: 20px;
            color: white;
            -webkit-transition: border 0.75s ease;
            -moz-transition: border 0.75s ease;
            -o-transition: border 0.75s ease;
            transition: border 0.75s ease;
        }

        .form-login {
            margin-top: 30px;
        }

        .label-or {
            font-size: 18px;
            line-height: 25px;
            color: white;
            transition: 0.5s;
            margin-top: 40px;
            margin-bottom: 20px;
        }

        .label-or:before {
            content: ' ';
            width: 30px;
            border-bottom: 2px solid white;
            position: absolute;
            margin-left: -45px;
            margin-top: 10px;
        }

        .label-or:after {
            content: ' ';
            width: 30px;
            border-bottom: 2px solid white;
            position: absolute;
            margin-left: 15px;
            margin-top: 10px;
        }

        .my-input:focus {
            border-color: yellow;
        }
    </style>

    <style>
        #scan-keytag-title {
            color: #414042;
            font-size: 28px;
        }

        #scan-keytag-title:before, #scan-keytag-title:after {
            content: "\2665 \0020";
            font-size: 18px;
            color: rebeccapurple;
        }

        #scan-keytag-title:after {
            content: "\0020 \2665";
        }

        #scan-warning {
            color: #414042;
            position: absolute;
            bottom: 10px;
            left: 5%;
            font-size: 15px;
            font-weight: bold;
        }

        #to-curves:after {
            content: '!';
            font-size: 140px;
            position: absolute;
            bottom: -65px;
            margin-left: 15px;
        }
    </style>
</head>
<body>
<div class="col-sm-7 the-side left-side"
     style="background-color: transparent; background-size: cover; background-position: 100% 0%; background-repeat: no-repeat; font-family: Adlery-Pro;">
    @if(!empty($club_login->clublogin_clubinfo->image))
        <img src="{{ asset('images/club_images/'.$club_login->clublogin_clubinfo->image) }}" style="width: 100%; height: 100%; object-fit: cover;">
    @else
        <div class="row" style="color: rebeccapurple;">
            <div class="col-md-6">
                <p style="font-size: 96px; margin-top: 20px; margin-left: 80px;">Welcome</p>
            </div>
            <div class="col-md-6">
                <p id="to-curves" style="font-size: 55px; margin-top: 120px;">to Curves</p>
            </div>
        </div>
        <div class="row" style="color: deeppink;">
            <div class="col-md-12">
                <p style="font-size: 77px; margin-top: 20px; margin-left: 80px;">It's a Great Day</p>
            </div>
            <div class="col-md-6 col-md-offset-6">
                <p id="to-curves" style="font-size: 60px;">to Workout</p>
            </div>
        </div>
        <div class="row" style="margin-top: 0;">
            <div class="col-md-6" style="color: deeppink;">
                <p style="font-size: 155px; margin-top: 10px; text-align: right;">Love</p>
            </div>
            <div class="col-md-6" style="text-transform: uppercase; font-family: 'Impact'; margin-top: 30px; color: rebeccapurple;">
                <p style="font-size: 50px; line-height: 50px;">Your Body</p>
                <p style="font-size: 44px; line-height: 44px;">Your Health</p>
                <p style="font-size: 59px; line-height: 59px;">Yourself</p>
            </div>
        </div>
    @endif
</div>
<div class="col-sm-5 the-side right-side" style="background-color: transparent; background: url('{{asset('images/assets/Interface Program Item-04.png')}}'); background-size: 100%; background-position: top right; background-repeat: no-repeat; border-left: 1px dashed rebeccapurple;">
    <div style="display: table; width: 100%; height: 100%; background: url('{{asset('images/assets/Interface Program Item-01.png')}}'); background-size: 100%; background-position: top right; background-repeat: no-repeat;">
        <div style="display: table-cell; vertical-align: middle; font-family: Raleway-Regular;">
            <center>
                <div class="col-xs-12">
                    {{--<img src="{{asset('images/logo-white.png')}}" width="150px"/>--}}
                    <hr style="border: 1px solid rebeccapurple; margin-bottom: 50px;">
                    <h3 id="scan-keytag-title">@lang('labels.please_scan_your_key_tag')!</h3>
                </div>
                {{--<div class="col-sm-8 col-sm-push-2">
                    <hr/>
                    <div>
                        <h3 style="color: white">:: Please Login ::</h3>
                        <div class="form form-login">
                            <div class="col-sm-8 col-sm-push-2">
                                <div class="form-group">
                                    <input type="text" id="txt-un" class="form-control my-input" name="username"
                                           placeholder="Username"/>
                                </div>
                                <div class="form-group">
                                    <input type="password" id="txt-pass" class="form-control my-input" name="username"
                                           placeholder="Password"/>
                                </div>
                                <div class="form-group">
                                    <button id="the_submit" type="submit" class="btn btn-warning"
                                            style="margin-top: 10px;">Masuk
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>--}}
                <div class="col-xs-12" style="margin-top: 50px;">
                    {{--<label class="label-or">OR</label>--}}
                    {{--<div content="col-xs-12">--}}
                        <img id="qr-box" src="{{asset('images/rfid.png')}}" width="150px">
                        <h5 style="color: #414042; font-weight: bold; margin-top: 20px;">"@lang('labels.tap_your_key_tag')"</h5>
                    {{--</div>--}}
                    {!! Form::open(['method' => 'POST', 'action' => ['ViewController@member_login', $club_login->clublogin_clubinfo->id], 'id' => 'form-member-scanner']) !!}
                    <div class="form-inline">
                        <div class="form-group">
                            {!! Form::label('keytag', '# ', ['class' => 'control-label', 'title' => trans('labels.keytag_id')]) !!}
                            {!! Form::text('keytag', null, ['id' => 'keytag_box', 'autofocus']) !!}
                        </div>
                    </div>
                    {!! Form::close() !!}
                    <div class="tulisan">{{$club_login->clublogin_clubinfo->club_name}}</div>
                    @include('cf_lite.includes.flash-message')
                </div>
            </center>
        </div>
    </div>
    <div id="scan-warning">Jika mengalami kesulitan, mintalah Coach untuk membantu Anda.</div>
</div>

<script src="{{asset('vendors/jquery/dist/jquery.min.js')}}"></script>
<script src="{{asset('vendors/bootstrap/dist/js/bootstrap.min.js')}}"></script>
<!-- PNotify -->
<script src={{asset("vendors/pnotify/dist/pnotify.js")}}></script>
<script src={{asset("vendors/pnotify/dist/pnotify.buttons.js")}}></script>
<script src={{asset("vendors/pnotify/dist/pnotify.nonblock.js")}}></script>
<script>
    $('#the_submit').click(function () {
        var username = $('#txt-un').val();
        var password = $('#txt-pass').val();
        if (password == "" && username == "") {
            new PNotify({
                title: 'Error !',
                text: 'Username & Password Tidak Boleh Kosong',
                type: 'error',
                styling: 'bootstrap3'
            });

            $('#txt-un').css('border-color', 'rgba(231,76,60,1)');
            $('#txt-pass').css('border-color', 'rgba(231,76,60,1)');
        } else if (password != "" && username != "") {
            window.location = "{{url('cf_member_view/1')}}";
        } else {
            if (password == "") {
                new PNotify({
                    title: 'Error !',
                    text: 'Password Tidak Boleh Kosong',
                    type: 'error',
                    styling: 'bootstrap3'
                });
                $('#txt-pass').css('border-color', 'rgba(231,76,60,1)');
            }
            if (username == "") {
                new PNotify({
                    title: 'Error !',
                    text: 'Username Tidak Boleh Kosong',
                    type: 'error',
                    styling: 'bootstrap3'
                });
                $('#txt-un').css('border-color', 'rgba(231,76,60,1)');
            }
        }
    });
</script>
<script>
    $(document).ready(function () {
        var gate = 'true';
        $('input[name="keytag"]').focus();

        $('input[name="keytag"]').on('keypress', function (e) {
            var code = e.keyCode || e.which;
            if (code == 13) {
                if (gate === 'true') {
                    gate = 'false';
                    $('#keytag_box').attr('readonly', true);
                    $('#form-member-scanner').submit();
                }
            }
        });

        setTimeout(
            function () {
                $('#flash-alert-message').hide();
            }, 2000
        );
    });
</script>

{{--Show QR Code--}}
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

    Echo.channel('ada-apa-denganmu-{{$club_login->clublogin_clubinfo->id}}').listen('julioTrigger', function(e) {
        console.log(e.club_id);
        var delay = 10000;

        $('#qr-box').attr('src', e.member);

        function hideMemberBox(){
            $('#qr-box').attr('src', '{{asset('images/rfid.png')}}');
        }

        clearTimeout(timeset);

        timeset = setTimeout(hideMemberBox, delay);
    });
</script>
</body>