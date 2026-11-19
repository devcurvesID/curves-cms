@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
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
            top: 28px!important;
            left: 25px !important;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <section id="staff-home">
        <div class="row">
            <a href="{{url('cf-lite/financial-management')}}" style="left: 25px; top: 5px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <a href="{{url('cf-lite/financial-management/daily-sales-report/print')}}" class="btn btn-default pull-right print-receipt" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: rebeccapurple; margin-right: 30px; margin-top: 30px; " title="@lang('labels.daily_sales_report')"><span class="fa fa-print" style="margin-top: 10px; color: rebeccapurple;"></span></a>
            <a href="{{url('cf-lite/financial-management/daily-sales-report/export')}}" class="btn btn-default pull-right export-excel" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: rebeccapurple; margin-right: 30px; margin-top: 30px; " title="@lang('labels.export')"><span class="glyphicon glyphicon-export" style="margin-top: 10px; color: rebeccapurple;"></span></a>
            <a id="my-btn-print" class="my-btn-print btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: rebeccapurple; margin-right: 30px; margin-top: 30px; " title="@lang('labels.print')"><span class="fa fa-print" style="margin-top: 10px; color: rebeccapurple;"></span></a>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <center>
                    <h2 class="page-title" style="margin-top: -10px;">
                        @lang('labels.daily_sales_report')
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
                {!! Form::open(['method' => 'GET', 'action' => 'FinancialManagementReportController@daily_sales', 'class' => 'form-inline']) !!}
                <div class="form-group">
                    {!! Form::label('d', trans('labels.choose_date').':  ', ['class' => 'control-label']) !!}
                    {!! Form::date('d', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                    {!! Form::submit(trans('labels.apply'), ['class' => 'btn btn-default']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <h4>@lang('labels.new_sales_for_today')</h4>
                <div class="table-responsive" id="responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>@lang('labels.membership_type')</th>
                                <th>@lang('labels.running_total')</th>
                                <th>@lang('labels.bank_card')</th>
                                <th>@lang('labels.credit_card')</th>
                                <th>@lang('labels.transfer')</th>
                                <th>@lang('labels.qrpayment')</th>
                                <th>@lang('labels.indodana')</th>
                                <th>@lang('labels.shopeepay')</th>
                                <th>@lang('labels.cash')</th>
                                <th>@lang('labels.money_owed')</th>
                                <th>@lang('labels.name')</th>
                                <th>@lang('labels.membership_number')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($total_daily_cash = 0)
                            @php($total_daily_transfer = 0)
                            @php($total_daily_qr = 0)
                            @php($total_daily_indodana = 0)
                            @php($total_daily_shopeepay = 0)
                            @php($total_daily_debet = 0)
                            @php($total_daily_credit = 0)
                            @php($total_daily_owed = 0)
                            @php($running_total = 0)
                            @php($owed_by_member_first = [])
                            @php($owed_by_member_monthly = [])

                            @if(count($member_payments) > 0)
                                @foreach($member_payments->where('renewal', false)->where('conversion', false) as $member_payment)
                                    @php($cash = 0)
                                    @php($transfer = 0)
                                    @php($qr = 0)
                                    @php($indodana = 0)
                                    @php($shopeepay = 0)
                                    @php($debet = 0)
                                    @php($credit = 0)
                                    @php($no_method = 0)
                                    @php($debt = 0)

                                    @if($member_payment->payment_category == 'First')
                                        <tr>
                                            @if(!empty($member_payment->payment_method_id))
                                                @if($member_payment->payment_method_id == 1)
                                                    @php($cash = $member_payment->amount_paid)
                                                @elseif($member_payment->payment_method_id == 2)
                                                    @php($debet = $member_payment->amount_paid)
                                                @elseif($member_payment->payment_method_id == 3)
                                                    @php($credit = $member_payment->amount_paid)
                                                @elseif($member_payment->payment_method_id == 4)
                                                    @php($transfer = $member_payment->amount_paid)
                                                @elseif($member_payment->payment_method_id == 5)
                                                    @php($qr = $member_payment->amount_paid)
                                                @elseif($member_payment->payment_method_id == 6)
                                                    @php($indodana = $member_payment->amount_paid)
                                                @elseif($member_payment->payment_method_id == 7)
                                                    @php($shopeepay = $member_payment->amount_paid)
                                                @endif
                                            @else
                                                @php($no_method = $member_payment->amount_paid)
                                            @endif

                                            @if(!empty($member_payment->memberpayment_memberdebtpayment))
                                                @foreach($member_payment->memberpayment_memberdebtpayment as $debt_payment)
                                                    @if(\Carbon\Carbon::parse($debt_payment->payment_date) < \Carbon\Carbon::parse($date))
                                                        @php($debt = $debt + $debt_payment->amount_paid)
                                                    @endif
                                                @endforeach
                                            @endif
                                            @php($running_total = $running_total + $member_payment->amount_paid - $debt)

                                            <td>{{$member_payment->membership_type_name}}</td>
                                            <td class="my_rupiah">{{$running_total}}</td>
                                            <td class="my_rupiah">
                                                {{$debet - $debt}}
                                                @php($total_daily_debet = $total_daily_debet + $debet - $debt)
                                            </td>
                                            <td class="my_rupiah">
                                                {{$credit - $debt}}
                                                @php($total_daily_credit = $total_daily_credit + $credit - $debt)
                                            </td>
                                            <td class="my_rupiah">
                                                {{$transfer - $debt}}
                                                @php($total_daily_transfer = $total_daily_transfer + $transfer - $debt)
                                            </td>
                                            <td class="my_rupiah">
                                                {{$qr - $debt}}
                                                @php($total_daily_qr = $total_daily_qr + $qr - $debt)
                                            </td>
                                            <td class="my_rupiah">
                                                {{$indodana - $debt}}
                                                @php($total_daily_indodana = $total_daily_indodana + $indodana - $debt)
                                            </td>
                                            <td class="my_rupiah">
                                                {{$shopeepay - $debt}}
                                                @php($total_daily_shopeepay = $total_daily_shopeepay + $shopeepay - $debt)
                                            </td>
                                            <td class="my_rupiah">
                                                {{$cash - $debt}}
                                                @php($total_daily_cash = $total_daily_cash + $cash - $debt)
                                            </td>
                                            <td class="my_rupiah">
                                                @if(!empty($member_payment->money_owed))
                                                    <?php
                                                        if(isset($owed_by_member_first[$member_payment->user_id])){
                                                            // TODO
                                                        }else{
                                                            $owed_by_member_first[$member_payment->user_id] = $member_payment->money_owed;
                                                        }
                                                    ?>
                                                    {{$member_payment->money_owed}}
                                                    @php($total_daily_owed = $total_daily_owed + $member_payment->money_owed)
                                                @else
                                                    0
                                                @endif
                                            </td>
                                            <td>{{$member_payment->memberpayment_user->name}}</td>
                                            <td>{{$member_payment->memberpayment_user->user_memberpersonal->key_tag_id}}</td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h4>@lang('labels.new_promo_sales_for_today')</h4>
                <div class="table-responsive" id="responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>@lang('labels.membership_promo')</th>
                            <th>@lang('labels.running_total')</th>
                            <th>@lang('labels.bank_card')</th>
                            <th>@lang('labels.credit_card')</th>
                            <th>@lang('labels.transfer')</th>
                            <th>@lang('labels.qrpayment')</th>
                            <th>@lang('labels.indodana')</th>
                            <th>@lang('labels.shopeepay')</th>
                            <th>@lang('labels.cash')</th>
                            <th>@lang('labels.money_owed')</th>
                            <th>@lang('labels.name')</th>
                            <th>@lang('labels.membership_number')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php($total_promo_cash = 0)
                        @php($total_promo_transfer = 0)
                        @php($total_promo_qr = 0)
                        @php($total_promo_indodana = 0)
                        @php($total_promo_shopeepay = 0)
                        @php($total_promo_indodana = 0)
                        @php($total_promo_shopeepay = 0)
                        @php($total_promo_debet = 0)
                        @php($total_promo_credit = 0)
                        @php($total_promo_owed = 0)

                        @if(count($promo_members) > 0)
                            @foreach($promo_members as $promo)
                                @php($cash = 0)
                                @php($transfer = 0)
                                @php($qr = 0)
                                @php($indodana = 0)
                                @php($shopeepay = 0)
                                @php($debet = 0)
                                @php($credit = 0)
                                @php($no_method = 0)
                                @php($debt = 0)
                                <tr>
                                    @if(!empty($promo->payment_type_id))
                                        @if($promo->payment_type_id == 1)
                                            @php($cash = $promo->amount_paid)
                                        @elseif($promo->payment_type_id == 2)
                                            @php($debet = $promo->amount_paid)
                                        @elseif($promo->payment_type_id == 3)
                                            @php($credit = $promo->amount_paid)
                                        @elseif($promo->payment_type_id == 4)
                                            @php($transfer = $promo->amount_paid)
                                        @elseif($promo->payment_type_id == 5)
                                            @php($qr = $promo->amount_paid)
                                        @elseif($promo->payment_type_id == 6)
                                            @php($indodana = $promo->amount_paid)
                                        @elseif($promo->payment_type_id == 7)
                                            @php($shopeepay = $promo->amount_paid)
                                        @endif
                                    @else
                                        @php($no_method = $promo->amount_paid)
                                    @endif

                                    @php($running_total = $running_total + $promo->amount_paid)

                                    <td>{{$promo->gmpayment_gmmember->gmmember_gm->gm_name}}</td>
                                    <td class="my_rupiah">{{$running_total}}</td>
                                    <td class="my_rupiah">
                                        {{$debet}}
                                        @php($total_promo_debet = $total_promo_debet + $debet)
                                    </td>
                                    <td class="my_rupiah">
                                        {{$credit}}
                                        @php($total_promo_credit = $total_promo_credit + $credit)
                                    </td>
                                    <td class="my_rupiah">
                                        {{$transfer}}
                                        @php($total_promo_transfer = $total_promo_transfer + $transfer)
                                    </td>
                                    <td class="my_rupiah">
                                        {{$qr}}
                                        @php($total_promo_qr = $total_promo_qr + $qr)
                                    </td>
                                    <td class="my_rupiah">
                                        {{$indodana}}
                                        @php($total_promo_indodana = $total_promo_indodana + $indodana)
                                    </td>
                                    <td class="my_rupiah">
                                        {{$shopeepay}}
                                        @php($total_promo_shopeepay = $total_promo_shopeepay + $shopeepay)
                                    </td>
                                    <td class="my_rupiah">
                                        {{$cash}}
                                        @php($total_promo_cash = $total_promo_cash + $cash)
                                    </td>
                                    <td class="my_rupiah">
                                        {{$promo->amount_due - $promo->amount_paid}}
                                    </td>
                                    <td>{{$promo->gmpayment_gmmember->gmmember_guest->fullname}}</td>
                                    <td>-</td>
                                </tr>
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        {{--Renewal Added 20022020--}}
        <div class="row">
            <div class="col-md-12">
                <h4>@lang('labels.renewal')</h4>
                <div class="table-responsive" id="responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>@lang('labels.membership_type')</th>
                            <th>@lang('labels.running_total')</th>
                            <th>@lang('labels.bank_card')</th>
                            <th>@lang('labels.credit_card')</th>
                            <th>@lang('labels.transfer')</th>
                            <th>@lang('labels.qrpayment')</th>
                            <th>@lang('labels.indodana')</th>
                            <th>@lang('labels.shopeepay')</th>
                            <th>@lang('labels.cash')</th>
                            <th>@lang('labels.money_owed')</th>
                            <th>@lang('labels.name')</th>
                            <th>@lang('labels.membership_number')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php($total_renewal_cash = 0)
                        @php($total_renewal_transfer = 0)
                        @php($total_renewal_qr = 0)
                        @php($total_renewal_indodana = 0)
                        @php($total_renewal_shopeepay = 0)
                        @php($total_renewal_debet = 0)
                        @php($total_renewal_credit = 0)
                        @php($total_renewal_owed = 0)

                        @if(count($member_payments) > 0)
                            @foreach($member_payments->where('renewal', true) as $member_payment)
                                @php($cash = 0)
                                @php($transfer = 0)
                                @php($qr = 0)
                                @php($indodana = 0)
                                @php($shopeepay = 0)
                                @php($debet = 0)
                                @php($credit = 0)
                                @php($no_method = 0)
                                @php($debt = 0)
                                {{--@if($member_payment->payment_category == 'First')--}}
                                    <tr>
                                        @if(!empty($member_payment->payment_method_id))
                                            @if($member_payment->payment_method_id == 1)
                                                @php($cash = $member_payment->amount_paid)
                                            @elseif($member_payment->payment_method_id == 2)
                                                @php($debet = $member_payment->amount_paid)
                                            @elseif($member_payment->payment_method_id == 3)
                                                @php($credit = $member_payment->amount_paid)
                                            @elseif($member_payment->payment_method_id == 4)
                                                @php($transfer = $member_payment->amount_paid)
                                            @elseif($member_payment->payment_method_id == 5)
                                                @php($qr = $member_payment->amount_paid)
                                            @elseif($member_payment->payment_method_id == 6)
                                                @php($indodana = $member_payment->amount_paid)
                                            @elseif($member_payment->payment_method_id == 7)
                                                @php($shopeepay = $member_payment->amount_paid)
                                            @endif
                                        @else
                                            @php($no_method = $member_payment->amount_paid)
                                        @endif

                                        @if(!empty($member_payment->memberpayment_memberdebtpayment))
                                            @foreach($member_payment->memberpayment_memberdebtpayment as $debt_payment)
                                                @if(\Carbon\Carbon::parse($debt_payment->payment_date) < \Carbon\Carbon::parse($date))
                                                    @php($debt = $debt + $debt_payment->amount_paid)
                                                @endif
                                            @endforeach
                                        @endif
                                        @php($running_total = $running_total + $member_payment->amount_paid - $debt)

                                        <td>{{$member_payment->membership_type_name}}</td>
                                        <td class="my_rupiah">{{$running_total}}</td>
                                        <td class="my_rupiah">
                                            {{$debet - $debt}}
                                            @php($total_renewal_debet = $total_renewal_debet + $debet - $debt)
                                        </td>
                                        <td class="my_rupiah">
                                            {{$credit - $debt}}
                                            @php($total_renewal_credit = $total_renewal_credit + $credit - $debt)
                                        </td>
                                        <td class="my_rupiah">
                                            {{$transfer - $debt}}
                                            @php($total_renewal_transfer = $total_renewal_transfer + $transfer - $debt)
                                        </td>
                                        <td class="my_rupiah">
                                            {{$qr - $debt}}
                                            @php($total_renewal_qr = $total_renewal_qr + $qr - $debt)
                                        </td>
                                        <td class="my_rupiah">
                                            {{$indodana - $debt}}
                                            @php($total_renewal_indodana = $total_renewal_indodana + $indodana - $debt)
                                        </td>
                                        <td class="my_rupiah">
                                            {{$shopeepay - $debt}}
                                            @php($total_renewal_shopeepay = $total_renewal_shopeepay + $shopeepay - $debt)
                                        </td>
                                        <td class="my_rupiah">
                                            {{$cash - $debt}}
                                            @php($total_renewal_cash = $total_renewal_cash + $cash - $debt)
                                        </td>
                                        <td class="my_rupiah">
                                            @if(!empty($member_payment->money_owed))
                                                {{$member_payment->money_owed}}
                                                @php($total_renewal_owed = $total_renewal_owed + $member_payment->money_owed)
                                            @else
                                                0
                                            @endif
                                        </td>
                                        <td>{{$member_payment->memberpayment_user->name}}</td>
                                        <td>{{$member_payment->memberpayment_user->user_memberpersonal->key_tag_id}}</td>
                                    </tr>
                                {{--@endif--}}
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{--Conversion Added 24022020--}}
        <div class="row">
            <div class="col-md-12">
                <h4>@lang('labels.membership_conversion')</h4>
                <div class="table-responsive" id="responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>@lang('labels.membership_type')</th>
                            <th>@lang('labels.running_total')</th>
                            <th>@lang('labels.bank_card')</th>
                            <th>@lang('labels.credit_card')</th>
                            <th>@lang('labels.transfer')</th>
                            <th>@lang('labels.qrpayment')</th>
                            <th>@lang('labels.indodana')</th>
                            <th>@lang('labels.shopeepay')</th>
                            <th>@lang('labels.cash')</th>
                            <th>@lang('labels.money_owed')</th>
                            <th>@lang('labels.name')</th>
                            <th>@lang('labels.membership_number')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php($total_conversion_cash = 0)
                        @php($total_conversion_transfer = 0)
                        @php($total_conversion_qr = 0)
                        @php($total_conversion_indodana = 0)
                        @php($total_conversion_shopeepay = 0)
                        @php($total_conversion_debet = 0)
                        @php($total_conversion_credit = 0)
                        @php($total_conversion_owed = 0)
                        @php($running_total = 0)

                        @if(count($member_payments) > 0)
                            @foreach($member_payments->where('conversion', true) as $member_payment)
                                @php($cash = 0)
                                @php($transfer = 0)
                                @php($qr = 0)
                                @php($indodana = 0)
                                @php($shopeepay = 0)
                                @php($debet = 0)
                                @php($credit = 0)
                                @php($no_method = 0)
                                @php($debt = 0)
                                @if($member_payment->payment_category == 'First')
                                    <tr>
                                        @if(!empty($member_payment->payment_method_id))
                                            @if($member_payment->payment_method_id == 1)
                                                @php($cash = $member_payment->amount_paid)
                                            @elseif($member_payment->payment_method_id == 2)
                                                @php($debet = $member_payment->amount_paid)
                                            @elseif($member_payment->payment_method_id == 3)
                                                @php($credit = $member_payment->amount_paid)
                                            @elseif($member_payment->payment_method_id == 4)
                                                @php($transfer = $member_payment->amount_paid)
                                            @elseif($member_payment->payment_method_id == 5)
                                                @php($qr = $member_payment->amount_paid)
                                            @elseif($member_payment->payment_method_id == 6)
                                                @php($indodana = $member_payment->amount_paid)
                                            @elseif($member_payment->payment_method_id == 7)
                                                @php($shopeepay = $member_payment->amount_paid)
                                            @endif
                                        @else
                                            @php($no_method = $member_payment->amount_paid)
                                        @endif

                                        @if(!empty($member_payment->memberpayment_memberdebtpayment))
                                            @foreach($member_payment->memberpayment_memberdebtpayment as $debt_payment)
                                                @if(\Carbon\Carbon::parse($debt_payment->payment_date) < \Carbon\Carbon::parse($date))
                                                    @php($debt = $debt + $debt_payment->amount_paid)
                                                @endif
                                            @endforeach
                                        @endif
                                        @php($running_total = $running_total + $member_payment->amount_paid - $debt)

                                        <td>{{$member_payment->membership_type_name}}</td>
                                        <td class="my_rupiah">{{$running_total}}</td>
                                        <td class="my_rupiah">
                                            {{$debet - $debt}}
                                            @php($total_conversion_debet = $total_conversion_debet + $debet - $debt)
                                        </td>
                                        <td class="my_rupiah">
                                            {{$credit - $debt}}
                                            @php($total_conversion_credit = $total_conversion_credit + $credit - $debt)
                                        </td>
                                        <td class="my_rupiah">
                                            {{$transfer - $debt}}
                                            @php($total_conversion_transfer = $total_conversion_transfer + $transfer - $debt)
                                        </td>
                                        <td class="my_rupiah">
                                            {{$qr - $debt}}
                                            @php($total_conversion_qr = $total_conversion_qr + $qr - $debt)
                                        </td>
                                        <td class="my_rupiah">
                                            {{$indodana - $debt}}
                                            @php($total_conversion_indodana = $total_conversion_indodana + $indodana - $debt)
                                        </td>
                                        <td class="my_rupiah">
                                            {{$shopeepay - $debt}}
                                            @php($total_conversion_shopeepay = $total_conversion_shopeepay + $shopeepay - $debt)
                                        </td>
                                        <td class="my_rupiah">
                                            {{$cash - $debt}}
                                            @php($total_conversion_cash = $total_conversion_cash + $cash - $debt)
                                        </td>
                                        <td class="my_rupiah">
                                            @if(!empty($member_payment->money_owed))
                                                {{$member_payment->money_owed}}
                                                @php($total_conversion_owed = $total_conversion_owed + $member_payment->money_owed)
                                            @else
                                                0
                                            @endif
                                        </td>
                                        <td>{{$member_payment->memberpayment_user->name}}</td>
                                        <td>{{$member_payment->memberpayment_user->user_memberpersonal->key_tag_id}}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- Monthly and Autodebet --}}
        <div class="row">
            <div class="col-md-12">
                <h4>@lang('labels.due_and_draft')</h4>
                <div class="table-responsive" id="responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>@lang('labels.membership_type')</th>
                            <th>@lang('labels.running_total')</th>
                            <th>@lang('labels.bank_card')</th>
                            <th>@lang('labels.credit_card')</th>
                            <th>@lang('labels.transfer')</th>
                            <th>@lang('labels.qrpayment')</th>
                            <th>@lang('labels.indodana')</th>
                            <th>@lang('labels.shopeepay')</th>
                            <th>@lang('labels.cash')</th>
                            <th>@lang('labels.money_owed')</th>
                            <th>@lang('labels.name')</th>
                            <th>@lang('labels.membership_number')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php($total_dnd_cash = 0)
                        @php($total_dnd_transfer = 0)
                        @php($total_dnd_qr = 0)
                        @php($total_dnd_indodana = 0)
                        @php($total_dnd_shopeepay = 0)
                        @php($total_dnd_debet = 0)
                        @php($total_dnd_credit = 0)
                        @php($total_dnd_owed = 0)
                        @if(count($member_payments) > 0)
                            @foreach($member_payments as $member_payment)
                                @php($cash = 0)
                                @php($transfer = 0)
                                @php($qr = 0)
                                @php($indodana = 0)
                                @php($shopeepay = 0)
                                @php($debet = 0)
                                @php($credit = 0)
                                @php($no_method = 0)
                                @if(($member_payment->payment_category == 'Monthly') && ($member_payment->payment_status != 'Unpaid'))
                                    <tr>
                                        @if(!empty($member_payment->payment_method_id))
                                            @if($member_payment->payment_method_id == 1)
                                                @php($cash = $member_payment->amount_paid)
                                            @elseif($member_payment->payment_method_id == 2)
                                                @php($debet = $member_payment->amount_paid)
                                            @elseif($member_payment->payment_method_id == 3)
                                                @php($credit = $member_payment->amount_paid)
                                            @elseif($member_payment->payment_method_id == 4)
                                                @php($transfer = $member_payment->amount_paid)
                                            @elseif($member_payment->payment_method_id == 5)
                                                @php($qr = $member_payment->amount_paid)
                                            @elseif($member_payment->payment_method_id == 6)
                                                @php($indodana = $member_payment->amount_paid)
                                            @elseif($member_payment->payment_method_id == 7)
                                                @php($shopeepay = $member_payment->amount_paid)
                                            @endif
                                        @else
                                            @php($cash = $member_payment->amount_paid)
                                        @endif
                                        @php($running_total = $running_total + $member_payment->amount_paid)

                                        <td>{{$member_payment->membership_type_name}}</td>
                                        <td class="my_rupiah">{{$running_total}}</td>
                                        <td class="my_rupiah">
                                            {{$debet}}
                                            @php($total_dnd_debet = $total_dnd_debet + $debet)
                                        </td>
                                        <td class="my_rupiah">
                                            {{$credit}}
                                            @php($total_dnd_credit = $total_dnd_credit + $credit)
                                        </td>
                                        <td class="my_rupiah">
                                            {{$transfer}}
                                            @php($total_dnd_transfer = $total_dnd_transfer + $transfer)
                                        </td>
                                        <td class="my_rupiah">
                                            {{$qr}}
                                            @php($total_dnd_qr = $total_dnd_qr + $qr)
                                        </td>
                                        <td class="my_rupiah">
                                            {{$indodana}}
                                            @php($total_dnd_indodana = $total_dnd_indodana + $indodana)
                                        </td>
                                        <td class="my_rupiah">
                                            {{$shopeepay}}
                                            @php($total_dnd_shopeepay = $total_dnd_shopeepay + $shopeepay)
                                        </td>
                                        <td class="my_rupiah">
                                            {{$cash}}
                                            @php($total_dnd_cash = $total_dnd_cash + $cash)
                                        </td>
                                        <td class="my_rupiah">
                                            @if(!empty($member_payment->total_money_owed))
                                                <?php
                                                    if(isset($owed_by_member_first[$member_payment->user_id])){
                                                        // TODO
                                                        unset($owed_by_member_first[$member_payment->user_id]);
                                                        $owed_by_member_monthly[$member_payment->user_id] = $member_payment->total_money_owed;
                                                    }else if(isset($owed_by_member_monthly[$member_payment->user_id])){
                                                        if($member_payment->total_money_owed < $owed_by_member_monthly[$member_payment->user_id])
                                                            $owed_by_member_monthly[$member_payment->user_id] = $member_payment->total_money_owed;
                                                    }else{
                                                        $owed_by_member_monthly[$member_payment->user_id] = $member_payment->total_money_owed;
                                                    }
                                                ?>
                                                {{$member_payment->total_money_owed}}
                                                @php($total_dnd_owed = $total_dnd_owed + $member_payment->total_money_owed)
                                            @else
                                                0
                                            @endif
                                        </td>
                                        <td>{{$member_payment->memberpayment_user->name}}</td>
                                        <td>{{$member_payment->memberpayment_user->user_memberpersonal->key_tag_id}}</td>
                                    </tr>
                                @endif
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{--Modified 20022020--}}
        <div class="row">
            <div class="col-md-12">
                <h4>@lang('labels.product_sales')</h4>
                <div class="table-responsive" id="responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>@lang('labels.product_type')</th>
                            <th>@lang('labels.running_total')</th>
                            <th>@lang('labels.bank_card')</th>
                            <th>@lang('labels.credit_card')</th>
                            <th>@lang('labels.transfer')</th>
                            <th>@lang('labels.qrpayment')</th>
                            <th>@lang('labels.indodana')</th>
                            <th>@lang('labels.shopeepay')</th>
                            <th>@lang('labels.cash')</th>
                            <th>@lang('labels.money_owed')</th>
                            <th>@lang('labels.name')</th>
                            <th>@lang('labels.membership_number')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php($total_product_cash = 0)
                        @php($total_product_transfer = 0)
                        @php($total_product_qr = 0)
                        @php($total_product_indodana = 0)
                        @php($total_product_shopeepay = 0)
                        @php($total_product_debet = 0)
                        @php($total_product_credit = 0)
                        @php($total_product_owed = 0)
                        <!-- @php($running_total = 0) //ACTIVATE TO SEE CORRECT COMPUTATION -->

                        @if(count($product_payments) > 0)
                            @foreach($product_payments as $product_payment)
                                @foreach($product_payment->productpayment_productpaymentdetail as $product_payment_detail)
                                    {{--Modified by adding IF 20022020--}}
                                    @if($product_payment_detail->productpaymentdetail_product->product_producttype->type != 'Air')
                                        @php($cash = 0)
                                        @php($qr = 0)
                                        @php($indodana = 0)
                                        @php($shopeepay = 0)
                                        @php($transfer = 0)
                                        @php($debet = 0)
                                        @php($credit = 0)
                                        <tr>
                                            @if($product_payment->payment_method_id == 1)
                                                @php($cash = ($product_payment_detail->price * $product_payment_detail->qty) - ($product_payment_detail->discount_value * $product_payment_detail->qty))
                                            @elseif($product_payment->payment_method_id == 2)
                                                @php($debet = ($product_payment_detail->price * $product_payment_detail->qty) - ($product_payment_detail->discount_value * $product_payment_detail->qty))
                                            @elseif($product_payment->payment_method_id == 3)
                                                @php($credit = ($product_payment_detail->price * $product_payment_detail->qty) - ($product_payment_detail->discount_value * $product_payment_detail->qty))
                                            @elseif($product_payment->payment_method_id == 4)
                                                @php($transfer = ($product_payment_detail->price * $product_payment_detail->qty) - ($product_payment_detail->discount_value * $product_payment_detail->qty))
                                            @elseif($product_payment->payment_method_id == 5)
                                                @php($qr = ($product_payment_detail->price * $product_payment_detail->qty) - ($product_payment_detail->discount_value * $product_payment_detail->qty))
                                            @elseif($product_payment->payment_method_id == 6)
                                                @php($indodana = ($product_payment_detail->price * $product_payment_detail->qty) - ($product_payment_detail->discount_value * $product_payment_detail->qty))
                                            @elseif($product_payment->payment_method_id == 7)
                                                @php($shopeepay = ($product_payment_detail->price * $product_payment_detail->qty) - ($product_payment_detail->discount_value * $product_payment_detail->qty))
                                            @endif

                                            <td>{{$product_payment_detail->productpaymentdetail_product->item_name}}</td>

                                            @php($running_total = $running_total + (($product_payment_detail->price * $product_payment_detail->qty) - ($product_payment_detail->discount_value * $product_payment_detail->qty)))

                                            <td class="my_rupiah">{{$running_total}}</td>
                                            <td class="my_rupiah">
                                                {{$debet}}
                                                @php($total_product_debet = $total_product_debet + $debet)
                                            </td>
                                            <td class="my_rupiah">
                                                {{$credit}}
                                                @php($total_product_credit = $total_product_credit + $credit)
                                            </td>
                                            <td class="my_rupiah">
                                                {{$transfer}}
                                                @php($total_product_transfer = $total_product_transfer + $transfer)
                                            </td>
                                            <td class="my_rupiah">
                                                {{$qr}}
                                                @php($total_product_qr = $total_product_qr + $qr)
                                            </td>
                                            <td class="my_rupiah">
                                                {{$indodana}}
                                                @php($total_product_indodana = $total_product_indodana + $indodana)
                                            </td>
                                            <td class="my_rupiah">
                                                {{$shopeepay}}
                                                @php($total_product_shopeepay = $total_product_shopeepay + $shopeepay)
                                            </td>
                                            <td class="my_rupiah">
                                                {{$cash}}
                                                @php($total_product_cash = $total_product_cash + $cash)
                                            </td>
                                            <td class="my_rupiah">0</td>
                                            @if(!empty($product_payment->member_id))
                                                <td>{{$product_payment->productpayment_user->name}}</td>
                                                <td>{{$product_payment->productpayment_user->user_memberpersonal->key_tag_id}}</td>
                                            @else
                                                <td>-</td>
                                                <td>-</td>
                                            @endif
                                        </tr>
                                    @endif
                                @endforeach
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{--Water Sales Added 20022020--}}
        <div class="row">
            <div class="col-md-12">
                <h4>@lang('labels.water_sales')</h4>
                <div class="table-responsive" id="responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>@lang('labels.water_type')</th>
                            <th>@lang('labels.running_total')</th>
                            <th>@lang('labels.bank_card')</th>
                            <th>@lang('labels.credit_card')</th>
                            <th>@lang('labels.transfer')</th>
                            <th>@lang('labels.qrpayment')</th>
                            <th>@lang('labels.indodana')</th>
                            <th>@lang('labels.shopeepay')</th>
                            <th>@lang('labels.cash')</th>
                            <th>@lang('labels.money_owed')</th>
                            <th>@lang('labels.name')</th>
                            <th>@lang('labels.membership_number')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php($total_water_cash = 0)
                        @php($total_water_transfer = 0)
                        @php($total_water_qr = 0)
                        @php($total_water_indodana = 0)
                        @php($total_water_shopeepay = 0)
                        @php($total_water_debet = 0)
                        @php($total_water_credit = 0)
                        @php($total_water_owed = 0)

                        @if(count($product_payments) > 0)
                            @foreach($product_payments as $product_payment)
                                @foreach($product_payment->productpayment_productpaymentdetail as $product_payment_detail)
                                    {{--Modified by adding IF 20022020--}}
                                    @if($product_payment_detail->productpaymentdetail_product->product_producttype->type == 'Air')
                                        @php($cash = 0)
                                        @php($transfer = 0)
                                        @php($qr = 0)
                                        @php($indodana = 0)
                                        @php($shopeepay = 0)
                                        @php($debet = 0)
                                        @php($credit = 0)
                                        <tr>
                                            @if($product_payment->payment_method_id == 1)
                                                @php($cash = ($product_payment_detail->price * $product_payment_detail->qty) - $product_payment_detail->discount_value)
                                            @elseif($product_payment->payment_method_id == 2)
                                                @php($debet = ($product_payment_detail->price * $product_payment_detail->qty) - $product_payment_detail->discount_value)
                                            @elseif($product_payment->payment_method_id == 3)
                                                @php($credit = ($product_payment_detail->price * $product_payment_detail->qty) - $product_payment_detail->discount_value)
                                            @elseif($product_payment->payment_method_id == 4)
                                                @php($transfer = ($product_payment_detail->price * $product_payment_detail->qty) - $product_payment_detail->discount_value)
                                            @elseif($product_payment->payment_method_id == 5)
                                                @php($qr = ($product_payment_detail->price * $product_payment_detail->qty) - $product_payment_detail->discount_value)
                                            @elseif($product_payment->payment_method_id == 6)
                                                @php($indodana = ($product_payment_detail->price * $product_payment_detail->qty) - $product_payment_detail->discount_value)
                                            @elseif($product_payment->payment_method_id == 7)
                                                @php($shopeepay = ($product_payment_detail->price * $product_payment_detail->qty) - $product_payment_detail->discount_value)
                                            @endif

                                            @php($running_total = $running_total + (($product_payment_detail->price * $product_payment_detail->qty) - $product_payment_detail->discount_value))
                                            <td>{{$product_payment_detail->productpaymentdetail_product->item_name}}</td>
                                            <td class="my_rupiah">{{$running_total}}</td>
                                            <td class="my_rupiah">
                                                {{$debet}}
                                                @php($total_water_debet = $total_water_debet + $debet)
                                            </td>
                                            <td class="my_rupiah">
                                                {{$credit}}
                                                @php($total_water_credit = $total_water_credit + $credit)
                                            </td>
                                            <td class="my_rupiah">
                                                {{$transfer}}
                                                @php($total_water_transfer = $total_water_transfer + $transfer)
                                            </td>
                                            <td class="my_rupiah">
                                                {{$qr}}
                                                @php($total_water_qr = $total_water_qr + $qr)
                                            </td>
                                            <td class="my_rupiah">
                                                {{$indodana}}
                                                @php($total_water_indodana = $total_water_indodana + $indodana)
                                            </td>
                                            <td class="my_rupiah">
                                                {{$shopeepay}}
                                                @php($total_water_shopeepay = $total_water_shopeepay + $shopeepay)
                                            </td>
                                            <td class="my_rupiah">
                                                {{$cash}}
                                                @php($total_water_cash = $total_water_cash + $cash)
                                            </td>
                                            <td class="my_rupiah">0</td>
                                            @if(!empty($product_payment->member_id))
                                                <td>{{$product_payment->productpayment_user->name}}</td>
                                                <td>{{$product_payment->productpayment_user->user_memberpersonal->key_tag_id}}</td>
                                            @else
                                                <td>-</td>
                                                <td>-</td>
                                            @endif
                                        </tr>
                                    @endif
                                @endforeach
                            @endforeach
                        @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{--Modified 20022020--}}
        <div class="row">
            <div class="col-md-12">
                <h4>@lang('labels.locker_sales')</h4>
                <div class="table-responsive" id="responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>@lang('labels.locker_type')</th>
                            <th>@lang('labels.running_total')</th>
                            <th>@lang('labels.bank_card')</th>
                            <th>@lang('labels.credit_card')</th>
                            <th>@lang('labels.transfer')</th>
                            <th>@lang('labels.qrpayment')</th>
                            <th>@lang('labels.indodana')</th>
                            <th>@lang('labels.shopeepay')</th>
                            <th>@lang('labels.cash')</th>
                            <th>@lang('labels.money_owed')</th>
                            <th>@lang('labels.name')</th>
                            <th>@lang('labels.membership_number')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php($total_locker_cash = 0)
                        @php($total_locker_transfer = 0)
                        @php($total_locker_qr = 0)
                        @php($total_locker_indodana = 0)
                        @php($total_locker_shopeepay = 0)
                        @php($total_locker_debet = 0)
                        @php($total_locker_credit = 0)
                        @php($total_locker_owed = 0)

                        @if(count($locker_payments) > 0)
                            @foreach($locker_payments as $locker_payment)
                                @php($cash = 0)
                                @php($transfer = 0)
                                @php($qr = 0)
                                @php($indodana = 0)
                                @php($shopeepay = 0)
                                @php($debet = 0)
                                @php($credit = 0)
                                <tr>
                                    @if($locker_payment->paymentmethod_id == 1)
                                        @php($cash = $locker_payment->pay)
                                    @elseif($locker_payment->paymentmethod_id == 2)
                                        @php($debet = $locker_payment->pay)
                                    @elseif($locker_payment->paymentmethod_id == 3)
                                        @php($credit = $locker_payment->pay)
                                    @elseif($locker_payment->paymentmethod_id == 4)
                                        @php($transfer = $locker_payment->pay)
                                    @elseif($locker_payment->paymentmethod_id == 5)
                                        @php($qr = $locker_payment->pay)
                                    @elseif($locker_payment->paymentmethod_id == 6)
                                        @php($indodana = $locker_payment->pay)
                                    @elseif($locker_payment->paymentmethod_id == 7)
                                        @php($shopeepay = $locker_payment->pay)
                                    @endif

                                    @php($running_total = $running_total + $locker_payment->pay)
                                    <td>@lang('labels.locker')</td>
                                    <td class="my_rupiah">{{$running_total}}</td>
                                    <td class="my_rupiah">
                                        {{$debet}}
                                        @php($total_locker_debet = $total_locker_debet + $debet)
                                    </td>
                                    <td class="my_rupiah">
                                        {{$credit}}
                                        @php($total_locker_credit = $total_locker_credit + $credit)
                                    </td>
                                    <td class="my_rupiah">
                                        {{$transfer}}
                                        @php($total_locker_transfer = $total_locker_transfer + $transfer)
                                    </td>
                                    <td class="my_rupiah">
                                        {{$qr}}
                                        @php($total_locker_qr = $total_locker_qr + $qr)
                                    </td>
                                    <td class="my_rupiah">
                                        {{$indodana}}
                                        @php($total_locker_indodana = $total_locker_indodana + $indodana)
                                    </td>
                                    <td class="my_rupiah">
                                        {{$shopeepay}}
                                        @php($total_locker_shopeepay = $total_locker_shopeepay + $shopeepay)
                                    </td>
                                    <td class="my_rupiah">
                                        {{$cash}}
                                        @php($total_locker_cash = $total_locker_cash + $cash)
                                    </td>
                                    <td class="my_rupiah">
                                        {{($locker_payment->price * $locker_payment->quantity) - $locker_payment->pay}}
                                        @php($total_locker_owed = $total_locker_owed + ($locker_payment->price * $locker_payment->quantity) - $locker_payment->pay)
                                    </td>
                                    @if(!empty($locker_payment->lockerpayment_memberlocker))
                                        <td>{{$locker_payment->lockerpayment_memberlocker->memberlocker_user->name}}</td>
                                        <td>{{$locker_payment->lockerpayment_memberlocker->memberlocker_user->user_memberpersonal->key_tag_id}}</td>
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

        @if(count($member_deposits) > 0)
            <div class="row">
                <div class="col-md-12">
                    <h4>@lang('labels.balance_correction')</h4>
                    <div class="table-responsive" id="responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>@lang('labels.payment_number')</th>
                                <th>@lang('labels.running_total')</th>
                                <th>@lang('labels.bank_card')</th>
                                <th>@lang('labels.credit_card')</th>
                                <th>@lang('labels.transfer')</th>
                                <th>@lang('labels.qrpayment')</th>
                                <th>@lang('labels.indodana')</th>
                                <th>@lang('labels.shopeepay')</th>
                                <th>@lang('labels.cash')</th>
                                <th>@lang('labels.money_owed')</th>
                                <th>@lang('labels.name')</th>
                                <th>@lang('labels.membership_number')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php($total_deposit_cash = 0)
                            @php($total_deposit_transfer = 0)
                            @php($total_deposit_qr = 0)
                            @php($total_deposit_indodana = 0)
                            @php($total_deposit_shopeepay = 0)
                            @php($total_deposit_debet = 0)
                            @php($total_deposit_credit = 0)
                            @php($total_deposit_owed = 0)

                            @foreach($member_deposits as $member_deposit)
                                @php($cash = 0)
                                @php($transfer = 0)
                                @php($qr = 0)
                                @php($indodana = 0)
                                @php($shopeepay = 0)
                                @php($debet = 0)
                                @php($credit = 0)
                                @php($no_method = 0)
                                @php($debt = 0)
                                <tr>
                                    @php($running_total = $running_total + $member_deposit->debet)
                                    <td>
                                        @php($payment_number = explode('/', $member_deposit->payment_number, 2))
                                        {{$payment_number[1]}}
                                    </td>
                                    <td class="my_rupiah">{{$running_total}}</td>
                                    <td class="my_rupiah">0</td>
                                    <td class="my_rupiah">0</td>
                                    <td class="my_rupiah">0</td>
                                    <td class="my_rupiah">0</td>
                                    <td class="my_rupiah">0</td>
                                    <td class="my_rupiah">0</td>
                                    <td class="my_rupiah">
                                        {{$member_deposit->debet}}
                                        @php($total_deposit_cash = $total_deposit_cash + $member_deposit->debet)
                                    </td>
                                    <td class="my_rupiah">0</td>
                                    <td>{{$member_deposit->memberdeposit_user->name}}</td>
                                    <td>{{$member_deposit->memberdeposit_user->user_memberpersonal->key_tag_id}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        @endif

        @php($total_bc = 0)
        @php($total_cc = 0)
        @php($total_trf = 0)
        @php($total_qr = 0)
        @php($total_indodana = 0)
        @php($total_shopeepay = 0)
        @php($total_cash = 0)
        @php($total_owed = 0)
        <div class="row">
            <div class="col-md-12">
                <h4>@lang('labels.today_sales')</h4>
                <div class="table-responsive" id="responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th></th>
                            <th>@lang('labels.bank_card')</th>
                            <th>@lang('labels.credit_card')</th>
                            <th>@lang('labels.transfer')</th>
                            <th>@lang('labels.qrpayment')</th>
                            <th>@lang('labels.indodana')</th>
                            <th>@lang('labels.shopeepay')</th>
                            <th>@lang('labels.cash')</th>
                            <th>@lang('labels.money_owed')</th>
                            <th>@lang('labels.total')</th>
                        </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>@lang('labels.new_sales_for_today')</td>
                                <td class="my_rupiah">
                                    {{$total_daily_debet}}
                                    @php($total_bc = $total_bc + $total_daily_debet)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_daily_credit}}
                                    @php($total_cc = $total_cc + $total_daily_credit)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_daily_transfer}}
                                    @php($total_trf = $total_trf + $total_daily_transfer)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_daily_qr}}
                                    @php($total_qr = $total_qr + $total_daily_qr)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_daily_indodana}}
                                    @php($total_indodana = $total_indodana + $total_daily_indodana)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_daily_shopeepay}}
                                    @php($total_shopeepay = $total_shopeepay + $total_daily_shopeepay)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_daily_cash}}
                                    @php($total_cash = $total_cash + $total_daily_cash)
                                </td>
                                <td class="my_rupiah">
                                    {{-- {{$total_daily_owed}} --}}
                                    {{ array_sum($owed_by_member_first) }}
                                    {{-- @php($total_owed = $total_owed + $total_daily_owed) --}}
                                    @php($total_owed = $total_owed + array_sum($owed_by_member_first))
                                </td>
                                <td class="my_rupiah">{{$total_daily_debet + $total_daily_credit + $total_daily_transfer + $total_daily_cash + $total_daily_owed}}</td>
                            </tr>

                            <!-- Promo Sale -->
                            <tr>
                                <td>@lang('labels.new_promo_sales_for_today')</td>
                                <td class="my_rupiah">
                                    {{$total_promo_debet}}
                                    @php($total_bc = $total_bc + $total_promo_debet)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_promo_credit}}
                                    @php($total_cc = $total_cc + $total_promo_credit)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_promo_transfer}}
                                    @php($total_trf = $total_trf + $total_promo_transfer)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_promo_qr}}
                                    @php($total_qr = $total_qr + $total_promo_qr)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_promo_indodana}}
                                    @php($total_indodana = $total_indodana + $total_promo_indodana)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_promo_shopeepay}}
                                    @php($total_shopeepay = $total_shopeepay + $total_promo_shopeepay)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_promo_cash}}
                                    @php($total_cash = $total_cash + $total_promo_cash)
                                </td>
                                <td class="my_rupiah">
                                    {{-- {{$total_daily_owed}} --}}
                                    {{ array_sum($owed_by_member_first) }}
                                    {{-- @php($total_owed = $total_owed + $total_daily_owed) --}}
                                    @php($total_owed = $total_owed + array_sum($owed_by_member_first))
                                </td>
                                <td class="my_rupiah">{{$total_promo_debet + $total_promo_credit + $total_promo_transfer + $total_promo_cash + $total_promo_owed}}</td>
                            </tr>

                            {{--Renewal Added 20022020--}}
                            <tr>
                                <td>@lang('labels.renewal')</td>
                                <td class="my_rupiah">
                                    {{$total_renewal_debet}}
                                    @php($total_bc = $total_bc + $total_renewal_debet)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_renewal_credit}}
                                    @php($total_cc = $total_cc + $total_renewal_credit)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_renewal_transfer}}
                                    @php($total_trf = $total_trf + $total_renewal_transfer)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_renewal_qr}}
                                    @php($total_qr = $total_qr + $total_renewal_qr)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_renewal_indodana}}
                                    @php($total_indodana = $total_indodana + $total_renewal_indodana)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_renewal_shopeepay}}
                                    @php($total_shopeepay = $total_shopeepay + $total_renewal_shopeepay)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_renewal_cash}}
                                    @php($total_cash = $total_cash + $total_renewal_cash)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_renewal_owed}}
                                    @php($total_owed = $total_owed + $total_renewal_owed)
                                </td>
                                <td class="my_rupiah">{{$total_renewal_debet + $total_renewal_credit + $total_renewal_transfer + $total_renewal_cash + $total_renewal_owed}}</td>
                            </tr>

                            {{--Conversion Added 24022020--}}
                            <tr>
                                <td>@lang('labels.membership_conversion')</td>
                                <td class="my_rupiah">
                                    {{$total_conversion_debet}}
                                    @php($total_bc = $total_bc + $total_conversion_debet)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_conversion_credit}}
                                    @php($total_cc = $total_cc + $total_conversion_credit)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_conversion_transfer}}
                                    @php($total_trf = $total_trf + $total_conversion_transfer)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_conversion_qr}}
                                    @php($total_qr = $total_qr + $total_conversion_qr)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_conversion_indodana}}
                                    @php($total_indodana = $total_indodana + $total_conversion_indodana)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_conversion_shopeepay}}
                                    @php($total_shopeepay = $total_shopeepay + $total_conversion_shopeepay)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_conversion_cash}}
                                    @php($total_cash = $total_cash + $total_conversion_cash)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_conversion_owed}}
                                    @php($total_owed = $total_owed + $total_conversion_owed)
                                </td>
                                <td class="my_rupiah">{{$total_conversion_debet + $total_conversion_credit + $total_conversion_transfer + $total_conversion_cash + $total_conversion_owed}}</td>
                            </tr>
                            {{-- Monthly --}}
                            <tr>
                                <td>@lang('labels.due_and_draft')</td>
                                <td class="my_rupiah">
                                    {{$total_dnd_debet}}
                                    @php($total_bc = $total_bc + $total_dnd_debet)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_dnd_credit}}
                                    @php($total_cc = $total_cc + $total_dnd_credit)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_dnd_transfer}}
                                    @php($total_trf = $total_trf + $total_dnd_transfer)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_dnd_qr}}
                                    @php($total_qr = $total_qr + $total_dnd_qr)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_dnd_indodana}}
                                    @php($total_indodana = $total_indodana + $total_dnd_indodana)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_dnd_shopeepay}}
                                    @php($total_shopeepay = $total_shopeepay + $total_dnd_shopeepay)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_dnd_cash}}
                                    @php($total_cash = $total_cash + $total_dnd_cash)
                                </td>
                                <td class="my_rupiah">
                                    {{-- {{$total_dnd_owed}}
                                    @php($total_owed = $total_owed + $total_dnd_owed) --}}
                                    {{array_sum($owed_by_member_monthly)}}
                                    @php($total_owed = $total_owed + array_sum($owed_by_member_monthly))
                                </td>
                                <td class="my_rupiah">{{$total_dnd_debet + $total_dnd_credit + $total_dnd_transfer + $total_dnd_cash + $total_dnd_owed}}</td>
                            </tr>
                            {{-- Loker --}}
                            <tr>
                                <td>@lang('labels.locker')</td>
                                <td class="my_rupiah">
                                    {{$total_locker_debet}}
                                    @php($total_bc = $total_bc + $total_locker_debet)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_locker_credit}}
                                    @php($total_cc = $total_cc + $total_locker_credit)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_locker_transfer}}
                                    @php($total_trf = $total_trf + $total_locker_transfer)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_locker_qr}}
                                    @php($total_qr = $total_qr + $total_locker_qr)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_locker_indodana}}
                                    @php($total_indodana = $total_indodana + $total_locker_indodana)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_locker_shopeepay}}
                                    @php($total_shopeepay = $total_shopeepay + $total_locker_shopeepay)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_locker_cash}}
                                    @php($total_cash = $total_cash + $total_locker_cash)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_locker_owed}}
                                    @php($total_owed = $total_owed + $total_locker_owed)
                                </td>
                                <td class="my_rupiah">{{$total_locker_debet + $total_locker_credit + $total_locker_transfer + $total_locker_cash + $total_locker_owed}}</td>
                            </tr>

                            {{-- Product --}}
                            <tr>
                                <td>@lang('labels.product_sales')</td>
                                <td class="my_rupiah">
                                    {{$total_product_debet}}
                                    @php($total_bc = $total_bc + $total_product_debet)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_product_credit}}
                                    @php($total_cc = $total_cc + $total_product_credit)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_product_transfer}}
                                    @php($total_trf = $total_trf + $total_product_transfer)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_product_qr}}
                                    @php($total_qr = $total_qr + $total_product_qr)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_product_indodana}}
                                    @php($total_indodana = $total_indodana + $total_product_indodana)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_product_shopeepay}}
                                    @php($total_shopeepay = $total_shopeepay + $total_product_shopeepay)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_product_cash}}
                                    @php($total_cash = $total_cash + $total_product_cash)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_product_owed}}
                                    @php($total_owed = $total_owed + $total_product_owed)
                                </td>
                                <td class="my_rupiah">{{$total_product_debet + $total_product_credit + $total_product_transfer + $total_product_cash + $total_product_owed}}</td>
                            </tr>

                            {{--Water Added 20022020--}}
                            <tr>
                                <td>@lang('labels.water_sales')</td>
                                <td class="my_rupiah">
                                    {{$total_water_debet}}
                                    @php($total_bc = $total_bc + $total_water_debet)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_water_credit}}
                                    @php($total_cc = $total_cc + $total_water_credit)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_water_transfer}}
                                    @php($total_trf = $total_trf + $total_water_transfer)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_water_qr}}
                                    @php($total_qr = $total_qr + $total_water_qr)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_water_indodana}}
                                    @php($total_indodana = $total_indodana + $total_water_indodana)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_water_shopeepay}}
                                    @php($total_shopeepay = $total_shopeepay + $total_water_shopeepay)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_water_cash}}
                                    @php($total_cash = $total_cash + $total_water_cash)
                                </td>
                                <td class="my_rupiah">
                                    {{$total_water_owed}}
                                    @php($total_owed = $total_owed + $total_water_owed)
                                </td>
                                <td class="my_rupiah">{{$total_water_debet + $total_water_credit + $total_water_transfer + $total_water_cash + $total_water_owed}}</td>
                            </tr>

                            @if(count($member_deposits) > 0)
                                <tr>
                                    <td>@lang('labels.balance_correction')</td>
                                    <td class="my_rupiah">
                                        {{$total_deposit_debet}}
                                        @php($total_bc = $total_bc + $total_deposit_debet)
                                    </td>
                                    <td class="my_rupiah">
                                        {{$total_deposit_credit}}
                                        @php($total_cc = $total_cc + $total_deposit_credit)
                                    </td>
                                    <td class="my_rupiah">
                                        {{$total_deposit_transfer}}
                                        @php($total_trf = $total_trf + $total_deposit_transfer)
                                    </td>
                                    <td class="my_rupiah">
                                        {{$total_deposit_qr}}
                                        @php($total_qr = $total_qr + $total_deposit_qr)
                                    </td>
                                    <td class="my_rupiah">
                                        {{$total_deposit_indodana}}
                                        @php($total_indodana = $total_indodana + $total_deposit_indodana)
                                    </td>
                                    <td class="my_rupiah">
                                        {{$total_deposit_shopeepay}}
                                        @php($total_shopeepay = $total_shopeepay + $total_deposit_shopeepay)
                                    </td>
                                    <td class="my_rupiah">
                                        {{$total_deposit_cash}}
                                        @php($total_cash = $total_cash + $total_deposit_cash)
                                    </td>
                                    <td class="my_rupiah">
                                        {{$total_deposit_owed}}
                                        @php($total_owed = $total_owed + $total_deposit_owed)
                                    </td>
                                    <td class="my_rupiah">{{$total_deposit_debet + $total_deposit_credit + $total_deposit_transfer + $total_deposit_cash + $total_deposit_owed}}</td>
                                </tr>
                            @endif
                            <tr>
                                <td>@lang('labels.total')</td>
                                <td class="my_rupiah">{{$total_bc}}</td>
                                <td class="my_rupiah">{{$total_cc}}</td>
                                <td class="my_rupiah">{{$total_trf}}</td>
                                <td class="my_rupiah">{{$total_qr}}</td>
                                <td class="my_rupiah">{{$total_indodana}}</td>
                                <td class="my_rupiah">{{$total_shopeepay}}</td>
                                <td class="my_rupiah">{{$total_cash}}</td>
                                <td class="my_rupiah">{{$total_owed}}</td>
                                <td class="my_rupiah">{{$total_bc + $total_cc + $total_trf  + $total_qr + $total_cash + $total_owed}}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <br>
        <div class="row">
            <div class="col-md-3">
                <p>@lang('labels.total'):</p>
                <p class="my_rupiah">{{$total_bc + $total_cc + $total_trf + $total_qr + $total_indodana + $total_shopeepay + $total_cash + $total_owed}}</p>
            </div>
            <div class="col-md-3">
                <p>@lang('labels.owed'):</p>
                <p class="my_rupiah">{{$total_owed}}</p>
            </div>
            <div class="col-md-6">
                <div class="col-md-6">@lang('labels.total_collected'):</div>
                <div class="col-md-6">
                    <h3 class="my_rupiah">{{$total_bc + $total_cc + $total_trf + $total_qr + $total_indodana + $total_shopeepay + $total_cash}}</h3>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('other-script')
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
        });
    </script>
    <script>
        $(document).ready(function () {
            init_format();

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
        });
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>

@endsection
