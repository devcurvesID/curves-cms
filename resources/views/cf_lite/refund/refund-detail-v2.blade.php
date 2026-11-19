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
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/cancelling-membership/list')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.membership_termination_detail')</h2>
            </center>
        </div>
    </div>

    {!! Form::open(['method' => 'POST', 'action' => ['RefundController@refund_cancellation_process', $refund_submission->id]]) !!}
    <div class="row">
        <div class="col-md-6">
            <table class="member-detail">
                <tr>
                    <td>@lang('labels.payment_number')</td>
                    <td>:</td>
                    <td>{{$refund_submission->refund_number}}</td>
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
                        {!! Form::date('start_membership', \Carbon\Carbon::parse($member->user_memberstatus->from_date), ['style' => 'display: none;', 'readonly']) !!}
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
                    <td>{{\Carbon\Carbon::parse($refund_submission->refund_submission_date)->format('d-m-Y')}}</td>
                </tr>
                <tr>
                    <td>@lang('labels.date')</td>
                    <td>:</td>
                    <td>
                        @if($refund_submission->refund_status == 'Pending')
                            {!! Form::date('date', \Carbon\Carbon::today(), ['class' => 'form-control', 'min' => \Carbon\Carbon::parse($refund_submission->refund_submission_date)->addMonth()->format('Y-m-d')]) !!}
                        @else
                            {{\Carbon\Carbon::parse($refund_submission->refund_process_date)->format('d-m-Y')}}
                        @endif
                    </td>
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
            @if($refund_submission->refund_status == 'Pending')
                <table>
                    <tr>
                        <td>{!! Form::label('fa_coach', trans('labels.fa_coach').': ', ['class' => 'control-label', 'style' => 'color: deeppink; margin-left: 10px;']) !!}</td>
                        <td style="padding-left: 20px;">
                            @if(!empty($refund_submission->submit_by))
                                {!! Form::select('fa_coach', $fa_coaches, $refund_submission->submit_by, ['class' => 'form-control']) !!}
                            @else
                                @if(!empty(Session::get('staff_session')))
                                    {!! Form::select('fa_coach', $fa_coaches, Session::get('staff_session'), ['class' => 'form-control']) !!}
                                @else
                                    {!! Form::select('fa_coach', $fa_coaches, Auth::id(), ['class' => 'form-control']) !!}
                                @endif
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>{!! Form::label('cancellation_reason_id', trans('labels.cancellation_reason').': ', ['class' => 'control-label', 'style' => 'color: deeppink; margin-left: 10px;']) !!}</td>
                        <td style="padding-left: 20px;">
                            {!! Form::select('cancellation_reason_id', $nosalereasons, null, ['class' => 'form-control', 'placeholder' => trans('labels.choose_reason'), 'style' => 'margin-top: 10px;']) !!}
                        </td>
                    </tr>
                    <tr>
                        <td>{!! Form::label('cancellation_reason', trans('labels.cancellation_reason').' *'.trans('labels.optional').': ', ['class' => 'control-label', 'style' => 'color: deeppink; margin-left: 10px;']) !!}</td>
                        <td style="padding-left: 20px;">
                            {!! Form::textarea('cancellation_reason', null, ['class' => 'form-control', 'rows' => '4', 'style' => 'margin-top: 10px;']) !!}
                        </td>
                    </tr>
                </table>

                @if($day_diff > 3)
                    @if(strtotime(\Carbon\Carbon::parse($refund_submission->refund_submission_date)->addMonth()) <= strtotime(\Carbon\Carbon::today()))
                        {!! Form::submit(trans('labels.process'), ['class' => 'btn btn-success pull-right', 'name' => 'process']) !!}
                    @endif
                @else
                    {!! Form::submit(trans('labels.process'), ['class' => 'btn btn-success pull-right', 'name' => 'process']) !!}
                @endif

                {!! Form::submit(trans('labels.cancel'), ['class' => 'btn btn-danger pull-right', 'name' => 'cancel']) !!}
            @endif
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