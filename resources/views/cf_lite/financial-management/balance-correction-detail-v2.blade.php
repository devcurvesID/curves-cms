@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/payment.css')}}" rel="stylesheet">
    <style>
        .column-no-border, .column-no-border td {
            border: none !important;
        }
        .modal-dialog, .modal-dialog label{
            color: #414042;
        }
        table{
            color: #414042;
        }
        .select2 {
            width: 100% !important;
            float: right;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.error-message')
    @php($member = $member_deposit->memberdeposit_user)
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/financial-management/member/'.$member_deposit->user_id)}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.balance_correction_payment_detail')</h2>
            </center>
        </div>
    </div>

    @if($member_deposit->status == 'Pending')
        {!! Form::open(['method' => 'PATCH', 'action' => ['FinancialManagementController@store_balance_correction', $member_deposit->id], 'id' => 'balance-form']) !!}
    @endif
    <div class="row">
        <div class="col-md-6">
            <table class="member-detail">
                <tr>
                    <td>@lang('labels.payment_number')</td>
                    <td>:</td>
                    <td>
                        @php($payment_number = explode('/', $member_deposit->payment_number, 2))
                        {{$payment_number[1]}}
                    </td>
                </tr>
                <tr>
                    <td>@lang('labels.name')</td>
                    <td>:</td>
                    <td>{{$member->name}}</td>
                </tr>
                <tr>
                    <td>@lang('labels.club')</td>
                    <td>:</td>
                    <td>{{$member->user_club->club_name}}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="member-detail">
                <tr>
                    <td>@lang('labels.payment_date')</td>
                    <td>:</td>
                    <td>
                        @if($member_deposit->status == 'Pending')
                            {!! Form::date('payment_date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                        @elseif($member_deposit->status == 'Done')
                            {{\Carbon\Carbon::parse($member_deposit->payment_date)->format('d-m-Y')}}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>@lang('labels.information')</td>
                    <td>:</td>
                    <td>
                        @if(!empty($member_deposit->memberdeposit_information->information))
                            {{$member_deposit->memberdeposit_information->information}}
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            @if($member_deposit->status == 'Pending')
                {!! Form::hidden('_type', null) !!}
                @if($member_deposit->payment_type == 'Payment')
                    <h3>
                        @lang('labels.bill'): <span class="my_rupiah">
                            @if(!empty($member_deposit->kredit))
                                {{abs($member_deposit->kredit)}}
                                @php($the_amount = $member_deposit->kredit)
                            @else
                                {{abs($member_deposit->saldo)}}
                                @php($the_amount = $member_deposit->saldo)
                            @endif
                        </span>,-
                    </h3>

                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('amount_paid', trans('labels.pay').': ', ['class' => 'control-label col-md-1']) !!}
                            <div class="col-md-3">
                                {!! Form::hidden('_amount', abs($the_amount)) !!}
                                {!! Form::text('amount_paid', abs($the_amount), ['class' => 'form-control rupiah']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-4">
                                {!! Form::button(trans('labels.pay'), ['class' => 'btn btn-success pull-right', 'id' => 'btn-submit-payment']) !!}
                            </div>
                        </div>
                    </div>
                @elseif($member_deposit->payment_type == 'Refund')
                    <h3>
                        @lang('labels.deposit'): <span class="my_rupiah">
                            @if(!empty($member_deposit->debet))
                                {{abs($member_deposit->debet)}}
                                @php($the_amount = $member_deposit->debet)
                            @else
                                {{abs($member_deposit->saldo)}}
                                @php($the_amount = $member_deposit->saldo)
                            @endif
                        </span>,-
                    </h3>

                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('cash_amount', trans('labels.cash'), ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-4">
                                {!! Form::text('cash_amount', abs($the_amount), ['class' => 'form-control rupiah']) !!}
                                {!! Form::hidden('old_cash', abs($the_amount)) !!}
                            </div>
                        </div>
                        <hr>
                        <div class="form-group">
                            <div class="checkbox" style="margin-left: 15px;">
                                <label>{!! Form::checkbox('use_voucher', 'voucher', false, ['id' => 'use_voucher']) !!} @lang('labels.voucher')</label>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('nominal', trans('labels.nominal'), ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-4">
                                {!! Form::text('nominal', '0', ['class' => 'form-control rupiah', 'disabled']) !!}
                                <p class="help-block">*@lang('labels.maximum'): <span class="my_rupiah">{{abs($the_amount)}}</span>,-</p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('valid', trans('labels.valid'), ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                {!! Form::date('from_date', \Carbon\Carbon::today(), ['class' => 'form-control', 'disabled']) !!}
                            </div>
                            {!! Form::label('until', trans('labels.until'), ['class' => 'control-label col-md-1']) !!}
                            <div class="col-md-3">
                                {!! Form::date('thru_date', null, ['class' => 'form-control', 'disabled']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('voucher_code', trans('labels.voucher_code'), ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                {!! Form::text('voucher_code', null, ['class' => 'form-control', 'disabled']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-md-10">
                                {!! Form::button(trans('labels.refund'), ['class' => 'btn btn-success pull-right', 'id' => 'btn-submit-refund']) !!}
                            </div>
                        </div>
                    </div>
                @endif
            @else
                <div class="col-md-8 col-md-offset-2">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th></th>
                                <th>@lang('labels.amount')</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                @if($member_deposit->payment_type == 'Payment')
                                    <td style="text-align: right;">@lang('labels.bill')</td>
                                    <td class="my_rupiah">
                                        @if(!empty($member_deposit->kredit))
                                            {{$member_deposit->kredit}}
                                            @php($the_amount = $member_deposit->kredit)
                                        @else
                                            {{$member_deposit->debet}}
                                            @php($the_amount = $member_deposit->debet)
                                        @endif
                                    </td>
                                    @php($total_amount = -$the_amount)
                                @elseif($member_deposit->payment_type == 'Refund')
                                    <td style="text-align: right;">@lang('labels.deposit')</td>
                                    <td class="my_rupiah">
                                        @if(!empty($member_deposit->debet))
                                            {{$member_deposit->debet}}
                                            @php($the_amount = $member_deposit->debet)
                                        @else
                                            @php($the_amount = $member_deposit->kredit)
                                            {{$member_deposit->kredit}}
                                        @endif
                                    </td>
                                    @php($total_amount = $the_amount)
                                @endif
                            </tr>
                            @if(count($the_balances) > 0)
                                @foreach($the_balances as $the_balance)
                                    <tr>
                                        @if($member_deposit->payment_type == 'Payment')
                                            <td style="text-align: right;"><span class="pull-left">({{\Carbon\Carbon::parse($the_balance->payment_date)->format('d-m-Y')}})</span>@lang('labels.payment')</td>
                                            <td class="my_rupiah">{{$the_balance->debet}}</td>
                                            @php($total_amount = $total_amount + $the_balance->debet)
                                        @elseif($member_deposit->payment_type == 'Refund')
                                            <td style="text-align: right;">
                                                @php($payment_number = explode('/', $the_balance->payment_number, 2))
                                                @if($payment_number[0] == 'cash-manual-debet')
                                                    <span class="pull-left">{{\Carbon\Carbon::parse($the_balance->payment_date)->format('d-m-Y')}}</span> @lang('labels.cash_refund')
                                                @elseif($payment_number[0] == 'voucher-manual-debet')
                                                    <span class="pull-left">{{\Carbon\Carbon::parse($the_balance->payment_date)->format('d-m-Y')}}</span> @lang('labels.voucher_refund')
                                                @endif
                                            </td>
                                            <td class="my_rupiah" style="font-weight: 600;">{{$the_balance->kredit}}</td>
                                            @php($total_amount = $total_amount - $the_balance->kredit)
                                        @endif
                                    </tr>
                                @endforeach
                            @endif
                            <tr>
                                <td style="text-align: right; font-weight: 600;">
                                    @if($member_deposit->payment_type == 'Payment')
                                        @lang('labels.rest_of_the_bill')
                                    @elseif($member_deposit->payment_type == 'Refund')
                                        @lang('labels.balance')
                                    @endif
                                </td>
                                <td class="my_rupiah">{{abs($total_amount)}}</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
    @if($member_deposit->status == 'Pending')
        {!! Form::close() !!}
    @endif
@endsection

@section('other-script')
    <script>
        $(document).ready(function () {
            $('.rupiah').each(function () {
                $(this).wrap('<div class="input-group"></div>')
                $(this).before('<span class="input-group-addon">Rp.</span>');
                $(this).after('<span class="input-group-addon">,-</span>');
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

    <script>
        $(document).ready(function () {
            $('#amount_paid').on('keyup', function () {
                if ($(this).val() == "") {
                    var nominal = 0;
                } else {
                    var nominal = parseInt(rupiah_format_destroyer($(this).val()));
                }

                $(this).val(rupiah_formater(nominal));
            });

            $('#btn-submit-payment').on('click', function () {
                var nominal = parseInt(rupiah_format_destroyer($('#amount_paid').val()));

                $('#amount_paid').val(nominal);
                $('input[name="_type"]').val('Payment');
                $('#balance-form').submit();
            });

            $('#use_voucher').on('click', function () {
                if ($(this).prop('checked') == true) {
                    $('input[name="nominal"]').prop('disabled', false);
                    $('input[name="from_date"]').prop('disabled', false);
                    $('input[name="thru_date"]').prop('disabled', false);
                    $('input[name="voucher_code"]').prop('disabled', false);
                } else {
                    $('input[name="nominal"]').val('0');
                    $('input[name="nominal"]').prop('disabled', true);
                    $('input[name="from_date"]').prop('disabled', true);
                    $('input[name="thru_date"]').prop('disabled', true);
                    $('input[name="voucher_code"]').prop('true', false);
                }
            });

            $('#btn-submit-refund').on('click', function () {
                var cash = parseInt(rupiah_format_destroyer($('#cash_amount').val()));
                var nominal = parseInt(rupiah_format_destroyer($('input[name="nominal"]').val()));
                var old_saldo = parseInt(rupiah_format_destroyer($('input[name="old_cash"]').val()));

                if ((cash + nominal) <= old_saldo) {
                    $('input[name="_type"]').val('Refund');
                    $('#cash_amount').val(cash);
                    $('input[name="nominal"]').val(nominal);
                    $('#balance-form').submit();
                } else {
                    alert('@lang('messages.balance_not_valid')');
                }
            });

            $('input[name="nominal"]').on('keyup', function () {
                if ($(this).val() == "") {
                    var nominal = 0;
                } else {
                    var nominal = parseInt(rupiah_format_destroyer($(this).val()));
                }

                var saldo = parseInt(rupiah_format_destroyer($('input[name="old_cash"]').val()));
                var new_saldo = saldo - nominal;

                $('#cash_amount').val(rupiah_formater(new_saldo));
                $('input[name="nominal"]').val(rupiah_formater(nominal));
            });

            $('#cash_amount').on('keyup', function () {
                if ($(this).val() == "") {
                    var cash = 0;
                } else {
                    var cash = parseInt(rupiah_format_destroyer($(this).val()));
                }

                $(this).val(rupiah_formater(cash));
            });
        });
    </script>
@endsection