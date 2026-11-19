@extends('cf_lite.layouts.staff-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">
    <link href="{{asset('css/appointment.css')}}" rel="stylesheet">

    <style>

        table th{
            color: #ff1493;
        }

        .btn-primary {
            background-color: #ff1493;
            color: #ffffff;
            border-color: #ff1493;
        }

        .btn-primary:hover {
            color: #ff1493;
            background-color: transparent;
            border-color: #ff1493;
        }

        .pagination > .active > a, .pagination > .active > a:focus, .pagination > .active > a:hover {
            background-color: #ff1493;
            border-color: #ff1493;
        }

        div.dataTables_wrapper div.dataTables_length select {
            margin: 0 10px;
        }

    </style>

@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/appointment/book')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.lead_appointment_list')<br><small>({{date('d F Y', strtotime($date))}})</small></h2>
            </center>
        </div>
        <div class="col-md-12">
            {!! Form::open(['method' => 'POST', 'action' => ['AppointmentController@store_bulk_contacted_leads', $date]]) !!}
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive" style="overflow: visible;">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                            <tr>
                                <th>@lang('labels.indexing')</th>
                                <th>@lang('labels.fullname')</th>
                                <th style="width: 105px;">@lang('labels.phone')</th>
                                <th style="width: 125px;">@lang('labels.confirmed')</th>
                                <th style="width: 135px;">@lang('labels.no_answer')</th>
                                <th>@lang('labels.reschedule')</th>
                                <th style="width: 125px;">@lang('labels.last_contact')</th>
                                <th style="width: 95px;">@lang('labels.contact_again')?</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($guest_appointments) > 0)
                                @php
                                    $index = 1;
                                @endphp
                                @foreach($guest_appointments as $guest_appointment)
                                    <tr>
                                        <td>{{$index++}}</td>
                                        <td>{{$guest_appointment->guestappointment_buddyreferrallead->fullname}}</td>
                                        <td>{{$guest_appointment->guestappointment_buddyreferrallead->phone}}</td>
                                        <td>
                                            @php
                                                if (!empty($guest_appointment->guestappointment_gastatus)):
                                                    if ($guest_appointment->guestappointment_gastatus->status_id == '1'):
                                                        $confirm = true;
                                                    else:
                                                        $confirm = false;
                                                    endif;
                                                else:
                                                    $confirm = false;
                                                endif;
                                            @endphp
                                            {!! Form::checkbox('confirm['.$guest_appointment->id.']', $guest_appointment->id, $confirm, ['id' => 'confirm-'.$guest_appointment->id, 'disabled']) !!}
                                        </td>
                                        <td>
                                            @php
                                                $min_contact = 0;
                                                $max_contact = 10;
                                                if (!empty($guest_appointment->guestappointment_logcontactedlead)):
                                                    foreach ($guest_appointment->guestappointment_logcontactedlead as $contact_lead):
                                                        if ($contact_lead->status_id == '7'):
                                                            $min_contact++;
                                                        endif;
                                                    endforeach;
                                                endif;
                                            @endphp
                                            {!! Form::number('no_answer['.$guest_appointment->id.']', $min_contact, ['class' => 'form-control', 'min' => $min_contact, 'max' => $max_contact, 'style' => 'width: 125px;', 'step' => '1', 'id' => 'no_answer-'.$guest_appointment->id, 'disabled']) !!}
                                        </td>
                                        <td>
                                            {!! Form::checkbox('reschedule['.$guest_appointment->id.']', $guest_appointment->id, false, ['class' => 'reschedule-checkbox', 'id' => 'reschedule-'.$guest_appointment->id, 'disabled']) !!}
                                            {!! Form::date('reschedule_date['.$guest_appointment->id.']', null, ['class' => 'form-control','style' => 'margin: 0px 5px;', 'id' => 'reschedule_date-'.$guest_appointment->id, 'disabled']) !!}
                                            {!! Form::text('reschedule_time['.$guest_appointment->id.']', null, ['class' => 'form-control reschedule-time', 'style' => 'color: #ff0000; font-weight: 600; margin: 0px 5px;', 'id' => 'reschedule_time-'.$guest_appointment->id, 'disabled']) !!}
                                        </td>
                                        <td>
                                            @if(count($guest_appointment->guestappointment_logcontactedlead) > 0)
                                                @foreach($guest_appointment->guestappointment_logcontactedlead as $contacted_lead)
                                                    @if($loop->last)
                                                        {{date('d-m-Y', strtotime($contacted_lead->created_at))}}
                                                    @endif
                                                @endforeach
                                            @else
                                                @lang('labels.has_not_contacted_yet')
                                            @endif
                                        </td>
                                        <td>
                                            @php($open_contact = 1)
                                            @if(!empty($guest_appointment->guestappointment_gastatus))
                                                @if($guest_appointment->guestappointment_gastatus->sa_date != null)
                                                    @lang('labels.attended')
                                                    @php($open_contact = 0)
                                                @endif
                                            @endif

                                            @if($open_contact == 1)
                                                {!! Form::checkbox('contacted['.$guest_appointment->id.']', $guest_appointment->id, false, ['class' => 'contacted', 'id' => 'contacted-'.$guest_appointment->id]) !!}
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="col-md-12" style="margin: 20px 0;">
                {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary pull-right']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('vendors/moment/min/moment.min.js')}}"></script>
    <script src="{{asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script>
        $('#data-table').dataTable();

        $('.reschedule-time').datetimepicker({
            format: 'HH:mm'
        });

        $('.reschedule-checkbox').on('click', function () {
            var id = $(this).attr('id').split('-').pop();
            if ($(this).prop('checked') == true) {
                $('#reschedule_date-' + id).prop('disabled', false);
                $('#reschedule_time-' + id).prop('disabled', false);
            } else {
                $('#reschedule_date-' + id).prop('disabled', true);
                $('#reschedule_time-' + id).prop('disabled', true);
            }
        });

        $('.contacted').on('click', function () {
            var c_id = $(this).attr('id').split('-').pop();
            if ($(this).prop('checked') == true) {
                $('#confirm-' + c_id).prop('disabled', false);
                $('#no_answer-' + c_id).prop('disabled', false);
                $('#reschedule-' + c_id).prop('disabled', false);
            } else {
                $('#confirm-' + c_id).prop('disabled', true);
                $('#no_answer-' + c_id).prop('disabled', true);
                $('#reschedule-' + c_id).prop('disabled', true);
            }
        });
    </script>
@endsection