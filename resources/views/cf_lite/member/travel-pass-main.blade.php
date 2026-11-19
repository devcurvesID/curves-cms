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
                    <h2 class="page-title">@lang('labels.travel_pass')</h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-3 main-btn">
                    <a href="{{url('cf-lite/travel-pass-new-request')}}">
                        <div class="home-btn" style="background-color: #afeeee; border: 5px solid #9dd6d6;">
                            <span class="fa fa-plus"></span>
                            <h3 class="home-btn-title">@lang('labels.new_travel_pass_request')</h3>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 main-btn">
                    <a href="{{url('cf-lite/travel-pass-active-list')}}">
                        <div class="home-btn" style="background-color: rgba(250,83,63,0.5); border: 5px solid rgba(225,74,56,0.7);">
                            <span class="fa fa-plane"></span>
                            <h3 class="home-btn-title">@lang('labels.active_travel_pass')</h3>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 main-btn">
                    <a href="{{url('cf-lite/travel-pass-finished-list')}}">
                        <div class="home-btn" style="background-color: #afeecf; border: 5px solid #9dd6ba;">
                            <span class="fa fa-plane"></span>
                            <h3 class="home-btn-title">@lang('labels.finished_travel_pass')</h3>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-12">
                <center>
                    <h2 class="page-title">@lang('labels.visiting_member')</h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-3 main-btn">
                    <a href="{{url('cf-lite/visiting-member')}}">
                        <div class="home-btn" style="background-color: #afeecf; border: 5px solid #9dd6ba;">
                            <span class="fa fa-plane"></span>
                            <h3 class="home-btn-title">@lang('labels.visiting_member')</h3>
                        </div>
                    </a>
                </div>
                <div class="col-md-3 main-btn">
                    <a href="{{url('cf-lite/visiting-member-inactive')}}">
                        <div class="home-btn" style="background-color: #afeecf; border: 5px solid #9dd6ba;">
                            <span class="fa fa-plane"></span>
                            <h3 class="home-btn-title">@lang('labels.visiting_member_inactive')</h3>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('other-script')
@endsection