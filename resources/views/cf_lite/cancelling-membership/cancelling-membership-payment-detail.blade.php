@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/payment.css')}}" rel="stylesheet">
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
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
        .select2 {
            width: 100% !important;
            float: right;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/cancelling-membership')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.membership_cancellation_request')</h2>
            </center>
        </div>
    </div>

    {!! Form::open(['method' => 'POST', 'action' => ['CancellingMembershipController@create_cancellation_submission', $member->id]]) !!}
    <div class="row">
        <div class="col-md-6">
            <table class="member-detail">
                <tr>
                    <td>@lang('labels.payment_number')</td>
                    <td>:</td>
                    <td>
                        @php($month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'))
                        {{sprintf('%08d', $temp_auto_number->current_number)}}/PY/CIND/{{sprintf('%03d', $member->club_id)}}/{{$month_roman[\Carbon\Carbon::now()->format('n')]}}/{{\Carbon\Carbon::now()->format('Y')}}
                    </td>
                </tr>
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
                    <td>@lang('labels.start_membership')</td>
                    <td>:</td>
                    <td>
                        {{date('d F Y', strtotime($member->user_memberstatus->from_date))}}
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="member-detail">
                <tr>
                    <td>@lang('labels.joined')</td>
                    <td>:</td>
                    <td>{{date('d F Y', strtotime($member->user_memberpersonal->joined))}}</td>
                </tr>
                <tr>
                    <td>@lang('labels.request_date')</td>
                    <td>:</td>
                    <td>{!! Form::date('request_date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}</td>
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
                            <th>@lang('labels.bill')</th>
                            <th>@lang('labels.paid')</th>
                            <th>@lang('labels.amount')</th>
                            <th>@lang('labels.information')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @php($total_amount = 0)
                    @php($month_count = 0)
                    @if($day_diff > 3)
                        <tr>
                            <td style="text-align: right;">{{date('d-m-Y', strtotime($first_payment->payment_date))}}</td>
                            <td>@lang('labels.first_payment')</td>
                            <td class="my_rupiah">{{$mm_price->mf_amount}}</td>
                            <td class="my_rupiah">{{$first_payment->amount_paid - $first_payment->sf_amount + $first_payment->dc_amount}}</td>
                            <td class="my_rupiah">{{$mm_price->mf_amount - ($first_payment->amount_paid - $first_payment->sf_amount + $first_payment->dc_amount)}}</td>
                            <td>
                                @lang('labels.holded_service_fee'): <span class="my_rupiah">{{$first_payment->sf_amount - $first_payment->dc_amount}}</span>
                            </td>
                            @php($total_amount = $total_amount + ($mm_price->mf_amount - ($first_payment->amount_paid - $first_payment->sf_amount + $first_payment->dc_amount)))
                        </tr>
                    @else
                        <tr>
                            <td style="text-align: right;">{{date('d-m-Y', strtotime($first_payment->payment_date))}}</td>
                            <td>@lang('labels.first_payment')</td>
                            <td class="my_rupiah">0</td>
                            @if($total_workouts > 0)
                                <td class="my_rupiah">{{$first_payment->amount_paid - $first_payment->sf_amount + $first_payment->dc_amount}}</td>
                                <td class="my_rupiah">{{$first_payment->amount_paid - $first_payment->sf_amount + $first_payment->dc_amount}}</td>
                                <td>
                                    @lang('labels.holded_service_fee'): <span class="my_rupiah">{{$first_payment->sf_amount - $first_payment->dc_amount}}</span>
                                </td>
                                @php($total_amount = $total_amount + (0 - ($first_payment->amount_paid - $first_payment->sf_amount + $first_payment->dc_amount)))
                            @else
                                <td class="my_rupiah">{{$first_payment->amount_paid}}</td>
                                <td class="my_rupiah">{{$first_payment->amount_paid}}</td>
                                <td>
                                    @lang('labels.holded_service_fee'): <span class="my_rupiah">0</span>
                                </td>
                                @php($total_amount = $total_amount + (0 - $first_payment->amount_paid))
                            @endif
                        </tr>
                    @endif
                    @if($member->user_memberstatus->memberstatus_membershiptype->payment_type == 'AD')
                        @if(!empty($monthly_payments))
                            @foreach($monthly_payments as $payment)
                                @if($month_count < $month_diff)
                                    @if($day_diff > 3)
                                        <tr>
                                            <td style="text-align: right;">{{date('d-m-Y', strtotime($payment->payment_date))}}</td>
                                            <td>@lang('labels.monthly_payment')</td>
                                            <td class="my_rupiah">{{$mm_price->mf_amount}}</td>
                                            <td class="my_rupiah">{{$payment->amount_paid}}</td>
                                            <td class="my_rupiah">{{$mm_price->mf_amount - $payment->amount_paid}}</td>
                                            <td></td>
                                            @php($total_amount = $total_amount + ($mm_price->mf_amount - $payment->amount_paid))
                                        </tr>
                                    @else
                                        @if($payment->payment_status != 'Unpaid')
                                            <tr>
                                                <td style="text-align: right;">{{date('d-m-Y', strtotime($payment->payment_date))}}</td>
                                                <td>@lang('labels.monthly_payment')</td>
                                                <td class="my_rupiah">0</td>
                                                <td class="my_rupiah">{{$payment->amount_paid}}</td>
                                                <td class="my_rupiah">{{$payment->amount_paid}}</td>
                                                <td></td>
                                                @php($total_amount = $total_amount + (0 - $payment->amount_paid))
                                            </tr>
                                        @endif
                                    @endif
                                    @php($month_count++)
                                @endif
                            @endforeach
                        @endif
                    @elseif($member->user_memberstatus->memberstatus_membershiptype->payment_type == 'Prepaid')
                        @if(!empty($monthly_payments))
                            @php($installment_number = 1)
                            @foreach($monthly_payments as $payment)
                                @if($payment->payment_status != 'Unpaid')
                                    <tr>
                                        <td style="text-align: right;">{{date('d-m-Y', strtotime($payment->payment_date))}}</td>
                                        <td colspan="3">@lang('labels.installment_payment')</td>
                                        <td class="my_rupiah">-{{$payment->amount_paid}}</td>
                                        <td>@lang('labels.installment') - {{$installment_number}}</td>
                                        @php($total_amount = $total_amount - $payment->amount_paid)
                                    </tr>
                                    @php($installment_number++)
                                @endif
                            @endforeach
                        @endif

                        @if($day_diff > 3)
                            @for($i = 1; $i <= $month_diff; $i++)
                                <tr>
                                    <td style="text-align: right;">{{\Carbon\Carbon::parse($member->user_memberstatus->from_date)->addMonths($i)->format('d-m-Y')}}</td>
                                    <td>@lang('labels.monthly_payment')</td>
                                    <td class="my_rupiah">{{$mm_price->mf_amount}}</td>
                                    <td class="my_rupiah">0</td>
                                    <td class="my_rupiah">{{$mm_price->mf_amount}}</td>
                                    <td></td>
                                    @php($total_amount = $total_amount + $mm_price->mf_amount)
                                </tr>
                            @endfor
                        @endif
                    @endif
                    <tr>
                        <td colspan="4" style="text-align: right;">
                            @if($total_amount < 0)
                                @lang('labels.refund')
                            @else
                                @lang('labels.bill')
                            @endif
                        </td>
                        <td class="my_rupiah">{{abs($total_amount)}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::hidden('refund_amount', $total_amount) !!}
            {!! Form::hidden('refund_diff_days', $day_diff) !!}
            <table>
                <tr>
                    <td>{!! Form::label('fa_coach', trans('labels.fa_coach').': ', ['class' => 'control-label', 'style' => 'color: deeppink; margin-left: 10px;']) !!}</td>
                    <td style="padding-left: 20px;">
                        @if(!empty(Session::get('staff_session')))
                            {!! Form::select('fa_coach', $fa_coaches, Session::get('staff_session'), ['class' => 'form-control']) !!}
                        @else
                            {!! Form::select('fa_coach', $fa_coaches, Auth::id(), ['class' => 'form-control']) !!}
                        @endif
                    </td>
                </tr>
            </table>
            {!! Form::submit(trans('labels.create_termination_membership_request'), ['class' => 'btn btn-primary pull-right']) !!}
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@section('other-script')
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script>
        $('#fa_coach').select2();
    </script>
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