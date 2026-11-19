@extends('cf_lite.layouts.staff-template')

@section('title', trans('labels.weigh_n_measure'))

@section('other-style')
    <style>
        .my-print-font {
            font-size: 10px !important;
        }

        .my-print-nomargin {
            margin-bottom: 0px !important;
            padding: 10px !important;
        }

        .my-print-padding {
            padding: 5px !important;
        }

        .my-print-title {
            font-size: 24px !important;
            text-align: center !important;
        }

        .my-print-subtitle {
            font-size: 18px !important;
            line-height: 20px !important;
        }

        .my-print-subtitle2 {
            font-size: 14px !important;
            line-height: 20px !important;
        }

        .my-print-hr {
            margin-top: 15px !important;
        }

        .my-print-nomargin {
            margin: 0px !important;
        }

        .my-print-float-right {
            float: right !important;
            right: 30px !important;
            top: 200px !important;
        }

        .my-print-col {
            padding-left: 5px !important;
            padding-right: 5px !important;
        }

        .my-print-frame {
            font-size: 48px !important;
            line-height: 32px !important;
        }

        .my-print-panel {
            height: auto !important;
            margin-bottom: 10px !important;
        }

        .my-print-foto {
            width: 85px !important;
            height: 85px !important;
        }

        .my-print-no-padding-top {
            padding-top: 0px !important;
        }

        .my-print-bawah-kiri {
            height: 150px !important;
            max-height: 150px !important;
        }

        .my-print-bawah-kanan {
            height: 435px !important;
            max-height: 435px !important;
        }

        .my-print-since-curves h4 {
            font-size: 10px !important;
        }

        .my-print-since-curves .since-curves-number {
            font-size: 16px;
            padding-top: 0;
        }

        .my-print-since-curves .since-curves-image {
            background-image: none;
            height: 30px;
        }

        .btn-primary {
            background-color: #77619c;
            color: #ffffff;
            border-color: #77619c;
        }

        .btn-primary:hover {
            background-color: #ffffff;
            color: #77619c;
            font-weight: 600;
            border: 2px solid #77619c;
        }

        .since-curves-number{
            font-size: 36px;
            font-weight: 600;
            padding-left: 33px;
            padding-top: 22px;
        }

        .since-curves-image{
            height: 100px;
            background-position: left center;
            background-size: 140px;
            background-repeat: no-repeat;
        }

        .curves-logo {
            display: none;
        }

        #menuutama {
            padding-top: 30px;
        }

        .btn-back-to {
            top: -75px!important;
        }

        /*#club-name-row {*/
        /*display: none;*/
        /*}*/

    </style>
@endsection

@section('content')
    @include('cf_lite.includes.error-message')
    @include('cf_lite.includes.flash-message')
    @php
        $member = $gwm->guestwm_guest;
    @endphp
    <a href="{{url('cf-wm/wm-member-scan')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
    <div class="row section-topz">
        <div class="col-sm-3 col-xs-3 wmimgprint">
            <img class="img-circle member-card-img" src="{{asset('images/img.png')}}">
        </div>
        <div class="col-sm-9 col-xs-7" id="print-area">

            <a id="my-btn-print" class="btn btn-primary"
               style="float: right; font-size: 54px; border-radius: 50%; padding: 20px;"><i class="fa fa-print"></i></a>
            <table style="margin-top: 30px; margin-left: -94px;">
                <tr>
                    <td colspan="2" width="400px"
                        style="border-bottom: 2px solid #6e5b9d; font-size: 40px; font-weight: 600; padding-left: 95px;">{{$member->fullname}}</td>
                </tr>
                <tr>
                    <td width="50%" height="45px"
                        style="border-right: 2px solid #6e5b9d; text-align: center; font-size: 27px; font-weight: 600;">{{\Carbon\Carbon::parse($gwm->created_at)->setTimezone(Session::get('timezone'))->format('H:i T')}}</td>
                    <td width="50%"
                        style="text-align: center; font-size: 27px; font-weight: 600;">{{date('d F Y', strtotime($gwm->wm_date))}}</td>
                </tr>
                {{--<tr id="club-name-row">--}}
                {{--<td colspan="2" style="text-align: center; font-size: 27px; font-weight: 600;">{{$member->user_club->club_name}}</td>--}}
                {{--</tr>--}}
            </table>
        </div>
        <div class="col-sm-2 col-xs-2 curves-logo">
            <img src="{{asset('images/Curves_Logo_Purple_Amaze.png')}}" style="width: 80px; height: auto;">
        </div>
    </div>
    <div class="row" style="padding-left: 20px; padding-right: 20px;">
        <div class="col-xs-12">
            <h2 class="page-title"
                style="font-weight: 600; color: #ee2382; margin-top: 30px; margin-bottom: 0px;">@lang('labels.weigh_n_measure')</h2>
        </div>
    </div>
    <div class="row" style="padding-left: 20px; padding-right: 20px;">
        <div class="col-xs-12">
            <div class="table-responsive data-top">
                <table class="table" style="margin-bottom: 0px;">
                    <tr>
                        <td>
                            <table>
                                <tr>
                                    <td style="font-size: 30px">@lang('labels.age') :</td>
                                    <td style="font-size: 50px; padding: 0 10px 20px 10px; color: #715c94; font-weight: 600;" class="header-detail-wm">{{$gwm->age}}</td>
                                    <td style="font-size: 30px"> @lang('labels.years_old')</td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td style="font-size: 30px">@lang('labels.height'):</td>
                                    <td style="font-size: 50px; padding: 0 10px 20px 10px; color: #715c94; font-weight: 600;" class="header-detail-wm">{{$gwm->height}}</td>
                                    <td style="font-size: 30px">cm</td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td style="font-size: 30px">@lang('labels.bp'):</td>
                                    <td style="font-size: 50px; padding: 0 10px 20px 10px; color: #715c94; font-weight: 600;" class="header-detail-wm">{{$gwm->bp_high}}</td>
                                    <td style="font-size: 30px; color: #d13384; font-weight: 600;"> /</td>
                                    <td style="font-size: 50px; padding: 0 10px 20px 10px; color: #715c94; font-weight: 600;" class="header-detail-wm">{{$gwm->bp_low}}</td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td style="font-size: 30px">@lang('labels.rhr'):</td>
                                    <td style="font-size: 50px; padding: 0 10px 20px 10px; color: #715c94; font-weight: 600;" class="header-detail-wm">{{$gwm->rhr}}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="wmdataprintout">
        <div class="row" style="padding-left: 15px; padding-right: 15px;">
            <div class="col-xs-8">
                <div class="row">
                    <div class="col-sm-6 col-xs-6 my-col">
                        <div class="panel panel-info my-data">
                            <div class="panel-heading">@lang('labels.current')</div>
                            <div class="datecurpre">{{date('d F Y', strtotime($gwm->wm_date))}}</div>
                            <div class="panel-body">
                                <h4 style="color: #dc3c91; font-size: 24px; font-weight: 600;">@lang('labels.body_composition')
                                    :</h4>
                                <table>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:290px;">@lang('labels.weight')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">{{$gwm->weight}}</td>
                                        <td style="font-size: 20px; font-weight: 600; ">@lang('labels.kg')</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:290px;">@lang('labels.body_fat')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">{{$gwm->body_fat}}</td>
                                        <td style="font-size: 20px; font-weight: 600; ">%</td>
                                    </tr>
                                </table>
                                <hr style="margin: 15px 0px; border-top: 2px dashed #79538f;">
                                <table>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:300px;">@lang('labels.muscle_mass')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">{{$gwm->muscle_mass}}</td>
                                        <td style="font-size: 20px; font-weight: 600; ">@lang('labels.kg')</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:300px;">@lang('labels.bone_mass')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">{{$gwm->bone_mass}}</td>
                                        <td style="font-size: 20px; font-weight: 600; ">@lang('labels.kg')</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:300px;">@lang('labels.bmi')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">{{$gwm->bmi}}</td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:300px;">@lang('labels.dci')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">{{$gwm->dci}}</td>
                                        <td style="font-size: 20px; font-weight: 600; ">@lang('labels.cal')</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:300px;">@lang('labels.metabolic')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">{{$gwm->metabolic}}</td>
                                        <td style="font-size: 20px; font-weight: 600; ">@lang('labels.years_old')</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:300px;">@lang('labels.body_water')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">{{$gwm->body_water}}</td>
                                        <td style="font-size: 20px; font-weight: 600; ">%</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:300px;">@lang('labels.visceral')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">{{$gwm->visceral}}</td>
                                        <td></td>
                                    </tr>
                                </table>
                                <h4 style="color: #dc3c91; font-size: 24px; font-weight: 600;">@lang('labels.size')
                                    :</h4>
                                <table>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:150px;">@lang('labels.chest')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">{{$gwm->chest}}</td>
                                        <td colspan="3"
                                            style="font-size: 20px; font-weight: 600; ">@lang('labels.cm')</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:150px;">@lang('labels.waist')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">{{$gwm->waist}}</td>
                                        <td colspan="3"
                                            style="font-size: 20px; font-weight: 600; ">@lang('labels.cm')</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:150px;">@lang('labels.abdomen')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">{{$gwm->abdomen}}</td>
                                        <td colspan="3"
                                            style="font-size: 20px; font-weight: 600; ">@lang('labels.cm')</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:150px;">@lang('labels.hip')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">{{$gwm->hip}}</td>
                                        <td colspan="3"
                                            style="font-size: 20px; font-weight: 600; ">@lang('labels.cm')</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:150px;">@lang('labels.thigh')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">{{$gwm->thigh}}</td>
                                        <td style="font-size: 20px; font-weight: 600; padding-left: 15px; padding-right: 15px;">
                                            &times;2
                                        </td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">{{$gwm->thigh * 2}}</td>
                                        <td style="font-size: 20px; font-weight: 600; ">@lang('labels.cm')</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:150px;">@lang('labels.arm')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">{{$gwm->arm}}</td>
                                        <td style="font-size: 20px; font-weight: 600; padding-left: 15px; padding-right: 15px;">
                                            &times;2
                                        </td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">{{$gwm->arm * 2}}</td>
                                        <td style="font-size: 20px; font-weight: 600; ">@lang('labels.cm')</td>
                                    </tr>
                                </table>
                                <hr style="margin: 15px 0px 5px 0px; border-top: 2px dashed #79538f;">
                                <table>
                                    <tr>
                                        <td style="font-size: 24px; font-weight: 600; width:150px;">@lang('labels.total')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">{{$gwm->total_measurement}}</td>
                                        <td style="font-size: 20px; font-weight: 600; ">@lang('labels.cm')</td>
                                    </tr>
                                </table>
                                <hr style="margin: 15px 0px 5px 0px; border-top: 2px dashed #79538f;">
                                <table>
                                    <tr>
                                        <td style="font-size: 17px; font-weight: 600; width:300px;">@lang('labels.current_weight')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 25px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">{{$gwm->weight}}</td>
                                        <td style="font-size: 17px; font-weight: 600; ">@lang('labels.kg')</td>
                                    </tr>
                                </table>
                                <hr style="margin: 5px 0px; border-top: 2px dashed #79538f;">
                                <table>
                                    <tr>
                                        <td style="font-size: 17px; font-weight: 600; width:300px;">@lang('labels.current_measurement')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 25px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">{{$gwm->total_measurement}}</td>
                                        <td style="font-size: 17px; font-weight: 600; ">@lang('labels.cm')</td>
                                    </tr>
                                </table>
                                <hr style="margin: 5px 0px; border-top: 2px dashed #79538f;">
                                <table>
                                    <tr>
                                        <td style="font-size: 17px; font-weight: 600; width:300px;">@lang('labels.current_bodyfat_percentage')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 25px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">{{$gwm->body_fat}}</td>
                                        <td style="font-size: 17px; font-weight: 600; ">%</td>
                                    </tr>
                                </table>
                                <hr style="margin: 5px 0px; border-top: 2px dashed #79538f;">
                                <table>
                                    <tr>
                                        <td style="font-size: 17px; font-weight: 600; width:300px;">@lang('labels.current_body_water')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 25px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">{{$gwm->total_hydration}}</td>
                                        <td style="font-size: 17px; font-weight: 600; ">%</td>
                                    </tr>
                                </table>
                                <hr style="margin: 5px 0px; border-top: 2px dashed #79538f;">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6 col-xs-6 my-col">
                        <div class="panel panel-info my-data">
                            <div class="panel-heading">@lang('labels.previous')</div>
                            <div class="datecurpre">
                                @if(!empty($prev_wm))
                                    {{date('d F Y', strtotime($prev_wm->wm_date))}}
                                @else
                                    -
                                @endif
                            </div>
                            <div class="panel-body">
                                <h4 style="color: #dc3c91; font-size: 24px; font-weight: 600;">@lang('labels.body_composition')
                                    :</h4>
                                <table>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:290px;">@lang('labels.weight')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">
                                            @if(!empty($prev_wm))
                                                {{$prev_wm->weight}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td style="font-size: 20px; font-weight: 600; ">@lang('labels.kg')</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:290px;">@lang('labels.body_fat')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">
                                            @if(!empty($prev_wm))
                                                {{$prev_wm->body_fat}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td style="font-size: 20px; font-weight: 600; ">%</td>
                                    </tr>
                                </table>
                                <hr style="margin: 15px 0px; border-top: 2px dashed #79538f;">
                                <table>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:290px;">@lang('labels.muscle_mass')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">
                                            @if(!empty($prev_wm))
                                                {{$prev_wm->muscle_mass}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td style="font-size: 20px; font-weight: 600; ">@lang('labels.kg')</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:290px;">@lang('labels.bone_mass')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">
                                            @if(!empty($prev_wm))
                                                {{$prev_wm->bone_mass}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td style="font-size: 20px; font-weight: 600; ">@lang('labels.kg')</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:290px;">@lang('labels.bmi')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">
                                            @if(!empty($prev_wm))
                                                {{$prev_wm->bmi}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:290px;">@lang('labels.dci')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">
                                            @if(!empty($prev_wm))
                                                {{$prev_wm->dci}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td style="font-size: 20px; font-weight: 600; ">@lang('labels.cal')</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:290px;">@lang('labels.metabolic')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">
                                            @if(!empty($prev_wm))
                                                {{$prev_wm->metabolic}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td style="font-size: 20px; font-weight: 600; ">@lang('labels.years_old')</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:290px;">@lang('labels.body_water')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">
                                            @if(!empty($prev_wm))
                                                {{$prev_wm->body_water}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td style="font-size: 20px; font-weight: 600; ">%</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:290px;">@lang('labels.visceral')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">
                                            @if(!empty($prev_wm))
                                                {{$prev_wm->visceral}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td></td>
                                    </tr>
                                </table>
                                <h4 style="color: #dc3c91; font-size: 24px; font-weight: 600;">@lang('labels.size')</h4>
                                <table>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:150px;">@lang('labels.chest')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">
                                            @if(!empty($prev_wm))
                                                {{$prev_wm->chest}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td colspan="3"
                                            style="font-size: 20px; font-weight: 600; ">@lang('labels.cm')</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:150px;">@lang('labels.waist')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">
                                            @if(!empty($prev_wm))
                                                {{$prev_wm->waist}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td colspan="3"
                                            style="font-size: 20px; font-weight: 600; ">@lang('labels.cm')</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:150px;">@lang('labels.abdomen')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">
                                            @if(!empty($prev_wm))
                                                {{$prev_wm->abdomen}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td colspan="3"
                                            style="font-size: 20px; font-weight: 600; ">@lang('labels.cm')</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:150px;">@lang('labels.hip')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">
                                            @if(!empty($prev_wm))
                                                {{$prev_wm->hip}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td colspan="3"
                                            style="font-size: 20px; font-weight: 600; ">@lang('labels.cm')</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:150px;">@lang('labels.thigh')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">
                                            @if(!empty($prev_wm))
                                                {{$prev_wm->thigh}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td style="font-size: 20px; font-weight: 600; padding-left: 15px; padding-right: 15px;">
                                            &times;2
                                        </td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">
                                            @if(!empty($prev_wm))
                                                {{$prev_wm->thigh * 2}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td style="font-size: 20px; font-weight: 600; ">@lang('labels.cm')</td>
                                    </tr>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:150px;">@lang('labels.arm')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">
                                            @if(!empty($prev_wm))
                                                {{$prev_wm->arm}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td style="font-size: 20px; font-weight: 600; padding-left: 15px; padding-right: 15px;">
                                            &times;2
                                        </td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">
                                            @if(!empty($prev_wm))
                                                {{$prev_wm->arm * 2}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td style="font-size: 20px; font-weight: 600; ">@lang('labels.cm')</td>
                                    </tr>
                                </table>
                                <hr style="margin: 15px 0px 5px 0px; border-top: 2px dashed #79538f;">
                                <table>
                                    <tr>
                                        <td style="font-size: 24px; font-weight: 600; width:150px;">@lang('labels.total')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 40px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">
                                            @if(!empty($prev_wm))
                                                {{$prev_wm->total_measurement}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td colspan="3"
                                            style="font-size: 20px; font-weight: 600; ">@lang('labels.cm')</td>
                                    </tr>
                                </table>
                                <hr style="margin: 15px 0px 5px 0px; border-top: 2px dashed #79538f;">
                                <table>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:290px;">@lang('labels.last_weight')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 25px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">
                                            @if(!empty($prev_wm))
                                                {{$prev_wm->weight}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td style="font-size: 17px; font-weight: 600; ">@lang('labels.kg')</td>
                                    </tr>
                                </table>
                                <hr style="margin: 5px 0px; border-top: 2px dashed #79538f;">
                                <table>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:290px;">@lang('labels.last_measurement')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 25px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">
                                            @if(!empty($prev_wm))
                                                {{$prev_wm->total_measurement}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td style="font-size: 17px; font-weight: 600; ">@lang('labels.cm')</td>
                                    </tr>
                                </table>
                                <hr style="margin: 5px 0px; border-top: 2px dashed #79538f;">
                                <table>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:290px;">@lang('labels.last_bodyfat_percentage')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 25px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">
                                            @if(!empty($prev_wm))
                                                {{$prev_wm->body_fat}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td style="font-size: 17px; font-weight: 600; ">%</td>
                                    </tr>
                                </table>
                                <hr style="margin: 5px 0px; border-top: 2px dashed #79538f;">
                                <table>
                                    <tr>
                                        <td style="font-size: 20px; font-weight: 600; width:290px;">@lang('labels.last_body_water')</td>
                                        <td style="font-size: 20px; width: 15px;">:</td>
                                        <td style="font-size: 25px; color: #6752a5; border-bottom: 2px solid #b74c7e; width: 60px; text-align: right; padding-right: 10px;">
                                            @if(!empty($prev_wm))
                                                {{$prev_wm->total_hydration}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td style="font-size: 17px; font-weight: 600; ">%</td>
                                    </tr>
                                </table>
                                <hr style="margin: 5px 0px; border-top: 2px dashed #79538f;">
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 my-col">
                        <div class="panel panel-primary panel-kiri-bawah" style="height: 269px;">
                            <div class="panel-heading">@lang('labels.since_curves') <span class="pull-right">@lang('labels.date'): {{\Carbon\Carbon::parse($gwm->from_date)->format('d M Y')}}</span></div>
                            <div class="panel-body">
                                <div class="col-md-4 col-xs-4 since-curves">
                                    <h4>@lang('labels.weight_difference')</h4>
                                    <div class="since-curves-image" style="background-image: url('{{asset('images/assets/stamp-wnm.png')}}')">
                                        <div class="since-curves-number">{{number_format(($gwm->weight - $first_wm->weight), 2)}} @lang('labels.kg')</div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xs-4 since-curves">
                                    <h4>@lang('labels.measurement_difference')</h4>
                                    <div class="since-curves-image" style="background-image: url('{{asset('images/assets/stamp-wnm.png')}}')">
                                        <div class="since-curves-number">{{number_format(($gwm->total_measurement - $first_wm->total_measurement), 2)}} @lang('labels.cm')</div>
                                    </div>
                                </div>
                                <div class="col-md-4 col-xs-4 since-curves">
                                    <h4>@lang('labels.body_fat_difference')</h4>
                                    <div class="since-curves-image" style="background-image: url('{{asset('images/assets/stamp-wnm.png')}}')">
                                        <div class="since-curves-number">{{number_format(($gwm->body_fat - $first_wm->body_fat), 2)}} %</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xs-4 my-col">
                <div class="panel panel-warning section-right section-right-top">
                    <div class="panel-heading">
                        <h3 style="margin-top: 0px;">
                            <span style="float:left; font-size: 40px; font-weight: 600; color: #ec068c;">@lang('labels.total_workout')</span><br>
                            <span style="text-align:right;float: right; font-size: 35px; font-weight: 600; color: #ec068c;">@lang('labels.since') @lang('labels.become') @lang('labels.member')</span>
                        </h3>
                    </div>
                    <div style="padding: 0px 15px;">
                        <hr style="margin-top: 37px; margin-bottom: 0px; border-top: 2px solid #79538f;">
                    </div>
                    <div class="panel-body">
                        <div style="display: inline-block; width: 100%; padding-left: 17px;">
                            @php($total_wo = 0)
                            @if(count($workouts) > 0)
                                @php($total_wo = count($workouts))
                            @endif
                            <span style="font-size: 54px">{{$total_wo}} &times;</span><b
                                    style="font-size: 17px; color: #754e9f; "> @lang('labels.workout') @lang('labels.since') @lang('labels.become') @lang('labels.member')</b>
                        </div>
                        <hr style="margin: 0px; border-top: 2px dashed #79538f;">

                        <div class="section-right-top-bawah"
                             style="display: inline-block; width: 80%; margin-left: 77px; margin-bottom: 50px; margin-top: 17px;">
                            <span style="font-size: 65px; color: #ec068c; line-height: 36px;">
                                @php($real_total_wo = 0)
                                @php($temp_counter = 1)
                                @if($total_wo < 100)
                                @php($real_total_wo = 100 - $total_wo)
                                @elseif($total_wo > 100)
                                @php($looping = true)
                                @php($temp_counter = 2)
                                @while($looping)
                                @if($total_wo < ($temp_counter * 100))
                                @php($real_total_wo = ($temp_counter * 100) - $total_wo)
                                @php($looping = false)
                                @else
                                @php($temp_counter++)
                                @endif
                                @endwhile
                                @endif
                                {{$real_total_wo}} &times;
                            </span>
                            <b style="font-size: 17px; color: #754e9f;"> @lang('labels.workout_to_go') @lang('labels.for')
                                <br>
                                <span style="text-align: left !important;">@lang('labels.achieve')</span>
                            </b>
                            <span class="next-goal"
                                  style="font-size: 88px; font-weight: 600; color: #ec068c; position: absolute; top: 227px; right: 107px;">{{$temp_counter * 100}} !</span>
                        </div>
                        <hr style="margin: 0px; border-top: 2px dashed #79538f;">
                        <b style="font-size: 17px; color: #754e9f;"> @lang('labels.total_wo_per_month'): {{$gwm->total_wo_per_month}}&times;</b>
                    </div>
                </div>

                <div class="panel panel-info panel-selisih panel-kanan-bawah">
                    <div class="panel-heading">@lang('labels.difference')</div>
                    <div class="panel-body">
                        @php
                            if (!empty($prev_wm)):
                                $diff_weight = $gwm->weight - $prev_wm->weight;
                                $diff_measurement = $gwm->total_measurement - $prev_wm->total_measurement;
                            else:
                                $diff_weight = 0;
                                $diff_measurement = 0;
                            endif;
                        @endphp
                        <p class="my-subtitle"
                           style="color: #dc3c91; font-size: 24px; font-weight: 600;">@lang('labels.weight_difference')
                            :</p>
                        <p class="frame"
                           style="font-size: 120px; text-align: center; color: #6752a5; line-height: 140px;">{{number_format($diff_weight, 2)}}</p>
                        <br>
                        <p class="my-subtitle"
                           style="color: #dc3c91; font-size: 24px; font-weight: 600;">@lang('labels.measurement_difference')
                            :</p>
                        <p class="frame"
                           style="font-size: 120px; text-align: center; color: #6752a5; line-height: 140px;">{{number_format($diff_measurement, 2)}}</p>
                        <hr style="margin: 15px 0px; border-top: 2px dashed #79538f;">
                        <h4 style="color: #dc3c91; font-size: 24px; font-weight: 600;">@lang('labels.target')
                            (@lang('labels.xfactor')):</h4>
                        {{--@if(count($xfactors) > 0)--}}
                        {{--<ul>--}}
                        {{--@foreach($xfactors as $xfactor)--}}
                        {{--<li>{{$xfactor->xfactor}}</li>--}}
                        {{--@endforeach--}}
                        {{--</ul>--}}
                        {{--@else--}}
                        {{-----}}
                        {{--@endif--}}
                    </div>
                </div>
                <div class="panel panel-nasihat">
                    <div style="padding: 0px 15px;">
                        <h4 style="color: #dc3c91; font-size: 24px; font-weight: 600;">@lang('labels.advice') :</h4>
                    </div>
                    <div class="panel-body">
                        {!! nl2br(e($gwm->recommendation)) !!}
                    </div>
                </div>
            </div>
        </div>


    </div>
@endsection

@section('other-script')
    <script>
        $('#my-btn-print').on('click', function () {
            $(this).addClass('hidden');
            $('.left-sidebar').addClass('hidden');
            $('.nav-tabs').addClass('hidden');
            $('#floating-button').addClass('hidden');
            $('#manajemenkeanggotaan').addClass('hidden');
            $('.my-data table td').addClass('my-print-font');
            $('.margincontent').addClass('my-print-padding');
            $('.page-title').addClass('my-print-title my-print-hr');
            $('.data-top table td').addClass('my-print-subtitle');
            $('.panel-heading').addClass('my-print-subtitle');
            $('.my-data .datecurpre').addClass('my-print-subtitle');
            $('.panel-heading h3 span').addClass('my-print-subtitle2');
            $('.section-right-top hr').addClass('my-print-hr');
            $('.section-right-top span').addClass('my-print-title');
            $('.section-right-top b').addClass('my-print-subtitle2');
            $('.section-right-top-bawah').addClass('my-print-nomargin');
            $('.next-goal').addClass('my-print-float-right');
            $('.panel-heading2').addClass('my-print-subtitle2');
            $('.my-col').addClass('my-print-col');
            $('.my-subtitle').addClass('my-print-subtitle2');
            $('.panel-selisih .frame').addClass('my-print-frame');
            $('.panel').addClass('my-print-panel');
            $('.wmimgprint img').addClass('my-print-foto');
            $('.section-topz table').addClass('my-print-nomargin');
            $('.panel-body').addClass('my-print-no-padding-top');
            $('.panel-nasihat').addClass('hidden');
            $('.panel-kiri-bawah').addClass('my-print-bawah-kiri');
            $('.panel-kanan-bawah').addClass('my-print-bawah-kanan');
            $('.tab-pane').addClass('my-print-nomargin');
            $('.since-curves').addClass('my-print-since-curves');
            $('.header-detail-wm').css('padding-top', '20');
            $('.box-menu').css('border', '0');
            $('.curves-logo').show();
            $('.page-title').text('{{$member->buddyreferrallead_club->club_name}}');
//            $('#club-name-row').show();
            window.print();
            $('.tab-pane').removeClass('my-print-nomargin');
            $('.panel-kanan-bawah').removeClass('my-print-bawah-kanan');
            $('.panel-kiri-bawah').removeClass('my-print-bawah-kiri');
            $('.panel-nasihat').removeClass('hidden');
            $('.panel-body').removeClass('my-print-no-padding-top');
            $('.section-topz table').removeClass('my-print-nomargin');
            $('.wmimgprint img').removeClass('my-print-foto');
            $('.panel').removeClass('my-print-panel');
            $('.panel-selisih .frame').removeClass('my-print-frame');
            $('.my-subtitle').removeClass('my-print-subtitle2');
            $('.my-col').removeClass('my-print-col');
            $('.panel-heading2').removeClass('my-print-subtitle2');
            $('.next-goal').removeClass('my-print-float-right');
            $('.section-right-top-bawah').removeClass('my-print-nomargin');
            $('.section-right-top b').removeClass('my-print-subtitle2');
            $('.section-right-top span').removeClass('my-print-title');
            $('.section-right-top hr').removeClass('my-print-hr');
            $('.panel-heading h3 span').removeClass('my-print-subtitle2');
            $('.my-data .datecurpre').removeClass('my-print-subtitle');
            $('.panel-heading').removeClass('my-print-subtitle');
            $('.data-top table td').removeClass('my-print-subtitle');
            $('.page-title').removeClass('my-print-title');
            $('.page-title').removeClass('my-print-hr');
            $('.margincontent').removeClass('my-print-padding');
            $('.my-data table td').removeClass('my-print-font');
            $('#floating-button').removeClass('hidden');
            $('#manajemenkeanggotaan').removeClass('hidden');
            $('.nav-tabs').removeClass('hidden');
            $('.left-sidebar').removeClass('hidden');
            $(this).removeClass('hidden');
            $('.since-curves').removeClass('my-print-since-curves');
            $('.header-detail-wm').css('padding-top', '0');
            $('.box-menu').css('border', '');
            $('.curves-logo').hide();
            $('.page-title').text('@lang('labels.weigh_n_measure')');
//            $('#club-name-row').hide();
        });
    </script>
@endsection