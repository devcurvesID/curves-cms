@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <style>
        .home-btn-title {
            font-family: 'Alegreya Sans', sans-serif;
        }

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
                <a href="{{url('cf-lite/settings')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.choose_mutation')</h2>
                </center>
            </div>
            <div class="col-sm-12">
                <div class="col-md-10 col-md-offset-2">
                    <div class="col-md-5 main-btn">
                        <a href="{{url('cf-lite/transfer-staff/transfer-staff-list')}}">
                            <div class="home-btn"
                                 style="background-color: #afeecf; border: 5px solid #9dd6ba;">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/icon daftar atribut aggota-02.png')}}">
                                </div>
                                <h3 class="home-btn-title">@lang('labels.transfer_staff')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-5 main-btn">
                        <a href="{{url('cf-lite/transfer-staff/staff-rotation/club')}}">
                            <div class="home-btn"
                                 style="background-color: #afeecf; border: 5px solid #9dd6ba;">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/icon daftar atribut aggota-02.png')}}">
                                </div>
                                <h3 class="home-btn-title">@lang('labels.staff_rotation')</h3>
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