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
                    <h2 class="page-title">@lang('labels.community_partner')</h2>
                </center>
            </div>
            <div class="col-sm-12">
                <div class="col-md-10 col-md-offset-1">
                    <div class="col-md-6 main-btn">
                        <a href="{{url('cf-lite/community-partner/community-partner-list')}}">
                            <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                <span class="fa fa-clipboard"></span>
                                <h3 class="home-btn-title">@lang('labels.manage_community_partner')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-6 main-btn">
                        <a href="{{url('cf-lite/community-partner/community-partner-message-list')}}">
                            <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                <span class="fa fa-clipboard"></span>
                                <h3 class="home-btn-title">@lang('labels.community_partner_message')</h3>
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