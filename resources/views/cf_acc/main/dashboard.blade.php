@extends('cf_acc.layouts.acc-dashboard-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
@endsection

@section('content')
    @if(Session::get('acc'))
        <h3>Hai {{Session::get('account')->name}}</h3>
    @else
        <h3>Hai guest</h3>
    @endif

    <section id="staff-home" class="" style="padding: 30px 0;">
        <div class="col-md-12 box-menu-padding">
            <div class="col-md-12">
                <div class="col-md-12 box-menu-utama box-menu-manajemen">
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-acc/temp-journal')}}">
                            <div class="home-btn home-btn-border-manajemen">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/Interface Program Item-79_2.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.temporary_journal')</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-acc/general-ledger')}}">
                            <div class="home-btn home-btn-border-manajemen">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/Interface Program Item-79_2.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.general_ledger')</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-acc/memorial-journal')}}">
                            <div class="home-btn home-btn-border-manajemen">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/Interface Program Item-79_2.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.memorial_journal')</h3>
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
                    <div class="col-md-4 col-md-offset-2 circle-menu">
                        <a href="{{url('cf-acc/amortization-report')}}">
                            <div class="home-btn home-btn-border-info">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/Interface Program Item-47_2.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.amortization_report')</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-acc/generate-amortization')}}">
                            <div class="home-btn home-btn-border-info">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/Interface Program Item-47_2.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.generate_amortization')</h3>
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
                        <a href="{{url('cf-acc/cash-flow')}}">
                            <div class="home-btn home-btn-border-pengaturan">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/Interface Program Item-81_2.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.cash_flow')</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-acc/bank-flow')}}">
                            <div class="home-btn home-btn-border-pengaturan">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/Interface Program Item-81_2.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.bank_flow')</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4 circle-menu">
                        <a href="{{url('cf-acc/purchasing-book')}}">
                            <div class="home-btn home-btn-border-pengaturan">
                                <div class="col-xs-12">
                                    <img src="{{asset('images/assets/interface program-08.png')}}">
                                </div>
                                <div class="col-xs-12">
                                    <h3 class="home-btn-title">@lang('labels.purchasing_book')</h3>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection