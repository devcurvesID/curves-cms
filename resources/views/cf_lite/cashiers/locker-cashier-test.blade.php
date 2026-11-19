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
            <button type="button" class="btn btn-info btn-lg pull-right" id="print-again" style="width: 200px; height: 100px; display: none;">@lang('labels.reprint')</button>
        </div>
    </div>
    {!! Form::close() !!}
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
                    '&id=0'
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
                                            $('#print-again').show();
                                            $('#payment-process').hide();
                                            $('input[name="pay"]').attr('readonly', 'readonly');
                                        }
                                    } else {
                                        alert('@lang("messages.please_choose_member")');
                                    }
                                }
                            }
                        }
                    });
                } else {
                    if ($('#payment_method').val() != 1 && $('#bank').val() == "") {
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
                                $('#print-again').show();
                                $('#payment-process').hide();
                                $('input[name="pay"]').attr('readonly', 'readonly');
                            }
                        } else {
                            alert('@lang("messages.please_choose_member")');
                        }
                    }
                }
                $('#form-product-payment').submit();
            });

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
