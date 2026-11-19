@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/payment.css')}}" rel="stylesheet">
    <style>
        body{
            color: black;
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

        .select2{
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
            {!! Form::hidden('bill_id', $gmp->id) !!}
            <a class="btn btn-success pull-right"
               style="top: 5px; right: 20px; font-size: 45px; border-radius: 50%; padding: 20px; margin-right: 15px;"
               title="@lang('labels.print_receipt')"
               data-toggle="modal" data-target="#modal"><i class="fa fa-print"></i>
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 col-xs-12">
            <table class="member-detail">
                <tr>
                    <td id="member-detail-1">@lang('labels.premember_name')</td>
                    <td>:</td>
                    <td>{{$gmp->gmpayment_gmmember->gmmember_guest->fullname}}</td>
                </tr>
                <tr>
                    <td id="member-detail-2">@lang('labels.payment_number')</td>
                    <td>:</td>
                    <td>{{$gmp->payment_number}}</td>
                </tr>
                <tr>
                    <td id="member-detail-3">@lang('labels.membership_type')</td>
                    <td>:</td>
                    <td>
                        {{--{{$member_payment->memberpayment_user->user_memberstatus->memberstatus_membershiptype->membership_type_name}}--}}
                        {{--{{$membership->membership_type_name}}--}}
                        {{$gmp->gmpayment_gmmember->gmmember_gm->gm_name}}
                    </td>
                </tr>
                <tr>
                    <td>@lang('labels.date')</td>
                    <td>:</td>
                    <td>{{date('d-m-Y', strtotime($gmp->payment_date))}}</td>
                </tr>
            </table>
        </div>
        {{--<div class="col-md-6 col-xs-6">--}}
            {{--<table class="member-detail">--}}
                {{--<tr>--}}
                    {{--<td>@lang('labels.payment_category')</td>--}}
                    {{--<td>:</td>--}}
                    {{--<td>@lang('labels.first_payment')</td>--}}
                {{--</tr>--}}
                {{--<tr>--}}
                    {{--<td>@lang('labels.date')</td>--}}
                    {{--<td>:</td>--}}
                    {{--<td>{{date('d-m-Y', strtotime($gmm->payment_date))}}</td>--}}
                {{--</tr>--}}

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
            {{--</table>--}}
        {{--</div>--}}
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th colspan="2">@lang('labels.payment')</th>
                        </tr>
                        <tr>
                            <th>@lang('labels.type')</th>
                            <th>@lang('labels.fee')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>@lang('labels.fee')</td>
                            <td class="my_rupiah">
                                {{$gmp->amount_due}}
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('labels.total')</td>
                            <td class="my_rupiah">
                                {{$gmp->amount_due}}
                            </td>
                        </tr>
                        <tr class="column-no-border">
                            <td style="text-align: right;">
                                @lang('labels.pay') ({{$gmp->gmpayment_paymentmethod->payment_method}}):
                            </td>
                            <td class="my_rupiah">{{$gmp->amount_paid}}</td>
                        </tr>
                        <tr class="column-no-border">
                            <td style="text-align: right;">@lang('labels.rest_of_the_bill') :</td>
                            <td class="my_rupiah">{{$gmp->rest_of_bill}}</td>
                        </tr>
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
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
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
@endsection

@section('other-script')
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            init_format();
        });

        $(document).ready(function () {
            $('.select2').select2();

            $('#modal').on('click', '#pos-print', function () {
                if ($('input[name="bill_id"]').val() != "") {
                    javascript:jsWebClientPrint.print(
                        'id=' + $('input[name="bill_id"]').val() +
                        '&club_id='+ $('input[name="club_id"]').val()+
                        '&staff=' + $('#staff').val() +
                        '&useDefaultPrinter=undefined&printerName=XP-80'
                    );
                }
            });
        });
        function init_format() {
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