@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/payment.css')}}" rel="stylesheet">
    <style>
        body{
            /*color: white;*/
        }
        .modal-dialog, .modal-dialog label{
            color: #414042;
        }

        .page-title{
            margin-top: 0px;
        }

        .data-profile-padding{
            padding: 30px 0;
        }

        #data-profile-table tr{
            height: 50px;
        }

        #data-profile-table td{
            padding: 15px;
            font-size: 20px;
            font-weight: 600;
            color: #77619c;
        }

        img.media-object{
            width: 180px;
            height: 180px;
            object-fit: cover;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
        }

        form#form-installment-setting table th{
            color: #414042;
        }

        .btn-primary{
            background-color: #77619c;
            color: #ffffff;
            border-color: #77619c;
        }

        .btn-primary:hover {
            background-color: #ffffff;
            color: #77619c;
            font-weight: 600;
            border-color: #77619c;
        }

    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <center>
                <h2 class="page-title">@lang('labels.installment_setting')</h2>
            </center>
        </div>
    </div>

    @include('cf_lite.includes.error-message')

    <div class="row data-profile-padding">
        <div class="col-md-2 col-md-offset-1">
            <div class="media">
                <div class="media-left">
                    @if(!empty($member->user_memberpersonal->photo))
                        <img class="media-object" src="{{asset('images/member_photos/'.$member->user_memberpersonal->photo)}}" style="width: 150px; height: 150px; object-fit: cover;">
                    @else
                        <img class="media-object" src="{{asset('images/img.png')}}">
                    @endif
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="media-body">
                <table id="data-profile-table">
                    <tr>
                        <td><span class="fa fa-female"></span> </td>
                        <td>:</td>
                        <td>{{$member->name}}</td>
                    </tr>
                    <tr>
                        <td><span class="fa fa-phone"></span> </td>
                        <td>:</td>
                        <td>
                            @if(!empty($member->user_memberpersonal->cellphone))
                                {{$member->user_memberpersonal->cellphone}}
                            @elseif(!empty($member->user_memberpersonal->phone))
                                {{$member->user_memberpersonal->phone}}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td><span class="fa fa-calendar" title="@lang('labels.active')"></span> </td>
                        <td>:</td>
                        <td>{{date('d F Y', strtotime($member->user_memberstatus->from_date))}} - {{date('d F Y', strtotime($member->user_memberstatus->thru_date))}}</td>
                    </tr>
                    <tr>
                        <td><span class="fa fa-newspaper-o" title="@lang('labels.membership_type')"></span> </td>
                        <td>:</td>
                        <td>{{$member->user_memberstatus->memberstatus_membershiptype->membership_type_name}}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

    {!! Form::open(['method' => 'POST', 'action' => ['PaymentController@store_installment_setting', $member->id], 'id' => 'form-installment-setting']) !!}
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>@lang('labels.type')</th>
                        <th>@lang('labels.membership_fee')</th>
                        <th>@lang('labels.digital_product')</th>
                        <th>@lang('labels.pay')</th>
                        <th>@lang('labels.rest_of_the_bill')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($installment_paid = 0)
                    @if($member->user_memberstatus->memberstatus_membershiptype->payment_type == 'Prepaid')
                        @php($installment_total = 0)
                        @php($tax_amount = 0)
                        @php($total_amount = $member_payment->rest_of_bill + $installment_total + $tax_amount)
                        <tr>
                            <td>@lang('labels.installment_payment')</td>
                            <td class="my_rupiah">{{ empty($member_payment->memberpayment_digitalproductpayment) ? ($member_payment->rest_of_bill - ($digital_product && isset($digital_product->selling_price) ? $digital_product->selling_price : 0) ) : $member_payment->rest_of_bill }}</td>
                            <td class="my_rupiah">{{ empty($member_payment->memberpayment_digitalproductpayment) ? ($digital_product && isset($digital_product->selling_price) ? $digital_product->selling_price : 0) : 0 }}</td>
                            <td class="my_rupiah">0</td>
                            <td class="my_rupiah">{{$member_payment->rest_of_bill}}</td>
                        </tr>
                    @else
                        @php($installment_paid_due = 0)
                        @if ($member->user_memberstatus->memberstatus_membershiptype->payment_type == 'AD')
                            @if (!empty($monthly_member_payments)):
                                @php($installment_paid_due = count($monthly_member_payments) * $club_sf->mf_amount)
                            @endif
                        @endif

                        @php($installment_total = ($member->user_memberstatus->memberstatus_membershiptype->duration - 1) * $club_sf->mf_amount)
                        @php($tax_amount = 0)
                        @php($total_amount = ($member_payment ? $member_payment->rest_of_bill : 0) + $installment_total + $tax_amount - $installment_paid_due)
                        <tr>
                            <td>
                                @lang('labels.first_payment')
                                @if(!empty($member_payment->rest_of_bill))
                                    <a class="btn btn-xs btn-success pull-right" href="{{url('cf-lite/payment/debt/'.$member_payment->id)}}">@lang('labels.pay_now_cash')</a>
                                @endif
                            </td>
                            <td class="my_rupiah">{{ $member_payment ? $member_payment->amount_due : 0 }}</td>
                            <td class="my_rupiah">{{ $member_payment ? $member_payment->amount_paid : 0 }}</td>
                            <td class="my_rupiah">{{ $member_payment ? $member_payment->rest_of_bill : 0 }}</td>
                        </tr>
                        {{--@php($installment_paid = 0)--}}
                        @if(count($monthly_member_payments) > 0)
                            @foreach($monthly_member_payments as $monthly_member_payment)
                                @php($installment_paid = $installment_paid + $monthly_member_payment->amount_paid)
                            @endforeach
                        @endif
                        <tr>
                            <td>@lang('labels.installment_payment')</td>
                            <td class="my_rupiah">{{$installment_total}}</td>
                            <td class="my_rupiah">{{$installment_paid}}</td>
                            <td class="my_rupiah">{{$installment_total - $installment_paid}}</td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="4">@lang('labels.billing_total')</td>
                        <td>
                            @if(count($monthly_member_payments) > 0)
                                {!! Form::hidden('total', $total_amount) !!}
                                {!! Form::text('total_view', ($installment_total - $installment_paid), ['class' => 'form-control rupiah', 'readonly']) !!}
                            @else
                                {!! Form::text('total', $total_amount, ['class' => 'form-control rupiah', 'readonly']) !!}
                            @endif
                        </td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($member->user_memberstatus->memberstatus_membershiptype->payment_type == 'Prepaid')
        <br>
        <h4 class="col-md-offset-1 col-md-10" style="font-size: 25px;margin-bottom: 25px;">@lang('labels.set_installment_amount')<span class="pull-right">
                <button type="button" class="btn btn-primary" id="add-installment-btn" title="@lang('labels.add_installment')"><span class="fa fa-plus"></span></button>
            </span></h4>
        {!! Form::hidden('ins_counter', 2) !!}
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>@lang('labels.variety')</th>
                                <th>@lang('labels.amount')</th>
                                <th>*@lang('labels.including_digital_product')</th>
                                <th>@lang('labels.payment_date')</th>
                            </tr>
                        </thead>
                        <tbody id="installment-body">
                            <tr>
                                <td>@lang('labels.first_installment')</td>
                                <td>{!! Form::text('cicilan1', ($total_amount / 2), ['class' => 'form-control rupiah', 'id' => 'cicilan-1']) !!}</td>
                                <td>{!! Form::text('digiprice1', (empty($member_payment->memberpayment_digitalproductpayment) && !empty($digital_product)) ? $digital_product->selling_price : 0, ['class' => 'form-control rupiah cicilan-addon', 'id' => 'digiprice-1']) !!}</td>

                                @if(isset($member->user_memberpersonal->joined))
                                    <td>{!! Form::date('cicilan1_date', \Carbon\Carbon::parse($member->user_memberpersonal->joined)->addMonth(1), ['class' => 'form-control']) !!}</td>
                                @else
                                    <td> - </td>
                                @endif
                            </tr>
                            <tr>
                                <td>@lang('labels.second_installment')</td>
                                <td>{!! Form::text('cicilan2', ($total_amount / 2), ['class' => 'form-control rupiah', 'id' => 'cicilan-2']) !!}</td>
                                <td>{!! Form::text('digiprice2', 0, ['class' => 'form-control rupiah cicilan-addon', 'id' => 'digiprice-2']) !!}</td>

                                @if(isset($member->user_memberpersonal->joined))
                                    <td>{!! Form::date('cicilan2_date', \Carbon\Carbon::parse($member->user_memberpersonal->joined)->addMonth(2), ['class' => 'form-control']) !!}</td>
                                @else
                                    <td> - </td>
                                @endif
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @elseif($member->user_memberstatus->memberstatus_membershiptype->payment_type == 'AD')
        <br>
        <h4>@lang('labels.set_installment_amount')</h4>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>@lang('labels.variety')</th>
                            <th>@lang('labels.amount')</th>
                            <th>@lang('labels.autodebet_date')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php($key = 0)
                        <?php $ad_date = $member->user_memberpersonal->joined ?>
                        @for($i = (1 + count($monthly_member_payments)); $i < ($member->user_memberstatus->memberstatus_membershiptype->duration); $i++)
                            <tr>
                                <td>@lang('labels.autodebet') - {{$i}}</td>
                                <td>{!! Form::text('ad['.$i.']', (($installment_total + $tax_amount)/($member->user_memberstatus->memberstatus_membershiptype->duration - 1)), ['class' => 'form-control rupiah', 'readonly']) !!}</td>
                                <td>
                                    @if($key == '0')
                                        @if(date('d', strtotime(\Carbon\Carbon::parse($ad_date)->addMonth($i))) != date('d', strtotime(\Carbon\Carbon::parse($ad_date))))
                                            <?php
                                                if (date('d', strtotime(\Carbon\Carbon::parse($ad_date))) == '29'):
                                                    $last_day = date('d', strtotime(\Carbon\Carbon::parse($ad_date)->lastOfMonth()));
                                                    $diff = $last_day - 29 + 1;
                                                    $ad_date = \Carbon\Carbon::parse($ad_date)->addDays($diff);
                                                elseif (date('d', strtotime(\Carbon\Carbon::parse($ad_date))) == '30'):
                                                    $last_day = date('d', strtotime(\Carbon\Carbon::parse($ad_date)->lastOfMonth()));
                                                    $diff = $last_day - 30 + 1;
                                                    $ad_date = \Carbon\Carbon::parse($ad_date)->addDays($diff);
                                                elseif (date('d', strtotime(\Carbon\Carbon::parse($ad_date))) == '31'):
                                                    $last_day = date('d', strtotime(\Carbon\Carbon::parse($ad_date)->lastOfMonth()));
                                                    $diff = $last_day - 31 + 1;
                                                    $ad_date = \Carbon\Carbon::parse($ad_date)->addDays($diff);
                                                endif;
                                                $key = 1;
                                            ?>
                                        @endif
                                    @endif
                                    {!! Form::date('ad_date['.$i.']', \Carbon\Carbon::parse($ad_date)->addMonth($i), ['class' => 'form-control']) !!}
                                </td>
                            </tr>
                        @endfor
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
    <?php
        $button = '';
        /*if($member->user_memberstatus->memberstatus_membershiptype->payment_type == 'AD' && $member_payment->rest_of_bill > 0):
            $button = 'disabled';
        endif;*/
    ?>

    @php($ext = '')
    @if($member->user_memberstatus->memberstatus_membershiptype->payment_type == 'AD')
        @php($ext = '-ad')
    @endif
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <button type="button" class="btn btn-primary pull-right" {{$button}} id="form-installment-submit-btn{{$ext}}">@lang('labels.save')</button>
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@section('other-script')
    <script>
        $(document).ready(function () {
            @if(!empty($digital_product))
                var digital_club = true;
            @else
                var digital_club = false;
            @endif

            var installment_total = parseInt(rupiah_format_destroyer($('input[name="total"]').val()));

            $('.rupiah').each(function () {
                $(this).wrap('<div class="input-group"></div>')
                $(this).before('<span class="input-group-addon">Rp.</span>');
                $(this).after('<span class="input-group-addon">,-</span>');
            });

            $('input[name="cicilan1"]').on('keyup', function () {
                var cicilan1 = parseInt(rupiah_format_destroyer($(this).val()));
                $('input[name="cicilan1"]').val(rupiah_formater(cicilan1));
                if ($('input[name="ins_counter"]').val() == '2') {
                    var cicilan2 = installment_total - cicilan1;
                    $('input[name="cicilan2"]').val(rupiah_formater(cicilan2));
                }
            });

            $('input[name="cicilan2"]').on('keyup', function () {
                var cicilan2 = parseInt(rupiah_format_destroyer($(this).val()));
                $('input[name="cicilan2"]').val(rupiah_formater(cicilan2));
                if ($('input[name="ins_counter"]').val() == '2') {
                    var cicilan1 = installment_total - cicilan2;
                    $('input[name="cicilan1"]').val(rupiah_formater(cicilan1));
                }
            });

            $('#form-payment-submit-btn').on('click', function () {
                $('.rupiah').each(function () {
                    var x = rupiah_format_destroyer($(this).val());
                    $(this).val((x));
                });
                $('#form-first-payment').submit();
            });

            $('#form-installment-submit-btn').on('click', function () {
                var current = parseInt($('input[name="ins_counter"]').val());
                var total = parseInt(rupiah_format_destroyer($('input[name="total"]').val()));
                var actual_total = parseInt('0');
                var access = 'open';
                for (var i = 1; i <= current; i++) {
                    actual_total = actual_total + parseInt(rupiah_format_destroyer($('input[name="cicilan' + i + '"]').val()));
                    if (($('input[name="cicilan' + i + '"]').val() !== '0') && ($('input[name="cicilan' + i + '"]').val() !== '')) {
                        if($('input[name="cicilan' + i + '_date"]').val() === '') {
                            access = 'close';
                        }

                        if((digital_club == true) && (parseInt(rupiah_format_destroyer($('input[name="digiprice' + i + '"]').val())) > parseInt(rupiah_format_destroyer($('input[name="cicilan' + i + '"]').val())))){
                            access = 'close2';
                        }
                    }
                }

                if (actual_total === total) {
                    if (access === 'open') {
                        $('.rupiah').each(function () {
                            var x = rupiah_format_destroyer($(this).val());
                            $(this).val((x));
                        });
                    $('#form-installment-setting').submit();
                    } else if (access === 'close') {
                        alert('@lang('messages.please_check_once_again_for_empty_date')');
                    } else if (access === 'close2') {
                        alert('@lang('messages.please_check_once_again_for_digital_product_price')');
                    }
                } else {
                    alert('@lang('messages.total_installment_not_match_the_bill')');
                }
            });

            //Added 14092018
            $('#form-installment-submit-btn-ad').on('click', function () {
                $('.rupiah').each(function () {
                    var x = rupiah_format_destroyer($(this).val());
                    $(this).val((x));
                });
                $('#form-installment-setting').submit();
            });

            //Add Installment
            $('#add-installment-btn').on('click', function () {
                var current = parseInt($('input[name="ins_counter"]').val());
                current = current + 1;
                $('input[name="ins_counter"]').val(current);

                var total = parseInt(rupiah_format_destroyer($('input[name="total"]').val()));
                for (var i = 1; i < current; i++) {
                    $('input[name="cicilan' + i + '"]').val(rupiah_formater(total/current));
                }
                var last_installment = total - ((total/current) * (current - 1));

                var html = '<tr>';
                html += '<td>@lang('labels.installment')-' + current + '</td>';
                html += '<td>';
                html += '<div class="input-group">';
                html += '<span class="input-group-addon">Rp.</span>';
                html += '<input class="form-control rupiah cicilan-addon" id="cicilan-' + current + '" name="cicilan' + current + '" type="text" value="' + rupiah_formater(last_installment) + '">';
                html += '<span class="input-group-addon">,-</span>';
                html += '</div>';
                html += '</td>';
                html += '<td>';
                html += '<div class="input-group">';
                html += '<span class="input-group-addon">Rp.</span>';
                html += '<input class="form-control rupiah cicilan-addon" id="digiprice-' + current + '" name="digiprice' + current + '" type="text" value="0">';
                html += '<span class="input-group-addon">,-</span>';
                html += '</div>';
                html += '</td>';
                html += '<td>';
                html += '<input class="form-control" name="cicilan' + current + '_date" type="date">';
                html += '</td>';
                html += '</tr>';

                $('#installment-body').append(html);
            });

            $('#installment-body').on('keyup', '.cicilan-addon', function () {
                $(this).val(rupiah_formater($(this).val()));
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
