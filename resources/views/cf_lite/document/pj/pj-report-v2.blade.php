@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

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
    @php($total_sales = 0)
    @php($current_total_income = 0)

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
                    <table id="datable_1" class="table table-bordered">
                        <thead>
                            <tr>
                                <th rowspan="2">@lang('labels.date')</th>
                                <th rowspan="2">@lang('labels.operation_date_counting') (1 @lang('labels.or') 0.5)</th>
                                <th rowspan="2">@lang('labels.working_day')</th>
                                <th rowspan="2">@lang('labels.daily_workout')</th>
                                <th colspan="2">@lang('labels.new_sales_paid_today')</th>
                                <th rowspan="2">@lang('labels.adjustment')</th>
                                <th rowspan="2">@lang('labels.today_sales_adjustment')</th>
                                <th rowspan="2">@lang('labels.sales_adjustment_until_today')</th>
                                <th rowspan="2">@lang('labels.debt_reduction_today')</th>
                                <th rowspan="2">@lang('labels.today_debt')</th>
                                <th colspan="{{count($sources) + 1}}" class="text-center">@lang('labels.source')</th>
                                <th colspan="8" class="text-center">@lang('labels.new_sales')</th>
                                <th colspan="8" class="text-center">@lang('labels.membership_renewal')</th>
                                <th colspan="8" class="text-center">@lang('labels.membership_cancellation')</th>
                                <th colspan="2" class="text-center">@lang('labels.member')</th>
                                <th rowspan="2" class="text-center">@lang('labels.appointment')</th>
                                <th colspan="{{count($product_categories) + 2}}" class="text-center">@lang('labels.product')</th>
                                <th rowspan="2" class="text-center">@lang('labels.total_whole_income_today')</th>
                                <th rowspan="2" class="text-center">@lang('labels.total_income_until_today')</th>
                            </tr>
                            <tr>
                                <th>@lang('labels.new_sales')</th>
                                <th>@lang('labels.dues_and_drafts')</th>
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
                                <th>@lang('labels.total_product_income')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @for($i = 0; $i <= $diff_days; $i++)
                                @php($today_sales_adjustment = 0)
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

                                    {{--New Sales / Dues & Drafts--}}
                                    <td class="my_rupiah">
                                        @php($newsale_pay = 0)
                                        @php($newsale_debt = 0)
                                        @foreach($new_sales as $new_sale)
                                            @if(\Carbon\Carbon::parse($from_date)->addDays($i) == \Carbon\Carbon::parse($new_sale->payment_date))
                                                @if($new_sale->installment != null)
                                                    @php($newsale_pay = $new_sale->paid - $new_sale->installment)
                                                    @php($newsale_debt = $new_sale->debt_today)
                                                @else
                                                    @php($newsale_pay = $new_sale->paid)
                                                @endif

                                                @if($new_sale->debt_today != null)
                                                    @php($newsale_debt = $new_sale->debt_today)
                                                @endif
                                            @endif
                                        @endforeach

                                        {{$newsale_pay}}
                                    </td>
                                    <td class="my_rupiah">
                                        @php($membership_payment = 0)
                                        @php($debt_payment = 0)

                                        @foreach($membership_payment_daily[$i] as $mp)
                                            @if($mp->paid != null)
                                                @php($membership_payment = $mp->paid)
                                            @endif
                                        @endforeach

                                        @foreach($debt_payment_daily[$i] as $dp)
                                            @if($dp->paid != null)
                                                @php($debt_payment = $dp->paid)
                                            @endif
                                        @endforeach

                                        {{$membership_payment + $debt_payment}}
                                    </td>
                                    <td class="my_rupiah"></td>
                                    <td class="my_rupiah">
                                        {{$newsale_pay + $membership_payment + $debt_payment}}
                                        @php($today_sales_adjustment = $newsale_pay + $membership_payment + $debt_payment)
                                    </td>
                                    <td class="my_rupiah">
                                        @php($total_sales += $today_sales_adjustment)
                                        {{$total_sales}}
                                    </td>
                                    <td class="my_rupiah">{{$membership_payment + $debt_payment}}</td>
                                    <td class="my_rupiah">{{$newsale_debt}}</td>

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
                                    @php($total_product_sales = 0)
                                    @foreach($product_categories as $product_category)
                                        <td class="my_rupiah">
                                            @foreach($product_sold[$product_category->id][$i] as $product_paid)
                                                @if($product_paid->pay != null)
                                                    {{$product_paid->pay - $product_paid->discount}}
                                                    @php($total_product_sales += $product_paid->pay - $product_paid->discount)
                                                @else
                                                    0
                                                @endif
                                            @endforeach
                                        </td>
                                    @endforeach

                                    {{--Locker--}}
                                    <td class="my_rupiah">
                                        @foreach($locker[$i] as $lock)
                                            @if($lock->pay != null)
                                                {{$lock->pay}}
                                                @php($total_product_sales += $lock->pay)
                                            @else
                                                0
                                            @endif
                                        @endforeach
                                    </td>
                                    <td class="my_rupiah">{{$total_product_sales}}</td>
                                    <td class="my_rupiah">{{$today_sales_adjustment + $total_product_sales}}</td>
                                    <td class="my_rupiah">
                                        @php($current_total_income += $today_sales_adjustment + $total_product_sales)
                                        {{$current_total_income}}
                                    </td>
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
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>

    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function () {
            init_format();

            $('#datable_1').dataTable({
                'iDisplayLength': -1,
                'lengthMenu': [[10, 50, 100, -1], [10, 50, 100, '@lang('labels.all')']],
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<span class="fa fa-download" title="@lang('labels.export_excel')"></span> Excel',
                        title: '@lang('labels.PJ.').{{\Illuminate\Support\Facades\Auth::user()->user_club->club_name}}.{{\Carbon\Carbon::parse($date)->format('F Y')}}',
                        footer: true
                    },
                    {
                        extend: 'print',
                        footer: true,
                        text: '<span class="fa fa-print" title="@lang('labels.print')"></span> Print',
                        customize: function ( win ) {
                            $(win.document.body).find( 'table' )
                                .addClass( 'compact' )
                                .css( 'font-size', 'inherit' );

                            $(win.document.body).find( 'h1' )
                                .css( 'font-size', '16px' );
                        }
                    }
                ]
            });

            function init_format() {
                $('.my_rupiah').each(function () {
                    var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                    $(this).text('Rp.' + (x) + ',-');
                });
                $('.rupiah').each(function () {
                    var x = rupiah_formater($(this).val().replace(/\s/g, ''));
                    $(this).val((x));
                });
            }
        });
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>
@endsection