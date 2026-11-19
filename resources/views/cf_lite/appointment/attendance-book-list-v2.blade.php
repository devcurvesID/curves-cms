@extends('cf_lite.layouts.staff-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    {{--<link href="{{asset('css/appointment.css')}}" rel="stylesheet">--}}
    <link href="{{asset('fullcalendar/fullcalendar.min.css')}}" rel="stylesheet">
    <link href="{{asset('fullcalendar/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">

    <link href="{{asset('css/appointment.css')}}" rel="stylesheet">
    <style>
        #if_br {
            display: none;
        }

        .circle-medium {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            padding-top: 50px;
        }
    </style>
    <style>
        body {
            padding-bottom: 50px;
            font-family: 'Alegreya Sans', sans-serif;
            font-size: 14px;
        }

        #calendar {
            max-width: 900px;
            margin: 0 auto;
        }

        .fc-day:hover {
            background-color: rgba(240, 248, 255, 0.4) !important;
            cursor: pointer;
        }

        @media screen and (max-width: 767px) {
            .xs-no-padding {
                padding: 0px;
            }

            .btn-big-apm {
                padding: 0px;
                font-size: 14px;
                border-radius: 0px;
            }

            .modal-body {
                padding-bottom: 0px;
            }

            .fc td, .fc th {
                padding: 5px;
            }

            h2 {
                font-size: 18px;
            }

            .fc-toolbar .fc-center:before, .fc-toolbar .fc-center:after {
                font-size: 14px;
                line-height: 20px;
                padding: 2.5px;
                width: 25px;
                height: 25px;
            }

            .fc-day-grid-container {
                height: 300px !important;
            }
        }

        .bottom-left {
            background: transparent;
            border: 5px solid transparent;
            border-image: linear-gradient(-90deg, #800000 0%, #ffeb7f 45%, #ffffff 50%, #ffeb7f 55%, #800000 100%);
            border-image-slice: 1;

            padding: 15px;
        }

        .bottom-right {
            background: transparent;
            border: 5px solid transparent;
            border-image: linear-gradient(-90deg, #800000 0%, #ffeb7f 45%, #ffffff 50%, #ffeb7f 55%, #800000 100%);
            border-image-slice: 1;

            padding: 15px;
        }

        .photo-img {
            width: 75px;
            height: 75px;
            border-radius: 50%;
        }

        .list-view {
            margin-top: 15px;
            padding: 10px;
        }

        .list-view:hover {
            background-color: rgba(0, 0, 0, .1);
            cursor: pointer;
        }

        .result-container {
            max-height: 220px;
            overflow-y: scroll;
        }

        .btn-calon-anggota a {
            margin: 15px;
        }

        #data-table th {
            color: #ff1493;
        }

        div.dataTables_length select {
            margin: 0 10px
        }

        .pagination > .active > a, .pagination > .active > a:focus, .pagination > .active > a:hover {
            background-color: #ff1493;
            border-color: #ff1493;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination {
            margin: 20px 0;
        }

        .btn-primary{
            background-color: #ff1493;
            color: #ffffff;
            border-color: #ff1493;
        }

        .btn-primary:hover {
            color: #ff1493;
            background-color: transparent;
            border-color: #ff1493;
        }

        .btn-undo-appointment{
            height: 32px;
            padding-top: 8px;
        }

        .badge {
            font-size: 25px;
            width: 30px;
            border-radius: 50px;
            background-color: orangered;
            position: absolute;
            top: 45px;
            margin-left: -30px;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/dashboard')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">
                    @if(count($need_completed_list) > 0)
                        <span class="badge">{{count($need_completed_list)}}</span>
                    @endif
                    @lang('labels.lead_attendance_list')
                    <br>
                    <small>({{date('d F Y', strtotime($date))}})</small>
                </h2>
            </center>
        </div>
        <div class="col-md-8">
            <div id='calendar'></div>
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12" style="margin-top: 55px;">
                    <div class="row">
                        <div class="row">
                            <a class="btn btn-big-apm btn-fa-lead circle-medium"
                               href="{{url('cf-lite/corporate/corporation-options')}}"
                               style="float: left;">
                                <div class="fa fa-group btn-icon-apm"></div>
                                <div class="btn-title-apm">@lang('labels.corporate_payment')</div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            @php($send_date = date('Y-m-d', strtotime($date)))
            {!! Form::open(['method' => 'POST', 'action' => ['AppointmentController@store_bulk_attendance_leads', $send_date]]) !!}
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive" style="background-color: #fff;">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                            <tr>
                                <th>@lang('labels.indexing')</th>
                                <th>@lang('labels.fullname')</th>
                                <th>@lang('labels.phone')</th>
                                <th>@lang('labels.showed')</th>
                                <th>@lang('labels.sale')</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($guest_appointments) > 0)
                                @php($index = 1)
                                @foreach($guest_appointments as $guest_appointment)
                                    <tr>
                                        <td>{{$index}}</td>
                                        <td>{{$guest_appointment->guestappointment_buddyreferrallead->fullname}}</td>
                                        <td>{{$guest_appointment->guestappointment_buddyreferrallead->phone}}</td>
                                        <td>
                                            <?php
                                                if (!empty($guest_appointment->guestappointment_gastatus)):
                                                    if ($guest_appointment->guestappointment_gastatus->status_id == '3' || $guest_appointment->guestappointment_gastatus->status_id == '5' || $guest_appointment->guestappointment_gastatus->status_id == '6'):
                                                        $showup = true;
                                                        $showup_enable = 'disabled';
                                                    else:
                                                        $showup = false;
                                                        $showup_enable = '';
                                                    endif;
                                                else:
                                                    $showup = false;
                                                    $showup_enable = '';
                                                endif;
                                            ?>
                                            {!! Form::checkbox('showup['.$guest_appointment->id.']', $guest_appointment->id, $showup, ['id' => 'showup-'.$guest_appointment->id, $showup_enable, 'class' => 'showup']) !!}
                                        </td>
                                        <td>
                                            <?php
                                                if (!empty($guest_appointment->guestappointment_gastatus)):
                                                    if ($guest_appointment->guestappointment_gastatus->status_id == '5'):
                                                        $sale = true;
                                                    else:
                                                        $sale = false;
                                                    endif;
                                                elseif ($guest_appointment->guestappointment_gastatus->status_id == '6'):
                                                    $sale = false;
                                                endif;
                                            ?>
                                            {!! Form::checkbox('sale['.$guest_appointment->id.']', $guest_appointment->id, $sale, ['id' => 'sale-'.$guest_appointment->id, 'disabled']) !!}
                                        </td>
                                        <td>
                                            @if(!empty($guest_appointment->guestappointment_gastatus))
                                                @if(($guest_appointment->guestappointment_gastatus->status_id == '3') || ($guest_appointment->guestappointment_gastatus->status_id == '6'))
                                                    @if(empty($guest_appointment->guestappointment_buddyreferrallead->buddyreferrallead_guestdetail))
                                                        <a href="{{url('cf-lite/lead/'.$guest_appointment->id.'/complete-fa-data')}}" class="btn btn-info btn-sm">@lang('labels.complete_fa_data') <span class="fa fa-chevron-circle-right"></span></a>
                                                        <a href="{{url('cf-lite/appointment/undo',$guest_appointment->id)}}" class="btn btn-danger btn-undo-appointment" style="margin-left:10px;" data-toggle="tooltip" tooltip="Undo"><span class="fa fa-undo"></span></a>
                                                    @else
                                                        {{$guest_appointment->guestappointment_guestnosalereason->guestnosalereason_nosalereason->nosale_reason}}
                                                    @endif
                                                @elseif($guest_appointment->guestappointment_gastatus->status_id == '5')
                                                    @if(!empty($guest_appointment->guestappointment_buddyreferrallead->buddyreferrallead_memberpersonal))
                                                        @if(empty($guest_appointment->guestappointment_buddyreferrallead->buddyreferrallead_guestdetail))
                                                            <a href="{{url('cf-lite/member/'.$guest_appointment->guestappointment_buddyreferrallead->buddyreferrallead_memberpersonal->user_id.'/edit')}}" class="btn btn-success btn-sm">@lang('labels.complete_member_data') <span class="fa fa-chevron-circle-right"></span></a>
                                                        @else
                                                            <a href="{{url('cf-lite/member/'.$guest_appointment->guestappointment_buddyreferrallead->buddyreferrallead_memberpersonal->user_id.'/edit')}}" target="_blank" class="btn btn-default btn-sm">@lang('labels.see_member_data') <span class="fa fa-chevron-circle-right"></span></a>
                                                        @endif
                                                    @else
                                                        {{-- OLD ONE --}}
                                                        {{-- <a href="{{url('cf-lite/pre-member/'.$guest_appointment->guest_id.'/membership')}}" class="btn btn-warning">
                                                            @lang('messages.please_finish_the_first_payment') <span class="fa fa-chevron-right"></span>
                                                        </a> --}}
                                                        {{-- END OF OLD ONE --}}
                                                        {{-- NEW ONE February 5th, 2020 --}}
                                                        <a href="{{url('cf-lite/cashier/membership/'.$guest_appointment->guest_id.'/pay')}}" class="btn btn-warning">
                                                            @lang('messages.please_finish_the_first_payment') <span class="fa fa-chevron-right"></span>
                                                        </a>
                                                        {{-- END OF NEW ONE --}}
                                                        <a href="{{url('cf-lite/appointment/undo',$guest_appointment->id)}}" class="btn btn-danger btn-undo-appointment" style="margin-left:10px;" data-toggle="tooltip" tooltip="Undo"><span class="fa fa-undo"></span></a>
                                                    @endif
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    @php($index++)
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary pull-right']) !!}
            {!! Form::close() !!}
        </div>

        @if(count($need_completed_list) > 0)
            <div class="col-md-12" style="margin-top: 30px; border-top: 1px solid deeppink;">
                <h4 style="margin-top: 20px; margin-bottom: -5px;">@lang('labels.need_completed')</h4>
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive" style="background-color: #fff;">
                            <table class="table table-hover table-bordered" id="data-table-2">
                                <thead>
                                <tr>
                                    <th>@lang('labels.indexing')</th>
                                    <th>@lang('labels.fullname')</th>
                                    <th>@lang('labels.phone')</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @php($index = 1)
                                @foreach($need_completed_list as $need_completed)
                                    <tr>
                                        <td>{{$index}}</td>
                                        <td>{{$need_completed->guestappointment_buddyreferrallead->fullname}}</td>
                                        <td>{{$need_completed->guestappointment_buddyreferrallead->phone}}</td>
                                        <td>
                                            @if(!empty($need_completed->guestappointment_gastatus))
                                                @if(($need_completed->guestappointment_gastatus->status_id == '3') || ($need_completed->guestappointment_gastatus->status_id == '6'))
                                                    @if(empty($need_completed->guestappointment_buddyreferrallead->buddyreferrallead_guestdetail))
                                                        <a href="{{url('cf-lite/lead/'.$need_completed->id.'/complete-fa-data')}}" class="btn btn-info btn-sm">@lang('labels.complete_fa_data') <span class="fa fa-chevron-circle-right"></span></a>
                                                        <a href="{{url('cf-lite/appointment/undo',$need_completed->id)}}" class="btn btn-danger btn-undo-appointment" style="margin-left:10px;" data-toggle="tooltip" tooltip="Undo"><span class="fa fa-undo"></span></a>
                                                    @else
                                                        {{$need_completed->guestappointment_guestnosalereason->guestnosalereason_nosalereason->nosale_reason}}
                                                    @endif
                                                @elseif($need_completed->guestappointment_gastatus->status_id == '5')
                                                    @if(!empty($need_completed->guestappointment_buddyreferrallead->buddyreferrallead_memberpersonal))
                                                        @if(empty($need_completed->guestappointment_buddyreferrallead->buddyreferrallead_guestdetail))
                                                            <a href="{{url('cf-lite/member/'.$need_completed->guestappointment_buddyreferrallead->buddyreferrallead_memberpersonal->user_id.'/edit')}}" class="btn btn-success btn-sm">@lang('labels.complete_member_data') <span class="fa fa-chevron-circle-right"></span></a>
                                                        @else
                                                            <a href="{{url('cf-lite/member/'.$need_completed->guestappointment_buddyreferrallead->buddyreferrallead_memberpersonal->user_id.'/edit')}}" target="_blank" class="btn btn-default btn-sm">@lang('labels.see_member_data') <span class="fa fa-chevron-circle-right"></span></a>

                                                        @endif
                                                    @else
                                                        <a href="{{url('cf-lite/pre-member/'.$need_completed->guest_id.'/membership')}}" class="btn btn-warning">
                                                            @lang('messages.please_finish_the_first_payment') <span class="fa fa-chevron-right"></span>
                                                        </a>
                                                        <a href="{{url('cf-lite/appointment/undo',$need_completed->id)}}" class="btn btn-danger btn-undo-appointment" style="margin-left:10px;" data-toggle="tooltip" tooltip="Undo"><span class="fa fa-undo"></span></a>
                                                    @endif
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                    @php($index++)
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>

    {!! Form::open(['method' => 'GET', 'action' => 'AppointmentController@attendance_main', 'id' => 'form-attendance-date']) !!}
    {!! Form::hidden('date_chosen', null) !!}
    {!! Form::close() !!}
@endsection

@section('other-script')
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('fullcalendar/fullcalendar.min.js')}}"></script>

    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $('#data-table').dataTable();

        $('#data-table').on('click', '.showup', function () {
            var m_id = $(this).attr('id').split('-').pop();
            if ($(this).prop('checked') == true) {
                $('#sale-' + m_id).prop('disabled', false);
            } else {
                $('#sale-' + m_id).prop('disabled', true);
            }
        });
    </script>

    <script>
        var appointment_date;
        $(document).ready(function () {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,basicWeek,basicDay'
                },
                defaultView: 'basicWeek',
                aspectRatio: 3,
                navLinks: false, // can click day/week names to navigate views
                editable: false,
                eventLimit: true, // allow "more" link when too many events
                weekends: true, // will Hide Saturdays and Sundays
                selectable: true,
                select: function (start, end, jsEvent, view) {
                    appointment_date = moment(start).format();
                    $('input[name="date_chosen"]').val(appointment_date);
                    $('#form-attendance-date').submit();
                },
                events: [
                        @foreach($fas as $fa)
                            @if((strtotime($fa->fa_date.' '.$fa->fa_hour) < strtotime(\Carbon\Carbon::now())) && (($fa->guestappointment_gastatus->status_id == '1') || ($fa->guestappointment_gastatus->status_id == '2') || ($fa->guestappointment_gastatus->status_id == '4')))
                                {
                                    title: 'FA-{{$fa->guestappointment_buddyreferrallead->fullname}}',
                                    start: '{{date('Y-m-d', strtotime($fa->fa_date)).'T'.date('H:i:s', strtotime(\Carbon\Carbon::parse($fa->fa_hour)->setTimezone(Session::get('timezone'))))}}',
                                    backgroundColor: '#838788',
                                    textColor: '#b4b7b9',
                                    borderColor: '#414345',
                                    className: 'fa-cal {{'fa-cal-'.$fa->id}}'
                                },
                            @else
                                {
                                    title: 'FA-{{$fa->guestappointment_buddyreferrallead->fullname}}',
                                    start: '{{date('Y-m-d', strtotime($fa->fa_date)).'T'.date('H:i:s', strtotime(\Carbon\Carbon::parse($fa->fa_hour)->setTimezone(Session::get('timezone'))))}}',
                                    backgroundColor: '#afeeee',
                                    textColor: '#465f5f',
                                    borderColor: '#7aa6a6',
                                    className: 'fa-cal {{'fa-cal-'.$fa->id}}'
                                },
                            @endif
                        @endforeach
                ]
            });

            $('.fc-day').css('cursor', 'pointer');

            $('.fc-day').each(function (index) {
                if ($(this).attr('data-date') == '{{date('Y-m-d', strtotime($date))}}') {
                    console.log(index + ': ' + $(this).attr('data-date'));
                    $(this).css('background', 'rgba(184, 130, 238, 0.7)');
                }
            });

            $('.fc-basicDay-button').on('click', function () {
                $('.fc-day').each(function (index) {
                    if ($(this).attr('data-date') == '{{date('Y-m-d', strtotime($date))}}') {
                        console.log(index + ': ' + $(this).attr('data-date'));
                        $(this).css('background', 'rgba(184, 130, 238, 0.7)');
                    }
                });
            });

            $('.fc-basicWeek-button').on('click', function () {
                $('.fc-day').each(function (index) {
                    if ($(this).attr('data-date') == '{{date('Y-m-d', strtotime($date))}}') {
                        console.log(index + ': ' + $(this).attr('data-date'));
                        $(this).css('background', 'rgba(184, 130, 238, 0.7)');
                    }
                });
            });

            $('.fc-month-button').on('click', function () {
                $('.fc-day').each(function (index) {
                    if ($(this).attr('data-date') == '{{date('Y-m-d', strtotime($date))}}') {
                        console.log(index + ': ' + $(this).attr('data-date'));
                        $(this).css('background', 'rgba(184, 130, 238, 0.7)');
                    }
                });
            });

            $('.fc-month-button').on('click', function () {
                $('.fc-day').each(function (index) {
                    if ($(this).attr('data-date') == '{{date('Y-m-d', strtotime($date))}}') {
                        console.log(index + ': ' + $(this).attr('data-date'));
                        $(this).css('background', 'rgba(184, 130, 238, 0.7)');
                    }
                });
            });

            $('.fc-next-button').on('click', function () {
                $('.fc-day').each(function (index) {
                    if ($(this).attr('data-date') == '{{date('Y-m-d', strtotime($date))}}') {
                        console.log(index + ': ' + $(this).attr('data-date'));
                        $(this).css('background', 'rgba(184, 130, 238, 0.7)');
                    }
                });
            });

            $('.fc-prev-button').on('click', function () {
                $('.fc-day').each(function (index) {
                    if ($(this).attr('data-date') == '{{date('Y-m-d', strtotime($date))}}') {
                        console.log(index + ': ' + $(this).attr('data-date'));
                        $(this).css('background', 'rgba(184, 130, 238, 0.7)');
                    }
                });
            });

            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>
@endsection