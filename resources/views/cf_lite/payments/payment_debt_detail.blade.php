@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/payment.css')}}" rel="stylesheet">
    <style>
        td{
            padding: 5px;
        }

        #print-btn {
            display: none;
            width: 100% !important;
            height: 100px;
            background-color: turquoise !important;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <center>
                <h2 class="page-title">@lang('labels.payment_detail')</h2>
            </center>
        </div>
    </div>

    @include('cf_lite.includes.error-message')

    {!! Form::open(['action' => ['PaymentController@store_payment_debt', $member_payment->id], 'method' => 'POST', 'id' => 'form-payment-debt']) !!}
    {!! Form::hidden('payment', $member_payment->id) !!}
    <div class="row">
        <div class="col-md-6">
            <table>
                <tr>
                    <td>@lang('labels.member_name')</td>
                    <td>:</td>
                    <td>{{$member_payment->memberpayment_user->name}}</td>
                </tr>
                <tr>
                    <td>@lang('labels.payment_number')</td>
                    <td>:</td>
                    <td>
                        @php($club_id = $member_payment->memberpayment_user->club_id)
                        @php($month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'));
                        {{sprintf('%08d', $temp_auto_number->current_number)}}/PY/CIND/{{sprintf('%03d', $club_id)}}/{{$month_roman[\Carbon\Carbon::now()->format('n')]}}/{{\Carbon\Carbon::now()->format('Y')}}
                        {!! Form::hidden('payment_number', '/PY/CIND/'.sprintf('%03d', $club_id).'/'.$month_roman[\Carbon\Carbon::now()->format('n')].'/'.\Carbon\Carbon::now()->format('Y')) !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang('labels.membership_type')</td>
                    <td>:</td>
                    <td>{{$member_payment->memberpayment_user->user_memberstatus->memberstatus_membershiptype->membership_type_name}}</td>
                </tr>
                <tr>
                    <td>@lang('labels.cashier')</td>
                    <td>:</td>
                    <td>
                        {!! Form::select('login_user', $staffs, null, ['class' => 'form-control select2', 'placeholder' => trans('labels.choose_staff')]) !!}
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <table>
                <tr>
                    <td>@lang('labels.payment_category')</td>
                    <td>:</td>
                    <td>@lang('labels.first_payment_bill')</td>
                </tr>
                <tr>
                    <td>@lang('labels.date')</td>
                    <td>:</td>
                    <td>
                        {!! Form::date('payment_date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                        {{--{{date('d-m-Y', strtotime(\Carbon\Carbon::now()))}}--}}
                    </td>
                </tr>
                <tr>
                    <td>{!! Form::label('payment_method', trans('labels.payment_method'), ['class' => 'control-label']) !!}</td>
                    <td>:</td>
                    <td>{!! Form::select('payment_method', $payment_methods, null, ['class' => 'form-control', 'placeholder' => '-- '.trans('labels.choose').' '.trans('labels.payment_method').' --']) !!}</td>
                </tr>
                <tr>
                    <td>{!! Form::label('bank', trans('labels.bank'), ['class' => 'control-label']) !!}</td>
                    <td>:</td>
                    <td>
                        <select name="bank" class="form-control select2">
                            <option value="">@lang('labels.choose_bank')</option>
                            @foreach($banks as $bank)
                                <option value="{{$bank->id}}">{{$bank->bank_abv}} - {{$bank->bank_account_number}}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-offset-3 col-md-6">
            <div class="form-horizontal">
                <div class="form-group">
                    {!! Form::label('total_bill', trans('labels.billing_total').': ', ['class' => 'control-label col-md-4']) !!}
                    <div class="col-md-6">
                        {!! Form::text('total_bill', $member_payment->rest_of_bill, ['class' => 'form-control rupiah', 'readonly']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('total_pay', trans('labels.pay').': ', ['class' => 'control-label col-md-4']) !!}
                    <div class="col-md-6">
                        {!! Form::text('total_pay', '0', ['class' => 'form-control rupiah']) !!}
                    </div>
                </div>
                <hr>
                <div class="form-group">
                    {!! Form::label('rest_of_bill', trans('labels.rest_of_the_bill').': ', ['class' => 'control-label col-md-4']) !!}
                    <div class="col-md-6">
                        {!! Form::text('rest_of_bill', '0', ['class' => 'form-control rupiah', 'readonly']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-offset-8 col-md-4">
                        {!! Form::button(trans('labels.process'), ['class' => 'btn btn-success btn-lg pull-right', 'id' => 'form-payment-debt-submit-btn']) !!}
                        {!! Form::button(trans('labels.reprint'), ['class' => 'btn btn-success btn-lg', 'id' => 'print-btn']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@section('other-script')
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2();

            $('.rupiah').each(function () {
                $(this).wrap('<div class="input-group"></div>')
                $(this).before('<span class="input-group-addon">Rp.</span>');
                $(this).after('<span class="input-group-addon">,-</span>');
            });

            $('#total_pay').on('keyup', function () {
                if ($(this).val() == "") {
                    var total_pay = 0;
                } else {
                    var total_pay = parseInt(rupiah_format_destroyer($('#total_pay').val()));
                }
                var rest_of_bill = parseInt(rupiah_format_destroyer($('#total_bill').val())) - parseInt(rupiah_format_destroyer(total_pay));
                $('#rest_of_bill').val(rupiah_formater(rest_of_bill));
                $('#total_pay').val(rupiah_formater(total_pay));
            });

            $('#form-payment-debt-submit-btn').on('click', function () {
//                $('#form-payment-debt').submit();
                var gate = false;
                if ($('#payment_method').val() != '') {
                    if ($('#payment_method').val() > 1 && $('#payment_method').val() < 6)  {
                        if ($('select[name="bank"]').val() != "") {
                            gate = true;
                        }
                    } else {
                        gate = true;
                    }

                    if (gate) {
                        if ($('input[name="total_pay"]').val() != '') {
                            $('.rupiah').each(function () {
                                var x = rupiah_format_destroyer($(this).val());
                                $(this).val((x));
                            });

                            $(this).remove();
                            $('#print-btn').show();

                            javascript:jsWebClientPrint.print(
                                '&payment='+ $('input[name="payment"]').val() +
                                '&payment_number='+ $('input[name="payment_number"]').val() +
                                '&login_user='+ $('select[name="login_user"]').val() +
                                '&payment_date='+ $('input[name="payment_date"]').val() +
                                '&payment_method='+ $('#payment_method').val()+
                                '&bank='+ $('select[name="bank"]').val()+
                                '&total_bill='+ $('input[name="total_bill"]').val()+
                                '&total_pay='+ $('input[name="total_pay"]').val()+
                                '&rest_of_bill='+ $('input[name="rest_of_bill"]').val()+
                                '&check=1'+
                                '&useDefaultPrinter=undefined&printerName=XP-80'+
                                '&id=1'
                            );
                        } else {
                            alert('@lang("messages.please_check_total_payment")');
                        }
                    } else {
                        alert('@lang("messages.please_check_payment_method")');
                    }
                } else {
                    alert('@lang("messages.please_check_payment_method")');
                }
            });

            $('#print-btn').on('click', function () {
                javascript:jsWebClientPrint.print(
                    '&payment='+ $('input[name="payment"]').val() +
                    '&payment_number='+ $('input[name="payment_number"]').val() +
                    '&login_user='+ $('select[name="login_user"]').val() +
                    '&payment_date='+ $('input[name="payment_date"]').val() +
                    '&payment_method='+ $('#payment_method').val()+
                    '&bank='+ $('select[name="bank"]').val()+
                    '&total_bill='+ $('input[name="total_bill"]').val()+
                    '&total_pay='+ $('input[name="total_pay"]').val()+
                    '&rest_of_bill='+ $('input[name="rest_of_bill"]').val()+
                    '&check=1'+
                    '&useDefaultPrinter=undefined&printerName=XP-80'+
                    '&id=0'
                );
            });

            init_format();
        });


        function init_format() {
            $('.rupiah').each(function () {
                var x = rupiah_formater($(this).val().replace(/\s/g, ''));
                $(this).val((x));
            });
        }
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>
    <script type="text/javascript">
        var wcppGetPrintersTimeout_ms = 10000; //10 sec
        var wcppGetPrintersTimeoutStep_ms = 500; //0.5 sec

        function wcpGetPrintersOnSuccess(){
            // Display client installed printers
            if(arguments[0].length > 0){
                var p=arguments[0].split("|");
                var options = '';
                for (var i = 0; i < p.length; i++) {
                    options += '<option>' + p[i] + '</option>';
                }
                $('#installedPrinters').css('visibility','visible');
                $('#installedPrinterName').html(options);
                $('#installedPrinterName').focus();
                $('#loadPrinters').hide();
            }else{
                alert("No printers are installed in your system.");
            }
        }

        function wcpGetPrintersOnFailure() {
            // Do something if printers cannot be got from the client
            alert("No printers are installed in your system.");
        }
    </script>
    {!!

    // Register the WebClientPrint script code
    // The $wcpScript was generated by PrintESCPOSController@index

    $wcpScript;

    !!}
@endsection
