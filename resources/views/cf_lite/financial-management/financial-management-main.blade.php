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
                    <h2 class="page-title">@lang('labels.financial_management')</h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="col-md-4 main-btn">
                    <a href="{{url('cf-lite/financial-management/member-deposit')}}">
                        <div class="home-btn"
                             style="background-color: #afeeee; border: 5px solid #9dd6d6;">
                            <span class="fa fa-money"></span>
                            <h3 class="home-btn-title">@lang('labels.member_deposit')</h3>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 main-btn">
                    <a href="{{url('cf-lite/financial-management/daily-sales-report')}}">
                        <div class="home-btn"
                             style="background-color: #afeeee; border: 5px solid #9dd6d6;">
                            <span class="fa fa-file"></span>
                            <h3 class="home-btn-title">@lang('labels.daily_sales_report')</h3>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 main-btn">
                    <a href="{{url('cf-lite/financial-management/daily-rewards-report')}}">
                        <div class="home-btn"
                             style="background-color: #afeeee; border: 5px solid #9dd6d6;">
                            <span class="fa fa-gift"></span>
                            <h3 class="home-btn-title">@lang('labels.daily_rewards_report')</h3>
                        </div>
                    </a>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="col-md-4 main-btn">
                    <a href="{{url('cf-lite/financial-management/product-sales-report')}}">
                        <div class="home-btn"
                             style="background-color: #afeeee; border: 5px solid #9dd6d6;">
                            <span class="fa fa-shopping-bag"></span>
                            <h3 class="home-btn-title">@lang('labels.product_sales_report')</h3>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 main-btn">
                    <a href="{{url('cf-lite/laporan/locker-sales-report')}}">
                        <div class="home-btn"
                             style="background-color: #afeeee; border: 5px solid #9dd6d6;">
                            <span class="fa fa-archive"></span>
                            <h3 class="home-btn-title">@lang('labels.locker_sales_report')</h3>
                        </div>
                    </a>
                </div>
                <div class="col-md-4 main-btn">
                    <a href="{{ route('ezypay.transactions.index') }}">
                        <div class="home-btn"
                             style="background-color: #afeeee; border: 5px solid #9dd6d6;">
                            <span class="fa fa-file-text-o" aria-hidden="true"></span>
                            <h3 class="home-btn-title">Laporan Transaksi Ezypay</h3>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('other-script')
@endsection
