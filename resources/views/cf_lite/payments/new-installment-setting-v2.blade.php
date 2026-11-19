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
        <div class="col-md-8 col-md-offset-1">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>@lang('labels.type')</th>
                        <th>@lang('labels.fee')</th>
                        <th>@lang('labels.pay')</th>
                        <th>@lang('labels.rest_of_the_bill')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @if($member->user_memberstatus->memberstatus_membershiptype->payment_type == 'Prepaid')
                        @php
                            $installment_total = 0;
                            /*$tax_amount = $member_payment->tax_percentage * $installment_total * 0.01;*/
                            $tax_amount = 0;
                            $total_amount = $member_payment->rest_of_bill + $installment_total + $tax_amount;
                        @endphp
                        <tr>
                            <td>@lang('labels.installment_payment')</td>
                            <td class="my_rupiah">{{$member_payment->rest_of_bill}}</td>
                            <td class="my_rupiah">0</td>
                            <td class="my_rupiah">{{$member_payment->rest_of_bill}}</td>
                        </tr>
                    @else
                        @php
                            $installment_total = ($member->user_memberstatus->memberstatus_membershiptype->duration - 1) * $club_sf->mf_amount;
                            /*$tax_amount = $member_payment->tax_percentage * $installment_total * 0.01;*/
                            $tax_amount = 0;
                            $total_amount = $member_payment->rest_of_bill + $installment_total + $tax_amount;
                        @endphp
                        <tr>
                            <td>
                                @lang('labels.first_payment')
                                @if(!empty($member_payment->rest_of_bill))
                                    <a class="btn btn-xs btn-success pull-right" href="{{url('cf-lite/payment/debt/'.$member_payment->id)}}">@lang('labels.pay_now_cash')</a>
                                @endif
                            </td>
                            <td class="my_rupiah">{{$member_payment->amount_due}}</td>
                            <td class="my_rupiah">{{$member_payment->amount_paid}}</td>
                            <td class="my_rupiah">{{$member_payment->rest_of_bill}}</td>
                        </tr>
                        <tr>
                            <td>@lang('labels.installment_payment')</td>
                            <td class="my_rupiah">{{$installment_total}}</td>
                            <td class="my_rupiah">0</td>
                            <td class="my_rupiah">{{$installment_total}}</td>
                        </tr>
                    @endif
                    <tr>
                        <td colspan="3">@lang('labels.billing_total')</td>
                        <td>{!! Form::text('total', $total_amount, ['class' => 'form-control rupiah', 'readonly']) !!}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @if($member->user_memberstatus->memberstatus_membershiptype->payment_type == 'Prepaid')
        <br>
        <h4 class="col-md-offset-1" style="font-size: 25px;margin-bottom: 25px;">@lang('labels.set_installment_amount')</h4>
        <div class="row">
            <div class="col-md-8 col-md-offset-1">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>@lang('labels.variety')</th>
                                <th>@lang('labels.amount')</th>
                                <th>@lang('labels.payment_date')</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>@lang('labels.first_installment')</td>
                                <td>{!! Form::text('cicilan1', ($total_amount / 2), ['class' => 'form-control rupiah']) !!}</td>
                                <td>{!! Form::date('cicilan1_date', \Carbon\Carbon::parse($member->user_memberpersonal->joined)->addMonth(1), ['class' => 'form-control']) !!}</td>
                            </tr>
                            <tr>
                                <td>@lang('labels.second_installment')</td>
                                <td>{!! Form::text('cicilan2', ($total_amount / 2), ['class' => 'form-control rupiah']) !!}</td>
                                <td>{!! Form::date('cicilan2_date', \Carbon\Carbon::parse($member->user_memberpersonal->joined)->addMonth(2), ['class' => 'form-control']) !!}</td>
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
            <div class="col-md-8 col-md-offset-1">
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
                        @php
                            $key = 0;
                            $ad_date = $member->user_memberpersonal->joined;
                        @endphp
                        @for($i = 1; $i < $member->user_memberstatus->memberstatus_membershiptype->duration; $i++)
                            <tr>
                                <td>@lang('labels.autodebet') - {{$i}}</td>
                                <td>{!! Form::text('ad['.$i.']', (($installment_total + $tax_amount)/($member->user_memberstatus->memberstatus_membershiptype->duration - 1)), ['class' => 'form-control rupiah', 'readonly']) !!}</td>
                                <td>
                                    @if($key == '0')
                                        @if(date('d', strtotime(\Carbon\Carbon::parse($ad_date)->addMonth($i))) != date('d', strtotime(\Carbon\Carbon::parse($ad_date))))
                                            @php
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
                                            @endphp
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
    @php
        $button = '';
        /*if($member->user_memberstatus->memberstatus_membershiptype->payment_type == 'AD' && $member_payment->rest_of_bill > 0):
            $button = 'disabled';
        endif;*/
    @endphp
    <div class="row">
        <div class="col-md-8 col-md-offset-1">
            <button class="btn btn-primary pull-right" {{$button}} id="form-installment-submit-btn">@lang('labels.save')</button>
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@section('other-script')
    <script>
        $(document).ready(function () {
            var installment_total = parseInt(rupiah_format_destroyer($('input[name="total"]').val()));

            $('.rupiah').each(function () {
                $(this).wrap('<div class="input-group"></div>')
                $(this).before('<span class="input-group-addon">Rp.</span>');
                $(this).after('<span class="input-group-addon">,-</span>');
            });

            $('input[name="cicilan1"]').on('keyup', function () {
                var cicilan1 = parseInt(rupiah_format_destroyer($(this).val()));
                var cicilan2 = installment_total - cicilan1;
                $('input[name="cicilan1"]').val(rupiah_formater(cicilan1));
                $('input[name="cicilan2"]').val(rupiah_formater(cicilan2));
            });

            $('input[name="cicilan2"]').on('keyup', function () {
                var cicilan2 = parseInt(rupiah_format_destroyer($(this).val()));
                var cicilan1 = installment_total - cicilan2;
                $('input[name="cicilan2"]').val(rupiah_formater(cicilan2));
                $('input[name="cicilan1"]').val(rupiah_formater(cicilan1));
            });

            $('#form-payment-submit-btn').on('click', function () {
                $('.rupiah').each(function () {
                    var x = rupiah_format_destroyer($(this).val());
                    $(this).val((x));
                });
                $('#form-first-payment').submit();
            });

            $('#form-installment-submit-btn').on('click', function () {
                $('.rupiah').each(function () {
                    var x = rupiah_format_destroyer($(this).val());
                    $(this).val((x));
                });
                $('#form-installment-setting').submit();
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