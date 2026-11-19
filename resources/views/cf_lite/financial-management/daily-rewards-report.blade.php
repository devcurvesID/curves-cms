@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css" rel="stylesheet">

    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">

    <style>
        #staff-home {
            padding: 15px;
        }

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

        .since-curves-number {
            font-size: 36px;
            font-weight: 600;
            padding-left: 33px;
            padding-top: 22px;
        }

        .since-curves-image {
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
            top: 28px !important;
            left: 25px !important;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <section id="staff-home">
        <div class="row">
            <a href="{{url('cf-lite/financial-management')}}" style="left: 25px; top: 5px;"
               class="btn btn-default btn-back-to" title="@lang('labels.back')"><span
                        class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <a id="my-btn-print" class="my-btn-print btn btn-default pull-right"
               style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: rebeccapurple; margin-right: 30px; margin-top: 30px; "
               title="@lang('labels.print')"><span class="fa fa-print"
                                                   style="margin-top: 10px; color: rebeccapurple;"></span></a>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <center>
                    <h2 class="page-title" style="margin-top: -10px;">
                        @lang('labels.daily_rewards_report')
                        <br>
                        @php
                            $date = \Carbon\Carbon::today();
                            if (!empty($_GET['d'])):
                                $date = \Carbon\Carbon::parse($_GET['d']);
                            endif;
                            $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                            $n= \Carbon\Carbon::parse($date)->format('w');
                        @endphp
                        <small>({{$days[$n].', '.\Carbon\Carbon::parse($date)->format('d-m-Y')}})</small>
                    </h2>
                </center>
            </div>
        </div>

        <div class="row" id="filter">
            <div class="col-md-12">
                <!-- Filter -->
                {!! Form::open(['method' => 'GET', 'class' => 'form-inline']) !!}
                <div class="form-group">
                    {!! Form::label('d', trans('labels.choose_date').':  ', ['class' => 'control-label']) !!}
                    {!! Form::date('d', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                    {!! Form::submit(trans('labels.apply'), ['class' => 'btn btn-default']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>

        @php($running_total = 0)
        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive" style="background-color: #fff;">
                            <table class="table table-hover table-bordered" id="data-table">
                                <thead>
                                <tr>
                                    <th>@lang('labels.product_type')</th>
                                    <th>@lang('labels.running_total')</th>
                                    <th>@lang('labels.price')</th>
                                    <th>@lang('labels.name')</th>
                                    <th>@lang('labels.membership_number')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php($total_product_price = 0)
                                @if(count($products) > 0)
                                    @foreach($products as $product)
                                        <tr>
                                            @php($running_total = $running_total + ($product->price * $product->qty))
                                            <td>{{$product->productpaymentdetail_product->item_name}}</td>
                                            <td class="my_rupiah">{{$running_total}}</td>
                                            <td class="my_rupiah">
                                                {{ ($product->price * $product->qty) }}
                                                @php($total_product_price += $product->price * $product->qty)
                                            </td>
                                            @if(!empty($product->productpaymentdetail_productpayment->member_id))
                                                <td>{{$product->productpaymentdetail_productpayment->productpayment_user->name}}</td>
                                                <td>{{$product->productpaymentdetail_productpayment->productpayment_user->user_memberpersonal->key_tag_id}}</td>
                                            @else
                                                <td>-</td>
                                                <td>-</td>
                                            @endif
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-offset-6 col-md-6">
                <div class="col-md-6">@lang('labels.total_rewards'):</div>
                <div class="col-md-6">
                    <h3 class="my_rupiah">{{ $running_total }}</h3>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>

    <script>
        $('#my-btn-print').on('click', function () {
            $(this).addClass('hidden');
            $('.left-sidebar').addClass('hidden');
            $('.nav-tabs').addClass('hidden');
            $('#floating-button').addClass('hidden');
            $('#manajemenkeanggotaan').addClass('hidden');
            // $('.my-data table td').addClass('my-print-font');
            // $('.margincontent').addClass('my-print-padding');
            $('.page-title').addClass('my-print-title my-print-hr');
            $('.data-top table td').addClass('my-print-subtitle');
            $('.panel-heading').addClass('my-print-subtitle');
            // $('.my-data .datecurpre').addClass('my-print-subtitle');
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
            // $('.section-topz table').addClass('my-print-nomargin');
            $('.panel-body').addClass('my-print-no-padding-top');
            $('.panel-nasihat').addClass('hidden');
            $('.panel-kiri-bawah').addClass('my-print-bawah-kiri');
            $('.panel-kanan-bawah').addClass('my-print-bawah-kanan');
            $('.tab-pane').addClass('my-print-nomargin');
            $('.since-curves').addClass('my-print-since-curves');
            $('.header-detail-wm').css('padding-top', '20');
            $('.box-menu').css('border', '0');
            $('.btn-back-to').hide();
            $('.print-receipt').hide();
            $('.export-excel').hide();
            $('#filter').addClass('hidden');
            $('#responsive').removeClass('table-responsive');
            $('.row').css('margin-bottom', '10');
            $('.table').css('font-size', '10');
            $('.dt-buttons').hide();
            $('#data-table_filter').hide();
            $('#data-table_info').hide();
            $('#data-table_paginate').hide();
            $('.page-title').css('margin-bottom', '0');

            window.print();

            $('.tab-pane').removeClass('my-print-nomargin');
            $('.panel-kanan-bawah').removeClass('my-print-bawah-kanan');
            $('.panel-kiri-bawah').removeClass('my-print-bawah-kiri');
            $('.panel-nasihat').removeClass('hidden');
            $('.panel-body').removeClass('my-print-no-padding-top');
            // $('.section-topz table').removeClass('my-print-nomargin');
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
            // $('.my-data .datecurpre').removeClass('my-print-subtitle');
            $('.panel-heading').removeClass('my-print-subtitle');
            $('.data-top table td').removeClass('my-print-subtitle');
            $('.page-title').removeClass('my-print-title');
            $('.page-title').removeClass('my-print-hr');
            // $('.margincontent').removeClass('my-print-padding');
            // $('.my-data table td').removeClass('my-print-font');
            $('#floating-button').removeClass('hidden');
            $('#manajemenkeanggotaan').removeClass('hidden');
            $('.nav-tabs').removeClass('hidden');
            $('.left-sidebar').removeClass('hidden');
            $(this).removeClass('hidden');
            $('.since-curves').removeClass('my-print-since-curves');
            $('.header-detail-wm').css('padding-top', '0');
            $('.box-menu').css('border', '');
            $('.btn-back-to').show();
            $('.print-receipt').show();
            $('.export-excel').show();
            $('#filter').removeClass('hidden');
            $('#responsive').addClass('table-responsive');
            $('.row').css('margin-bottom', '');
            $('.table').css('font-size', '');
            $('.dt-buttons').show();
            $('#data-table_filter').show();
            $('#data-table_info').show();
            $('#data-table_paginate').show();
            $('.page-title').css('margin-bottom', '50px');
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#data-table').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excel',
                    title: '@lang('labels.daily_rewards_report') - ({{\Carbon\Carbon::parse($date)->format('d-m-Y')}})',
                }]
            });

            init_format();
        });

        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp.' + (x));
            });
            $('.rupiah').each(function () {
                var x = rupiah_formater($(this).val().replace(/\s/g, ''));
                $(this).val((x));
            });
        }
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>

@endsection