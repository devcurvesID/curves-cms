@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/appointment.css')}}" rel="stylesheet">
    <link href="{{asset('bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">
    <style>
        .modal-header{
            background: linear-gradient(to bottom right, white, mediumpurple);
            color: white;
        }
        .modal-title{
            color: #414042;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')

    @if(Session::has('appointment'))
        <div class="row">
            <div class="col-sm-12">
                <center>
                    <h2 class="page-title">
                        @lang('labels.fitness_assessment_appointment')
                    </h2>
                </center>
            </div>

            <div class="col-md-offset-3 col-md-6">
                @if(Session::get('appointment') == 'Guest')
                    @if(!empty($appointment))
                        @php
                            $appointment_status = $appointment->guestappointment_gastatus;
                        @endphp
                        <div class="guest-card">
                            {{--<h1 class="page-header text-center">@lang('labels.fitness_assessment_appointment')</h1>--}}
                            <h4 style="font-weight: bold;">
                                @lang('labels.status'):
                                @if(empty($appointment_status))
                                    @lang('labels.not_confirmed_yet')
                                @else
                                    @if($appointment_status->status_id == '1')
                                        @lang('labels.confirmed') ({{date('d F Y', strtotime($appointment_status->c_date))}})
                                    @elseif($appointment_status->status_id == '2')
                                        @lang('labels.cancelled') ({{date('d F Y', strtotime($appointment_status->c_date))}})
                                    @elseif($appointment_status->status_id == '3')
                                        @lang('labels.showed') ({{date('d F Y', strtotime($appointment_status->s_date))}})
                                    @elseif($appointment_status->status_id == '4')
                                        @lang('labels.not_showed') ({{date('d F Y', strtotime($appointment_status->s_date))}})
                                    @elseif($appointment_status->status_id == '5')
                                        @lang('labels.sold') ({{date('d F Y', strtotime($appointment_status->sa_date))}})
                                    @elseif($appointment_status->status_id == '6')
                                        @lang('labels.failed') ({{date('d F Y', strtotime($appointment_status->sa_date))}})
                                    @endif
                                @endif
                            </h4>
                            <div class="col-md-12" style="margin-bottom: 10px">
                                <table class="table-appointment-schedule">
                                    <tr>
                                        <td>@lang('labels.appointment_date')</td>
                                        <td>:</td>
                                        <td>{{date('d F Y', strtotime($appointment->fa_date))}}</td>
                                    </tr>
                                    <tr>
                                        <td>@lang('labels.appointment_time')</td>
                                        <td>:</td>
                                        <td>{{date('H:i T', strtotime($appointment->fa_hour))}}</td>
                                    </tr>
                                </table>
                            </div>
                            <hr>
                            <table>
                                <tr>
                                    <td>@lang('labels.name')</td>
                                    <td>:</td>
                                    <td>{{$appointment->guestappointment_buddyreferrallead->fullname}}</td>
                                    @if(empty($appointment_status) || (!empty($appointment_status) && ($appointment_status->status_id == '1' || $appointment_status->status_id == '2' || $appointment_status->status_id == '4')))
                                        <td rowspan="4">
                                            <button type="button" id="reschedule-btn" class="btn btn-primary btn-lg" data-toggle="modal"
                                                    data-target="#reschedule-modal"><div class="fa fa-calendar"></div><div>@lang('labels.reschedule')</div></button>
                                        </td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>@lang('labels.phone')</td>
                                    <td>:</td>
                                    <td>{{$appointment->guestappointment_buddyreferrallead->phone}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('labels.source')</td>
                                    <td>:</td>
                                    <td>
                                        @if($appointment->guestappointment_buddyreferrallead->is_br == 'BR')
                                            @lang('labels.buddy_referral')
                                        @elseif($appointment->guestappointment_buddyreferrallead->is_br == 'NotBR')
                                            {{$appointment->guestappointment_buddyreferrallead->buddyreferrallead_membersource->source}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang('labels.staff')</td>
                                    <td>:</td>
                                    <td>{{$appointment->guestappointment_buddyreferrallead->buddyreferrallead_user->name}}</td>
                                </tr>
                            </table>
                            @if(!empty($appointment->guestappointment_ganotes))
                                <hr>
                                <div class="panel panel-warning">
                                    <div class="panel-heading">@lang('labels.note')</div>
                                    <div class="panel-body">{{$appointment->guestappointment_ganotes->note}}</div>
                                </div>
                            @endif
                        </div>
                        {!! Form::open(['method' => 'POST', 'action' => ['AppointmentController@update_status', $appointment->id], 'id' => 'form-status-appointment']) !!}
                        <div class="appointment-confirmation-card">
                            {!! Form::hidden('status_id', null) !!}
                            @if(empty($appointment_status))
                                <div class="form-group">
                                    <div class="col-md-6">
                                        {!! Form::button(trans('labels.confirm'), ['class' => 'btn btn-primary form-control', 'id' => 'status-confirm']) !!}
                                    </div>
                                    <div class="col-md-6">
                                        {!! Form::button(trans('labels.cancel'), ['class' => 'btn btn-primary form-control', 'id' => 'status-cancel']) !!}
                                    </div>
                                </div>
                            @else
                                <div class="form-group">
                                    @if($appointment_status->status_id == '1')
                                        <div class="col-md-6">
                                            {!! Form::button(trans('labels.showed'), ['class' => 'btn btn-primary form-control', 'id' => 'status-showup']) !!}
                                        </div>
                                        <div class="col-md-6">
                                            {!! Form::button(trans('labels.not_showed'), ['class' => 'btn btn-primary form-control', 'id' => 'status-noshow']) !!}
                                        </div>
                                    @elseif($appointment_status->status_id == '3')
                                        <div class="col-md-6">
                                            {!! Form::button(trans('labels.sale'), ['class' => 'btn btn-sale form-control', 'id' => 'status-sale']) !!}
                                        </div>
                                        <div class="col-md-6">
                                            {!! Form::button(trans('labels.no_sale'), ['class' => 'btn btn-nosale form-control', 'id' => 'status-nosale', 'data-toggle' => 'modal', 'data-target' => '#nosale-reason-modal']) !!}
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>

                        <!-- Modal No Sale Reasons -->
                        <div class="modal fade" id="nosale-reason-modal" tabindex="-1" role="dialog" aria-labelledby="nosale-modalLabel">
                            <div class="modal-dialog" role="document">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="nosale-modalLabel">@lang('labels.reason')</h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="form-horizontal">
                                                {!! Form::label('nosale_reason', trans('labels.reason'), ['class' => 'control-label col-md-3']) !!}
                                                <div class="col-md-6">
                                                    {!! Form::select('nosale_reason', $nosalereasons, null, ['class' => 'form-control', 'placeholder' => '-- '.trans('labels.choose').' '.trans('labels.reason').' --']) !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('labels.cancel')</button>
                                        <button type="button" class="btn btn-primary" id="form-nosale-reason-submit-btn">@lang('labels.process')</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    @endif
                @elseif(Session::get('appointment') == 'Member')
                    @if(!empty($appointment))
                        @php
                            $appointment_status = $appointment->memberappointment_mastatus;
                            if ($appointment->ap_type_id == '1'):
                                $member_card = 'member-card-wm';
                            elseif ($appointment->ap_type_id == '2'):
                                $member_card = 'member-card-wo';
                            endif;
                        @endphp
                        <div class="member-card {{$member_card}}">
                            <h1 class="page-header text-center">
                                @if($appointment->ap_type_id == '1')
                                    @lang('labels.weigh_n_measure_appointment')
                                @elseif($appointment->ap_type_id == '2')
                                    @lang('labels.workout_appointment')
                                @endif
                            </h1>
                            <h4 style="font-weight: bold;">
                                @lang('labels.status'):
                                @if(empty($appointment_status))
                                    @lang('labels.not_confirmed_yet')
                                @else
                                    @if($appointment_status->status_id == '1')
                                        @lang('labels.confirmed') ({{date('d F Y', strtotime($appointment_status->c_date))}})
                                    @elseif($appointment_status->status_id == '2')
                                        @lang('labels.cancelled') ({{date('d F Y', strtotime($appointment_status->c_date))}})
                                    @elseif($appointment_status->status_id == '3')
                                        @lang('labels.showed') ({{date('d F Y', strtotime($appointment_status->s_date))}})
                                    @elseif($appointment_status->status_id == '4')
                                        @lang('labels.not_showed') ({{date('d F Y', strtotime($appointment_status->s_date))}})
                                    @endif
                                @endif
                            </h4>
                            <table class="table-appointment-schedule">
                                <tr>
                                    <td>@lang('labels.appointment_date')</td>
                                    <td>:</td>
                                    <td>{{date('d F Y', strtotime($appointment->app_date))}}</td>
                                    @if(empty($appointment_status) || (!empty($appointment_status) && ($appointment_status->status_id == '1' || $appointment_status->status_id == '2' || $appointment_status->status_id == '4')))
                                        <td rowspan="4">
                                            <button type="button" id="reschedule-btn" class="btn btn-primary btn-lg" data-toggle="modal"
                                                    data-target="#reschedule-modal"><div class="fa fa-calendar"></div><div>@lang('labels.reschedule')</div></button>
                                        </td>
                                    @endif
                                </tr>
                                <tr>
                                    <td>@lang('labels.appointment_time')</td>
                                    <td>:</td>
                                    <td>{{date('H:i T', strtotime($appointment->app_hour))}}</td>
                                </tr>
                            </table>
                            <hr>
                            <div class="row">
                                <div class="col-md-4">
                                    @if(!empty($appointment->memberappointment_user->user_memberpersonal->photo))
                                        <img class="img-circle member-card-detail-img" src="{{asset('images/member_photos/'.$appointment->memberappointment_user->user_memberpersonal->photo)}}">
                                    @else
                                        <img class="img-circle member-card-detail-img" src="{{asset('images/img.png')}}">
                                    @endif
                                </div>
                                <div class="col-md-8">
                                    <table id="member-detail-status">
                                        <tr>
                                            <td><span class="fa fa-female"></span></td>
                                            <td>:</td>
                                            <td>{{$appointment->memberappointment_user->name}}</td>
                                        </tr>
                                        <tr>
                                            <td><span class="fa fa-phone-square"></span></td>
                                            <td>:</td>
                                            <td>
                                                @if(!empty($appointment->memberappointment_user->user_memberpersonal->phone))
                                                    {{$appointment->memberappointment_user->user_memberpersonal->phone}}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><span class="fa fa-users" title="@lang('labels.staff')"></span></td>
                                            <td>:</td>
                                            <td>{{$appointment->memberappointment_staff->name}}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                            @if(!empty($appointment->memberappointment_memberappointmentnote))
                                <hr>
                                <div class="panel panel-warning">
                                    <div class="panel-heading">@lang('labels.note')</div>
                                    <div class="panel-body">{{$appointment->memberappointment_memberappointmentnote->notes}}</div>
                                </div>
                            @endif
                        </div>
                        <div class="appointment-confirmation-card">
                            {!! Form::open(['method' => 'POST', 'action' => ['AppointmentController@update_status', $appointment->id], 'id' => 'form-status-appointment']) !!}
                            {!! Form::hidden('status_id', null) !!}
                            @if(empty($appointment_status))
                                <div class="form-group">
                                    <div class="col-md-6">
                                        {!! Form::button(trans('labels.confirm'), ['class' => 'btn btn-primary form-control', 'id' => 'status-confirm']) !!}
                                    </div>
                                    <div class="col-md-6">
                                        {!! Form::button(trans('labels.cancel'), ['class' => 'btn btn-primary form-control', 'id' => 'status-cancel']) !!}
                                    </div>
                                </div>
                            @else
                                <div class="form-group">
                                    @if($appointment_status->status_id == '1')
                                        <div class="col-md-6">
                                            {!! Form::button(trans('labels.showed'), ['class' => 'btn btn-primary form-control', 'id' => 'status-showup']) !!}
                                        </div>
                                        <div class="col-md-6">
                                            {!! Form::button(trans('labels.not_showed'), ['class' => 'btn btn-primary form-control', 'id' => 'status-noshow']) !!}
                                        </div>
                                    @endif
                                </div>
                            @endif
                            {!! Form::close() !!}
                        </div>
                    @endif
                @endif
            </div>
        </div>
    @endif

    @if(empty($appointment_status) || (!empty($appointment_status) && ($appointment_status->status_id == '1' || $appointment_status->status_id == '2' || $appointment_status->status_id == '4')))
        <!-- Modal Reschedule -->
        <div class="modal fade" id="reschedule-modal" tabindex="-1" role="dialog" aria-labelledby="reschedule-modalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="reschedule-modalLabel">@lang('labels.reschedule')</h4>
                    </div>
                    @if(Session::get('appointment') == 'Guest')
                        {!! Form::open(['method' => 'PATCH', 'action' => ['AppointmentController@store_fa_reschedule', $appointment->id]]) !!}
                        @php
                            $reschedule_date = $appointment->fa_date;
                            $reschedule_time = $appointment->fa_hour;
                        @endphp
                    @elseif(Session::get('appointment') == 'Member')
                        {!! Form::open(['method' => 'PATCH', 'action' => ['AppointmentController@store_ma_reschedule', $appointment->id]]) !!}
                        @php
                            $reschedule_date = $appointment->app_date;
                            $reschedule_time = $appointment->app_hour;
                        @endphp
                    @endif
                    <div class="modal-body">
                        <div class="row">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <label class="col-md-2 control-label"><span class="fa fa-calendar" title="@lang('labels.choose') @lang('labels.date')"></span> </label>
                                    <div class="col-md-6">
                                        {!! Form::text('reschedule_date', date('Y-m-d', strtotime($reschedule_date)), ['class' => 'form-control', 'id' => 'reschedule_date']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-md-2 control-label"><span class="fa fa-clock-o" title="@lang('labels.choose') @lang('labels.time')"></span> </label>
                                    <div class="col-md-6">
                                        {!! Form::text('reschedule_time', date('H:i T', strtotime($reschedule_time)), ['class' => 'form-control', 'id' => 'reschedule_time']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('labels.cancel')</button>
                        {!! Form::submit(trans('labels.process'), ['class' => 'btn btn-primary']) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    @endif
@endsection

@section('other-script')
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#status-confirm').on('click', function () {
                $('input[name="status_id"]').val(1);
                $('#form-status-appointment').submit();
            });

            $('#status-cancel').on('click', function () {
                $('input[name="status_id"]').val(2);
                $('#form-status-appointment').submit();
            });

            $('#status-showup').on('click', function () {
                $('input[name="status_id"]').val(3);
                $('#form-status-appointment').submit();
            });

            $('#status-noshow').on('click', function () {
                $('input[name="status_id"]').val(4);
                $('#form-status-appointment').submit();
            });

            $('#status-sale').on('click', function () {
                $('input[name="status_id"]').val(5);
                $('#form-status-appointment').submit();
            });

            /*$('#status-nosale').on('click', function () {
                $('input[name="status_id"]').val(6);
                $('#form-status-appointment').submit();
            });*/

            $('#form-nosale-reason-submit-btn').on('click', function () {
                $('input[name="status_id"]').val(6);
                if ($('#nosale_reason').val() == '') {
                    return false;
                }
                $('#form-status-appointment').submit();
            });

            $('#member-detail-status td:last-child').css('padding-left', '10px');
        });
    </script>

    <script>
        $('#reschedule_date').datetimepicker({
            format: 'YYYY/MM/DD'
        });

        $('#reschedule_time').datetimepicker({
            format: 'HH:mm'
        });
    </script>
@endsection