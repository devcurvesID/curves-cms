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
            background: url('{{asset('images/assets/Interface_Program_Item-02.png')}}');
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

        .keytag-input{
            border-radius: 10px;
        }

        .a-close {
            position: absolute;
            bottom: 25px;
            right: 30px;
        }

        .a-close a:hover {
            color: rebeccapurple!important;
        }

        .club-name {
            text-align: left;
            color: deeppink;
            font-weight: 600;
            margin-left: 30px;
            margin-top: 5px;
        }
    </style>
</head>
<body>
@include('cf_lite.includes.flash-message')
<div class="col-sm-12" style="background: url('{{asset('images/wm-login/login-screen-19.png')}}'); background-size: 100%; background-position: top left; height:950px;">
<div class="col-sm-8 the-side left-side"
     style="font-family: Adlery-Pro; border-right: 1px dashed rebeccapurple;">
    <div class="row" style="color: deeppink;">
        <div class="col-md-12">
            <p style="font-size: 77px; margin-top: 20px; margin-left: 80px;padding-top: 60px;">It's a Great Day</p>
        </div>
        <div class="col-md-3 col-md-offset-2">
            <p style="font-size: 60px;"><img src="{{asset('images/wm-login/login-screen-11.png')}}" style="width: auto; height: 100px;"></p>
        </div>
    </div>
    <div class="row" style="color: deeppink;">
        <div class="col-md-4 col-md-offset-4">
            <p id="to-curves" style="font-size: 60px;"><img src="{{asset('images/wm-login/login-screen-12.png')}}" style="width: auto; height: 80px;"></p>
        </div>
    </div>
    <div class="row" style="margin-top: 0;">
        <div class="col-md-6" style="color: deeppink;">
            <p style="font-size: 155px; margin-top: 10px; text-align: right;"><img src="{{asset('images/wm-login/login-screen-18.png')}}" style="width: auto; height: 150px;"></p>
        </div>
        <div class="col-md-6" style="text-transform: uppercase; font-family: 'Impact'; margin-top: 30px; color: rebeccapurple;">
            <img src="{{asset('images/wm-login/login-screen-17.png')}}" style="width: auto; height: 150px;">
        </div>
    </div>
</div>
<div class="col-sm-4 the-side right-side" style="background-color: transparent; background: url('{{asset('images/wm-login/login-screen-08.png')}}'); background-size: 100%; background-repeat: no-repeat;  background-position: top right;">
    <div style="display: table; width: 100%; height: 100%; background: url('{{asset('images/wm-login/login-screen-09.png')}}'); background-size: 100%; background-position: top right; background-repeat: no-repeat;">
        <div style="display: table-cell; font-family: Raleway-Regular; height: 700px;">
            <center><img src="{{asset('images/wm-login/login-screen-10.png')}}" style="width: auto; height: 150px;vertical-align: middle; margin-top: 50px;"><br/>
                <div class="col-xs-12" style="padding-top: 100px;">
                    <hr style="border: 1px solid rebeccapurple;">
                    <h3 id="scan-keytag-title">@lang('labels.input_member_number')</h3>
                </div>
                <div class="col-xs-12" style="margin-top: 50px;">
                    {!! Form::open(['method' => 'POST', 'action' => 'WMLiteController@wm_main', 'class' => 'form-horizontal' , 'id' => 'form-member-scanner']) !!}
                    <div class="form-inline">
                        <div class="form-group">
                            {!! Form::text('keytag', null, ['id' => 'keytag_box', 'autofocus' , 'class' => 'keytag-input form-control' , 'placeholder' => Lang::get('labels.input_member_number') , 'style' => 'width:300px; text-align:center;']) !!}
                            <a id="submitid" href="javascript:;"><img role="button" src="{{asset('images/wm-login/login-screen-06-2.png')}}" style="width: auto; height: 35px;"></a>
                        </div>
                    </div>
                    {!! Form::close() !!}
                    <div class="club-name"><span class="fa fa-building"></span> {{\Illuminate\Support\Facades\Auth::user()->user_club->club_name}}</div>
                    <div class="col-xs-12">
                        <hr style="border: 1px solid rebeccapurple;">
                        <a href="{{url('cf-wm/guest-membership/')}}" id="scan-keytag-title">@lang('labels.guest_membership')</a>
                    </div>
                </div>
                <div class="a-close">
                    <a href="{{ url('cf-wm/logout') }}" onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();" style="font-size: 24px; color: deeppink;"><span style="font-size: 13px; vertical-align: middle;">❤</span><span style="margin: 0 10px;">@lang('labels.system_logout')</span><span style="font-size: 13px; vertical-align: middle;">❤</span></a>
                </div>
                <form id="logout-form" action="{{ url('cf-wm/logout') }}" method="POST" style="display: none;">
                    {{ csrf_field() }}
                </form>
            </center>
        </div>
    </div>
</div>
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
        $('input[name="keytag"]').focus();

        $('input[name="keytag"]').on('keypress', function (e) {
            var code = e.keyCode || e.which;
            if (code == 13) {
                $('#form-member-scanner').submit();
            }
        });

        $('#submitid').on('click',function(){
            $('#form-member-scanner').submit();
        });
    });
</script>

</body>
