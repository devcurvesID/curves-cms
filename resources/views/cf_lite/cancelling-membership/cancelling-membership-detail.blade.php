@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/payment.css')}}" rel="stylesheet">
    <style>
        body{
            color: white;
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
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/cancelling-membership/request')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.membership_cancellation_detail')</h2>
            </center>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <table class="member-detail">
                <tr>
                    <td>@lang('labels.name')</td>
                    <td>:</td>
                    <td>{{$member->name}}</td>
                </tr>
                <tr>
                    <td>@lang('labels.membership_type')</td>
                    <td>:</td>
                    <td>{{$member->user_memberstatus->memberstatus_membershiptype->membership_type_name}}</td>
                </tr>
                <tr>
                    <td>@lang('labels.joined')</td>
                    <td>:</td>
                    <td>{{date('d F Y', strtotime($member->user_memberpersonal->joined))}}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="member-detail">
                <tr>
                    <td>@lang('labels.date')</td>
                    <td>:</td>
                    <td>{{date('d-m-Y', strtotime(\Carbon\Carbon::today()))}}</td>
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
                            <th>@lang('labels.payment_date')</th>
                            <th>@lang('labels.type')</th>
                            <th>@lang('labels.amount')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @php($total_refund = 0)
                    @foreach($payments as $payment)
                        <tr>
                            <td style="text-align: right;">{{date('d-m-Y', strtotime($payment->payment_date))}}</td>
                            <td>
                                @if($payment->payment_category == 'First')
                                    @lang('labels.first_payment')
                                @elseif($payment->payment_category == 'Monthly')
                                    @lang('labels.monthly_payment')
                                @endif
                            </td>
                            <td class="my_rupiah">
                                @if($diff <= '3')
                                    @if($total_workouts == 0)
                                        @if($payment->payment_category == 'First')
                                            @php($must_refund = $payment->amount_paid)
                                        @elseif($payment->payment_category == 'Monthly')
                                            @php($must_refund = $payment->amount_paid)
                                        @endif
                                    @else
                                        @if($payment->payment_category == 'First')
                                            @php($must_refund = $payment->mf_amount)
                                        @elseif($payment->payment_category == 'Monthly')
                                            @php($must_refund = $payment->amount_paid)
                                        @endif
                                    @endif
                                @endif
                                {{$must_refund}}
                            </td>
                        </tr>
                        @php($total_refund = $total_refund + $must_refund)
                    @endforeach
                    <tr>
                        <td colspan="2" style="text-align: right;">@lang('labels.refund')</td>
                        <td class="my_rupiah">{{$total_refund}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['method' => 'POST', 'action' => ['CancellingMembershipController@create_refund', $member->id]]) !!}
            {!! Form::hidden('refund_amount', $total_refund) !!}
            {!! Form::hidden('refund_diff_days', $diff) !!}
            {!! Form::submit(trans('labels.create_refund_submission'), ['class' => 'btn btn-primary']) !!}
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('other-script')
    <script>
        $(document).ready(function () {
            init_format();
        });


        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp.' + (x));
            });
        }
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>
@endsection