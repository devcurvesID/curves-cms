@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <link href="{{asset('css/appointment.css')}}" rel="stylesheet">
    <link href="{{asset('css/payment.css')}}" rel="stylesheet">

    <style>

        .home-btn {
            border: 5px solid #9dd6ba;
            color: #9dd6ba;
        }

        h3.home-btn-title{
            color: #9dd6ba;
        }

        .main-btn:hover .home-btn{
            background-color: #afeecf;
            border-color: #9dd6ba;
        }

        .main-btn:hover a div h3,
        .main-btn:hover a div span {
            color: #ffffff;
        }

        a:hover {
            text-decoration: none !important;
        }
    </style>

@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/dashboard')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.payment')</h2>
            </center>
        </div>
    </div>

    @include('cf_lite.includes.error-message')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-md-6 main-btn">
            <a class="payment-setting-left" href="{{url('cf-lite/payment/installment')}}">
                <div class="home-btn payment-choose-btn" style="">
                    <span class="fa fa-bank"></span>
                    <h3 class="home-btn-title" >@lang('labels.installment_setting')</h3>
                </div>
            </a>
        </div>
        <div class="col-md-6 main-btn">
            <a class="payment-setting-right" href="#">
                <div class="home-btn payment-choose-btn">
                    <span class="fa fa-credit-card-alt"></span>
                    <h3 class="home-btn-title">@lang('labels.payment_setting')</h3>
                </div>
            </a>
        </div>
    </div>
    <div class="row">
        <h1 style="margin-top: 50px; color: black">@lang('labels.rest_of_first_payment_bill_list')</h1>
        <br>
        @if(count($member_payments) > 0)
            @foreach($member_payments as $member_payment)
                <div class="col-md-4">
                    <a href="{{url('cf-lite/payment/debt/'.$member_payment->id)}}">
                        <div class="member-debt-card">
                            <div class="row">
                                <div class="col-md-4">
                                    @if(!empty($member_payment->memberpayment_user->user_memberpersonal->photo))
                                        <img class="img-circle member-card-img"
                                             src="{{asset('images/member_photos/'.$member_payment->memberpayment_user->user_memberpersonal->photo)}}">
                                    @else
                                        <img class="img-circle member-card-img" src="{{asset('images/img.png')}}">
                                    @endif
                                </div>
                                <div class="col-md-8">
                                    <table>
                                        <tr>
                                            <td><span class="fa fa-female" title="@lang('labels.name')"></span></td>
                                            <td>:</td>
                                            <td>{{$member_payment->memberpayment_user->name}}</td>
                                        </tr>
                                        <tr>
                                            <td><span class="fa fa-phone" title="@lang('labels.phone')"></span></td>
                                            <td>:</td>
                                            <td>
                                                @if(!empty($member_payment->memberpayment_user->user_memberpersonal->cellphone))
                                                    {{$member_payment->memberpayment_user->user_memberpersonal->cellphone}}
                                                @elseif(isset($member_payment->memberpayment_user->user_memberpersonal->phone))
                                                    {{$member_payment->memberpayment_user->user_memberpersonal->phone}}
                                                @else

                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><span class="fa fa-calendar-o"
                                                      title="@lang('labels.last_payment')"></span></td>
                                            <td>:</td>
                                            <td>
                                                @php
                                                    $payment_date = $member_payment->payment_date;
                                                @endphp
                                                @if(!empty($member_payment->memberpayment_memberdebtpayment))
                                                    @foreach($member_payment->memberpayment_memberdebtpayment as $debt_payment)
                                                        @if(strtotime($debt_payment->payment_date) > strtotime($payment_date))
                                                            @php
                                                                $payment_date = $debt_payment->payment_date;
                                                            @endphp
                                                        @endif
                                                    @endforeach
                                                @endif
                                                {{date('d F Y', strtotime($payment_date))}}
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><span class="fa fa-money"
                                                      title="@lang('labels.rest_of_the_bill')"></span></td>
                                            <td>:</td>
                                            <td class="my_rupiah">{{$member_payment->rest_of_bill}}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        @else
            <span class="no_data_found">@lang('messages.there_is_no_debt')</span>
        @endif
    </div>
@endsection

@section('other-script')
    <script>
        $(document).ready(function () {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp.' + (x));
            });
        });
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>
@endsection
