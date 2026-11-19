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
                    <h2 class="page-title">@lang('labels.product_request')</h2>
                </center>
            </div>
            <div class="col-sm-12">
                <div class="col-md-10 col-md-offset-1">
                    <div class="col-md-3 main-btn">
                        <a href="{{url('cf-lite/product-request')}}">
                            <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                <span class="fa fa-clipboard"></span>
                                <h3 class="home-btn-title">@lang('labels.product_request')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 main-btn">
                        <a href="{{url('cf-lite/product-request/requests')}}">
                            <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                <span class="fa fa-clipboard"></span>
                                <h3 class="home-btn-title">@lang('labels.request_list')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 main-btn">
                        <a href="{{url('cf-lite/product-request/accepted-requests')}}">
                            <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                <span class="fa fa-clipboard"></span>
                                <h3 class="home-btn-title">@lang('labels.accepted_requests')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 main-btn">
                        <a href="{{url('cf-lite/product-shipment-arrival')}}">
                            <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                <span class="fa fa-clipboard"></span>
                                <h3 class="home-btn-title">@lang('labels.product_arrival')</h3>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            @if(\Illuminate\Support\Facades\Auth::user()->user_club->hq)
                <div class="col-sm-12">
                    <div class="col-md-10 col-md-offset-1">
                        <div class="col-md-3 main-btn">
                            <a href="{{url('cf-lite/consignment-order')}}">
                                <div class="home-btn" style="background-color: rgba(128,221,128,0.9); border: 5px solid #98c673;">
                                    <span class="fa fa-clipboard"></span>
                                    <h3 class="home-btn-title">@lang('labels.consignment_order')</h3>
                                </div>
                            </a>
                        </div>
                        <div class="col-md-3 main-btn">
                            <a href="{{url('cf-lite/consignment-order/bill')}}">
                                <div class="home-btn" style="background-color: rgba(128,221,128,0.9); border: 5px solid #98c673;">
                                    <span class="fa fa-clipboard"></span>
                                    <h3 class="home-btn-title">@lang('labels.consignment_order_bill')</h3>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            @endif
			<div class="col-sm-12">
                <div class="col-md-10 col-md-offset-1">
                    <div class="col-md-3 main-btn">
                        <a href="{{url('cf-lite/product-consignment-acceptance')}}">
                            <div class="home-btn" style="background-color: rgba(128,221,128,0.9); border: 5px solid #98c673;">
                                <span class="fa fa-clipboard"></span>
                                <h3 class="home-btn-title">@lang('labels.product_consignment_acceptance')</h3>
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