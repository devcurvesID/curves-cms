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

        #cl-span{
            color: hotpink;
        }

        #cl-span:hover a div h3,
        #cl-span:hover a div span{
            -webkit-animation: colorchange 1s infinite ease-in-out alternate;
            animation: colorchange1 1s infinite ease-in-out alternate;
        }
        @keyframes colorchange1 {
            from {
                color: pink;
            }
            to {
                color: #666666;
            }
        }

        .purple-span {
            color: rebeccapurple;
        }

        .purple-span:hover a div h3,
        .purple-span:hover a div span{
            -webkit-animation: colorchange 1s infinite ease-in-out alternate;
            animation: colorchange2 1s infinite ease-in-out alternate;
        }
        @keyframes colorchange2 {
            from {
                color: rebeccapurple;
            }
            to {
                color: #666666;
            }
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
                    <h2 class="page-title">@lang('labels.setting')</h2>
                </center>
            </div>
            <div class="col-sm-12">
                <div class="col-md-10 col-md-offset-1">
                    <div class="col-md-3 main-btn">
                        @foreach($access_list->where('key', 'setting_club_choose') as $key => $value)
                            @if($loop->last)
                                @php($unlock = 0)
                                @foreach(Auth::user()->user_accessmenu as $access)
                                    @if($access->id == $value['id'])
                                        @php($unlock = 1)
                                    @endif
                                @endforeach
                                @if($unlock == 1)
                                    @php($club_link = 'cf-lite/club')
                                @else
                                    @php($club_link = 'cf-lite/club/'.Auth::user()->club_id)
                                @endif
                            @endif
                        @endforeach
                        <a href="{{url($club_link)}}">
                            <div class="home-btn"
                                 style="background-color: #fab752; border: 5px solid #e19e38;">
                                <span class="fa fa-cog"></span>
                                <h3 class="home-btn-title">@lang('labels.club')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 main-btn">
                        @foreach($access_list->where('key', 'setting_staff_club_choose') as $key => $value)
                            @if($loop->last)
                                @php($unlock = 0)
                                @foreach(Auth::user()->user_accessmenu as $access)
                                    @if(($access->id == $value['id']))
                                        @php($unlock = 1)
                                        @php(session(['staff_club_choose' => 'Yes']))
                                    @endif
                                @endforeach
                                @if($unlock == 1)
                                    @php($club_link = 'cf-lite/staff')
                                @else
                                    @php($club_link = 'cf-lite/staff/'.Auth::user()->club_id)
                                @endif
                            @endif
                        @endforeach
                        <a href="{{url($club_link)}}">
                            <div class="home-btn"
                                 style="background-color: #ccadd1; border: 5px solid #b79bbc;">
                                <span class="fa fa-group"></span>
                                <h3 class="home-btn-title">@lang('labels.staff')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 main-btn">
                        <a href="{{url('cf-lite/product-setting')}}">
                            <div class="home-btn"
                                 style="background-color: #AFEECF; border: 5px solid #9dd6ba;">
                                <span class="fa fa-shopping-bag"></span>
                                <h3 class="home-btn-title">@lang('labels.product_setting')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 main-btn">
                        <a href="{{url('cf-lite/voucher')}}">
                            <div class="home-btn"
                                 style="background-color: #afeeee; border: 5px solid #9dd6d6;">
                                <span class="fa fa-qrcode"></span>
                                <h3 class="home-btn-title">@lang('labels.voucher')</h3>
                            </div>
                        </a>
                    </div>
                    @foreach($access_list->where('key', 'setting_tax_type') as $key => $value)
                        @if($loop->last)
                            @php($unlock = 0)
                            @foreach(Auth::user()->user_accessmenu as $access)
                                @if(($access->id == $value['id']))
                                    @php($unlock = 1)
                                @endif
                            @endforeach
                            @if($unlock == 1)
                                <div class="col-md-3 main-btn">
                                    <a href="{{url('cf-lite/tax-type')}}">
                                        <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                            <div class="col-xs-12">
                                                <img src="{{asset('images/assets/icon tipe pajak-08.png')}}" style="width: 40px; margin-bottom: 15px;">
                                            </div>
                                            <h3 class="home-btn-title">@lang('labels.tax_type')</h3>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        @endif
                    @endforeach
                    <div class="col-md-3 main-btn">
                        <a href="{{url('cf-lite/access-menu/club')}}">
                            <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                <span class="fa fa-wrench"></span>
                                <h3 class="home-btn-title">@lang('labels.access_menu')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 main-btn">
                        <a href="{{url('cf-lite/roles/functional-roles-list')}}">
                            <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/icon peran fungsional-09.png')}}" style="width: 40px; margin-bottom: 10px;">
                                </div>
                                <h3 class="home-btn-title">@lang('labels.functional_role')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 main-btn">
                        <a href="{{url('cf-lite/attributes/attribute-setting-choice')}}">
                            <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/icon atribut-01.png')}}" style="width: 40px; margin-bottom: 10px;">
                                </div>
                                <h3 class="home-btn-title">@lang('labels.attributes')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 main-btn">
                        <a href="{{url('cf-lite/predefined-message')}}">
                            <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                <span class="fa fa-envelope"></span>
                                <h3 class="home-btn-title">@lang('labels.predefined_message')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 main-btn">
                        <a href="{{url('cf-lite/challenges/challenges-list')}}">
                            <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/icon tantangan-06.png')}}" style="width: 40px; margin-bottom: 15px;">
                                </div>
                                <h3 class="home-btn-title">@lang('labels.challenges')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 main-btn">
                        {{--Edited by Ricky--}}
                        <a href="{{url('cf-lite/transfer-staff/transfer-staff-list')}}">
                            <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                <span class="fa fa-group"></span>
                                <h3 class="home-btn-title">@lang('labels.transfer_staff')</h3>
                            </div>
                        </a>
                        {{--<a href="{{url('cf-lite/transfer-staff/transfer-staff-list')}}">--}}
                            {{--<div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">--}}
                                {{--<span class="fa fa-group"></span>--}}
                                {{--<h3 class="home-btn-title">@lang('labels.transfer_staff')</h3>--}}
                            {{--</div>--}}
                        {{--</a>--}}
                    </div>
                    <div class="col-md-3 main-btn">
                        <a href="{{url('cf-lite/event/global-event-club-list')}}">
                            <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/icon kegiatan global-07.png')}}" style="width: 40px; margin-bottom: 10px;">
                                </div>
                                <h3 class="home-btn-title">@lang('labels.global_event')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 main-btn">
                        <a href="{{url('cf-lite/member/global-message-list')}}">
                            <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                <span class="fa fa-envelope"></span>
                                <h3 class="home-btn-title">@lang('labels.global_message')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 main-btn">
                        <a href="{{url('cf-lite/membership-types')}}">
                            <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                <span class="fa fa-group"></span>
                                <h3 class="home-btn-title">@lang('labels.membership_types')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 main-btn">
                        <a href="{{url('cf-lite/locker/locker-setting-choice')}}">
                            <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                <span class="glyphicon glyphicon-modal-window"></span>
                                <h3 class="home-btn-title">@lang('labels.lockers')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 main-btn">
                        <a href="{{url('cf-lite/corporate/corporate-mst-list')}}">
                            <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                <span class="glyphicon glyphicon-lock"></span>
                                <h3 class="home-btn-title">@lang('labels.corporation')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 main-btn">
                        <a href="{{url('cf-lite/certification/certification-list')}}">
                            <div class="home-btn" style="background-color: rgba(229,221,128,0.9); border: 5px solid #cec673;">
                                <span class="glyphicon glyphicon-book"></span>
                                <h3 class="home-btn-title">@lang('labels.certification')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 main-btn" id="cl-span">
                        <a href="{{url('cf-lite/curves-learning/choose-menus')}}">
                            <div class="home-btn" id="cl-span" style="background-color: #fbfbfb; border: 5px solid deeppink;">
                                <span class="glyphicon glyphicon-book" id=" cl-span" style="color: pink"></span>
                                <h3 class="home-btn-title" id=" cl-span" style="color: pink">@lang('labels.curves_learning')</h3>
                            </div>
                        </a>
                    </div>
                    @if((Auth::user()->role_id <= 4) || (Auth::user()->role_id == 8))
                        <div class="col-md-3 main-btn" id="cl-span">
                            <a href="{{url('cf-lite/digital-product')}}">
                                <div class="home-btn" id="cl-span" style="background-color: #fbfbfb; border: 5px solid darkcyan;">
                                    <span class="glyphicon glyphicon-book" id=" cl-span" style="color: darkcyan"></span>
                                    <h3 class="home-btn-title" id=" cl-span" style="color: darkcyan">@lang('labels.digital_product')</h3>
                                </div>
                            </a>
                        </div>
                    @endif
                    <div class="col-md-3 main-btn" id="cl-span">
                        <a href="{{url('cf-lite/digital-product/inventory')}}">
                            <div class="home-btn" id="cl-span" style="background-color: #fbfbfb; border: 5px solid darkcyan;">
                                <span class="glyphicon glyphicon-book" id=" cl-span" style="color: darkcyan"></span>
                                <h3 class="home-btn-title" id=" cl-span" style="color: darkcyan">@lang('labels.product_digital_inventories')</h3>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-12">
                <div class="col-md-10 col-md-offset-1">
                    <div class="col-md-3 main-btn purple-span">
                        <a href="{{url('cf-lite/guest-membership')}}">
                            <div class="home-btn" style="background-color: #fbfbfb; border: 5px solid rebeccapurple;">
                                <span class="fa fa-ticket purple-span" style="color: rebeccapurple"></span>
                                <h3 class="home-btn-title purple-span" style="color: rebeccapurple">@lang('labels.membership_promo')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 main-btn purple-span">
                        <a href="{{url('cf-lite/bank')}}">
                            <div class="home-btn" style="background-color: #fbfbfb; border: 5px solid rebeccapurple;">
                                <span class="fa fa-bank purple-span" style="color: rebeccapurple"></span>
                                <h3 class="home-btn-title purple-span" style="color: rebeccapurple">@lang('labels.bank')</h3>
                            </div>
                        </a>
                    </div>
                    @if(\Illuminate\Support\Facades\Auth::user()->role_id == 1 || \Illuminate\Support\Facades\Auth::user()->role_id == 2 || \Illuminate\Support\Facades\Auth::user()->role_id == 3 || \Illuminate\Support\Facades\Auth::user()->role_id == 8)
                        @if(\Illuminate\Support\Facades\Auth::user()->user_club->club_jubilant->type == 'Main')
                            <div class="col-md-3 main-btn purple-span">
                                <a href="{{url('cf-lite/master/jubilant')}}">
                                    <div class="home-btn" style="background-color: #fbfbfb; border: 5px solid rebeccapurple;">
                                        <span class="fa fa-building-o purple-span" style="color: rebeccapurple"></span>
                                        <h3 class="home-btn-title purple-span" style="color: rebeccapurple">@lang('labels.jubilant')</h3>
                                    </div>
                                </a>
                            </div>
                        @endif
                    @endif
                    @if(\Illuminate\Support\Facades\Auth::user()->role_id == 1 || \Illuminate\Support\Facades\Auth::user()->role_id == 2 || \Illuminate\Support\Facades\Auth::user()->role_id == 3 || \Illuminate\Support\Facades\Auth::user()->role_id == 8)
                        @if(\Illuminate\Support\Facades\Auth::user()->user_club->club_jubilant->type == 'Main')
                            <div class="col-md-3 main-btn purple-span">
                                <a href="{{url('cf-lite/setting/accounting-account')}}">
                                    <div class="home-btn" style="background-color: #fbfbfb; border: 5px solid rebeccapurple;">
                                        <span><img src="{{ asset('images/icons/setting_personal.png') }}"></span>
                                        <h3 class="home-btn-title purple-span" style="color: rebeccapurple">@lang('labels.setting_accounting_account')</h3>
                                    </div>
                                </a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="col-md-10 col-md-offset-1">
                    <div class="col-md-3 main-btn purple-span">
                        <a href="{{url('cf-lite/member-scan-image')}}">
                            <div class="home-btn" style="background-color: #fbfbfb; border: 5px solid rebeccapurple;">
                                <span class="fa fa-image purple-span" style="color: rebeccapurple"></span>
                                <h3 class="home-btn-title purple-span" style="color: rebeccapurple">@lang('labels.member_scan_image')</h3>
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