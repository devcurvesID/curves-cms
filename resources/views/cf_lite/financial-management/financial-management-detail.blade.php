@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/payment.css')}}" rel="stylesheet">
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <style>
        body{
            color: white;
        }
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
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/cancelling-membership')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.membership_termination_detail')</h2>
            </center>
        </div>
    </div>

    {!! Form::open(['method' => 'PATCH', 'action' => ['FinancialManagementController@process_member_deposit_payment', $member_deposit->id], 'id' => 'member-deposit-form']) !!}
    <div class="row">
        <div class="col-md-6">
            <table class="member-detail">
                <tr>
                    <td>@lang('labels.payment_number')</td>
                    <td>:</td>
                    <td>{{$refund_submission->refund_number}}</td>
                </tr>
                <tr>
                    <td>@lang('labels.name')</td>
                    <td>:</td>
                    <td>{{$member_deposit->memberdeposit_user->name}}</td>
                </tr>
                <tr>
                    <td>@lang('labels.membership_type')</td>
                    <td>:</td>
                    <td>{{$member_deposit->memberdeposit_user->user_memberstatus->memberstatus_membershiptype->membership_type_name}}</td>
                </tr>
                <tr>
                    <td>@lang('labels.start_membership')</td>
                    <td>:</td>
                    <td>
                        {{date('d F Y', strtotime($member_deposit->memberdeposit_user->user_memberstatus->from_date))}}
                        {!! Form::date('start_membership', \Carbon\Carbon::parse($member_deposit->memberdeposit_user->user_memberstatus->from_date), ['style' => 'display: none;', 'readonly']) !!}
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="member-detail">
                <tr>
                    <td>@lang('labels.joined')</td>
                    <td>:</td>
                    <td>{{date('d F Y', strtotime($member_deposit->memberdeposit_user->user_memberpersonal->joined))}}</td>
                </tr>
                <tr>
                    <td>@lang('labels.request_date')</td>
                    <td>:</td>
                    <td>{{\Carbon\Carbon::parse($refund_submission->refund_submission_date)->format('d-m-Y')}}</td>
                </tr>
                <tr>
                    <td>@lang('labels.date')</td>
                    <td>:</td>
                    <td>
                        @if($refund_submission->refund_status == 'Pending')
                            {!! Form::date('date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                        @else
                            {{\Carbon\Carbon::parse($refund_submission->refund_process_date)->format('d-m-Y')}}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>@lang('labels.reason')</td>
                    <td>:</td>
                    <td>
                        @if(!empty($refund_submission->refundsubmission_cancellationreason))
                            <ul>
                                @if(!empty($refund_submission->refundsubmission_cancellationreason->cancellation_reason_id))
                                    <li>{{$refund_submission->refundsubmission_cancellationreason->cancellationreason_nosalereason->nosale_reason}}</li>
                                @endif

                                @if(!empty($refund_submission->refundsubmission_cancellationreason->cancellation_reason))
                                    <li>{{$refund_submission->refundsubmission_cancellationreason->cancellation_reason}}</li>
                                @endif
                            </ul>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>@lang('labels.payment_date')</th>
                            <th>@lang('labels.type')</th>
                            <th>@lang('labels.bill')</th>
                            <th>@lang('labels.paid')</th>
                            <th>@lang('labels.amount')</th>
                            <th>@lang('labels.information')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @php($total_amount = 0)
                    @php($month_count = 0)
                    @if($day_diff > 3)
                        <tr>
                            <td style="text-align: right;">{{date('d-m-Y', strtotime($first_payment->payment_date))}}</td>
                            <td>@lang('labels.first_payment')</td>
                            <td class="my_rupiah">{{$mm_price->mf_amount}}</td>
                            <td class="my_rupiah">{{$first_payment->amount_paid - $first_payment->sf_amount + $first_payment->dc_amount}}</td>
                            <td class="my_rupiah">{{$mm_price->mf_amount - ($first_payment->amount_paid - $first_payment->sf_amount + $first_payment->dc_amount)}}</td>
                            <td>
                                @lang('labels.holded_service_fee'): <span class="my_rupiah">{{$first_payment->sf_amount - $first_payment->dc_amount}}</span>
                            </td>
                            @php($total_amount = $total_amount + ($mm_price->mf_amount - ($first_payment->amount_paid - $first_payment->sf_amount + $first_payment->dc_amount)))
                        </tr>
                    @else
                        <tr>
                            <td style="text-align: right;">{{date('d-m-Y', strtotime($first_payment->payment_date))}}</td>
                            <td>@lang('labels.first_payment')</td>
                            <td class="my_rupiah">0</td>
                            @if($total_workouts > 0)
                                <td class="my_rupiah">{{$first_payment->amount_paid - $first_payment->sf_amount + $first_payment->dc_amount}}</td>
                                <td class="my_rupiah">{{$first_payment->amount_paid - $first_payment->sf_amount + $first_payment->dc_amount}}</td>
                                <td>
                                    @lang('labels.holded_service_fee'): <span class="my_rupiah">{{$first_payment->sf_amount - $first_payment->dc_amount}}</span>
                                </td>
                                @php($total_amount = $total_amount + (0 - ($first_payment->amount_paid - $first_payment->sf_amount + $first_payment->dc_amount)))
                            @else
                                <td class="my_rupiah">{{$first_payment->amount_paid}}</td>
                                <td class="my_rupiah">{{$first_payment->amount_paid}}</td>
                                <td>
                                    @lang('labels.holded_service_fee'): <span class="my_rupiah">0</span>
                                </td>
                                @php($total_amount = $total_amount + (0 - $first_payment->amount_paid))
                            @endif
                        </tr>
                    @endif
                    @if($member_deposit->memberdeposit_user->user_memberstatus->memberstatus_membershiptype->payment_type == 'AD')
                        @if(!empty($monthly_payments))
                            @foreach($monthly_payments as $payment)
                                @if($month_count < $month_diff)
                                    @if($day_diff > 3)
                                        <tr>
                                            <td style="text-align: right;">{{date('d-m-Y', strtotime($payment->payment_date))}}</td>
                                            <td>@lang('labels.monthly_payment')</td>
                                            <td class="my_rupiah">{{$mm_price->mf_amount}}</td>
                                            <td class="my_rupiah">{{$payment->amount_paid}}</td>
                                            <td class="my_rupiah">{{$mm_price->mf_amount - $payment->amount_paid}}</td>
                                            <td></td>
                                            @php($total_amount = $total_amount + ($mm_price->mf_amount - $payment->amount_paid))
                                        </tr>
                                    @else
                                        @if($payment->payment_status != 'Unpaid')
                                            <tr>
                                                <td style="text-align: right;">{{date('d-m-Y', strtotime($payment->payment_date))}}</td>
                                                <td>@lang('labels.monthly_payment')</td>
                                                <td class="my_rupiah">0</td>
                                                <td class="my_rupiah">{{$payment->amount_paid}}</td>
                                                <td class="my_rupiah">{{$payment->amount_paid}}</td>
                                                <td></td>
                                                @php($total_amount = $total_amount + (0 - $payment->amount_paid))
                                            </tr>
                                        @endif
                                    @endif
                                    @php($month_count++)
                                @endif
                            @endforeach
                        @endif
                    @elseif($member_deposit->memberdeposit_user->user_memberstatus->memberstatus_membershiptype->payment_type == 'Prepaid')
                        @if(!empty($monthly_payments))
                            @php($installment_number = 1)
                            @foreach($monthly_payments as $payment)
                                @if($payment->payment_status != 'Unpaid')
                                    <tr>
                                        <td style="text-align: right;">{{date('d-m-Y', strtotime($payment->payment_date))}}</td>
                                        <td colspan="3">@lang('labels.installment_payment')</td>
                                        <td class="my_rupiah">-{{$payment->amount_paid}}</td>
                                        <td>@lang('labels.installment') - {{$installment_number}}</td>
                                        @php($total_amount = $total_amount - $payment->amount_paid)
                                    </tr>
                                    @php($installment_number++)
                                @endif
                            @endforeach
                        @endif

                        @if($day_diff > 3)
                            @for($i = 1; $i <= $month_diff; $i++)
                                <tr>
                                    <td style="text-align: right;">{{\Carbon\Carbon::parse($member_deposit->memberdeposit_user->user_memberstatus->from_date)->addMonths($i)->format('d-m-Y')}}</td>
                                    <td>@lang('labels.monthly_payment')</td>
                                    <td class="my_rupiah">{{$mm_price->mf_amount}}</td>
                                    <td class="my_rupiah">0</td>
                                    <td class="my_rupiah">{{$mm_price->mf_amount}}</td>
                                    <td></td>
                                    @php($total_amount = $total_amount + $mm_price->mf_amount)
                                </tr>
                            @endfor
                        @endif
                    @endif
                    <tr>
                        <td colspan="4" style="text-align: right;">
                            @if($total_amount < 0)
                                @lang('labels.member_deposit')
                            @else
                                @lang('labels.bill')
                            @endif
                        </td>
                        <td class="my_rupiah">{{abs($total_amount)}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @if($member_deposit->status == 'Pending')
                @if($member_deposit->payment_type == 'Refund')
                    {{--{!! Form::submit(trans('labels.refund_now'), ['class' => 'btn btn-primary pull-right']) !!}--}}
                    <a href="javascript:;" class="btn btn-primary pull-right" data-toggle="modal" data-target="#refundModal">@lang('labels.refund_now')</a>
                @elseif($member_deposit->payment_type == 'Payment')
                    {!! Form::submit(trans('labels.pay_bill_now'), ['class' => 'btn btn-success pull-right']) !!}
                @endif
            @endif
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="refundModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="padding: 20px;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.refund_type')</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="form-horizontal">
                            <div class="form-group">
                                {!! Form::label('cash_amount', trans('labels.cash'), ['class' => 'control-label col-md-3']) !!}
                                <div class="col-md-4">
                                    {!! Form::text('cash_amount', abs($total_amount), ['class' => 'form-control rupiah']) !!}
                                    <p class="help-block">*@lang('labels.maximum'): <span class="my_rupiah">{{abs($total_amount)}}</span></p>
                                    {!! Form::hidden('old_cash', abs($total_amount)) !!}
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
                                    <p class="help-block">*@lang('labels.maximum'): <span class="my_rupiah">{{abs($total_amount)}}</span></p>
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
                                    {!! Form::text('voucher_code', null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">@lang('labels.close')</button>
                    <button type="button" id="refund-btn-submit" class="btn btn-primary">@lang('labels.save') @lang('labels.and') @lang('labels.refund_now')</button>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@section('other-script')
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script>
        $('#fa_coach').select2();
    </script>
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
            $('#use_voucher').on('click', function () {
                if ($(this).prop('checked') == true) {
                    $('input[name="nominal"]').prop('disabled', false);
                    $('input[name="from_date"]').prop('disabled', false);
                    $('input[name="thru_date"]').prop('disabled', false);
                } else {
                    $('input[name="nominal"]').val('0');
                    $('input[name="nominal"]').prop('disabled', true);
                    $('input[name="from_date"]').prop('disabled', true);
                    $('input[name="thru_date"]').prop('disabled', true);
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

            $('#refund-btn-submit').on('click', function () {
                var cash = parseInt(rupiah_format_destroyer($('#cash_amount').val()));
                var nominal = parseInt(rupiah_format_destroyer($('input[name="nominal"]').val()));
                var old_saldo = parseInt(rupiah_format_destroyer($('input[name="old_cash"]').val()));

                if ((cash + nominal) <= old_saldo) {
                    $('#cash_amount').val(cash);
                    $('input[name="nominal"]').val(nominal);
                    $('#member-deposit-form').submit();
                } else {
                    alert('@lang('messages.balance_not_valid')');
                }
            });
        });
    </script>
@endsection