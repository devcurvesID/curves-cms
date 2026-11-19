@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/appointment.css')}}" rel="stylesheet">
@endsection

@section('content')
    @if(Session::has('date') && Session::has('time') && Session::has('appointment_type'))
        @if(!empty($lead))
            <div class="row">
                <div class="col-sm-12">
                    <center>
                        <h2 class="page-title">
                            @if(Session::has('appointment_type') == 'FA')
                                @lang('labels.fitness_assessment_appointment')
                            @elseif(Session::has('appointment_type') == 'WM')
                                @lang('labels.weigh_n_measure_appointment')
                            @elseif(Session::has('appointment_type') == 'WO')
                                @lang('labels.workout_appointment')
                            @endif
                        </h2>
                    </center>
                </div>

                <div class="col-md-offset-3 col-md-6">
                    <div class="guest-card">
                        {{--<h1 class="page-header text-center">--}}
                            {{--@if(Session::has('appointment_type') == 'FA')--}}
                                {{--@lang('labels.fitness_assessment_appointment')--}}
                            {{--@elseif(Session::has('appointment_type') == 'WM')--}}
                                {{--@lang('labels.weigh_n_measure_appointment')--}}
                            {{--@elseif(Session::has('appointment_type') == 'WO')--}}
                                {{--@lang('labels.workout_appointment')--}}
                            {{--@endif--}}
                        {{--</h1>--}}

                        <table>
                            <tr>
                                <td>@lang('labels.name')</td>
                                <td>:</td>
                                <td>{{$lead->fullname}}</td>
                            </tr>
                            <tr>
                                <td>@lang('labels.phone')</td>
                                <td>:</td>
                                <td>{{$lead->phone}}</td>
                            </tr>
                            <tr>
                                <td>@lang('labels.source')</td>
                                <td>:</td>
                                <td>
                                    @if($lead->is_br == 'BR')
                                        @lang('labels.buddy_referral')
                                    @elseif($lead->is_br == 'NotBR')
                                        {{$lead->buddyreferrallead_membersource->source}}
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3"></td>
                            </tr>
                            <tr>
                                <td>@lang('labels.appointment_date')</td>
                                <td>:</td>
                                <td>{{date('d F Y', strtotime(Session::get('date')))}}</td>
                            </tr>
                            <tr>
                                <td>@lang('labels.appointment_time')</td>
                                <td>:</td>
                                <td>{{date('H:i T', strtotime(Session::get('time')))}}</td>
                            </tr>
                        </table>

                        <br>
                        {!! Form::open(['url' => 'cf-lite/appointment/submit/fa']) !!}
                        <div class="form-group">
                            {!! Form::textarea('note', null, ['class' => 'form-control', 'placeholder' => trans('labels.add').' '.trans('labels.note'), 'rows' => '4']) !!}
                        </div>
                        {!! Form::hidden('appointment_type', Session::get('appointment_type')) !!}
                        {!! Form::hidden('date', Session::get('date')) !!}
                        {!! Form::hidden('time', Session::get('time')) !!}
                        {!! Form::hidden('lead_id', $lead->id) !!}
                        {!! Form::submit(trans('labels.submit'), ['class' => 'btn btn-primary form-control']) !!}
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>

        @endif
    @endif
@endsection