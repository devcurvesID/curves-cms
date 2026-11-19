@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <style>
        .home-btn-title {
            font-family: 'Alegreya Sans', sans-serif;
        }

        /*.home-btn{*/
            /*background-color: #fab752 !important;*/
            /*border: 5px solid #e19e38 !important;*/
        /*}*/

        .home-btn span{
            color: #ffffff;
        }

        h3.home-btn-title{
            color: #ffffff;
            margin: 20px 20px 10px 20px;
        }

        .main-btn:hover .home-btn span,
        .main-btn:hover h3.home-btn-title{
            color: #666666;
        }

    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/dashboard')}}" style="left: 25px; top: 5px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.other_setting')</h2>
                </center>
            </div>
            <div class="col-sm-12">
                <div class="col-md-10 col-md-offset-1">
                    <div class="col-md-3 main-btn">
                        <a href="{{url('cf-lite/change-password-club-list')}}">
                            <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/icon peran fungsional-09.png')}}" style="width: 40px; margin-bottom: 10px;">
                                </div>
                                <h3 class="home-btn-title">@lang('labels.club_login_authentication')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 main-btn">
                        <a href="{{url('cf-lite/auto-assign-member')}}">
                            <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/icon peran fungsional-09.png')}}" style="width: 40px; margin-bottom: 10px;">
                                </div>
                                <h3 class="home-btn-title">@lang('labels.auto_assign_member')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 main-btn">
                        <a href="{{url('cf-lite/coach-sequence')}}">
                            <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/icon peran fungsional-09.png')}}" style="width: 40px; margin-bottom: 10px;">
                                </div>
                                <h3 class="home-btn-title">@lang('labels.coach_sequence_setting')</h3>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-10 col-md-offset-1">
                    <div class="col-md-3 main-btn">
                        <a href="{{ url('cf-lite/coach-lead-setting') }}">
                            <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/icon peran fungsional-09.png')}}" style="width: 40px; margin-bottom: 10px;">
                                </div>
                                <h3 class="home-btn-title">@lang('labels.coach_lead_setting')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 main-btn">
                        <a href="{{ url('cf-lite/coach-lead-sequence') }}">
                            <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/icon peran fungsional-09.png')}}" style="width: 40px; margin-bottom: 10px;">
                                </div>
                                <h3 class="home-btn-title">@lang('labels.coach_lead_sequence_setting')</h3>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-10 col-md-offset-1">
                    <div class="col-md-3 main-btn">
                        <a href="{{ url('cf-lite/coach-target') }}">
                            <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/icon peran fungsional-09.png')}}" style="width: 40px; margin-bottom: 10px;">
                                </div>
                                <h3 class="home-btn-title">@lang('labels.coach_target')</h3>
                            </div>
                        </a>
                    </div>
                </div>
                <div class="col-md-10 col-md-offset-1">
                    <div class="col-md-3 main-btn">
                        <a href="{{ url('cf-lite/publicity') }}">
                            <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/icon peran fungsional-09.png')}}" style="width: 40px; margin-bottom: 10px;">
                                </div>
                                <h3 class="home-btn-title">@lang('labels.publicity')</h3>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('other-script')
@endsection