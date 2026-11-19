@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <style>
        .home-btn-title {
            font-family: 'Alegreya Sans', sans-serif;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/dashboard')}}" style="left: 25px; top: 5px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.message')</h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="col-md-3 main-btn">
                    <a href="{{url('cf-lite/member-message-note')}}">
                        <div class="home-btn"
                             style="background-color: #afeeee; border: 5px solid #9dd6d6;">
                            <div class="col-xs-12">
                                <img src="{{asset('images/assets/interface program-04.png')}}">
                            </div>
                            <h3 class="home-btn-title">@lang('labels.member_message_note')</h3>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 main-btn">
                    <a href="{{url('cf-lite/whatsapp')}}">
                        <div class="home-btn"
                             style="background-color: forestgreen; border: 5px solid #dedede;">
                            <div class="col-xs-12">
                                <img src="{{asset('images/assets/whatsapp.png')}}">
                            </div>
                            <h3 class="home-btn-title">@lang('labels.whatsapp')</h3>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 main-btn">
                    <a href="{{url('cf-lite/sms')}}">
                        <div class="home-btn"
                             style="border: 5px solid orangered;">
                            <div class="col-xs-12">
                                <img src="{{asset('images/assets/interface program-04.png')}}">
                            </div>
                            <h3 class="home-btn-title" style="color: orangered;">@lang('labels.sms')</h3>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 main-btn">
                    <a href="{{url('cf-lite/whatsapp/no-sale')}}">
                        <div class="home-btn"
                             style="background-color: forestgreen; border: 5px solid #dedede;">
                            <div class="col-xs-12">
                                <img src="{{asset('images/assets/whatsapp.png')}}">
                            </div>
                            <h3 class="home-btn-title">@lang('labels.whatsapp') (@lang('labels.no_sale'))</h3>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('other-script')
@endsection