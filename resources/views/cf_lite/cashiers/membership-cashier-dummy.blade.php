@extends('cf_lite.layouts.staff-template')

@section('other-style')
<link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
<link href="{{asset('css/payment.css')}}" rel="stylesheet">
<style>
    /*body{*/
    /*color: white;*/
    /*}*/
    .modal-dialog,
    .modal-dialog label {
        color: #414042;
    }

    table {
        color: #414042;
    }

    .vip-box,
    .vip-box-gold {
        width: 400px;
        height: 100px;
        padding: 5px;
        padding-left: 15px;
        padding-right: 15px;
        border: 2px solid deeppink;
        border-radius: 7px;
        color: deeppink;
    }

    .vip-box-gold {
        border-color: gold;
        color: darkgoldenrod;
    }

    #print-btn {
        display: none;
        width: 100% !important;
        height: 50px;
        margin-bottom: 10px;
        background-color: turquoise !important;
    }

    #reprint-customer {
        display: none;
        width: 100% !important;
        height: 50px;
    }

    #reprint-club {
        display: none;
        width: 100% !important;
        height: 50px;
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
        <center>
            <h2 class="page-title">@lang('labels.membership_payment')</h2>
        </center>
    </div>
</div>

@include('cf_lite.includes.error-message')

{!! Form::open(['action' => 'PaymentController@store_cashier_membership', 'method' => 'POST', 'id' => 'form-payment'])
!!}
{!! Form::hidden('sf_amount', null) !!}
{!! Form::hidden('mf_amount', null) !!}
{!! Form::hidden('club_id', Auth::user()->club_id) !!}
{!! Form::hidden('login_user', Auth::user()->id) !!}
{!! Form::hidden('receipt', null, ['id' => 'receipt']) !!}
{!! Form::hidden('receiptID', null, ['id' => 'receiptID']) !!}
<div class="row">
    <div class="col-md-6">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('fa_coach', trans('labels.fa_coach').': ', ['class' => 'control-label col-md-4', 'style'
                => 'color: deeppink; margin-left: 10px;']) !!}
                <div class="col-md-6">
                    @if(!empty(Session::get('staff_session')))
                    {!! Form::select('fa_coach', $staffs, Session::get('staff_session'), ['class' => 'form-control
                    select2']) !!}
                    @else
                    {!! Form::select('fa_coach', $staffs, Auth::id(), ['class' => 'form-control select2']) !!}
                    @endif
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('fa_date', trans('labels.date').': ', ['class' => 'control-label col-md-4', 'style' =>
                'color: deeppink; margin-left: 10px;']) !!}
                <div class="col-md-6">
                    {!! Form::date('fa_date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                </div>
            </div>
            {{--Added 19032020--}}
            <div class="form-group">
                {!! Form::label('bonus_duration', trans('labels.bonus_duration').': ', ['class' => 'control-label
                col-md-4', 'style' => 'color: deeppink; margin-left: 10px;']) !!}
                <div class="col-md-3">
                    {!! Form::number('bonus_duration', 0, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::select('bonus_type', ['day' => trans('labels.day'), 'week' => trans('labels.week'),
                    'month' => trans('labels.month')], null, ['class' => 'form-control']) !!}
                </div>
            </div>
            {{--Added 19032020--}}
        </div>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-6">
        <table class="member-detail">
            <tr>
                <td>{!! Form::label('lead', trans('labels.lead'), ['class' => 'control-label']) !!}</td>
                <td>:</td>
                <td>
                    <select name="lead" class="form-control select2" id="lead">
                        <option value="">@lang('labels.choose') @lang('labels.lead')</option>
                        @foreach($brls as $brl)
                        @if(empty($selected_brl))
                        <option value="{{$brl->id}}" nm="{{ $brl->fullname }}" ph="{{ $brl->phone }}"
                            so="{{ $brl->source_id }}">{{ $brl->fullname }}</option>
                        @else
                        @if($brl->id == $selected_brl->id)
                        <option value="{{$brl->id}}" nm="{{ $brl->fullname }}" ph="{{ $brl->phone }}"
                            so="{{ $brl->source_id }}" selected>{{ $brl->fullname }}</option>
                        @else
                        <option value="{{$brl->id}}" nm="{{ $brl->fullname }}" ph="{{ $brl->phone }}"
                            so="{{ $brl->source_id }}">{{ $brl->fullname }}</option>
                        @endif
                        @endif
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td>{!! Form::label('premember_name', trans('labels.premember_name'), ['class' => 'control-label']) !!}
                </td>
                <td>:</td>
                @if(empty($selected_brl))
                <td>{!! Form::text('premember_name', null, ['class' => 'form-control']) !!}</td>
                @else
                <td>{!! Form::text('premember_name', $selected_brl->fullname, ['class' => 'form-control','readonly' =>
                    'readonly']) !!}</td>
                @endif
            </tr>
            <tr>
                <td>{!! Form::label('phone', trans('labels.phone'), ['class' => 'control-label']) !!}</td>
                <td>:</td>
                @if(empty($selected_brl))
                <td>{!! Form::text('phone', null, ['class' => 'form-control']) !!}</td>
                @else
                <td>{!! Form::text('phone', $selected_brl->phone, ['class' => 'form-control','readonly' => 'readonly'])
                    !!}</td>
                @endif
            </tr>
            <tr>
                <td>{!! Form::label('source', trans('labels.source'), ['class' => 'control-label']) !!}</td>
                <td>:</td>
                @if(empty($selected_brl))
                <td>{!! Form::select('source', $sources, null, ['class' => 'form-control select2', 'id' =>
                    'source','placeholder' => trans('labels.choose')]) !!}</td>
                @else
                <td>{!! Form::select('source', $sources, $selected_brl->source_id, ['class' => 'form-control select2',
                    'id' => 'source','disabled'=>'disabled','placeholder' => trans('labels.choose')]) !!}</td>
                @endif
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <table class="member-detail">
            <tr>
                <td>{!! Form::label('membership_type', trans('labels.membership_type'), ['class' => 'control-label'])
                    !!}</td>
                <td>:</td>
                <td>
                    <select name="membership_type" class="form-control select2" id="membership_type">
                        <option value="">@lang('labels.choose_membership')</option>
                        @foreach($membership_types as $membership_type)
                        <option value="{{$membership_type->id}}" sf="{{$membership_type->sf_amount}}"
                            mf="{{$membership_type->mf_amount}}" dc="{{$membership_type->pre_pay_discount_value}}"
                            dur="{{$membership_type->duration}}">{{$membership_type->membership_type_name}}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td>{!! Form::label('payment_method', trans('labels.payment_method'), ['class' => 'control-label']) !!}
                </td>
                <td>:</td>
                <td>{!! Form::select('payment_method', $payment_methods, null, ['class' => 'form-control']) !!}</td>
            </tr>
            <tr>
                <td>{!! Form::label('bank', trans('labels.bank'), ['class' => 'control-label']) !!}</td>
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
            <tr id="referral-detail">

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
                        <th colspan="3">@lang('labels.payment')</th>
                    </tr>
                    <tr>
                        <th>@lang('labels.type')</th>
                        <th colspan="2">@lang('labels.fee')</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>@lang('labels.service_fee')</td>
                        <td colspan="2" class="my_rupiah" id="sf-amount-txt">0</td>
                    </tr>
                    <tr>
                        <td>@lang('labels.monthly_fee')</td>
                        <td colspan="2" class="my_rupiah" id="mf-amount-txt">0</td>
                    </tr>
                    <tr>
                        <td>@lang('labels.discount')</td>
                        <td colspan="2">
                            <div class="input-group" style="max-width: 250px;">
                                <span class="input-group-addon">Rp.</span>
                                {!! Form::text('discount', null, ['class' => 'form-control rupiah']) !!}
                                <span class="input-group-addon">,-</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('labels.deposit')</td>
                        {{-- <td>
                            {!! Form::select('depositorvouch',['deposit' => trans('labels.deposit'), 'voucher' =>
                            trans('labels.voucher')]) !!}
                        </td> --}}
                        <td colspan="2">
                            <div class="input-group" style="max-width: 250px;">
                                <span class="input-group-addon">Rp.</span>
                                {!! Form::text('deposit', 0, ['class' => 'form-control rupiah','id'=>'deposit']) !!}
                                <span class="input-group-addon">,-</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('labels.total')</td>
                        <td colspan="2">
                            <div class="input-group" style="max-width: 250px;">
                                <span class="input-group-addon">Rp.</span>
                                {!! Form::text('total', null, ['class' => 'form-control rupiah', 'readonly']) !!}
                                <span class="input-group-addon">,-</span>
                            </div>
                        </td>
                    </tr>
                    <tr id="first_click">
                        <td>@lang('labels.pay')</td>
                        <td>
                            <div class="input-group" style="max-width: 250px;">
                                <span class="input-group-addon">Rp.</span>
                                {!! Form::text('pay', 0, ['class' => 'form-control rupiah', 'id' => 'pay']) !!}
                                <span class="input-group-addon">,-</span>
                            </div>
                        </td>
                        <td rowspan="2">
                            {!! Form::button(trans('labels.pay'), ['class' => 'btn btn-success btn-lg', 'id' =>
                            'pay-btn']) !!}

                            {!! Form::button('Send Receipt', ['class' => 'btn btn-success btn-lg', 'id' =>
                            'print-btn']) !!}
                            {{-- {!! Form::button(trans('labels.reprint'), ['class' => 'btn btn-success btn-lg', 'id' =>
                            'print-btn']) !!} --}}
                        </td>
                    </tr>
                    <tr id="after_click">
                        <td>@lang('labels.rest_of_the_bill')</td>
                        <td class="my_rupiah" id="rest-of-bill-txt">0</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
{!! Form::close() !!}

<!-- MODAL -->
<div id="SendMailWrapper">
    <div id="SendMailForm">
        <h3 align="center">Please enter Member's Email</h3>
        <div style="margin-top: 20px;"">
            {!! Form::text('memberEmail', '', ['class' => 'form-control rupiah','id'=>'memberEmail', 'placeholder'=>'Email address']) !!}
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
<script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
<script>
    $(document).ready(function () {

            $('.select2').select2();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('select[name="membership_type"]').on('change', function () {
                var mfdur = parseInt($(this).find(':selected').attr('mf')) * parseInt($(this).find(':selected').attr('dur'));
                var total = parseInt($(this).find(':selected').attr('sf')) + parseInt(mfdur) - parseInt($(this).find(':selected').attr('dc'));
                var rob = parseInt(total) - parseInt(rupiah_format_destroyer($('input[name="pay"]').val()));

                $('input[name="sf_amount"]').val($(this).find(':selected').attr('sf'));
                $('input[name="mf_amount"]').val(mfdur);
                $('input[name="discount"]').val(rupiah_formater($(this).find(':selected').attr('dc')));

                $('#sf-amount-txt').text('Rp. ' + rupiah_formater($(this).find(':selected').attr('sf')) + ',-');
                $('#mf-amount-txt').text('Rp. ' + rupiah_formater(mfdur) + ',-');
                $('input[name="total"]').val(rupiah_formater(total));
                $('#rest-of-bill-txt').text('Rp. ' + rupiah_formater(rob) + ',-');
            });

            $('select[name="lead"]').on('change', function () {
                var name = $(this).find(':selected').attr('nm');
                var phone = $(this).find(':selected').attr('ph');
                var source = $(this).find(':selected').attr('so');
                var id = $(this).val();

                $('input[name="premember_name"]').val(name);
                $('input[name="phone"]').val(phone);
                $('#source').val(source).trigger('change');

                if (id != "") {
                    $('input[name="premember_name"]').prop('readonly', true);
                    $('input[name="phone"]').prop('readonly', true);
                    $('#source').attr('disabled', true);
                } else {
                    $('input[name="premember_name"]').prop('readonly', false);
                    $('input[name="phone"]').prop('readonly', false);
                    $('#source').attr('disabled', false);
                }
            });


            $('select[name="source"]').on('change', function () {
                var selected = $(this).find(':selected').text();
                var referral_field = $('#referral-detail');

                if(selected === 'BR'){
                    referral_field.html(`
                        <td>{!! Form::label('source_detail') !!}</td>
                        <td>:</td>
                        <td>
                            {!! Form::select('source_detail',[],null,['class' => 'form-control select3','required' => 'true']) !!}
                        </td>
                    `);

                    $(".select3").select2({
                        ajax: {
                            delay: 250,
                            method: "POST",
                            url: "{{ url('cf-lite/cashier/membership/get-user/ajax') }}",
                            data: function (params) {
                                var queryParameters = {
                                    q: params.term
                                }

                                return queryParameters;
                            },
                            processResults: function (data, params) {
                                return{
                                    results: $.map(data.users, function(obj){
                                        return {
                                            id: obj.id, text: obj.name
                                        };
                                    })
                                };
                            },
                        }
                    });
                }else if (['Instagram','Facebook','Papan Nama'].includes(selected)){
                    referral_field.html(`
                        <td>{!! Form::label('source_detail') !!}</td>
                        <td>:</td>
                        <td>
                            {!! Form::select('source_detail', [], null, ['class' => 'form-control select3','required' => 'true']) !!}
                        </td>
                    `);

                    $(".select3").select2({
                        ajax: {
                            delay: 250,
                            method: "POST",
                            url: "{{ url('cf-lite/cashier/membership/get-clubs/ajax') }}",
                            data: function (params) {
                                var queryParameters = {
                                    q: params.term
                                }

                                return queryParameters;
                            },
                            processResults: function (data, params) {
                                return{
                                    results: $.map(data.clubs, function(obj){
                                        return {
                                            id: obj.club_name, text: obj.club_name
                                        };
                                    })
                                };
                            },
                        }
                    });
                }else if (['Brosur Langsung','Leadbox','Guest Pass (Eksternal)','Guest Pass (Internal)'].includes(selected)){
                    referral_field.html(`
                        <td>{!! Form::label('source_detail') !!}</td>
                        <td>:</td>
                        <td>{!! Form::text('source_detail',null,['class' => 'form-control','placeholder' => trans('labels.location'),'required' => 'true','onfocus' => 'geolocate()']) !!}</td>
                    `);
                    initAutocomplete();
                }else{
                    referral_field.html(``);
                }
            });

            $('input[name="discount"]').on('keyup', function () {
                if ($(this).val() == "") {
                    var discount = 0;
                } else {
                    var discount = parseInt(rupiah_format_destroyer($(this).val()));
                }

                var total = parseInt($('input[name="sf_amount"]').val()) + parseInt($('input[name="mf_amount"]').val()) - parseInt(discount) - parseInt(rupiah_format_destroyer($('input[name="deposit"]').val()));
                var rob = parseInt(total) - parseInt(rupiah_format_destroyer($('input[name="pay"]').val()));
                $('input[name="discount"]').val(rupiah_formater(discount));
                $('input[name="total"]').val(rupiah_formater(total));
                $('#rest-of-bill-txt').text('Rp. ' + rupiah_formater(rob) + ',-');
            });

            $('input[name="deposit"]').on('keyup', function () {
                if ($(this).val() == "") {
                    var deposit = 0;
                } else {
                    var deposit = parseInt(rupiah_format_destroyer($(this).val()));
                }

                var total = parseInt($('input[name="sf_amount"]').val()) + parseInt($('input[name="mf_amount"]').val()) - parseInt(rupiah_format_destroyer($('input[name="discount"]').val())) - parseInt(deposit);
                var rob = parseInt(total)-parseInt(rupiah_format_destroyer($('input[name="pay"]').val()));
                $('input[name="deposit"]').val(rupiah_formater(deposit));
                $('input[name="total"]').val(rupiah_formater(total));
                $('#rest-of-bill-txt').text('Rp. ' + rupiah_formater(rob) + ',-');
            });

            $('input[name="pay"]').on('keyup', function () {
                if ($(this).val() == "") {
                    var pay = 0;
                } else {
                    var pay = parseInt(rupiah_format_destroyer($(this).val()));
                }

                var rob = parseInt(rupiah_format_destroyer($('input[name="total"]').val())) - parseInt(pay);
                $('input[name="pay"]').val(rupiah_formater(pay));
                $('#rest-of-bill-txt').text('Rp. ' + rupiah_formater(rob) + ',-');
            });

            /* PDF SENDING & VALIDATION */
            $('#cancelSendEmail-btn').on('click', function () {
                $('#SendMailWrapper').css('display', 'none');
                $('#memberEmail').val('');
                $('#cancelSendEmail-btn').text('Cancel');
            });

            $('#sendEmail-btn').on('click', function () {
                if($('#memberEmail').val() != ''){
                    $printURL = "{{ url('cf-lite/sendpdf') }}/" + $('#receipt').val() + '/' + $('#memberEmail').val()  + '/' + $('#receiptID').val() + '/' + $('#premember_name').val();

                    $.ajax({
                        url: $printURL,
                        type: 'GET',
                        success: function(res) {
                           console.log('result: ', res)
                           $('#cancelSendEmail-btn').text('Close');
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

            $('#pay-btn').on('click', function () {
                var vip = $('select[name="membership_type"] option:selected').val();
                if ($('#payment_method').val() != 1 && $('#bank').val() == "") {
                    alert('@lang('messages.please_choose_bank')');
                } else if ($('#payment_method').val() == 1 && $('#bank').val() != "") {
                    alert('@lang('messages.please_check_payment_method')');
                } else {
                    if ($('#membership_type').val() != "" && $('input[name="fa_date"]').val() != "") {
                        if ($('#premember_name').val() != "" && $('#phone').val() != "") {
                            $('input[name="discount"]').val(rupiah_format_destroyer($('input[name="discount"]').val()));
                            $('input[name="deposit"]').val(rupiah_format_destroyer($('input[name="deposit"]').val()));
                            $('input[name="total"]').val(rupiah_format_destroyer($('input[name="total"]').val()));
                            $('input[name="pay"]').val(rupiah_format_destroyer($('input[name="pay"]').val()));
                            $('#pay').val(rupiah_format_destroyer($('#pay').val()));
                            $('#deposit').val(rupiah_format_destroyer($('#deposit').val()));

                            if ($('#pay').val() > 0 || vip == 10) {
                                //$(this).remove();
                                $(this).text('Processing...').attr('disabled', true)


                                @if($user->user_club->digital_product)
                                    console.log('D1');
                                    // Remove last element
                                    var get_last_element = $('#first_click').children()[2];
                                    $( get_last_element ).remove();
                                    $('#first_click').append(`<td>{!! Form::button('Send Receipt', ['class' => 'btn btn-success btn-md', 'id' => 'print-btn']) !!} {!! Form::button(trans('labels.reprint_customer'), ['class' => 'btn btn-warning btn-md', 'id' => 'reprint-customer']) !!} </td>`);

                                    $('#after_click').append(`<td>{!! Form::button(trans('labels.reprint_club'), ['class' => 'btn btn-success btn-md', 'id' => 'reprint-club', 'style' => 'background-color: rebeccapurple']) !!}</td>`);

                                    //$('#reprint-club').show();
                                    //$('#reprint-customer').show();
                                    //$('#print-btn').show();
                                @else
                                    console.log('D2');
                                    //$('#print-btn').show();

                                    var get_last_element = $('#first_click').children()[2];
                                    $( get_last_element ).remove();
                                    $('#first_click').append(`<td>{!! Form::button('Send Receipt', ['class' => 'btn btn-success btn-md', 'id' => 'print-btn']) !!} {!! Form::button(trans('labels.reprint_customer'), ['class' => 'btn btn-warning btn-md', 'id' => 'reprint-customer']) !!}</td>`);

                                    $('#after_click').append(`<td>{!! Form::button(trans('labels.reprint_club'), ['class' => 'btn btn-success btn-md', 'id' => 'reprint-club', 'style' => 'background-color: rebeccapurple']) !!}</td>`);

                                    //$('#reprint-club').show();
                                    //$('#reprint-customer').show();
                                    //$('#print-btn').show();

                                @endif

                                $('input[name="premember_name"]').attr('readonly', 'readonly');
                                $('input[name="phone"]').attr('readonly', 'readonly');
                                $('input[name="discount"]').attr('readonly', 'readonly');
                                $('input[name="deposit"]').attr('readonly', 'readonly');
                                $('#pay').attr('readonly', 'readonly');
                                $('input[name="bonus_duration"]').attr('readonly', 'readonly');

                                $printURL = "{{ url('PrintESCPOSMembershipCashierDummy?clientPrint') }}" + '&club_id=' + $('input[name="club_id"]').val() +
                                    '&login_user=' + $('input[name="login_user"]').val() +
                                    '&digital_product_check=' + `{{$user->user_club->digital_product? 'true':'false'}}` +
                                    '&lead=' + $('#lead').val() +
                                    '&fa_coach=' + $('#fa_coach').val() +
                                    '&fa_date=' + $('input[name="fa_date"]').val() +
                                    '&premember_name=' + $('input[name="premember_name"]').val() +
                                    '&phone=' + $('input[name="phone"]').val() +
                                    '&source=' + $('#source').val() +
                                    '&source_detail=' + $('#source_detail').val() +
                                    '&membership_type=' + $('#membership_type').val() +
                                    '&payment_method=' + $('#payment_method').val() +
                                    '&bank=' + $('#bank').val() +
                                    '&sf_amount=' + $('input[name="sf_amount"]').val() +
                                    '&mf_amount=' + $('input[name="mf_amount"]').val() +
                                    '&discount=' + $('input[name="discount"]').val() +
                                    '&deposit=' + $('input[name="deposit"]').val() +
                                    '&total=' + $('input[name="total"]').val() +
                                    '&pay=' + $('input[name="pay"]').val() +
                                    '&bonus_duration=' + $('input[name="bonus_duration"]').val() +
                                    '&bonus_type=' + $('select[name="bonus_type"]').val() +
                                    '&for=club' +
                                    '&useDefaultPrinter=undefined&printerName=XP-80' +
                                    '&id=1';
                                //console.log($printURL)
                                $.ajax({
                                    url: $printURL,
                                    type: 'GET',
                                    success: function(res) {
                                        var result = JSON.parse(res);;
                                        $('#receipt').val(result.pdf);
                                        $('#receiptID').val(result.receiptID);

                                        $('#reprint-club').show();
                                        $('#reprint-customer').show();
                                        $('#print-btn').show();
                                    }
                                });
                                /* javascript:jsWebClientPrint.print(
                                    'club_id=' + $('input[name="club_id"]').val() +
                                    '&login_user=' + $('input[name="login_user"]').val() +
                                    '&digital_product_check=' + `{{$user->user_club->digital_product? 'true':'false'}}` +
                                    '&lead=' + $('#lead').val() +
                                    '&fa_coach=' + $('#fa_coach').val() +
                                    '&fa_date=' + $('input[name="fa_date"]').val() +
                                    '&premember_name=' + $('input[name="premember_name"]').val() +
                                    '&phone=' + $('input[name="phone"]').val() +
                                    '&source=' + $('#source').val() +
                                    '&source_detail=' + $('#source_detail').val() +
                                    '&membership_type=' + $('#membership_type').val() +
                                    '&payment_method=' + $('#payment_method').val() +
                                    '&bank=' + $('#bank').val() +
                                    '&sf_amount=' + $('input[name="sf_amount"]').val() +
                                    '&mf_amount=' + $('input[name="mf_amount"]').val() +
                                    '&discount=' + $('input[name="discount"]').val() +
                                    '&deposit=' + $('input[name="deposit"]').val() +
                                    '&total=' + $('input[name="total"]').val() +
                                    '&pay=' + $('input[name="pay"]').val() +
                                    '&bonus_duration=' + $('input[name="bonus_duration"]').val() +
                                    '&bonus_type=' + $('select[name="bonus_type"]').val() +
                                    '&for=club' +
                                    '&useDefaultPrinter=undefined&printerName=XP-80' +
                                    '&id=1'
                                ); */
                            } else {
                                alert('@lang('messages.please_check_amount_pay')');
                            }
                        } else {
                            alert('@lang("messages.please_check_name_and_phone")');
                        }
                    } else {
                        alert('@lang("messages.please_choose_date_and_membership_type")');
                    }
                }
                // $('#form-payment').submit();
            });


            $('#print-btn').on('click', function () {
                @if($user->user_club->digital_product)
                    // Remove last element
                    var get_last_element = $('#first_click').children()[2];
                    $( get_last_element ).remove();
                    $('#first_click').append(
                        `<td>
                            {!! Form::button(trans('labels.reprint_customer'), ['class' => 'btn btn-success btn-md', 'id' => 'reprint-customer']) !!}
                        </td>`
                    );
                    $('#after_click').append(
                        `<td>
                            {!! Form::button(trans('labels.reprint_club'), ['class' => 'btn btn-success btn-md', 'id' => 'reprint-club']) !!}
                        </td>`
                    );

                    $('#reprint-club').show();
                    $('#reprint-customer').show();
                @endif

                //SEND PDF TO CUSTOMER
                console.log('pdf: ',$('#receipt').val());
                $('#SendMailWrapper').css('display', 'block')

                /* javascript:jsWebClientPrint.print(
                    'club_id=' + $('input[name="club_id"]').val() +
                    '&login_user=' + $('input[name="login_user"]').val() +
                    '&digital_product_check=' + `{{$user->user_club->digital_product? 'true':'false'}}` +
                    '&lead=' + $('#lead').val() +
                    '&fa_coach=' + $('#fa_coach').val() +
                    '&fa_date=' + $('input[name="fa_date"]').val() +
                    '&premember_name=' + $('input[name="premember_name"]').val() +
                    '&phone=' + $('input[name="phone"]').val() +
                    '&source=' + $('#source').val() +
                    '&source_detail=' + $('#source_detail').val() +
                    '&membership_type=' + $('#membership_type').val() +
                    '&payment_method=' + $('#payment_method').val() +
                    '&bank=' + $('#bank').val() +
                    '&sf_amount=' + $('input[name="sf_amount"]').val() +
                    '&mf_amount=' + $('input[name="mf_amount"]').val() +
                    '&discount=' + $('input[name="discount"]').val() +
                    '&deposit=' + $('input[name="deposit"]').val() +
                    '&total=' + $('input[name="total"]').val() +
                    '&pay=' + $('input[name="pay"]').val() +
                    '&bonus_duration=' + $('input[name="bonus_duration"]').val() +
                    '&bonus_type=' + $('select[name="bonus_type"]').val() +
                    '&for=club' +
                    '&useDefaultPrinter=undefined&printerName=XP-80' +
                    '&id=0'
                ); */
            });

            @if($user->user_club->digital_product)
            $('#after_click').on('click', '#reprint-club', function () {
                javascript:jsWebClientPrint.print(
                    'club_id=' + $('input[name="club_id"]').val() +
                    '&login_user=' + $('input[name="login_user"]').val() +
                    '&digital_product_check=' + `{{$user->user_club->digital_product? 'true':'false'}}` +
                    '&lead=' + $('#lead').val() +
                    '&fa_coach=' + $('#fa_coach').val() +
                    '&fa_date=' + $('input[name="fa_date"]').val() +
                    '&premember_name=' + $('input[name="premember_name"]').val() +
                    '&phone=' + $('input[name="phone"]').val() +
                    '&source=' + $('#source').val() +
                    '&source_detail=' + $('#source_detail').val() +
                    '&membership_type=' + $('#membership_type').val() +
                    '&payment_method=' + $('#payment_method').val() +
                    '&bank=' + $('#bank').val() +
                    '&sf_amount=' + $('input[name="sf_amount"]').val() +
                    '&mf_amount=' + $('input[name="mf_amount"]').val() +
                    '&discount=' + $('input[name="discount"]').val() +
                    '&deposit=' + $('input[name="deposit"]').val() +
                    '&total=' + $('input[name="total"]').val() +
                    '&pay=' + $('input[name="pay"]').val() +
                    '&bonus_duration=' + $('input[name="bonus_duration"]').val() +
                    '&bonus_type=' + $('select[name="bonus_type"]').val() +
                    '&for=club' +
                    '&useDefaultPrinter=undefined&printerName=XP-80' +
                    '&id=0'
                );
            });
            $('#first_click').on('click', '#reprint-customer', function () {
                $printURL = 'club_id=' + $('input[name="club_id"]').val() +
                    '&login_user=' + $('input[name="login_user"]').val() +
                    '&digital_product_check=' + `{{$user->user_club->digital_product? 'true':'false'}}` +
                    '&lead=' + $('#lead').val() +
                    '&fa_coach=' + $('#fa_coach').val() +
                    '&fa_date=' + $('input[name="fa_date"]').val() +
                    '&premember_name=' + $('input[name="premember_name"]').val() +
                    '&phone=' + $('input[name="phone"]').val() +
                    '&source=' + $('#source').val() +
                    '&source_detail=' + $('#source_detail').val() +
                    '&membership_type=' + $('#membership_type').val() +
                    '&payment_method=' + $('#payment_method').val() +
                    '&bank=' + $('#bank').val() +
                    '&sf_amount=' + $('input[name="sf_amount"]').val() +
                    '&mf_amount=' + $('input[name="mf_amount"]').val() +
                    '&discount=' + $('input[name="discount"]').val() +
                    '&deposit=' + $('input[name="deposit"]').val() +
                    '&total=' + $('input[name="total"]').val() +
                    '&pay=' + $('input[name="pay"]').val() +
                    '&bonus_duration=' + $('input[name="bonus_duration"]').val() +
                    '&bonus_type=' + $('select[name="bonus_type"]').val() +
                    '&for=customer' +
                    '&useDefaultPrinter=undefined&printerName=XP-80' +
                    '&id=0';
                console.log('first_click', $printURL)
                /* javascript:jsWebClientPrint.print(
                    'club_id=' + $('input[name="club_id"]').val() +
                    '&login_user=' + $('input[name="login_user"]').val() +
                    '&digital_product_check=' + `{{$user->user_club->digital_product? 'true':'false'}}` +
                    '&lead=' + $('#lead').val() +
                    '&fa_coach=' + $('#fa_coach').val() +
                    '&fa_date=' + $('input[name="fa_date"]').val() +
                    '&premember_name=' + $('input[name="premember_name"]').val() +
                    '&phone=' + $('input[name="phone"]').val() +
                    '&source=' + $('#source').val() +
                    '&source_detail=' + $('#source_detail').val() +
                    '&membership_type=' + $('#membership_type').val() +
                    '&payment_method=' + $('#payment_method').val() +
                    '&bank=' + $('#bank').val() +
                    '&sf_amount=' + $('input[name="sf_amount"]').val() +
                    '&mf_amount=' + $('input[name="mf_amount"]').val() +
                    '&discount=' + $('input[name="discount"]').val() +
                    '&deposit=' + $('input[name="deposit"]').val() +
                    '&total=' + $('input[name="total"]').val() +
                    '&pay=' + $('input[name="pay"]').val() +
                    '&bonus_duration=' + $('input[name="bonus_duration"]').val() +
                    '&bonus_type=' + $('select[name="bonus_type"]').val() +
                    '&for=customer' +
                    '&useDefaultPrinter=undefined&printerName=XP-80' +
                    '&id=0'
                ); */
            });
            @endif

            $('#first_click').on('click', '#print-btn', function () {
                $('#SendMailWrapper').css('display', 'block')
            });

            init_format();
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
</script>
<script>
    var placeSearch, autocomplete;

        var componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        country: 'long_name',
        postal_code: 'short_name'
        };

        function initAutocomplete() {
            // Create the autocomplete object, restricting the search to geographical
            // location types.
            autocomplete = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */(document.getElementById('source_detail')),
            {types: ['geocode']});

            // When the user selects an address from the dropdown, populate the address
            // fields in the form.
            autocomplete.addListener('place_changed', fillInAddress);
        }

        function fillInAddress() {
            // Get the place details from the autocomplete object.
            var place = autocomplete.getPlace();

            for (var component in componentForm) {
                document.getElementById(component).value = '';
                document.getElementById(component).disabled = false;
            }

            // Get each component of the address from the place details
            // and fill the corresponding field on the form.
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType=place.address_components[i].types[0];
                if (componentForm[addressType]) {
                    var val=place.address_components[i][componentForm[addressType]];
                    document.getElementById(addressType).value=val;
                }
            }
        }

        function geolocate() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    var geolocation={
                        lat: position.coords.
                        latitude, lng: position.coords.longitude
                    };
                    var circle=new google.maps.Circle({
                        center: geolocation,
                        radius: position.coords.accuracy
                    });
                    autocomplete.setBounds(circle.getBounds());
                });
            }
        }
</script>
<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAx42dzcUZAXbWOYbCUV8-sEyN9_ljWzcs&libraries=places&callback=initAutocomplete"
    async defer></script>

<script type="text/javascript">
    var wcppGetPrintersTimeout_ms = 10000; //10 sec
        var wcppGetPrintersTimeoutStep_ms = 500; //0.5 sec

        function wcpGetPrintersOnSuccess() {
            // Display client installed printers
            if (arguments[0].length > 0) {
                var p = arguments[0].split("|");
                var options = '';
                for (var i = 0; i < p.length; i++) {
                    options += '<option>' + p[i] + '</option>';
                }
                $('#installedPrinters').css('visibility', 'visible');
                $('#installedPrinterName').html(options);
                $('#installedPrinterName').focus();
                $('#loadPrinters').hide();
            } else {
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

<script src="{{asset('js/my_js.js')}}"></script>
@endsection
