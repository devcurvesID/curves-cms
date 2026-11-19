<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Styles -->
    {{--<link href="{{ asset('css/app.css') }}" rel="stylesheet">--}}
    <link href="{{ asset('css/login_curves.css') }}" rel="stylesheet">
    {{--<link rel="stylesheet" href="{{asset('css/hr/styles.css')}}">--}}

    <!-- Access Favicon -->
    <link rel="shortcut icon" href="{{asset('images/assets/curveslogo.ico')}}">
    <link rel="icon" href="{{asset('images/assets/curveslogo.ico')}}" type="image/x-icon">

    <!-- Bootstrap -->
    <link href="{{asset('bootstrap-3_3_7/css/bootstrap.min.css')}}" rel="stylesheet">
    <script src="{{asset('bootstrap-3_3_7/js/bootstrap.min.js')}}"></script>

    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">

    <style>
        body {
            /*background-color: rgba(224, 224, 255, 1);*/
            background-color: #020215;
        }

        #container {
            display: none;
        }

        #member-name {
            margin-left: 20px;
            float: left;
            font-size: 28px;
            font-weight: bold;
            color: #ff00fc;
        }

        #member-panel {
            border: 2px solid #ff00fc;
            padding: 10px;
            border-radius: 53px;
            margin-top: 10px;
        }

        #photo {
            height: 75px;
            width: 75px;
            border-radius: 100%;
            float: left;
        }

        #outer-container {
            display: none;
        }

        #statusText {
            cursor: pointer;
            text-align: center;
            font-size: 24px;
            margin-top: 5px;
            color: #ff007c;
        }

        /*#waves {*/
            /*height: 80px;*/
            /*width: 190px;*/
            /*width: 50%;*/
            /*border: 1px solid #efefef;*/
        /*}*/

        .empty {
            cursor: pointer;
            color: #ff00fc;
            text-align: center;
        }

        .select2 {
            width: 100% !important;
        }

        @keyframes zoominoutsinglefeatured {
            0% {
                transform: scale(1,1);
            }
            50% {
                transform: scale(1.2,1.2);
            }
            100% {
                transform: scale(1,1);
            }
        }

        #heart {
            display: none;
            position: absolute;
            font-size: 50px;
            color: #ff007c;
            right: 0;
            bottom: 7px;
            animation: zoominoutsinglefeatured 0.5s infinite ;
        }
    </style>
</head>
<body>
<h3 class="empty">@lang('labels.click_to_start')</h3>
<div class="container" id="outer-container">
    <div class="col-sm-12">
        <div class="form-horizontal" style="margin-top: 10px;">
            <div class="form-group">
                {!! Form::label('user', trans('labels.member').':', ['class' => 'control-label col-sm-4', 'style' => 'color: #ff00fc']) !!}
                <div class="col-sm-8">
                    <select name="user" id="user" class="form-control select2">
                        <option value="">@lang('labels.choose') @lang('labels.member')</option>
                        @foreach($members as $member)
                            <option value="{{$member->id}}" photo="{{!empty($member->user_memberpersonal->photo) ? asset('images/member_photos/'.$member->user_memberpersonal->photo) : asset('images/img.png')}}">{{$member->name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-12">
        <div id="container" class="container">
            <div class="row" id="member-panel">
                <img src="" id="photo">
                <h4 id="member-name"></h4>
            </div>
            <div class="row">
                <div id="heart">&#x2764;</div>
                <div id="statusText">START &#x2764;</div>
                <canvas id="waves"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="{{ asset('js/app.js') }}"></script>

<script src="{{asset('js/hr/heartRateSensor.js')}}"></script>
<script src="{{asset('js/hr/app.js')}}"></script>

<script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
<script>
    $(document).ready(function () {
        $('.select2').select2();

        $('.empty').on('click', function () {
            $(this).hide();
            $('#outer-container').show();
        });

        $('#user').on('change', function () {
            $('#photo').attr('src', $(this).find(':selected').attr('photo'));
            $('#member-name').text($(this).find(':selected').text());
            $('#container').show();
            $('.form-horizontal').hide();
        });

        $('#statusText').on('click', function () {
            $('#statusText').css({
                'position' : 'absolute',
                'right' : '0',
                'font-size' : '54px',
                'color' : '#FF007C',
                'margin-top' : '22px'
            });

            $('#waves').css({
                'margin-top' : '20px',
                'height' : '80px',
                'width' : '50%'
//                'border' : '1px solid #efefef'
            });
        });
    });
</script>
</body>
</html>
