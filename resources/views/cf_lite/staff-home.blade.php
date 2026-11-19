@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <section id="staff-home" class="" style="padding: 30px 0;">
        {{--<div class="col-sm-12">--}}
        {{--<center>--}}
        {{--<h2 class="page-title">@lang('labels.dashboard')</h2>--}}
        {{--</center>--}}
        {{--</div>--}}
        <div class="col-md-12 box-menu-padding">
            <div class="col-md-6">
                <div class="col-md-12 box-menu-utama box-menu-anggota">
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/member/list')}}">
                            <div class="home-btn home-btn-border-anggota">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/Interface Program Item-47_2.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.member_data')</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/new-member')}}">
                            <div class="home-btn home-btn-border-anggota">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/Interface Program Item-45_2.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.new_member')</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/lead/choose')}}">
                            <div class="home-btn home-btn-border-anggota">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/Interface Program Item-42_2.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.lead')</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="col-md-12 box-menu-utama box-menu-anggota2">
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/cancelling-membership')}}">
                            <div class="home-btn home-btn-border-anggota2">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/Interface Program Item-51_2.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.membership_cancellation')</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/member-hold')}}">
                            <div class="home-btn home-btn-border-anggota2">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/interface program-07.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.member_hold')</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/continue-membership')}}">
                            <div class="home-btn home-btn-border-anggota2">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/Interface Program Item-45_2.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.continue_membership')</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12 box-menu-padding">
            <div class="col-md-6">
                <div class="col-md-12 box-menu-utama box-menu-payment">
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/payment')}}">
                            <div class="home-btn home-btn-border-payment">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/interface program-08.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.payment')</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/product')}}">
                            <div class="home-btn home-btn-border-payment">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/interface program-09.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.product')</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/debt-list')}}">
                            <div class="home-btn home-btn-border-payment">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/Interface Program Item-29_2.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.debt')</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="col-md-12 box-menu-utama box-menu-manajemen">
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/refund')}}">
                            <div class="home-btn home-btn-border-manajemen">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/Interface Program Item-28_2.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.refund')</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/financial-management')}}">
                            <div class="home-btn home-btn-border-manajemen">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/Interface Program Item-81_2.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.financial_management')</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/laporan')}}">
                            <div class="home-btn home-btn-border-manajemen">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/Interface Program Item-79_2.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.report')</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 box-menu-padding">
            <div class="col-md-6">
                <div class="col-md-12 box-menu-utama box-menu-pengaturan">
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/locker/locker-sale-list',Auth::user()->club_id)}}">
                            <div class="home-btn home-btn-border-pengaturan">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/Penyewaan loker-14.png')}}">
                                </div>
                                <h3 class="home-btn-title">@lang('labels.locker_rental')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/member/member-monitoring-option')}}">
                            <div class="home-btn home-btn-border-pengaturan">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/interface program-12.png')}}">
                                </div>
                                <h3 class="home-btn-title">@lang('labels.member_monitoring')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/weigh-measure')}}">
                            <div class="home-btn home-btn-border-pengaturan">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/interface program-02.png')}}">
                                </div>
                                <h3 class="home-btn-title">@lang('labels.weigh_n_measure')</h3>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="col-md-12 box-menu-utama box-menu-info">
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/appointment/attendance')}}">
                            <div class="home-btn home-btn-border-info">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/interface program-13.png')}}">
                                </div>
                                @if($unfinished_drafts > 0)
                                    <span class="badge" style="position: absolute; left: 30px; top: 0; z-index: 99; background-color: red; color: #ffffff;" title="@lang('labels.uncompleted_draft')">{{$unfinished_drafts}}</span>
                                @endif
                                <h3 class="home-btn-title">@lang('labels.lead_attendance_list')</h3>
                            </div>
                        </a>
                    </div>
                    {{--<div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/pre-member')}}">
                            <div class="home-btn home-btn-border-info">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/Interface Program Item-47_2.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.pre_member')</h3>
                                </div>
                            </div>
                        </a>
                    </div>--}}
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/community-partner/choose-cp-menu')}}">
                            <div class="home-btn home-btn-border-info">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/Interface Program Item-47_2.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.community_partner')</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/product-request/product-request-main')}}">
                            <div class="home-btn home-btn-border-info">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/Permintaan product-15.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.product_request')</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-12 box-menu-padding">
            <div class="col-md-6">
                <div class="col-md-12 box-menu-utama box-menu-pengaturan">
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/document')}}">
                            <div class="home-btn home-btn-border-pengaturan">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/interface program-03.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.document')</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/settings')}}">
                            <div class="home-btn home-btn-border-pengaturan">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/Interface Program Item-78_2.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.application_setting')</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/other-settings')}}">
                            <div class="home-btn home-btn-border-pengaturan">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/Interface Program Item-77_2.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.other_setting')</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="col-md-12 box-menu-utama box-menu-info">
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/guest-member/list')}}">
                            <div class="home-btn home-btn-border-info">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/Interface Program Item-47_2.png')}}">
                                </div>
                                <h3 class="home-btn-title">@lang('labels.guest_membership')</h3>
                            </div>
                        </a>
                    </div>
                    {{--<div class="col-md-4 circle-menu">--}}
                        {{--<a href="{{url('cf-lite/manual-login')}}">--}}
                            {{--<div class="home-btn home-btn-border-info">--}}
                                {{--<div class="col-xs-12">--}}
                                    {{--<img src="{{asset('images/assets/interface program-11.png')}}">--}}
                                {{--</div>--}}
                                {{--<h3 class="home-btn-title">@lang('labels.manual_login')</h3>--}}
                            {{--</div>--}}
                        {{--</a>--}}
                    {{--</div>--}}
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/travel-pass')}}">
                            <div class="home-btn home-btn-border-info">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/interface program-10.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.travel_pass')</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/message')}}">
                            <div class="home-btn home-btn-border-info">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/interface program-04.png')}}">
                                </div>
                                <h3 class="home-btn-title">@lang('labels.message')</h3>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @if((\Illuminate\Support\Facades\Auth::user()->role_id == 8) && (\Illuminate\Support\Facades\Auth::id() == 8))
            <div class="col-md-12 box-menu-padding">
                <div class="col-md-6">
                    <div class="col-md-6 box-menu-utama box-menu-pengaturan">
                        <div class="col-md-12 circle-menu">
                            <a href="{{url('cf-lite/weigh-measure/all-club')}}">
                                <div class="home-btn home-btn-border-pengaturan" style="background-color: darkslategray;">
                                    <div class="col-xs-12">
                                        <img src="{{asset('images/assets/interface program-02.png')}}">
                                    </div>
                                    <h3 class="home-btn-title">@lang('labels.weigh_n_measure') (@lang('labels.all_club'))</h3>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @endif





    </section>
@endsection

@section('other-script')
@endsection