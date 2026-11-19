@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <style>
        .home-btn-title {
            font-family: 'Alegreya Sans', sans-serif;
        }

        .grey-bg {
            background-color: #efefef;
        }

        td {
            font-size: 14px;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @php($date = $_GET['date'])
    @php($working_day = 0)

    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/pj')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">
                        @lang('labels.projection_sheet')<br><small>({{\Carbon\Carbon::parse($date)->format('F Y')}})</small>
                    </h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th rowspan="2">@lang('labels.date')</th>
                                <th rowspan="2">@lang('labels.operation_date_counting') (1 @lang('labels.or') 0.5)</th>
                                <th rowspan="2">@lang('labels.working_day')</th>
                                <th rowspan="2">@lang('labels.daily_workout')</th>
                                <th colspan="{{count($sources) + 1}}" class="text-center">@lang('labels.source')</th>
                                <th colspan="8" class="text-center">@lang('labels.new_sales')</th>
                                <th colspan="8" class="text-center">@lang('labels.membership_renewal')</th>
                                <th colspan="8" class="text-center">@lang('labels.membership_cancellation')</th>
                                <th colspan="2" class="text-center">@lang('labels.member')</th>
                                <th rowspan="2" class="text-center">@lang('labels.appointment')</th>
                                <th colspan="{{count($product_categories) + 1}}" class="text-center">@lang('labels.product')</th>
                            </tr>
                            <tr>
                                @foreach($sources as $source)
                                    <th>{{$source->source}}</th>
                                @endforeach
                                <th>@lang('labels.real_total_guest') (@lang('labels.TOG'))</th>
                                <th>@lang('labels.pp_18_months')</th>
                                <th>@lang('labels.ad_12_months')</th>
                                <th>@lang('labels.pp_12_months')</th>
                                <th>@lang('labels.add_on_member')</th>
                                <th>@lang('labels.st_12_months')</th>
                                <th>@lang('labels.st_6_months')</th>
                                <th>@lang('labels.month_to_month')</th>
                                <th>@lang('labels.total_new_sales')</th>
                                <th>@lang('labels.pp_18_months')</th>
                                <th>@lang('labels.ad_12_months')</th>
                                <th>@lang('labels.pp_12_months')</th>
                                <th>@lang('labels.add_on_member')</th>
                                <th>@lang('labels.st_12_months')</th>
                                <th>@lang('labels.st_6_months')</th>
                                <th>@lang('labels.month_to_month')</th>
                                <th>@lang('labels.total_renewal')</th>
                                <th>@lang('labels.pp_18_months')</th>
                                <th>@lang('labels.ad_12_months')</th>
                                <th>@lang('labels.pp_12_months')</th>
                                <th>@lang('labels.add_on_member')</th>
                                <th>@lang('labels.st_12_months')</th>
                                <th>@lang('labels.st_6_months')</th>
                                <th>@lang('labels.month_to_month')</th>
                                <th>@lang('labels.total_cancellation')</th>
                                <th>@lang('labels.member_on_hold_status')</th>
                                <th>@lang('labels.member_on_hold_activated_status')</th>

                                @foreach($product_categories as $product_category)
                                    <th>{{$product_category->type}}</th>
                                @endforeach
                                <th>@lang('labels.locker')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 0; $i <= $diff_days; $i++)
                                <tr>
                                    <td>{{\Carbon\Carbon::parse($from_date)->addDays($i)->format('d-m-Y')}}</td>
                                    <td>
                                        @if(\Carbon\Carbon::parse($from_date)->addDays($i)->format('N') == 6)
                                            @php($working_day = $working_day + 0.5)
                                            0.5
                                        @elseif(\Carbon\Carbon::parse($from_date)->addDays($i)->format('N') == 7)
                                            0
                                        @else
                                            @php($working_day++)
                                            1
                                        @endif
                                    </td>
                                    <td>{{$working_day}}</td>
                                    <td>{{$wo_daily[$i]}}</td>

                                    {{--Source--}}
                                    @foreach($sources as $source)
                                        <td>{{$guest[$source->id][$i]}}</td>
                                    @endforeach

                                    <td>{{$tog[$i]}}</td>

                                    {{--New Sales--}}
                                    <td>{{$sales_pp_18[$i]}}</td>
                                    <td>{{$sales_ad_12[$i]}}</td>
                                    <td>{{$sales_pp_12[$i]}}</td>
                                    <td>0</td>
                                    <td>{{$sales_st_12[$i]}}</td>
                                    <td>{{$sales_st_6[$i]}}</td>
                                    <td>{{$sales_mm[$i]}}</td>
                                    <td>{{$sales_daily[$i]}}</td>

                                    {{--Membership Renewal--}}
                                    <td>{{$renewal_pp18[$i]}}</td>
                                    <td>{{$renewal_ad12[$i]}}</td>
                                    <td>{{$renewal_pp12[$i]}}</td>
                                    <td>0</td>
                                    <td>{{$renewal_st12[$i]}}</td>
                                    <td>{{$renewal_st6[$i]}}</td>
                                    <td>{{$renewal_mm[$i]}}</td>
                                    <td>{{$renewal_daily[$i]}}</td>

                                    {{--Membership Cancellation--}}
                                    <td>{{$cancellation_pp18[$i]}}</td>
                                    <td>{{$cancellation_ad12[$i]}}</td>
                                    <td>{{$cancellation_pp12[$i]}}</td>
                                    <td>0</td>
                                    <td>{{$cancellation_st12[$i]}}</td>
                                    <td>{{$cancellation_st6[$i]}}</td>
                                    <td>{{$cancellation_mm[$i]}}</td>
                                    <td>{{$cancellation_daily[$i]}}</td>

                                    {{--Member Hold--}}
                                    <td>{{$member_hold[$i]}}</td>
                                    <td>{{$member_hold_activated[$i]}}</td>

                                    {{--Guest Appointment--}}
                                    <td>{{$guest_appointment[$i]}}</td>

                                    {{--Product--}}
                                    @foreach($product_categories as $product_category)
                                        <td>{{$product_sold[$product_category->id][$i]}}</td>
                                    @endforeach

                                    {{--Locker--}}
                                    <td>{{$locker[$i]}}</td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('other-script')
@endsection