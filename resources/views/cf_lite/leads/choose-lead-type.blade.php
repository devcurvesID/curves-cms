@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/appointment.css')}}" rel="stylesheet">
    <style>
        .circle-medium{
            width: 200px;
            height: 200px;
            border-radius: 50%;
            padding-top: 50px;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <center>
                <h2 class="page-title">@lang('labels.choose') @lang('labels.lead_source')</h2>
            </center>
        </div>
        <div class="col-md-12">
            <center>
                <div class="col-md-6">
                    <a class="btn btn-big-apm btn-fa-lead circle-medium" href="{{url('cf-lite/lead/add/regular')}}">
                        <div class="fa fa-user-plus btn-icon-apm"></div>
                        <div class="btn-title-apm">@lang('labels.not') @lang('labels.buddy_referral')</div>
                    </a>
                </div>
                <div class="col-md-6">
                    <a class="btn btn-big-apm btn-fa-br circle-medium" href="{{url('cf-lite/lead/add/br')}}">
                        <div class="fa fa-user-plus btn-icon-apm"></div>
                        <div class="btn-title-apm">@lang('labels.buddy_referral')</div>
                    </a>
                </div>
            </center>
        </div>
    </div>
@endsection