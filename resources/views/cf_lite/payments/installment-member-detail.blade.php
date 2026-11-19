@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <link href="{{asset('css/appointment.css')}}" rel="stylesheet">
    <link href="{{asset('css/payment.css')}}" rel="stylesheet">
    <style>
        td{
            padding: 5px;
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
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-md-3">
            @if(!empty($member->user_memberpersonal->photo))
                <img class="img-circle member-card-img" src="{{asset('images/member_photos/'.$member->user_memberpersonal->photo)}}" style="width: 150px; height: 150px; object-fit: cover;">
            @else
                <img class="img-circle member-card-img" src="{{asset('images/img.png')}}">
            @endif
        </div>
        <div class="col-md-9">
            <table>
                <tr>
                    <td><span class="fa fa-female" title="@lang('labels.name')"></span></td>
                    <td>:</td>
                    <td>{{$member->name}}</td>
                </tr>
                <tr>
                    <td><span class="fa fa-calendar" title="@lang('labels.joined')"></span></td>
                    <td>:</td>
                    <td>{{date('d F Y', strtotime($member->user_memberpersonal->joined))}}</td>
                </tr>
            </table>
            @if($member->user_memberstatus->status == 'Active')
                @if(count($member_payments) == 0)
                    @php
                        $installment_setting_link = 'new-installment-setting';
                    @endphp
                @else
                    @php
                        $installment_setting_link = 'installment-setting/'.$member->id;
                    @endphp
                @endif
            @endif
            <div class="pull-right">
                <a href="{{url('cf-lite/payment/'.$installment_setting_link)}}" class="btn btn-primary">@lang('labels.installment_setting')</a>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        @if(count($member_payments) > 0)
            <h4>@lang('labels.installment_list')</h4>
            @foreach($member_payments as $member_payment)
                <div class="col-md-3">
                    <table>
                        <tr>
                            <td><span class="fa fa-calendar" title="@lang('labels.payment_date')"></span></td>
                            <td>:</td>
                            <td>{{date('d F Y', strtotime($member_payment->payment_date))}}</td>
                        </tr>
                        <tr>
                            <td><span class="fa fa-money" title="@lang('labels.amount')"></span></td>
                            <td>:</td>
                            <td class="my_rupiah">{{$member_payment->amount_due}}</td>
                        </tr>
                    </table>
                    <div class="pull-right">
                        @if($member_payment->payment_status == 'Paid')
                            <h3>@lang('labels.paid')</h3>
                        @else
                            <h3>@lang('labels.not_paid')</h3>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <p>@lang('messages.installment_hasnt_been_set'). @lang('messages.please_set_installment') <a href="{{url('cf-lite/payment/'.$installment_setting_link)}}">{{strtolower(trans('labels.here'))}}</a>.</p>
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