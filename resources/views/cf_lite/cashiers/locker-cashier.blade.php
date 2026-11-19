@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/payment.css')}}" rel="stylesheet">
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <style>
        body{
            color: black;
        }
        .modal-dialog, .modal-dialog label{
            color: #414042;
        }
        table{
            color: #414042;
        }

        #send_receipt {
            display: none;
            height: 50px;
            margin-right: 10px;
            background-color: turquoise !important;
        }

        #SendMailWrapper{
            width: 100%;
            background: rgba(255,255,255, 0.8);
            height: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 9999;
            display: none;
        }
            #SendMailForm{
                width: 400px;
                height: 180px;
                background-color: rgba(255, 240, 147, 1);
                border: 6px solid rgba(255, 20, 147, 1);
                color: rgba(118, 43, 144, 1);
                border-radius: 10px;
                position: absolute; /*Can also be `fixed`*/
                left: 0;
                right: 0;
                top: 0;
                bottom: 0;
                margin: auto;
                /*Solves a problem in which the content is being cut when the div is smaller than its' wrapper:*/
                max-width: 100%;
                max-height: 100%;
                overflow: auto;
                padding: 0 1.5rem;
            }
            .marginRight10{ margin-right: 5px;}

        .modalBtns{
            display: grid;
            grid-template-columns: auto auto ;
        }
        .modalBtns > div {
            text-align: center;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/dashboard')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.locker_payment')</h2>
            </center>
        </div>
    </div>

    @include('cf_lite.includes.error-message')

    {!! Form::open(['method' => 'POST', 'action' => 'LockerController@store_locker_payment_cashier', 'id' => 'form-product-payment']) !!}
    {!! Form::hidden('club_id', Auth::user()->club_id) !!}
    {!! Form::hidden('receipt', null, ['id' => 'receipt']) !!}
    {!! Form::hidden('receiptID', null, ['id' => 'receiptID']) !!}
    <div class="row">
        <div class="col-md-6">
            <table class="member-detail">
                <tr>
                    <td style="color: deeppink; margin-left: 10px; font-weight: bold;">@lang('labels.staff')</td>
                    <td>:</td>
                    <td>
                        {!! Form::select('staff',$staffs,Auth::user()->id,['class'=>'form-control', 'id'=>'staff']) !!}
                    </td>
                </tr>
                <tr>
                    <td style="color: deeppink; margin-left: 10px; font-weight: bold;">@lang('labels.date')</td>
                    <td>:</td>
                    <td>{!! Form::date('payment_date',Carbon\Carbon::now(),['class'=>'form-control']) !!}</td>
                </tr>
            </table>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <table class="member-detail">
                <tr>
                    <td>@lang('labels.payment_number')</td>
                    <td>:</td>
                    <td>
                        @php($month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'))
                        {{sprintf('%08d', $temp_auto_number->current_number)}}/PY/CIND/{{sprintf('%03d', $club_id)}}/{{$month_roman[\Carbon\Carbon::now()->format('n')]}}/{{\Carbon\Carbon::now()->format('Y')}}
                        {!! Form::hidden('payment_number', '/PY/CIND/'.sprintf('%03d', $club_id).'/'.$month_roman[\Carbon\Carbon::now()->format('n')].'/'.\Carbon\Carbon::now()->format('Y')) !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang('labels.member')</td>
                    <td>:</td>
                    <td>
                        {!! Form::select('member',$members,null,['class'=>'form-control', 'id'=>'member', 'placeholder' => '-- '.trans('labels.choose').' '.trans('labels.member').' --']) !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang('labels.locker')</td>
                    <td>:</td>
                    <td>
                        {!! Form::select('locker',$lockers,null,['class'=>'form-control', 'id'=>'locker']) !!}
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="member-detail">
                <tr>
                    <td>{!! Form::label('payment_method', trans('labels.payment_method'), ['class' => 'control-label']) !!}</td>
                    <td>:</td>
                    <td>{!! Form::select('payment_method', $payment_methods, null, ['class' => 'form-control', 'id'=>'payment_method']) !!}</td>
                </tr>
                <tr>
                    <td>@lang('labels.bank')</td>
                    <td>:</td>
                    <td>
                        <select name="bank" class="form-control select2" id="bank">
                            <option value="">@lang('labels.choose') @lang('labels.bank')</option>
                            @foreach($banks as $bank)
                                <option value="{{$bank->id}}">{{$bank->bank_abv}} - {{ $bank->bank_account_number }}</option>
                            @endforeach
                        </select>
                    </td>
                </tr>
                <tr id='start_date_row'>
                    <td>@lang('labels.locker_rent_from_date')</td>
                    <td>:</td>
                    <td>{!! Form::date('start_date',null,['class' => 'form-control','id'=>'input-start-date']) !!}</td>
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
                            <th>@lang('labels.indexing')</th>
                            <th>@lang('labels.name')</th>
                            <th>@lang('labels.price')</th>
                            <th>@lang('labels.quantity')</th>
                            <th>@lang('labels.subtotal')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($index = 1)
                        @php($total = 0)
                        <tr>
                            <td>{{$index}}</td>
                            <td>
                                <select class="form-control" id="packet-select-id" name="packet" style="max-width: 450px;">
                                    <option value="0">-- @lang('labels.choose') @lang('labels.packet') --</option>
                                    @foreach($packets as $p)
                                        <option value="{{$p->id}}" price="{{$p->price}}">{{$p->name}}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td id="each-packet-price"></td>
                            <td>{!! Form::number('amount',1,['class'=>'form-control','id'=>'amount','min'=>'1','style'=>'max-width: 150px;']) !!}</td>
                            <td id="subtotal"></td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: right;">@lang('labels.total'): </td>
                            <td id="total-all">
                            </td>
                            {!! Form::hidden('billingtotal',0) !!}
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: right;">@lang('labels.pay'): </td>
                            <td>
                                {!! Form::text('pay', 0, ['class' => 'form-control rupiah','style'=>'max-width: 250px;']) !!}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: right;">@lang('labels.change'): </td>
                            <td id="change"></td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-success btn-lg pull-right" id="payment-process" style="width: 200px; height: 100px;">@lang('labels.pay')</button>
            <button type="button" class="btn btn-info btn-lg pull-right" id="print-again" style="width: 200px; height: 50px; display: none;">@lang('labels.print')</button>
            <button type="button" class="btn btn-success btn-lg pull-right" id="send_receipt" style="width: 200px; height: 50px;">Send Receipt</button>
        </div>
    </div>
    {!! Form::close() !!}

<!-- MODAL -->
<div id="SendMailWrapper">
    <div id="SendMailForm">
        <h3 align="center">Please enter Member's Email</h3>
        <div style="margin-top: 20px;"">
            {!! Form::text('memberEmail', '', ['class' => 'form-control','id'=>'memberEmail', 'placeholder'=>'Email address']) !!}
            <p id="result"></p>
        </div>
        <div class="modalBtns">
            <div style="text-align: left">
            {!! Form::button('Download', ['class' => 'btn btn-info btn-md', 'id' =>'download-btn']) !!}
            </div>
            <div style="text-align: right">
            {!! Form::button('Cancel', ['class' => 'btn btn-warning btn-md marginRight10', 'id' =>'cancelSendEmail-btn']) !!}

            {!! Form::button('Send Email', ['class' => 'btn btn-success btn-md', 'id' =>'sendEmail-btn']) !!}
            </div>
        </div>
    </div>
</div>
<!-- / MODAL -->

@endsection

@section('other-script')
    <script>
        $(document).ready(function () {
            $('.rupiah').each(function () {
                $(this).wrap('<div class="input-group" style="max-width: 250px;"></div>')
                $(this).before('<span class="input-group-addon">Rp.</span>');
                $(this).after('<span class="input-group-addon">,-</span>');
            });

            init_format();

            $('input[name="pay"]').on('keyup',function($q){
                if($('input[name="pay"]').val() == ''){
                    $('input[name="pay"]').val(0);
                }

                if(parseInt(rupiah_format_destroyer($('input[name="pay"]').val())) - parseInt($('input[name="billingtotal"]').val()) < 0){
                    $('#change').html('Rp. 0');
                }else{
                    $('#change').html('Rp. '+rupiah_formater(parseInt(rupiah_format_destroyer($('input[name="pay"]').val())) - parseInt($('input[name="billingtotal"]').val())));
                }
            });

            $("#packet-select-id").on('change',function($q){
                if($('#packet-select-id').find(':selected').val() == 0){
                    $('#each-packet-price').html("");
                    $('#subtotal').html("");
                }else{
                    $('#each-packet-price').html('Rp. '+rupiah_formater($('#packet-select-id').find(':selected').attr('price')));
                    $('#subtotal').html('Rp. '+rupiah_formater($('#packet-select-id').find(':selected').attr('price') * $('#amount').val()));
                    $('#total-all').html('Rp. '+rupiah_formater($('#packet-select-id').find(':selected').attr('price') * $('#amount').val()));
                    $('input[name="billingtotal"]').val($('#packet-select-id').find(':selected').attr('price') * $('#amount').val());
                    if(parseInt(rupiah_format_destroyer($('input[name="pay"]').val())) - parseInt($('input[name="billingtotal"]').val()) < 0){
                        $('#change').html('Rp. 0');
                    }else{
                        $('#change').html('Rp. '+rupiah_formater(parseInt(rupiah_format_destroyer($('input[name="pay"]').val())) - parseInt($('input[name="billingtotal"]').val())));
                    }
                }
            });

            $('#amount').on('change',function($q){
                if($('#amount').val() == ''){
                    $('#amount').val(0);
                }else{
                    $('#amount').val(parseInt($('#amount').val()));
                }

                if($('#packet-select-id').find(':selected').val() == 0){
                    $('#subtotal').html("");
                    $('#change').html("");
                    $('#total-all').html("");
                }else{
                    $('#subtotal').html('Rp. '+rupiah_formater($('#packet-select-id').find(':selected').attr('price') * $('#amount').val()));
                    $('#total-all').html('Rp. '+rupiah_formater($('#packet-select-id').find(':selected').attr('price') * $('#amount').val()));
                    $('input[name="billingtotal"]').val($('#packet-select-id').find(':selected').attr('price') * $('#amount').val());
                    if(parseInt(rupiah_format_destroyer($('input[name="pay"]').val())) - parseInt($('input[name="billingtotal"]').val()) < 0){
                        $('#change').html('Rp. 0');
                    }else{
                        $('#change').html('Rp. '+rupiah_formater(parseInt(rupiah_format_destroyer($('input[name="pay"]').val())) - parseInt($('input[name="billingtotal"]').val())));
                    }

                }
            });

            $('#amount').on('keyup',function($q){
                if($('#amount').val() == ''){
                    $('#amount').val(0);
                }else{
                    $('#amount').val(parseInt($('#amount').val()));
                }

                if($('#packet-select-id').find(':selected').val() == 0){
                    $('#subtotal').html("");
                    $('#change').html("");
                    $('#total-all').html("");
                }else{
                    $('#subtotal').html('Rp. '+rupiah_formater($('#packet-select-id').find(':selected').attr('price') * $('#amount').val()));
                    $('#total-all').html('Rp. '+rupiah_formater($('#packet-select-id').find(':selected').attr('price') * $('#amount').val()));
                    $('input[name="billingtotal"]').val($('#packet-select-id').find(':selected').attr('price') * $('#amount').val());
                    if(parseInt(rupiah_format_destroyer($('input[name="pay"]').val())) - parseInt($('input[name="billingtotal"]').val()) < 0){
                        $('#change').html('Rp. 0');
                    }else{
                        $('#change').html('Rp. '+rupiah_formater(parseInt(rupiah_format_destroyer($('input[name="pay"]').val())) - parseInt($('input[name="billingtotal"]').val())));
                    }
                }
            });
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

        $('input[name="pay"]').on('keyup', function () {
            if ($(this).val() == "") {
                var total_pay = 0;
            } else {
                var total_pay = parseInt(rupiah_format_destroyer($('input[name="pay"]').val()));
            }
            $('input[name="pay"]').val(rupiah_formater(total_pay));
        });
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>

    <script src={{asset("bootbox/bootbox.min.js")}}></script>
    <script>
        $(document).ready(function () {
            var url = "{{url('cf-lite')}}/";

            $('#member').on('change',function(){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var e = document.getElementById('member');

                var formData = {
                    selected_user: e.options[e.selectedIndex].value,
                };

                var type = 'POST';
                var my_url = url + 'cashier/get-locker-list-ajax';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        $('#locker').empty();
                        data.sort(function(a,b){
                            return b.locker_name_edited.length - a.locker_name_edited.length;
                        });
                        var html = '<option value="">@lang('labels.choose_locker')</option>'
                        $.each(data, function(key, value) {
                            html += '<option value="'+value.id+'">'+value.locker_name_edited+'</option>'
                            // html += $('#locker').append($("<option></option>").attr("value", value.id).text(value.locker_name_edited)); // name refers to the objects value when you do you ->lists('name', 'id') in laravel
                        });
                        $('#locker').html(html);
                        $('#locker').select2(); //reload the list and select the first option
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            }).trigger('change');

            $('#locker').on('change', function () {
                var temp = $('#locker').select().find('option:selected').text();
                var strsplit = temp.split(' ');
                var tgl = strsplit[2];

                var dt = new Date(tgl);
                var start = dt.setDate(dt.getDate() + 1);

                var d = new Date(start),
                    month = '' + (d.getMonth() + 1),
                    day = '' + d.getDate(),
                    year = d.getFullYear();

                if (month.length < 2)
                    month = '0' + month;
                if (day.length < 2)
                    day = '0' + day;

                var final = [year, month, day].join('-');
                $('#input-start-date').val(final);
            });

            $('#print-again').on('click',function(){
                javascript:jsWebClientPrint.print(
                    'club_id='+ $('input[name="club_id"]').val()+
                    '&staff='+ $('#staff').val()+
                    '&payment_date='+ $('input[name="payment_date"]').val()+
                    '&member='+ $('#member').val()+
                    '&locker='+ $('#locker').val()+
                    '&payment_method='+ $('#payment_method').val()+
                    '&bank='+ $('#bank').val()+
                    '&start_date='+ $('input[name="start_date"]').val()+
                    '&packet='+ $('#packet-select-id').val()+
                    '&amount='+ $('#amount').val()+
                    '&billingtotal='+ $('input[name="billingtotal"]').val()+
                    '&pay='+ $('input[name="pay"]').val()+
                    '&useDefaultPrinter=undefined&printerName=XP-80'+
                    '&id=0' +
                    '&axn=print'
                );
            });

            $(document).on('click', '#payment-process', function () {
                if (rupiah_format_destroyer($('input[name="pay"]').val()) == 0) {
                    bootbox.confirm({
                        message: "@lang('messages.are_you_sure')",
                        buttons: {
                            confirm: {
                                label: '@lang('labels.yes')',
                                className: 'btn-success'
                            },
                            cancel: {
                                label: '@lang('labels.no')',
                                className: 'btn-danger'
                            }
                        },
                        callback: function (result) {
                            if (result == false) {
                            } else {
                                if ($('#payment_method').val() > 1 && $('#payment_method').val() < 6 && $('#bank').val() == "") {
                                    alert('@lang('messages.please_choose_bank')');
                                } else if($('#payment_method').val() == 1 && $('#bank').val() != ""){
                                    alert('@lang('messages.please_check_payment_method')');
                                } else if(!$('#input-start-date').val()){
                                    alert('@lang('messages.please_choose_locker_rent_from_date')');
                                }else{
                                    if ($('#member').val() != "") {
                                        if (parseInt(rupiah_format_destroyer($('input[name="pay"]').val())) < parseInt(rupiah_format_destroyer($('input[name="billing-total"]').val()))) {
                                            alert('@lang('messages.not_enough_money')');
                                        } else {
                                            var pay = parseInt(rupiah_format_destroyer($('input[name="pay"]').val()));
                                            var billing = parseInt(rupiah_format_destroyer($('input[name="billing-total"]').val()));
                                            $('input[name="pay"]').val(pay);
                                            $('input[name="billing-total"]').val(billing);

                                            $('#payment-process').text('Processing...').attr('disabled', true);

                                            $printURL = "{{ url('PrintESCPOSLockerCashier?clientPrint') }}" + '&club_id='+ $('input[name="club_id"]').val()+
                                                '&staff='+ $('#staff').val()+
                                                '&payment_date='+ $('input[name="payment_date"]').val()+
                                                '&member='+ $('#member').val()+
                                                '&locker='+ $('#locker').val()+
                                                '&payment_method='+ $('#payment_method').val()+
                                                '&bank='+ $('#bank').val()+
                                                '&start_date='+ $('input[name="start_date"]').val()+
                                                '&packet='+ $('#packet-select-id').val()+
                                                '&amount='+ $('#amount').val()+
                                                '&billingtotal='+ $('input[name="billingtotal"]').val()+
                                                '&pay='+ $('input[name="pay"]').val()+
                                                '&useDefaultPrinter=undefined&printerName=XP-80'+
                                                '&id=1';

                                                $.ajax({
                                                    url: $printURL,
                                                    type: 'GET',
                                                    success: function(res) {
                                                        var result = JSON.parse(res);;
                                                        $('#receipt').val(result.pdf);
                                                        $('#receiptID').val(result.receiptID);
                                                        $('#send_receipt').show();

                                                        $('#print-again').show();
                                                        $('#payment-process').hide();
                                                        $('input[name="pay"]').attr('readonly', 'readonly');

                                                    }
                                                });

                                            /* javascript:jsWebClientPrint.print(
                                                'club_id='+ $('input[name="club_id"]').val()+
                                                '&staff='+ $('#staff').val()+
                                                '&payment_date='+ $('input[name="payment_date"]').val()+
                                                '&member='+ $('#member').val()+
                                                '&locker='+ $('#locker').val()+
                                                '&payment_method='+ $('#payment_method').val()+
                                                '&bank='+ $('#bank').val()+
                                                '&start_date='+ $('input[name="start_date"]').val()+
                                                '&packet='+ $('#packet-select-id').val()+
                                                '&amount='+ $('#amount').val()+
                                                '&billingtotal='+ $('input[name="billingtotal"]').val()+
                                                '&pay='+ $('input[name="pay"]').val()+
                                                '&useDefaultPrinter=undefined&printerName=XP-80'+
                                                '&id=1'
                                            ); */

                                        }
                                    } else {
                                        alert('@lang("messages.please_choose_member")');
                                    }
                                }
                            }
                        }
                    });
                } else {
                    if ($('#payment_method').val() > 1 && $('#payment_method').val() < 6 && $('#bank').val() == "") {
                        alert('@lang('messages.please_choose_bank')');
                    } else if($('#payment_method').val() == 1 && $('#bank').val() != ""){
                        alert('@lang('messages.please_check_payment_method')');
                    } else if(!$('#input-start-date').val()){
                        alert('@lang('messages.please_choose_locker_rent_from_date')');
                    }else{
                        if ($('#member').val() != "") {
                            if (parseInt(rupiah_format_destroyer($('input[name="pay"]').val())) < parseInt(rupiah_format_destroyer($('input[name="billing-total"]').val()))) {
                                alert('@lang('messages.not_enough_money')');
                            } else {
                                var pay = parseInt(rupiah_format_destroyer($('input[name="pay"]').val()));
                                var billing = parseInt(rupiah_format_destroyer($('input[name="billing-total"]').val()));
                                $('input[name="pay"]').val(pay);
                                $('input[name="billing-total"]').val(billing);

                                $('#payment-process').text('Processing...').attr('disabled', true);

                                $printURL = "{{ url('PrintESCPOSLockerCashier?clientPrint') }}" + '&club_id='+ $('input[name="club_id"]').val()+
                                    '&staff='+ $('#staff').val()+
                                    '&payment_date='+ $('input[name="payment_date"]').val()+
                                    '&member='+ $('#member').val()+
                                    '&locker='+ $('#locker').val()+
                                    '&payment_method='+ $('#payment_method').val()+
                                    '&bank='+ $('#bank').val()+
                                    '&start_date='+ $('input[name="start_date"]').val()+
                                    '&packet='+ $('#packet-select-id').val()+
                                    '&amount='+ $('#amount').val()+
                                    '&billingtotal='+ $('input[name="billingtotal"]').val()+
                                    '&pay='+ $('input[name="pay"]').val()+
                                    '&useDefaultPrinter=undefined&printerName=XP-80'+
                                    '&id=1';

                                $.ajax({
                                    url: $printURL,
                                    type: 'GET',
                                    success: function(res) {
                                        var result = JSON.parse(res);;
                                        $('#receipt').val(result.pdf);
                                        $('#receiptID').val(result.receiptID);
                                        $('#send_receipt').show();

                                        $('#print-again').show();
                                        $('#payment-process').hide();
                                        $('input[name="pay"]').attr('readonly', 'readonly');

                                    }
                                });

                                /* javascript:jsWebClientPrint.print(
                                    'club_id='+ $('input[name="club_id"]').val()+
                                    '&staff='+ $('#staff').val()+
                                    '&payment_date='+ $('input[name="payment_date"]').val()+
                                    '&member='+ $('#member').val()+
                                    '&locker='+ $('#locker').val()+
                                    '&payment_method='+ $('#payment_method').val()+
                                    '&bank='+ $('#bank').val()+
                                    '&start_date='+ $('input[name="start_date"]').val()+
                                    '&packet='+ $('#packet-select-id').val()+
                                    '&amount='+ $('#amount').val()+
                                    '&billingtotal='+ $('input[name="billingtotal"]').val()+
                                    '&pay='+ $('input[name="pay"]').val()+
                                    '&useDefaultPrinter=undefined&printerName=XP-80'+
                                    '&id=1'
                                );
                                $('#print-again').show();
                                $('#payment-process').hide();
                                $('input[name="pay"]').attr('readonly', 'readonly'); */
                            }
                        } else {
                            alert('@lang("messages.please_choose_member")');
                        }
                    }
                }
            });

            /* PDF SENDING & VALIDATION */

            /* $('#print-again').show();
            $('#send_receipt').show();
            $('#payment-process').remove(); */

            $('#send_receipt').on('click', function () {
                $('#SendMailWrapper').css('display', 'block')
                $('#memberEmail').val('');
                $('#cancelSendEmail-btn').text('Cancel');
            });

            $('#cancelSendEmail-btn').on('click', function () {
                $('#SendMailWrapper').css('display', 'none');
                $('#memberEmail').val('');
                $('#cancelSendEmail-btn').text('Cancel');
            });

            $('#sendEmail-btn').on('click', function () {
                if($('#memberEmail').val() != ''){
                    $(this).text('Sending...').attr('disabled', true)
                    $printURL = "{{ url('cf-lite/sendpdf') }}/" + $('#receipt').val() + '/' + $('#memberEmail').val()  + '/' + $('#receiptID').val() + '/' + $('#member').val() + '/locker';

                    $.ajax({
                        url: $printURL,
                        type: 'GET',
                        success: function(res) {
                           console.log('result: ', res)
                           $('#cancelSendEmail-btn').text('Close');
                           $('#sendEmail-btn').text('Send Email').removeAttr('disabled');
                        }
                    });
                }else{
                    alert('No valid email address')
                }
            });

            $('#download-btn').on('click', function () {
                if($('#receipt').val() != ''){
                    $printURL = "{{ url('cf-lite/downloadpdf') }}/" + $('#receipt').val();
                    $('#cancelSendEmail-btn').text('Close');

                    var win =  window.open($printURL, '_blank');
                    if (win) {
                        //Browser has allowed it to be opened
                        win.focus();
                    } else {
                        //Browser has blocked it
                        alert('Please allow popups for this website');
                    }
                }else{
                    console.log('no file to download')
                }
            });
            const validateEmail = (email) => {
                return email.match(
                    /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                );
            };

            const validate = () => {
                const $result = $('#result');
                const email = $('#memberEmail').val();
                $result.text('');

                if(validateEmail(email)){
                    //$result.text(email + ' is valid.');
                    //$result.css('color', 'green');
                } else{
                    $result.text(email + ' is invalid.');
                    $result.css('color', 'red');
                }
                return false;
            }

            $('#memberEmail').on('input', validate);
            /* PDF SENDING & VALIDATION */

        });
    </script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script type="text/javascript">
        $('select[name="locker"]').select2();
        $('select[name="member"]').select2();
        $('select[name="staff"]').select2();
    </script>

    {!!

    // Register the WebClientPrint script code
    // The $wcpScript was generated by PrintESCPOSController@index

    $wcpScript;

    !!}

@endsection
