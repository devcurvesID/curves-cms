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
            <a href="{{url('cf-lite/continue-membership/cancel')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.continue_membership_cancellation_detail')</h2>
            </center>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <table class="member-detail">
                <tr>
                    <td>@lang('labels.name')</td>
                    <td>:</td>
                    <td>{{$continue->continuemembership_user->name}}</td>
                </tr>
                <tr>
                    <td>@lang('labels.membership_type')</td>
                    <td>:</td>
                    <td>{{$continue->continuemembership_membershiptype->membership_type_name}}</td>
                </tr>
                <tr>
                    <td>@lang('labels.starting_from')</td>
                    <td>:</td>
                    <td>{{date('d F Y', strtotime($continue->from_date))}}</td>
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
                            {{$payment->amount_paid}}
                        </td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: right;">@lang('labels.refund')</td>
                        <td class="my_rupiah">{{$payment->amount_paid}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['method' => 'POST', 'action' => ['ContinueMembershipController@create_refund', $continue->id]]) !!}
            {!! Form::hidden('payment', $payment->id) !!}
            <div class="form-horizontal">
                <div class="form-group">
                    {!! Form::label('cancellation_reason_id', trans('labels.cancellation_reason').': ', ['class' => 'control-label col-md-3', 'style' => 'color: black; text-align: right;']) !!}
                    <div class="col-md-4">
                        {!! Form::select('cancellation_reason_id', $nosalereasons, null, ['class' => 'form-control', 'placeholder' => trans('labels.choose_reason')]) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('cancellation_reason', trans('labels.another_cancellation_reason').': ', ['class' => 'control-label col-md-3', 'style' => 'color: black; text-align: right;']) !!}
                    <div class="col-md-4">
                        {!! Form::textarea('cancellation_reason', null, ['class' => 'form-control', 'rows' => '3']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('submit_by', trans('labels.staff_admin').': ', ['class' => 'control-label col-md-3', 'style' => 'color: deeppink; text-align: right;']) !!}
                    <div class="col-md-4">
                        @if(!empty(Session::get('staff_session')))
                            {!! Form::select('submit_by', $fa_coaches, Session::get('staff_session'), ['class' => 'form-control']) !!}
                        @else
                            {!! Form::select('submit_by', $fa_coaches, Auth::id(), ['class' => 'form-control']) !!}
                        @endif
                    </div>
                </div>
            </div>
            <div class="form-group">
                {!! Form::submit(trans('labels.process_refund_submission'), ['class' => 'btn btn-primary']) !!}
            </div>
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