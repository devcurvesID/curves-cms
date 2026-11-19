@extends('cf_lite.layouts.hq-dashboard-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <section id="staff-home" class="" style="padding: 30px 0;">
        <div class="col-md-12 box-menu-padding">
            <div class="col-md-12">
                <div class="col-md-12 box-menu-utama box-menu-manajemen">
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-lite/hq/credit-index')}}">
                            <div class="home-btn home-btn-border-manajemen">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/undraw_payments_21w6.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.hq_credit')</h3>
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
            <div class="col-md-12">
                <div class="col-md-12 box-menu-utama box-menu-info">
                    <div class="col-md-3 col-md-offset-3 circle-menu">
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
                    <div class="col-md-3 circle-menu">
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
            <div class="col-md-12">
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
        </div>

        @if((\Illuminate\Support\Facades\Auth::user()->role_id == 8) && (\Illuminate\Support\Facades\Auth::id() == 8))
            <div class="col-md-12 box-menu-padding">
                <div class="col-md-6">
                    <div class="col-md-6 box-menu-utama box-menu-pengaturan">
                        <div class="col-md-12 circle-menu">
                            <a href="{{url('cf-lite/weigh-measure/all-club')}}">
                                <div class="home-btn home-btn-border-pengaturan"
                                     style="background-color: darkslategray;">
                                    <div class="col-xs-12">
                                        <img src="{{asset('images/assets/interface program-02.png')}}">
                                    </div>
                                    <h3 class="home-btn-title">@lang('labels.weigh_n_measure') (@lang('labels.all_club')
                                        )</h3>
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