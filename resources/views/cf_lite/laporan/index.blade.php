@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <style>
        #report-container a {
            text-decoration: none;
        }

        #report-container a:hover {
            cursor: pointer;
        }

        .main-btn{
            margin-bottom: 0px;
        }

        .laporan-box-category{
            padding: 10px;
        }

    </style>
@endsection

@section('content')
    <section id="report-container">
        <div class="row">
            <div class="col-xs-12">
                <a href="{{url('cf-lite/dashboard')}}" style="left: 25px; top: 5px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.choose') @lang('labels.report')</h2>
                </center>
            </div>
            <div class="col-xs-12" style="margin-bottom: 30px;">
                <div class="col-md-10 col-md-offset-1" style="box-shadow: 5px 10px 8px #888888; padding: 0px;">
                    <div class="col-xs-12 laporan-box-category" style="background-color: #a8dfe6;">
                        <div class="col-xs-3 main-btn">
                            <a href="{{url('cf-lite/laporan/traffic-control')}}">
                                <div class="home-btn"
                                     style="background-color: #a8dfe6; border: 5px solid #97c8cf;">
                                    <span class="fa fa-file"></span>
                                    <h3 class="home-btn-title">@lang('labels.traffic_control')</h3>
                                </div>
                            </a>
                        </div>
                        <div class="col-xs-3 main-btn">
                            <a href="{{url('cf-lite/laporan/source-report')}}">
                                <div class="home-btn"
                                     style="background-color: #a8dfe6; border: 5px solid #97c8cf;">
                                    <span class="fa fa-file"></span>
                                    <h3 class="home-btn-title">@lang('labels.fa_source_report')</h3>
                                </div>
                            </a>
                        </div>
                        <div class="col-xs-3 main-btn">
                            <a href="{{url('cf-lite/laporan/source-cancel-report')}}">
                                <div class="home-btn"
                                     style="background-color: #a8dfe6; border: 5px solid #97c8cf;">
                                    <span class="fa fa-file"></span>
                                    <h3 class="home-btn-title">@lang('labels.fa_source_cancel')</h3>
                                </div>
                            </a>
                        </div>
                        <div class="col-xs-3 main-btn">
                            <a href="{{url('cf-lite/laporan/membership-type')}}">
                                <div class="home-btn"
                                     style="background-color: #a8dfe6; border: 5px solid #97c8cf;">
                                    <span class="fa fa-file"></span>
                                    <h3 class="home-btn-title">@lang('labels.membership_type_report')</h3>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-xs-12 laporan-box-category" style="background-color: #a8e6ce;">
                        <div class="col-xs-3 main-btn">
                            <a href="{{url('cf-lite/laporan/ulang-tahun')}}">
                                <div class="home-btn"
                                     style="background-color: #a8dfe6; border: 5px solid #97c8cf;">
                                    <span class="fa fa-birthday-cake"></span>
                                    <h3 class="home-btn-title">@lang('labels.report_birthday')</h3>
                                </div>
                            </a>
                        </div>
                        <div class="col-xs-3 main-btn">
                            <a href="{{url('cf-lite/laporan/member')}}">
                                <div class="home-btn"
                                     style="background-color: #a8e6af; border: 5px solid #97cf9d;">
                                    <div class="col-xs-12">
                                        <img src="{{asset('images/assets/icon status anggota-13.png')}}" style="width: 40px; margin-bottom: 5px;">
                                    </div>
                                    <h3 class="home-btn-title">@lang('labels.report_member_status')</h3>
                                </div>
                            </a>
                        </div>
                        <div class="col-xs-3 main-btn">
                            <a href="{{url('cf-lite/laporan/tshirt-size')}}">
                                <div class="home-btn"
                                     style="background-color: #a8e6af; border: 5px solid #97cf9d;">
                                    <span class="fa fa-child"></span>
                                    <h3 class="home-btn-title">@lang('labels.tshirt_size_report')</h3>
                                </div>
                            </a>
                        </div>
                        <div class="col-xs-3 main-btn">
                            <a href="{{url('cf-lite/laporan/product-report')}}">
                                <div class="home-btn"
                                     style="background-color: #a8e6af; border: 5px solid #97cf9d;">
                                    <span class="fa fa-shopping-bag"></span>
                                    <h3 class="home-btn-title">@lang('labels.product_report')</h3>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-xs-12 laporan-box-category" style="background-color: #dcedc2;">
                        <div class="col-xs-3 main-btn">
                            <a href="{{url('cf-lite/laporan/penjualan-staff')}}">
                                <div class="home-btn" style="background-color: #c7edc2; border: 5px solid #b3d5ae;">
                                    <span class="fa fa-line-chart"></span>
                                    <h3 class="home-btn-title">@lang('labels.report_staff_selling')</h3>
                                </div>
                            </a>
                        </div>
                        <div class="col-xs-3 main-btn">
                            <a href="{{url('cf-lite/laporan/staff-fa-success')}}">
                                <div class="home-btn" style="background-color: #c7edc2; border: 5px solid #b3d5ae;">
                                    <span class="fa fa-line-chart"></span>
                                    <h3 class="home-btn-title">@lang('labels.report_staff_selling_fa_success')</h3>
                                </div>
                            </a>
                        </div>
                        <div class="col-xs-3 main-btn">
                            <a href="{{url('cf-lite/laporan/wm-difference')}}">
                                <div class="home-btn"
                                     style="background-color: #e5dfa2; border: 5px solid #ccc690;">
                                    <span class="fa fa-tachometer"></span>
                                    <h3 class="home-btn-title">@lang('labels.report_wm_comparison')</h3>
                                </div>
                            </a>
                        </div>
                        <div class="col-xs-3 main-btn">
                            <a href="{{url('cf-lite/laporan/top-weight-loser')}}">
                                <div class="home-btn"
                                     style="background-color: #ffb5e1; border: 5px solid #e5a2ca;">
                                    <span class="fa fa-child"></span>
                                    <h3 class="home-btn-title">@lang('labels.top_loser')</h3>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-xs-12 laporan-box-category" style="background-color: #ffd3b5;">
                        <div class="col-xs-3 main-btn">
                            <a href="{{url('cf-lite/laporan/wo')}}">
                                <div class="home-btn"
                                     style="background-color: #ffb5bc; border: 5px solid #e5a2a9;">
                                    <div class="col-xs-12">
                                        <img src="{{asset('images/assets/icon laporan latihan anggota-10.png')}}" style="width: 40px; margin-bottom: 15px;">
                                    </div>
                                    <h3 class="home-btn-title">@lang('labels.report_wo')</h3>
                                </div>
                            </a>
                        </div>
                        <div class="col-xs-3 main-btn">
                            <a href="{{url('cf-lite/laporan/challenge')}}">
                                <div class="home-btn"
                                     style="background-color: #e5dfa2; border: 5px solid #ccc690;">
                                    <div class="col-xs-12">
                                        <img src="{{asset('images/assets/icon laporan tantangan-16.png')}}" style="width: 40px; margin-bottom: 10px;">
                                    </div>
                                    <h3 class="home-btn-title">@lang('labels.report_challenge')</h3>
                                </div>
                            </a>
                        </div>
                        <div class="col-xs-3 main-btn">
                            <a href="{{url('cf-lite/laporan/reward')}}">
                                <div class="home-btn"
                                     style="background-color: #b5e1ff; border: 5px solid #a2cae5;">
                                    <div class="col-xs-12">
                                        <img src="{{asset('images/assets/icon laporan penghargaan-15.png')}}" style="width: 40px; margin-bottom: 5px;">
                                    </div>
                                    <h3 class="home-btn-title">@lang('labels.report_rewards')</h3>
                                </div>
                            </a>
                        </div>
                        <div class="col-xs-3 main-btn">
                            <a href="{{url('cf-lite/laporan/cancellation')}}">
                                <div class="home-btn"
                                     style="background-color: #ffb5e1; border: 5px solid #e5a2ca;">
                                    <span class="fa fa-ban"></span>
                                    <h3 class="home-btn-title">@lang('labels.report_cancellation')</h3>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-xs-12 laporan-box-category" style="background-color: #ffaaa6;">
                        <div class="col-xs-3 main-btn">
                            <a href="{{url('cf-lite/laporan/appointment')}}">
                                <div class="home-btn"
                                     style="background-color: #ffd7a6; border: 5px solid #e5c195;">
                                    <div class="col-xs-12">
                                        <img src="{{asset('images/assets/icon laporan janji temu-14.png')}}" style="width: 40px; margin-bottom: 15px;">
                                    </div>
                                    <h3 class="home-btn-title">@lang('labels.report_appointment')</h3>
                                </div>
                            </a>
                        </div>
                        <div class="col-xs-3 main-btn">
                            <a href="{{url('cf-lite/laporan/member-hold')}}">
                                <div class="home-btn"
                                     style="background-color: #ffa6cf; border: 5px solid #e595ba;">
                                    <span class="fa fa-pause-circle-o "></span>
                                    <h3 class="home-btn-title">@lang('labels.report_member_hold')</h3>
                                </div>
                            </a>
                        </div>
                        <div class="col-xs-3 main-btn">
                            <a href="{{url('cf-lite/laporan/absent-member')}}">
                                <div class="home-btn"
                                     style="background-color: #e1e595; border: 5px solid #c8cc84;">
                                    <span class="fa fa-child "></span>
                                    <h3 class="home-btn-title">@lang('labels.report_absent_member')</h3>
                                </div>
                            </a>
                        </div>
                        <div class="col-xs-3 main-btn">
                            <a href="{{url('cf-lite/laporan/not-fwo-yet')}}">
                                <div class="home-btn"
                                     style="background-color: #ffd7a6; border: 5px solid #e5c195;">
                                    <span class="fa fa-child "></span>
                                    <h3 class="home-btn-title">@lang('labels.member_not_fwo_report')</h3>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="col-xs-12 laporan-box-category" style="background-color: #a8dfe6;">
                        <div class="col-xs-3 main-btn">
                            <a href="{{url('cf-lite/laporan/member-source-report')}}">
                                <div class="home-btn"
                                     style="background-color: #a8dfe6; border: 5px solid #97c8cf;">
                                    <span class="fa fa-file"></span>
                                    <h3 class="home-btn-title">@lang('labels.member_source_report')</h3>
                                </div>
                            </a>
                        </div>
                        <div class="col-xs-3 main-btn">
                            <a href="{{url('cf-lite/laporan/member-data-report')}}">
                                <div class="home-btn"
                                     style="background-color: #a8dfe6; border: 5px solid #97c8cf;">
                                    <span class="fa fa-file"></span>
                                    <h3 class="home-btn-title">@lang('labels.member_data')</h3>
                                </div>
                            </a>
                        </div>
                        <div class="col-xs-3 main-btn">
                            <a href="{{url('cf-lite/laporan/staff-attendance-report')}}">
                                <div class="home-btn"
                                     style="background-color: #a8dfe6; border: 5px solid #97c8cf;">
                                    <span class="fa fa-file"></span>
                                    <h3 class="home-btn-title">@lang('labels.staff_attendance_report')</h3>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('other-script')
@endsection