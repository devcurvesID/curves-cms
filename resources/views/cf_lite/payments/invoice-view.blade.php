@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/payment.css')}}" rel="stylesheet">
    <style>
        body {
            color: black;
        }

        .column-no-border, .column-no-border td {
            border: none !important;
        }

        .modal-dialog, .modal-dialog label {
            color: #414042;
        }

        table {
            color: #414042;
        }
    </style>

    <style>
        #logo-print, #ttd-print {
            display: none;
        }

        #logo-print img {
            width: 100px;
            height: auto;
            margin-bottom: 30px;
        }

        #ttd-print h4 {
            margin-top: 20px;
            font-size: 12px;
            color: #414042;
        }

        .select2 {
            width: 100% !important;
        }
    </style>
@endsection

@section('content')
    {!! Form::hidden('club_id', Auth::user()->club_id) !!}
    <div class="row">
        <div class="col-sm-4" id="logo-print">
            <img src="{{asset('images/Curves_Logo_Purple_Amaze.png')}}">
        </div>
        <div class="col-sm-12">
            <center>
                <h2 class="page-title">@lang('labels.payment_detail')</h2>
            </center>
            {!! Form::hidden('bill_id', $member_payment->id) !!}

            @if($user->user_club->digital_product)
                <a id="reprint-customer" class="btn btn-warning pull-right"
                   style="top: 5px; right: 25px; font-size: 45px; border-radius: 50%; padding: 20px;"
                   title="@lang('labels.print_customer')"><i class="fa fa-print"></i>
                </a>
                <a class="btn btn-success pull-right"
                   style="top: 5px; right: 20px; font-size: 45px; border-radius: 50%; padding: 20px; margin-right: 15px;"
                   title="@lang('labels.print_club')"
                   id="reprint-club"><i class="fa fa-print"></i>
                </a>
            @else
                <a id="my-btn-print" class="btn btn-primary pull-right"
                   style="top: 5px; right: 25px; font-size: 45px; border-radius: 50%; padding: 20px;"
                   title="@lang('labels.print_invoice')"><i class="fa fa-print"></i>
                </a>
                <a class="btn btn-success pull-right"
                   style="top: 5px; right: 20px; font-size: 45px; border-radius: 50%; padding: 20px; margin-right: 15px;"
                   title="@lang('labels.print_receipt')"
                   data-toggle="modal" data-target="#modal"><i class="fa fa-print"></i>
                </a>
                @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 3 || Auth::user()->role_id == 8)
                    @if($member_payment->payment_category == 'First')
                        <a class="btn btn-info pull-right"
                           style="top: 5px; right: 20px; font-size: 45px; border-radius: 50%; padding: 20px; margin-right: 15px;"
                           title="@lang('labels.edit')"
                           data-toggle="modal" data-target="#edit"><i class="fa fa-edit"></i>
                        </a>
                    @elseif($member_payment->payment_category == 'Monthly')
                        <a class="btn btn-info pull-right"
                           style="top: 5px; right: 20px; font-size: 45px; border-radius: 50%; padding: 20px; margin-right: 15px;"
                           title="@lang('labels.edit')"
                           data-toggle="modal" data-target="#edit-bill"><i class="fa fa-edit"></i>
                        </a>
                    @endif
                @endif
                @if(($member_payment->memberpayment_user->user_memberstatus->payment_status == 'Set') && ($member_payment->renewal) && ($member_payment->payment_category == 'First'))
                    <a href="javascript:;" class="btn btn-warning pull-left"
                       style="top: 5px; right: 20px; font-size: 45px; border-radius: 5%; padding: 20px; margin-right: 15px;"
                       data-toggle="modal"
                       data-target="#installment-modal">@lang('labels.installment')</a>
                @endif
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-xs-6">
            <table class="member-detail">
                <tr>
                    <td id="member-detail-1">@lang('labels.premember_name')</td>
                    <td>:</td>
                    <td>{{$member_payment->memberpayment_user->name}}</td>
                </tr>
                <tr>
                    <td id="member-detail-2">@lang('labels.payment_number')</td>
                    <td>:</td>
                    <td>{{$member_payment->payment_number}}</td>
                </tr>
                <tr>
                    <td id="member-detail-3">@lang('labels.membership_type')</td>
                    <td>:</td>
                    <td>
                        {{--{{$member_payment->memberpayment_user->user_memberstatus->memberstatus_membershiptype->membership_type_name}}--}}
                        {{$membership->membership_type_name}}
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-md-6 col-xs-6">
            <table class="member-detail">
                <tr>
                    <td>@lang('labels.payment_category')</td>
                    <td>:</td>
                    <td>
                        @if($member_payment->payment_category == 'First')
                            @lang('labels.first_payment')
                        @elseif($member_payment->payment_category == 'Monthly')
                            @lang('labels.installment_payment')
                        @endif
                    </td>
                </tr>
                <tr>
                    <td>@lang('labels.date')</td>
                    <td>:</td>
                    <td>{{date('d-m-Y', strtotime($member_payment->payment_date))}}</td>
                </tr>
                {{--<tr>--}}
                {{--<td>@lang('labels.payment_method')</td>--}}
                {{--<td>:</td>--}}
                {{--<td>--}}
                {{--@if($member_payment->payment_category == 'First')--}}
                {{--{{$member_payment->memberpayment_paymentmethod->payment_method}}--}}
                {{--@elseif($member_payment->payment_category == 'Monthly')--}}
                {{--@if(empty($member_payment->payment_method_id))--}}
                {{--{{$member_payment->memberpayment_user->user_memberstatus->memberstatus_membershiptype->payment_type}}--}}
                {{--@endif--}}
                {{--@endif--}}
                {{--</td>--}}
                {{--</tr>--}}
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th colspan="3">@lang('labels.first_payment')</th>
                    </tr>
                    <tr>
                        <th>@lang('labels.type')</th>
                        <th>@lang('labels.duration')</th>
                        <th>@lang('labels.fee')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($temp_digiprice = 0)
                    <tr>
                        <td>@lang('labels.service_fee')</td>
                        <td>One Time</td>
                        <td class="my_rupiah">
                            {{--Updated 12042021--}}
                            @if (!empty($member_payment->memberpayment_digitalproductpayment))
                                @if (($member_payment->sf_amount - $member_payment->memberpayment_digitalproductpayment->price) >= 0)
                                    {{ $member_payment->sf_amount - $member_payment->memberpayment_digitalproductpayment->price }}
                                @else
                                    @php($temp_digiprice = $member_payment->memberpayment_digitalproductpayment->price - $member_payment->sf_amount)
                                    0
                                @endif
                            @else
                                {{$member_payment->sf_amount}}
                            @endif
                            {{--Updated 12042021--}}
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('labels.monthly_fee')</td>
                        <td>Monthly</td>
                        <td class="my_rupiah">
                            {{--Updated 12042021--}}
                            {{  $member_payment->mf_amount - $temp_digiprice}}
                            {{--Updated 12042021--}}
                        </td>
                    </tr>
                    {{--Added 12042021--}}
                    @if (!empty($member_payment->memberpayment_digitalproductpayment))
                        <tr>
                            <td>@lang('labels.digital_product')</td>
                            <td>{{ $member_payment->memberpayment_digitalproductpayment->digitalproductpayment_digitalproductinventory->digitalproductinventory_digitalproduct->item_name }}</td>
                            <td class="my_rupiah">
                                {{$member_payment->memberpayment_digitalproductpayment->price}}
                            </td>
                        </tr>
                    @endif
                    {{--Added 12042021--}}
                    <tr>
                        <td>@lang('labels.discount')</td>
                        <td>1&times;</td>
                        <td class="my_rupiah" style="background-color: gainsboro;">{{$member_payment->deposit_amount > 0 ? $member_payment->dc_amount - $member_payment->deposit_amount : $member_payment->dc_amount}}</td>
                    </tr>
                    <tr>
                        <td>@lang('labels.down_payment_deposit')</td>
                        <td></td>
                        <td class="my_rupiah" style="background-color: gainsboro;">{{$member_payment->deposit_amount}}</td>
                    </tr>
                    <tr>
                        <td>@lang('labels.voucher')</td>
                        <td>
                            @if(!empty($member_payment->memberpayment_memberpaymentvoucher))
                                #{{$member_payment->memberpayment_memberpaymentvoucher->voucher_code}}
                            @else
                                -
                            @endif
                        </td>
                        <td class="my_rupiah" style="background-color: gainsboro;">
                            @php($voucher_value = 0)
                            @if(!empty($member_payment->memberpayment_memberpaymentvoucher))
                                {{$member_payment->memberpayment_memberpaymentvoucher->voucher_value}}
                                @php($voucher_value = $member_payment->memberpayment_memberpaymentvoucher->voucher_value)
                            @else
                                0
                            @endif
                        </td>
                    </tr>
                    @if(!empty($member_payment->memberpayment_membershipchangingdeposit))
                        <tr>
                            <td colspan="2">@lang('labels.deposit')</td>
                            <td class="my_rupiah" style="background-color: gainsboro;">
                                {{$member_payment->memberpayment_membershipchangingdeposit->deposit_amount}}
                            </td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="2" style="text-align: right;">@lang('labels.subtotal')</td>
                        <td class="my_rupiah">{{$member_payment->amount_due - $member_payment->tax_amount}}</td>
                    </tr>
                    <tr>
                        <td>@lang('labels.tax')</td>
                        <td style="text-align: right;">
                            @if(!empty($member_payment->tax_type_id))
                                {{$member_payment->memberpayment_taxtype->tax_type}}
                            @else
                                -
                            @endif
                        </td>
                        <td class="my_rupiah">{{$member_payment->tax_amount}}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: right;">@lang('labels.billing_total')</td>
                        <td class="my_rupiah">{{$member_payment->amount_due}}</td>
                    </tr>
                    @if(count($member_payment->memberpayment_memberdebtpayment) == 0)
                        <tr class="column-no-border">
                            <td style="text-align: right;">{{date('d-m-Y', strtotime($member_payment->payment_date))}}</td>
                            <td style="text-align: right;">
                                @if(!empty($member_payment->memberpayment_paymentmethod->payment_method))
                                    @lang('labels.pay')
                                    ({{$member_payment->memberpayment_paymentmethod->payment_method}}):
                                @else
                                    @lang('labels.pay'):
                                @endif
                            </td>
                            <td class="my_rupiah">{{$member_payment->amount_paid}}</td>
                        </tr>
                        <tr class="column-no-border">
                            <td style="text-align: right;">{{date('d-m-Y', strtotime($member_payment->payment_date))}}</td>
                            <td style="text-align: right;">@lang('labels.rest_of_the_bill') :</td>
                            {{--<td class="my_rupiah">{{$member_payment->rest_of_bill}}</td>--}}
                            <td class="my_rupiah">{{$member_payment->amount_due - $member_payment->amount_paid}}</td>
                        </tr>
                    @else
                        @php($debt_index = 0)
                        @foreach($member_payment->memberpayment_memberdebtpayment as $member_debt)
                            @if($debt_index == 0)
                                <tr class="column-no-border">
                                    <td style="text-align: right;">{{date('d-m-Y', strtotime($member_payment->payment_date))}}</td>
                                    <td style="text-align: right;">
                                        @if(!empty($member_payment->memberpayment_paymentmethod->payment_method))
                                            @lang('labels.pay')
                                            ({{$member_payment->memberpayment_paymentmethod->payment_method}}):
                                        @else
                                            @lang('labels.pay'):
                                        @endif
                                    </td>
                                    <td class="my_rupiah">{{$member_payment->amount_due - $member_debt->amount_due}}</td>
                                </tr>
                                <tr class="column-no-border">
                                    <td style="text-align: right;">{{date('d-m-Y', strtotime($member_payment->payment_date))}}</td>
                                    <td style="text-align: right;">@lang('labels.rest_of_the_bill') :</td>
                                    <td class="my_rupiah">{{$member_debt->amount_due}}</td>
                                </tr>
                            @endif
                            <tr class="column-no-border">
                                <td style="text-align: right;">{{date('d-m-Y', strtotime($member_debt->payment_date))}}</td>
                                <td style="text-align: right;">
                                    @if(!empty($member_debt->memberdebtpayment_paymentmethod->payment_method))
                                        @lang('labels.pay')
                                        ({{$member_debt->memberdebtpayment_paymentmethod->payment_method}}):
                                    @else
                                        @lang('labels.pay'):
                                    @endif
                                </td>
                                <td class="my_rupiah">{{$member_debt->amount_paid}}</td>
                            </tr>
                            <tr class="column-no-border">
                                <td style="text-align: right;">{{date('d-m-Y', strtotime($member_debt->payment_date))}}</td>
                                <td style="text-align: right;">@lang('labels.rest_of_the_bill') :</td>
                                <td class="my_rupiah">{{$member_debt->rest_of_bill}}</td>
                            </tr>
                            @php($debt_index++)
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-4" id="ttd-print">
            <h4>@lang('labels.staff_signature'):</h4>
            <h4 style="margin-top: 50px;">@lang('labels.staff_name')</h4>
        </div>
    </div>

    {{--Modal--}}
    <div class="modal fade" role="dialog" id="modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.staff')</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-inline">
                                <div class="form-group">
                                    {!! Form::label('staff', trans('labels.choose_staff').': ', ['class' => 'control-label']) !!}
                                    @if(!empty(Session::get('staff_session')))
                                        {!! Form::select('fa_coach', $staffs, Session::get('staff_session'), ['class' => 'form-control select2', 'id' => 'staff']) !!}
                                    @else
                                        {!! Form::select('fa_coach', $staffs, Auth::id(), ['class' => 'form-control select2', 'id' => 'staff']) !!}
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('labels.cancel')</button>
                    <button type="button" class="btn btn-primary" id="pos-print">@lang('labels.print')</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" role="dialog" id="edit">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'PATCH', 'action' => ['InvoiceController@edit_member_payment_invoice',$member_payment->id], 'id' => 'edit-form']) !!}
                {!! Form::hidden('membership_type', $membership->id) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    @if($member_payment->payment_category == 'First')
                        <h4 class="modal-title">@lang('labels.edit_payment') - @lang('labels.first_payment')</h4>
                    @elseif($member_payment->payment_category == 'Monthly')
                        <h4 class="modal-title">@lang('labels.edit_payment') - @lang('labels.installment_payment')</h4>
                    @endif
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                @if($member_payment->payment_category == 'First')
                                    <th colspan="3">@lang('labels.first_payment')</th>
                                @elseif($member_payment->payment_category == 'Monthly')
                                    <th colspan="3">@lang('labels.installment_payment')</th>
                                @endif
                            </tr>
                            <tr>
                                <td colspan="2">@lang('labels.date')</td>
                                <td>{!! Form::date('date', \Carbon\Carbon::parse($member_payment->payment_date), ['class' => 'form-control']) !!}</td>
                            </tr>
                            <tr>
                                <th>@lang('labels.type')</th>
                                <th>@lang('labels.duration')</th>
                                <th>@lang('labels.fee')</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td>@lang('labels.service_fee')</td>
                                <td>One Time</td>
                                <td id="sf" class="my_rupiah">
                                    {{$member_payment->sf_amount}}
                                </td>
                            </tr>
                            <tr>
                                <td>@lang('labels.monthly_fee')</td>
                                <td>Monthly</td>
                                <td id="mf" class="my_rupiah">
                                    {{$member_payment->mf_amount}}
                                </td>
                            </tr>
                            <tr>
                                <td>@lang('labels.discount')</td>
                                <td>1&times;</td>
                                <td>
                                    {!! Form::text('discount', $member_payment->dc_amount, ['class' => 'form-control rupiah', 'id' => 'discount', 'onkeypress' => 'return onlyNumber(event)']) !!}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">@lang('labels.billing_total')</td>
                                <td>
                                    <div id="billing-total" class="my_rupiah">
                                        {{$member_payment->amount_due}}
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">@lang('labels.pay')</td>
                                <td>
                                    {!! Form::text('pay', $member_payment->amount_paid, ['class' => 'form-control rupiah', 'id' => 'pay', 'onkeypress' => 'return onlyNumber(event)']) !!}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">@lang('labels.payment_method')</td>
                                <td>
                                    {!! Form::select('payment_method', $payment_methods, null, ['class' => 'form-control', 'id' => 'payment-method']) !!}
                                </td>
                            </tr>
                            <tr style="display: none;" id="select-bank">
                                <td colspan="2">@lang('labels.bank')</td>
                                <td>
                                    <select id="bank" name="bank" class="form-control">
                                        <option value="">@lang('labels.choose') @lang('labels.bank')</option>
                                        @foreach($banks as $bank)
                                            <option value="{{ $bank->id }}">{{ $bank->bank_abv }}
                                                - {{ $bank->bank_account_number }}</option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">@lang('labels.rest_of_the_bill')</td>
                                <td>
                                    <div id="rest-of-bill" class="my_rupiah">
                                        {{$member_payment->rest_of_bill}}
                                    </div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('labels.cancel')</button>
                    <button type="button" class="btn btn-primary" id="edit-payment">@lang('labels.edit')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    {{--Edit Installment--}}
    <div class="modal fade" role="dialog" id="edit-bill">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'PATCH', 'action' => ['InvoiceController@edit_member_payment_invoice',$member_payment->id], 'id' => 'form-edit-bill']) !!}
                {!! Form::hidden('membership_type', $membership->id) !!}
                <div class="modal-header">
                    <h4 class="modal-title">@lang('labels.installment_payment')
                        - @lang('labels.amount_installment')</h4>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th colspan="3">@lang('labels.installment_payment')</th>
                            </tr>
                            <tr>
                                <td colspan="2">@lang('labels.set_due_date')</td>
                                <td>{!! Form::date('payment_date_temp', \Carbon\Carbon::parse($member_payment->payment_date), ['class' => 'form-control']) !!}</td>
                            </tr>
                            <tr>
                                <td colspan="2">@lang('labels.bill')</td>
                                <td>
                                    {!! Form::text('bill', $member_payment->amount_due, ['class' => 'form-control rupiah', 'id' => 'bill', 'onkeypress' => 'return onlyNumber(event)']) !!}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('labels.cancel')</button>
                    <button type="button" class="btn btn-primary" id="edit-installment">@lang('labels.edit')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    {{--Pay installment from first payment--}}
    <div class="modal fade" tabindex="-1" role="dialog" id="installment-modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.installment_payment')</h4>
                </div>
                {!! Form::open(['method' => 'POST', 'action' => ['InvoiceController@member_payment_installment_invoice', $member_payment->id], 'id' => 'installment-form']) !!}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    {!! Form::label('i_payment_date', trans('labels.set_payment_date').':  ', ['class' => 'control-label col-md-5']) !!}
                                    <div class="col-md-5">
                                        {!! Form::date('i_payment_date', \Carbon\Carbon::today(), ['class' => 'form-control', 'id' => 'payment_date_temp', 'min' => \Carbon\Carbon::parse($member_payment->payment_date)->format('Y-m-d')]) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('i_must_paid', trans('labels.rest_of_the_bill').':  ', ['class' => 'control-label col-md-5']) !!}
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-addon">Rp.</div>
                                            {!! Form::text('i_must_paid', $member_payment->rest_of_bill, ['class' => 'form-control rupiah', 'readonly']) !!}
                                            <div class="input-group-addon">,-</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('i_amount', trans('labels.paid').':  ', ['class' => 'control-label col-md-5']) !!}
                                    <div class="col-md-5">
                                        <div class="input-group">
                                            <div class="input-group-addon">Rp.</div>
                                            {!! Form::text('i_amount', 0, ['class' => 'form-control', 'id' => 'amount-temp']) !!}
                                            <div class="input-group-addon">,-</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('i_payment_method', trans('labels.payment_method').': ', ['class' => 'control-label col-md-5']) !!}
                                    <div class="col-md-5">
                                        {!! Form::select('i_payment_method', $payment_methods, null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('i_bank', trans('labels.bank').': ', ['class' => 'control-label col-md-5']) !!}
                                    <div class="col-md-5">
                                        <select id="i_bank_temp" name="i_bank" class="form-control select2"
                                                style="width: 100%;">
                                            <option value="">@lang('labels.choose') @lang('labels.bank')</option>
                                            @foreach($banks as $bank)
                                                <option value="{{ $bank->id }}">{{ $bank->bank_abv }}
                                                    - {{ $bank->bank_account_number }}</option>
                                            @endforeach
                                        </select>
                                        {{--{!! Form::select('bank', $banks, null, ['class' => 'form-control', 'id' => 'bank_temp', 'placeholder' => trans('labels.choose').' '.trans('labels.bank')]) !!}--}}
                                    </div>
                                </div>
                                <!-- ACCURATE -->
                                {{-- @if(isset(Session::get('accurate')->accurate_id))
                                    <div class="form-group">
                                @else
                                    <div class="form-group" style="display: none">
                                @endif
                                    {!! Form::label('accurate_code', 'Accurate', ['class' => 'control-label col-md-5']) !!}

                                    <div class="col-md-5">
                                        <select name="accurate_code" class="form-control select2" id="accurate_code">
                                            <option value="">Choose Accurate Item</option>
                                            @foreach($accurateItems as $key => $val)
                                                <option value="{{$key}}">{{$val}}</option>
                                            @endforeach
                                        </select>
                                        <span style="display:block; margin-top:5px; font-size:12px; color: #858585;">If you can't find appropriate ACCURATE Item, please create it first in Accurate and reload this page.</span>
                                    </div>
                                </div> --}}
                                <!-- /ACCURATE -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('labels.cancel')</button>
                    <button type="button" class="btn btn-primary"
                            id="payment-installment-submit">@lang('labels.submit')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            init_format();

            $('#my-btn-print').on('click', function () {
                $('.left-sidebar').addClass('hidden');
                $('.nav-tabs').addClass('hidden');
                $('#floating-button').addClass('hidden');
                $('#manajemenkeanggotaan').addClass('hidden');
                $('#my-btn-print').addClass('hidden');
                $('.page-title').addClass('hidden');
                $('.member-detail').css('font-size', '12');
                $('.member-detail td').css('padding', '3');
                $('.box-menu').css('border-image', 'none');
                $('#member-detail-1').text('@lang('labels.name')');
                $('#member-detail-2').text('@lang('labels.indexing')');
                $('#member-detail-3').text('@lang('labels.type')');
                $('#logo-print').show();
                $('#ttd-print').show();
                $('.table-bordered').css('margin-top', '25');
                $('td').css({'padding-top': '3px', 'padding-bottom': '3px', 'font-size': '11px'});
                $('th').css({'padding-top': '3px', 'padding-bottom': '3px', 'font-size': '12px'});
                window.print();
                $('#member-detail-1').text("@lang('labels.premember_name')");
                $('#member-detail-2').text('@lang('labels.payment_number')');
                $('#member-detail-3').text('@lang('labels.membership_type')');
                $('.member-detail td').css('padding', '10');
                $('.member-detail').css('font-size', '');
                $('.page-title').removeClass('hidden');
                $('#my-btn-print').removeClass('hidden');
                $('#manajemenkeanggotaan').removeClass('hidden');
                $('#floating-button').removeClass('hidden');
                $('.nav-tabs').removeClass('hidden');
                $('.left-sidebar').removeClass('hidden');
                $('#logo-print').hide();
                $('#ttd-print').hide();
                $('.table-bordered').css('margin-top', '0');
                $('td').css({'padding-top': '8px', 'padding-bottom': '8px', 'font-size': '16px'});
                $('th').css({'padding-top': '8px', 'padding-bottom': '8px', 'font-size': '16px'});
            });
        });

        $(document).ready(function () {
            $('.select2').select2();

            $('#modal').on('click', '#pos-print', function () {
                if ($('input[name="bill_id"]').val() != "") {
                    console.log(jsWebClientPrint);
                    jsWebClientPrint.print(
                        'id=' + $('input[name="bill_id"]').val() +
                        '&club_id=' + $('input[name="club_id"]').val() +
                        '&staff=' + $('#staff').val() +
                        '&useDefaultPrinter=undefined&printerName=XP-80'
                    );
                }
            });

            @if($user->user_club->digital_product)
            $('#reprint-club').on('click', function () {
                jsWebClientPrint.print(
                    'id=' + $('input[name="bill_id"]').val() +
                    '&club_id=' + $('input[name="club_id"]').val() +
                    '&digital_product_check=' + `{{$user->user_club->digital_product? 'true':'false'}}` +
                    '&membership_type=' + `{{ $membership->id }}` +
//                    '&staff=' + $('#staff').val() +
                    '&premember_name=' + `{{$member_payment->memberpayment_user->name}}` +
                    '&for=club' +
                    '&useDefaultPrinter=undefined&printerName=XP-80'
                );
            });
            $('#reprint-customer').on('click', function () {
                jsWebClientPrint.print(
                    'id=' + $('input[name="bill_id"]').val() +
                    '&club_id=' + $('input[name="club_id"]').val() +
                    '&digital_product_check=' + `{{$user->user_club->digital_product? 'true':'false'}}` +
                    '&membership_type=' + `{{ $membership->id }}` +
//                    '&staff=' + $('#staff').val() +
                    '&premember_name=' + `{{$member_payment->memberpayment_user->name}}` +
                    '&for=customer' +
                    '&useDefaultPrinter=undefined&printerName=XP-80'
                );
            });
            @endif


            $('#payment-method').on('change', function () {
                if ($('#payment-method').val() != 1) {
                    $('#select-bank').removeAttr('style');
                } else {
                    $('#select-bank').attr('style', 'display: none;');
                }
            });

            $('#discount').on('keyup', function () {
                var temp_sf = parseInt(rupiah_format_destroyer($('#sf').html()));
                var temp_mf = parseInt(rupiah_format_destroyer($('#mf').html()));
                if ($(this).val() != "") {
                    var temp_discount = parseInt(rupiah_format_destroyer($('#discount').val()));
                } else {
                    var temp_discount = 0;
                }
                var temp_pay = parseInt(rupiah_format_destroyer($('#pay').val()));

                var temp_total = (temp_sf + temp_mf) - temp_discount;
                var new_rest_of_bill = temp_total - temp_pay;

                $('#billing-total').html('Rp.' + rupiah_formater(temp_total));
                $('#rest-of-bill').html('Rp.' + rupiah_formater(new_rest_of_bill));

                if ($(this).val() == "") {
                    var discount = 0;
                } else {
                    var discount = parseInt(rupiah_format_destroyer($(this).val()));
                }

                // Cash formatter
                if ($("#discount").val() != "") {
                    $("#discount").val(rupiah_formater(discount));
                } else {
                    $("#discount").val(0);
                }
            });

            $('#pay').on('keyup', function () {
                var temp_total = rupiah_format_destroyer($('#billing-total').html()) - rupiah_format_destroyer($('#pay').val());

                $('#rest-of-bill').html('Rp.' + rupiah_formater(temp_total));

                if ($(this).val() == "") {
                    var pay = 0;
                } else {
                    var pay = parseInt(rupiah_format_destroyer($(this).val()));
                }

                // Cash formatter
                if ($("#pay").val() != "") {
                    $("#pay").val(rupiah_formater(pay));
                } else {
                    $("#pay").val(0);
                }
            });

            $('#edit-payment').on('click', function () {
                $('#discount').val(rupiah_format_destroyer($('#discount').val()));
                $('#pay').val(rupiah_format_destroyer($('#pay').val()));
                if (rupiah_format_destroyer($('#rest-of-bill').html()) > 0 || rupiah_format_destroyer($('#rest-of-bill').html()) == 0) {
                    if ($('#payment-method').val() == 1) {
                        $('#edit-form').submit();
                    } else {
                        if ($('#bank').val() == "") {
                            alert('@lang('messages.please_choose_bank')');
                        } else {
                            $('#edit-form').submit();
                        }
                    }
                } else {
                    alert('@lang('messages.please_check_rest_of_bill')');
                }

            });

            $('#bill').on('keyup', function () {
                if ($(this).val() == "") {
                    var bill = 0;
                } else {
                    var bill = parseInt(rupiah_format_destroyer($(this).val()));
                }

                // Cash formatter
                if ($("#bill").val() != "") {
                    $("#bill").val(rupiah_formater(bill));
                } else {
                    $("#bill").val(0);
                }
            });

            $('#edit-installment').on('click', function () {
                var temp = rupiah_format_destroyer($('#bill').val());
                $('#bill').val(temp);
                $('#form-edit-bill').submit();
            });

            $('input[name="i_amount"]').on('keyup', function () {
                if ($(this).val() == "") {
                    var iamount = 0;
                } else {
                    var iamount = parseInt(rupiah_format_destroyer($(this).val()));
                }

                // Cash formatter
                if ($('input[name="i_amount"]').val() != "") {
                    $('input[name="i_amount"]').val(rupiah_formater(iamount));
                } else {
                    $('input[name="i_amount"]').val(0);
                }
            });

            $('#payment-installment-submit').on('click', function () {
                if (($('select[name="i_payment_method"]').val() > 1) && ($('select[name="i_bank"]').val() == '')) {
                    alert('@lang('messages.please_choose_bank')');
                }
                /* else if($('#accurate_code').val() == ""){
                    @if(isset(Session::get('accurate')->accurate_id))
                        alert('Accurate Code is required!');
                    @else
                        $('input[name="i_amount"]').val(parseInt(rupiah_format_destroyer($('input[name="i_amount"]').val())));
                        $('#installment-form').submit();
                    @endif
                } */
                else {
                    $('input[name="i_amount"]').val(parseInt(rupiah_format_destroyer($('input[name="i_amount"]').val())));
                    $('#installment-form').submit();
                }
            });
        });

        function onlyNumber(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode;
            if (charCode == 8 || //space
                charCode == 9 || //tab
                charCode == 46 || //point
                (charCode > 47 && charCode < 58) // number
            ) {
                return true;
            }
            return false;
        }

        function init_format() {
            $('.rupiah').each(function () {
                if ($(this).val() == "") {
                    $(this).val('0');
                } else {
                    var x = rupiah_formater($(this).val().replace(/\s/g, ''));
                    $(this).val((x));
                }
            });

            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp.' + (x));
            });
        }
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>


    {!!

    // Register the WebClientPrint script code
    // The $wcpScript was generated by PrintESCPOSController@index

    $wcpScript;

    !!}
@endsection
