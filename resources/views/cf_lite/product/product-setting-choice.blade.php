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
                <ol class="breadcrumb pull-right" style="background-color: transparent;">
                    <li><a href="{{url('cf-lite/settings')}}" style="color: deeppink;">@lang('labels.setting')</a></li>
                    <li class="active" style="color: rebeccapurple;"><span>@lang('labels.product_setting')</span></li>
                </ol>
            </div>
            <div class="col-sm-12">
                <a href="{{url('cf-lite/settings')}}" style="left: 25px; top: 5px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.product_setting')</h2>
                </center>
            </div>
            <div class="col-sm-12">
                <div class="col-md-12">
                    @foreach($access_list->where('key', 'setting_product_type') as $key => $value)
                        @if($loop->last)
                            @php($unlock = 0)
                            @foreach(Auth::user()->user_accessmenu as $access)
                                @if(($access->id == $value['id']))
                                    @php($unlock = 1)
                                @endif
                            @endforeach
                            @if($unlock == 1)
                                <div class="col-md-3 main-btn">
                                    <a href="{{url('cf-lite/product/type')}}">
                                        <div class="home-btn"
                                             style="background-color: #afeecf; border: 5px solid #9dd6ba;">
                                            <span class="fa fa-shopping-bag"></span>
                                            <h3 class="home-btn-title">@lang('labels.product_type_list')</h3>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        @endif
                    @endforeach
                    @foreach($access_list->where('key', 'setting_product') as $key => $value)
                        @if($loop->last)
                            @php($unlock = 0)
                            @foreach(Auth::user()->user_accessmenu as $access)
                                @if(($access->id == $value['id']))
                                    @php($unlock = 1)
                                @endif
                            @endforeach
                            @if($unlock == 1)
                                <div class="col-md-3 main-btn">
                                    <a href="{{url('cf-lite/master-product')}}">
                                        <div class="home-btn" style="background-color: #afeeee; border: 5px solid #9dd6d6;">
                                            <span class="fa fa-shopping-bag"></span>
                                            <h3 class="home-btn-title">@lang('labels.product_list')</h3>
                                        </div>
                                    </a>
                                </div>
                            @endif
                        @endif
                    @endforeach
                    <div class="col-md-3 main-btn">
                        @foreach($access_list->where('key', 'setting_inventory_club_choose') as $key => $value)
                            @if($loop->last)
                                @php($unlock = 0)
                                @foreach(Auth::user()->user_accessmenu as $access)
                                    @if(($access->id == $value['id']))
                                        @php($unlock = 1)
                                    @endif
                                @endforeach
                                @if($unlock == 1)
                                    @php($club_link = 'cf-lite/product-inventory/club')
                                @elseif($unlock == 0)
                                    @php($club_link = 'cf-lite/product-inventory/'.Auth::user()->club_id)
                                @endif
                            @endif
                        @endforeach
                        <a href="{{url($club_link)}}">
                            <div class="home-btn"
                                 style="background-color: #afeeb0; border: 5px solid #9dd69e;">
                                <span class="fa fa-shopping-bag"></span>
                                <h3 class="home-btn-title">@lang('labels.product_inventory')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3 main-btn">
                        @foreach($access_list->where('key', 'setting_inventory_club_choose') as $key => $value)
                            @if($loop->last)
                                @php($unlock = 0)
                                @foreach(Auth::user()->user_accessmenu as $access)
                                    @if(($access->id == $value['id']))
                                        @php($unlock = 1)
                                    @endif
                                @endforeach
                                @if($unlock == 1)
                                    @php($club_link = 'cf-lite/product-inventory-correction/club')
                                @elseif($unlock == 0)
                                    @php($club_link = 'cf-lite/product-inventory/'.Auth::user()->club_id)
                                @endif
                            @endif
                        @endforeach
                        <a href="{{url($club_link)}}">
                            <div class="home-btn"
                                 style="background-color: #afeeb0; border: 5px solid #9dd69e;">
                                <span class="fa fa-shopping-bag"></span>
                                <h3 class="home-btn-title">@lang('labels.product_inventory_correction')</h3>
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