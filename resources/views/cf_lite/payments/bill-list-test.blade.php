@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <style>
        #c-logo {
            display: none;
        }

        #payment_date {
            display: none;
        }

        @media print {
            #bill-list-table tr td:nth-child(1), #bill-list-table tr td:nth-child(5), #bill-list-table tr td:last-child,
            #bill-list-table tr th:nth-child(1), #bill-list-table tr th:nth-child(5), #bill-list-table tr th:last-child {
                display: none;
            }

            #c-logo {
                display: block;
            }

            .page-title {
                margin-top: 0;
                margin-bottom: 10px;
                font-size: 30px;
            }

            tr .warning {
                display: none;
            }
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <img id="c-logo" src="{{asset('images/Curves_Logo_Purple_Amaze.png')}}" style="width: 80px; height: auto; position: absolute; right: 0;">
    <div class="row">
        <div class="col-sm-12">
            <center>
                <h2 class="page-title">
                    @if(!empty($type))
                        @if($type == 'paid')
                            @lang('labels.paid_list')
                        @endif
                    @else
                        @lang('labels.bill_list')
                    @endif
                </h2>
            </center>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3 img-box">
            @if(!empty($member->user_memberpersonal->photo))
                <img class="img-circle member-card-img" src="{{asset('images/member_photos/'.$member->user_memberpersonal->photo)}}" style="width: 150px; height: 150px; object-fit: cover;">
            @else
                <img class="img-circle member-card-img" src="{{asset('images/img.png')}}">
            @endif
        </div>
        <div class="col-md-9 col-xs-12">
            <table id="identity-table">
                <tr>
                    <td><span class="fa fa-female" title="@lang('labels.name')"></span></td>
                    <td>:</td>
                    <td>{{$member->name}}</td>
                </tr>
                <tr id="joined-row">
                    <td><span class="fa fa-calendar" title="@lang('labels.joined')"></span></td>
                    <td>:</td>
                    <td>{{date('d F Y', strtotime($member->user_memberpersonal->joined))}}</td>
                </tr>
                <tr>
                    <td><span class="fa fa-group" title="@lang('labels.membership_type')"></span></td>
                    <td>:</td>
                    <td>
                        {{$member->user_memberstatus->memberstatus_membershiptype->membership_type_name}}
                        <br>
                        ({{date('d F Y', strtotime($member->user_memberstatus->from_date))}} - {{date('d F Y', strtotime($member->user_memberstatus->thru_date))}})
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <a id="my-btn-print" class="btn btn-default" style="float: left; font-size: 16px; margin-bottom: 20px;"><i class="fa fa-print"></i> @lang('labels.print')</a>
        </div>
    </div>
    <div class="row">
        <div class="table-responsive">
            <table class="table" id="bill-list-table">
                <thead>
                <tr>
                    <th>@lang('labels.indexing')</th>
                    <th>@lang('labels.due_date')</th>
                    <th>@lang('labels.bill_type')</th>
                    <th>@lang('labels.amount')</th>
                    <th>@lang('labels.payment_status')</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @php($indexing = 1)
                @foreach($bills as $bill)
                    @if($bill->payment_status == 'Paid')
                        @php($status = 'success')
                    @elseif($bill->payment_status == 'Halfpaid')
                        @php($status = 'warning')
                    @elseif($bill->payment_status == 'Unpaid')
                        @php($status = 'danger')
                    @endif
                    <tr class="{{$status}}">
                        <td>{{$indexing}}</td>
                        <td>
                            @if($bill->payment_status == 'Unpaid')
                                <a href="{{url('cf-lite/payment/invoice/'.$bill->id.'/detail/')}}">
                                    {{date('d-m-Y', strtotime($bill->payment_date))}}
                                </a>
                            @else
                                <a href="{{url('cf-lite/payment/invoice/'.$bill->id.'/member')}}">
                                    {{date('d-m-Y', strtotime($bill->payment_date))}}
                                </a>
                            @endif
                        </td>
                        <td>{{$bill->payment_category}}</td>
                        <td class="my_rupiah" id="amount-{{$bill->id}}" amount="{{$bill->amount_due}}">{{$bill->amount_due}}</td>
                        <td>{{$bill->payment_status}}</td>
                        <td>
                            @if(!empty($bill->rest_of_bill) && ($bill->payment_category == 'First') && ($bill->payment_status == 'Halfpaid'))
                                @if($bill->memberpayment_user->user_memberstatus->memberstatus_membershiptype->payment_type != 'Prepaid')
                                    @if($next_membership != null)
                                        @if(($next_membership->payment_type == 'Cash') && ($next_membership->is_vip == 'general'))
                                            <a href="{{url('cf-lite/payment/debt/'.$bill->id)}}" class="btn btn-default btn-sm"><span class="fa fa-money"></span> @lang('labels.pay_now')</a>
                                        @else
                                            <div class="label label-warning">@lang('labels.wait_until_membership_activated')</div>
                                        @endif
                                    @else
                                        <a href="{{url('cf-lite/payment/debt/'.$bill->id)}}" class="btn btn-default btn-sm"><span class="fa fa-money"></span> @lang('labels.pay_now')</a>
                                    @endif
                                @else
                                    <a href="{{url('cf-lite/payment/new-installment-setting/'.$member->id)}}" class="btn btn-default btn-sm"><span class="fa fa-bank"></span> @lang('labels.set_installment')</a>
                                @endif
                                {{--<a href="{{url('cf-lite/payment/debt/'.$bill->id)}}" class="btn btn-default btn-sm"><span class="fa fa-money"></span> @lang('labels.pay_now')</a>--}}
                            @elseif(($bill->payment_category == 'Monthly') && ($bill->payment_status == 'Unpaid'))
                                @php($accept_payment = 0)
                                @if($bill->memberpayment_user->user_memberstatus->memberstatus_membershiptype->payment_type == 'Prepaid')
                                    @php($accept_payment = 1)
                                @elseif($bill->memberpayment_user->user_memberstatus->memberstatus_membershiptype->payment_type == 'AD')
                                    @if(strtotime(\Carbon\Carbon::parse($bill->payment_date)->subDays(14)) <= strtotime(\Carbon\Carbon::now()))
                                        @php($accept_payment = 1)
                                    @endif
                                @endif

                                @if($accept_payment == 1)
                                    {!! Form::open(['method' => 'PATCH', 'action' => ['PaymentController@accept_installment_payment_test', $bill->id], 'id' => 'installment-payment-form']) !!}
                                    {!! Form::button('<span class="fa fa-money"></span> '.trans('labels.payment_accepted'), ['class' => 'btn btn-default btn-sm payment-button', 'data-toggle' => 'modal', 'data-target' => '#modal', 'id' => 'payment-button-'.$bill->id]) !!}
                                    @if($bill->memberpayment_user->user_memberstatus->memberstatus_membershiptype->payment_type == 'Prepaid')
                                        {!! Form::date('payment_date', \Carbon\Carbon::today(), ['class' => 'form-control', 'id' => 'payment_date']) !!}
                                    @elseif($bill->memberpayment_user->user_memberstatus->memberstatus_membershiptype->payment_type == 'AD')
                                        {!! Form::date('payment_date', \Carbon\Carbon::today(), ['class' => 'form-control', 'id' => 'payment_date']) !!}
                                        {!! Form::date('min_payment_date', \Carbon\Carbon::parse($bill->payment_date)->subDays(14), ['style' => 'display: none;']) !!}
                                    @endif
                                    {!! Form::hidden('payment_method', '', ['id' => 'payment_method']) !!}
                                    {!! Form::hidden('bank', '', ['id' => 'bank']) !!}
                                    {!! Form::hidden('amount', '', ['id' => 'amount']) !!}
                                    {!! Form::hidden('accurate_code', '', ['id' => 'accurate_code']) !!}
                                    {!! Form::close() !!}
                                @endif
                            @elseif(!empty($bill->rest_of_bill) && ($bill->payment_category == 'Monthly') && ($bill->payment_status == 'Halfpaid') && ($bill->memberpayment_user->user_memberstatus->memberstatus_membershiptype->payment_type == 'AD'))
                                <a href="{{url('cf-lite/payment/debt/'.$bill->id)}}" class="btn btn-default btn-sm"><span class="fa fa-money"></span> @lang('labels.pay_now')</a>
                            @endif

                        </td>
                    </tr>
                    @php($indexing++)
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!--Modal-->
    <div class="modal fade" role="dialog" id="modal">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.installment_payment')</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    {!! Form::label('payment_date_temp', trans('labels.set_payment_date').':  ', ['class' => 'control-label col-md-5']) !!}
                                    <div class="col-md-5">
                                        {!! Form::date('payment_date_temp', \Carbon\Carbon::today(), ['class' => 'form-control', 'id' => 'payment_date_temp']) !!}
                                    </div>
                                    {!! Form::hidden('the_id', null) !!}
                                </div>
                                <div class="form-group">
                                    {!! Form::label('amount', trans('labels.amount').':  ', ['class' => 'control-label col-md-5']) !!}
                                    <div class="col-md-5">
                                        {!! Form::text('amount', 0, ['class' => 'form-control', 'id' => 'amount-temp']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('payment_method', trans('labels.payment_method').': ', ['class' => 'control-label col-md-5']) !!}
                                    <div class="col-md-5">
                                        {!! Form::select('payment_method', $payment_methods, null, ['class' => 'form-control', 'id' => 'payment_method_temp']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('bank', trans('labels.bank').': ', ['class' => 'control-label col-md-5']) !!}
                                    <div class="col-md-5">
                                        <select id="bank_temp" name="bank" class="form-control select2" style="width: 100%;">
                                            <option value="">@lang('labels.choose') @lang('labels.bank')</option>
                                            @foreach($banks as $bank)
                                                <option value="{{ $bank->id }}">{{ $bank->bank_abv }} - {{ $bank->bank_account_number }}</option>
                                            @endforeach
                                        </select>
                                        {{--{!! Form::select('bank', $banks, null, ['class' => 'form-control', 'id' => 'bank_temp', 'placeholder' => trans('labels.choose').' '.trans('labels.bank')]) !!}--}}
                                    </div>
                                </div>

                                <!-- ACCURATE -->
                                @if(isset(Session::get('accurate')->accurate_id))
                                    <div class="form-group">
                                @else
                                    <div class="form-group" style="display: none">
                                @endif

                                    {!! Form::label('accurate_code_temp', 'Accurate', ['class' => 'control-label col-md-5']) !!}

                                    <div class="col-md-5">
                                        <select name="accurate_code_temp" class="form-control select2" id="accurate_code_temp">
                                            <option value="">Choose Accurate Item</option>
                                            @foreach($accurateItems as $key => $val)
                                                <option value="{{$key}}">{{$val}}</option>
                                            @endforeach
                                        </select>
                                        <span style="display:block; margin-top:5px; font-size:12px; color: #858585;">If you can't find appropriate ACCURATE Item, please create it first in Accurate and reload this page.</span>
                                    </div>
                                </div>
                                <!-- /ACCURATE -->
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('labels.cancel')</button>
                    <button type="button" class="btn btn-primary" id="payment-date-temp-submit">@lang('labels.submit')</button>
                </div>
            </div>
        </div>
    </div>
    <style>
        .select2 {
            width: 100% !important;
        }
    </style>
@endsection

@section('other-script')
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2();
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
            $('.payment-button').on('click', function () {
                var the_id = $(this).attr('id').split('-').pop();
                var amount_temp = $('#amount-'+the_id).attr('amount');
                $('#the_id').val(the_id);
                $('#amount-temp').val(rupiah_formater(amount_temp.replace(/\s/g, '')));
            });

            $('#modal').on('click', '#payment-date-temp-submit', function () {
                var amount = $('#amount-temp').val().replace(/\./g, '');
                console.log(amount);
                if($('#accurate_code_temp').val() == ""){
                    alert('Accurate Code is required!');
                }else{
                    $('#payment_date').val($('#payment_date_temp').val());
                    $('#amount').val(amount);
                    $('#payment_method').val($('#payment_method_temp').val());
                    $('#bank').val($('#bank_temp').val());
                    $('#accurate_code').val($('#accurate_code_temp').val());
                    $('#installment-payment-form').submit();
                }
            });

            $('#payment_date_temp').on('change', function () {
                var x = confirm('@lang('messages.are_you_sure_change_date')');
                var date_temp = '{{\Carbon\Carbon::now()->format('Y-m-d')}}';

                if (x == false) {
                    $('#payment_date_temp').val(date_temp);
                }
            });

            $('#my-btn-print').on('click', function () {
                $(this).addClass('hidden');
                $('.left-sidebar').addClass('hidden');
                $('.nav-tabs').addClass('hidden');
                $('#floating-button').addClass('hidden');
                $('#manajemenkeanggotaan').addClass('hidden');
                $('.img-box').addClass('hidden');
                $('#joined-row').addClass('hidden');
                $('.box-menu').css('border', '0');
                $('#bill-list-table').addClass('table-bordered');
                window.print();
                $('.left-sidebar').removeClass('hidden');
                $('.nav-tabs').removeClass('hidden');
                $('#floating-button').removeClass('hidden');
                $('#manajemenkeanggotaan').removeClass('hidden');
                $(this).removeClass('hidden');
                $('.img-box').removeClass('hidden');
                $('#joined-row').removeClass('hidden');
                $('.box-menu').css('border', '');
                $('#bill-list-table').removeClass('table-bordered');
            });
        });
    </script>
@endsection
