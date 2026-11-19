@extends('cf_lite.layouts.staff-template')

@section('other-style')

    <meta name="_token" content="{!! csrf_token() !!}"/>
    <link href="{{asset('css/appointment.css')}}" rel="stylesheet">
    <link href="{{asset('fullcalendar/fullcalendar.min.css')}}" rel="stylesheet">
    <link href="{{asset('fullcalendar/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">

    <link href="{{asset('bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">

    {{--<link href="{{asset('css/calendar/my-calendar.css')}}" rel="stylesheet">--}}
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

        .result-container{
            max-height: 220px;
            overflow-y: scroll;
        }

    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')


    <div id='calendar'></div>
    <div id="section-bottom" style="padding-left: 5%; padding-right: 5%; margin-top: 50px;">
        <div class="row">
            <div class="col-xs-6">
                <div class="bottom-left">
                    <div id="the-user">
                        <div class="row">
                            <div class="col-xs-3">
                                <img class="img-circle" src="{{asset('images/img.png')}}" style="width: 50px; height: 50px; object-fit: cover;">
                            </div>
                            <div class="col-xs-9">
                                <h4>
                                    @lang('labels.name')
                                </h4>
                            </div>
                        </div>
                    </div>
                    {!! Form::open(['method' => 'POST', 'action' => 'AppointmentController@create_appointment_ver_2']) !!}
                    {!! Form::hidden('user_id', null) !!}
                    {!! Form::hidden('appointment_id', null) !!}
                    <div id="the-choice"></div>
                    <div id="the-schedule">
                        <div class="form-horizontal">
                            <div class="form-group">
                                {!! Form::label('appointment_date', trans('labels.date').': ', ['class' => 'control-label col-md-4']) !!}
                                <div class="col-md-8">
                                    {!! Form::date('appointment_date', \Carbon\Carbon::now(), ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('appointment_hour', trans('labels.hour').': ', ['class' => 'control-label col-md-4']) !!}
                                <div class="col-md-8">
                                    {!! Form::text('appointment_hour', '10:00', ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('appointment_note', trans('labels.note').': ', ['class' => 'control-label col-md-4']) !!}
                                <div class="col-md-8">
                                    {!! Form::textarea('appointment_note', null, ['class' => 'form-control', 'rows' => '3']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div id="the-button"></div>
                    {!! Form::close() !!}
                </div>
            </div>
            <div class="col-xs-6">
                <div class="bottom-right">
                    <input id="search-field" type="text" placeholder="Search" class="form-control">
                    <div class="row">
                        <div class="result-container">

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--MODAL TAMBAH--}}
    <button type="button" id="btn-pop-add-not-used-anymore" class="hidden" data-toggle="modal"
            data-target="#myModal"></button>

    <!-- Modal -->
    <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">@lang('labels.choose')  @lang('labels.appointment_type')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        {!! Form::label('fa_hour', trans('labels.choose').' '.trans('labels.hour').':', ['class' => 'control-label']) !!}
                        {!! Form::text('fa_hour', null, ['class' => 'form-control', 'id' => 'single-fa-hour']) !!}
                    </div>

                    <div class="row">
                        <div class="col-sm-4 col-xs-4 xs-no-padding">
                            <a class="btn btn-big-apm btn-fa-apm">
                                <div class="my_v_m">
                                    <div class="fa fa-user-plus btn-icon-apm"></div>
                                    <div class="btn-title-apm">@lang('labels.fitness_assessment')</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-4 col-xs-4 xs-no-padding">
                            <a class="btn btn-big-apm btn-wm-apm">
                                <div class="my_v_m">
                                    <div class="glyphicon glyphicon-scale btn-icon-apm"></div>
                                    <div class="btn-title-apm">@lang('labels.weigh_n_measure')</div>
                                </div>
                            </a>
                        </div>
                        <div class="col-sm-4 col-xs-4 xs-no-padding">
                            <a class="btn btn-big-apm btn-wo-apm">
                                <div class="my_v_m">
                                    <div class="fa fa-universal-access btn-icon-apm"></div>
                                    <div class="btn-title-apm">@lang('labels.workout')</div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('fullcalendar/fullcalendar.min.js')}}"></script>
    <script src="{{asset('bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script>
        function hour(hour) {
            if (hour === '') {
                alert('@lang('messages.hour_empty')');
                return false;
            }
            return true;
        }

        $('input[name="appointment_hour"]').datetimepicker({
            format: 'HH:mm',
            stepping: 30
        });

        $('#single-fa-hour').datetimepicker({
            format: 'HH:mm',
            stepping: 30
        });

        $('.btn-fa-apm').on('click', function () {
            if (hour($('#fa_hour').val()) == true) {
                window.location.href = "appointment/fa/" + appointment_date + "/" + $('#fa_hour').val();
            }
        });

        $('.btn-wm-apm').on('click', function () {
            if (hour($('#fa_hour').val()) == true) {
                window.location.href = "appointment/wm/" + appointment_date + "/" + $('#fa_hour').val();
            }
        });

        $('.btn-wo-apm').on('click', function () {
            if (hour($('#fa_hour').val()) == true) {
                window.location.href = "appointment/wo/" + appointment_date + "/" + $('#fa_hour').val();
            }
        });


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
                    $('#btn-pop-add').click();
                },
                events: [
                        @foreach($fas as $fa)
                            @if((strtotime($fa->fa_date.' '.$fa->hour) < strtotime(\Carbon\Carbon::now())) && (($fa->guestappointment_gastatus->status_id == '1') || ($fa->guestappointment_gastatus->status_id == '2') || ($fa->guestappointment_gastatus->status_id == '4')))
                                {
                                    title: 'FA-{{$fa->guestappointment_buddyreferrallead->fullname}}',
                                    start: '{{date('Y-m-d', strtotime($fa->fa_date)).'T'.date('H:i:s', strtotime(\Carbon\Carbon::parse($fa->fa_hour)->setTimezone(Session::get('timezone'))))}}',
                                    backgroundColor: '#838788',
                                    textColor: '#b4b7b9',
                                    borderColor: '#414345',
                                    url: '{{url('cf-lite/appointment/status/fa/'.$fa->id)}}',
                                    className: 'fa-cal {{'fa-cal-'.$fa->id}}'
                                },
                            @else
                                {
                                    title: 'FA-{{$fa->guestappointment_buddyreferrallead->fullname}}',
                                    start: '{{date('Y-m-d', strtotime($fa->fa_date)).'T'.date('H:i:s', strtotime(\Carbon\Carbon::parse($fa->fa_hour)->setTimezone(Session::get('timezone'))))}}',
                                    backgroundColor: '#afeeee',
                                    textColor: '#465f5f',
                                    borderColor: '#7aa6a6',
                                    url: '{{url('cf-lite/appointment/status/fa/'.$fa->id)}}',
                                    className: 'fa-cal {{'fa-cal-'.$fa->id}}'
                                },
                            @endif
                        @endforeach

                        @foreach($mas as $ma)
                    {
                        @if($ma->ap_type_id == '1')
                        title: 'WM-{{$ma->memberappointment_user->name}}',
                        backgroundColor: '#ffe4e1',
                        textColor: '#665b5a',
                        borderColor: '#ffb6ae',
                        url: '{{url('cf-lite/appointment/status/ma/'.$ma->id)}}',
                        @elseif($ma->ap_type_id == '2')
                        title: 'FWO-{{$ma->memberappointment_user->name}}',
                        backgroundColor: '#e6e6fa',
                        textColor: '#73737d',
                        borderColor: '#bcbcf2',
                        url: '{{url('cf-lite/appointment/status/ma/'.$ma->id)}}',
                        @endif
                        start: '{{date('Y-m-d', strtotime($ma->app_date)).'T'.date('H:i:s', strtotime($ma->app_hour))}}',
                        className: 'ma-cal {{'ma-cal-'.$ma->id}}'
                    },
                    @endforeach
                ]
            });

            $('.fc-day').css('cursor', 'pointer');

            @foreach($fas as $fa)
                @if(empty($fa->guestappointment_gastatus))
                    $('.fa-cal-{{$fa->id}} .fc-content').append('<a href="javascript:;" role="button" class="btn btn-reschedule" id="btn-reschedule-{{$fa->id}}_G" title="@lang('labels.reschedule')"><span class="fa fa-refresh"></span></a>');
                @else
                    @if($fa->guestappointment_gastatus->status_id != '3' && $fa->guestappointment_gastatus->status_id != '5' && $fa->guestappointment_gastatus->status_id != '6')
                        $('.fa-cal-{{$fa->id}} .fc-content').append('<a href="javascript:;" role="button" class="btn btn-reschedule" id="btn-reschedule-{{$fa->id}}_G" title="@lang('labels.reschedule')"><span class="fa fa-refresh"></span></a>');
                    @endif
                @endif
            @endforeach

            @foreach($mas as $ma)
                @if(empty($ma->memberappointment_mastatus))
                    $('.ma-cal-{{$ma->id}} .fc-content').append('<a href="javascript:;" role="button" class="btn btn-reschedule" id="btn-reschedule-{{$ma->id}}_M" title="@lang('labels.reschedule')"><span class="fa fa-refresh"></span></a>');
                @else
                    @if($ma->memberappointment_mastatus->status_id != '3')
                        $('.ma-cal-{{$ma->id}} .fc-content').append('<a href="javascript:;" role="button" class="btn btn-reschedule" id="btn-reschedule-{{$ma->id}}_M" title="@lang('labels.reschedule')"><span class="fa fa-refresh"></span></a>');
                    @endif
                @endif
            @endforeach

            $('.fc-basicDay-button').on('click', function () {
                        @foreach($fas as $fa)
                        @if(empty($fa->guestappointment_gastatus))
                            $('.fa-cal-{{$fa->id}} .fc-content').append('<a href="javascript:;" role="button" class="btn btn-reschedule" id="btn-reschedule-{{$fa->id}}_G" title="@lang('labels.reschedule')"><span class="fa fa-refresh"></span></a>');
                        @else
                            @if($fa->guestappointment_gastatus->status_id != '3' && $fa->guestappointment_gastatus->status_id != '5' && $fa->guestappointment_gastatus->status_id != '6')
                                $('.fa-cal-{{$fa->id}} .fc-content').append('<a href="javascript:;" role="button" class="btn btn-reschedule" id="btn-reschedule-{{$fa->id}}_G" title="@lang('labels.reschedule')"><span class="fa fa-refresh"></span></a>');
                        @endif
                    @endif
                @endforeach

                @foreach($mas as $ma)
                    @if(empty($ma->memberappointment_mastatus))
                        $('.ma-cal-{{$ma->id}} .fc-content').append('<a href="javascript:;" role="button" class="btn btn-reschedule" id="btn-reschedule-{{$ma->id}}_M" title="@lang('labels.reschedule')"><span class="fa fa-refresh"></span></a>');
                        @else
                            @if($ma->memberappointment_mastatus->status_id != '3')
                                $('.ma-cal-{{$ma->id}} .fc-content').append('<a href="javascript:;" role="button" class="btn btn-reschedule" id="btn-reschedule-{{$ma->id}}_M" title="@lang('labels.reschedule')"><span class="fa fa-refresh"></span></a>');
                        @endif
                        @endif
                        @endforeach
            });

            $('.fc-basicWeek-button').on('click', function() {
                @foreach($fas as $fa)
                    @if(empty($fa->guestappointment_gastatus))
                        $('.fa-cal-{{$fa->id}} .fc-content').append('<a href="javascript:;" role="button" class="btn btn-reschedule" id="btn-reschedule-{{$fa->id}}_G" title="@lang('labels.reschedule')"><span class="fa fa-refresh"></span></a>');
                    @else
                        @if($fa->guestappointment_gastatus->status_id != '3' && $fa->guestappointment_gastatus->status_id != '5' && $fa->guestappointment_gastatus->status_id != '6')
                            $('.fa-cal-{{$fa->id}} .fc-content').append('<a href="javascript:;" role="button" class="btn btn-reschedule" id="btn-reschedule-{{$fa->id}}_G" title="@lang('labels.reschedule')"><span class="fa fa-refresh"></span></a>');
                        @endif
                    @endif
                @endforeach

                @foreach($mas as $ma)
                    @if(empty($ma->memberappointment_mastatus))
                        $('.ma-cal-{{$ma->id}} .fc-content').append('<a href="javascript:;" role="button" class="btn btn-reschedule" id="btn-reschedule-{{$ma->id}}_M" title="@lang('labels.reschedule')"><span class="fa fa-refresh"></span></a>');
                    @else
                        @if($ma->memberappointment_mastatus->status_id != '3')
                            $('.ma-cal-{{$ma->id}} .fc-content').append('<a href="javascript:;" role="button" class="btn btn-reschedule" id="btn-reschedule-{{$ma->id}}_M" title="@lang('labels.reschedule')"><span class="fa fa-refresh"></span></a>');
                        @endif
                    @endif
                @endforeach
            });

            $('.fc-prev-button').on('click', function() {
                @foreach($fas as $fa)
                    @if(empty($fa->guestappointment_gastatus))
                        $('.fa-cal-{{$fa->id}} .fc-content').append('<a href="javascript:;" role="button" class="btn btn-reschedule" id="btn-reschedule-{{$fa->id}}_G" title="@lang('labels.reschedule')"><span class="fa fa-refresh"></span></a>');
                @else
                    @if($fa->guestappointment_gastatus->status_id != '3' && $fa->guestappointment_gastatus->status_id != '5' && $fa->guestappointment_gastatus->status_id != '6')
                        $('.fa-cal-{{$fa->id}} .fc-content').append('<a href="javascript:;" role="button" class="btn btn-reschedule" id="btn-reschedule-{{$fa->id}}_G" title="@lang('labels.reschedule')"><span class="fa fa-refresh"></span></a>');
                @endif
            @endif
        @endforeach

        @foreach($mas as $ma)
            @if(empty($ma->memberappointment_mastatus))
                $('.ma-cal-{{$ma->id}} .fc-content').append('<a href="javascript:;" role="button" class="btn btn-reschedule" id="btn-reschedule-{{$ma->id}}_M" title="@lang('labels.reschedule')"><span class="fa fa-refresh"></span></a>');
                @else
                    @if($ma->memberappointment_mastatus->status_id != '3')
                        $('.ma-cal-{{$ma->id}} .fc-content').append('<a href="javascript:;" role="button" class="btn btn-reschedule" id="btn-reschedule-{{$ma->id}}_M" title="@lang('labels.reschedule')"><span class="fa fa-refresh"></span></a>');
                @endif
                @endif
                @endforeach
            });

            $('.fc-next-button').on('click', function() {
                @foreach($fas as $fa)
                    @if(empty($fa->guestappointment_gastatus))
                        $('.fa-cal-{{$fa->id}} .fc-content').append('<a href="javascript:;" role="button" class="btn btn-reschedule" id="btn-reschedule-{{$fa->id}}_G" title="@lang('labels.reschedule')"><span class="fa fa-refresh"></span></a>');
                @else
                    @if($fa->guestappointment_gastatus->status_id != '3' && $fa->guestappointment_gastatus->status_id != '5' && $fa->guestappointment_gastatus->status_id != '6')
                        $('.fa-cal-{{$fa->id}} .fc-content').append('<a href="javascript:;" role="button" class="btn btn-reschedule" id="btn-reschedule-{{$fa->id}}_G" title="@lang('labels.reschedule')"><span class="fa fa-refresh"></span></a>');
                @endif
            @endif
        @endforeach

        @foreach($mas as $ma)
            @if(empty($ma->memberappointment_mastatus))
                $('.ma-cal-{{$ma->id}} .fc-content').append('<a href="javascript:;" role="button" class="btn btn-reschedule" id="btn-reschedule-{{$ma->id}}_M" title="@lang('labels.reschedule')"><span class="fa fa-refresh"></span></a>');
                @else
                    @if($ma->memberappointment_mastatus->status_id != '3')
                        $('.ma-cal-{{$ma->id}} .fc-content').append('<a href="javascript:;" role="button" class="btn btn-reschedule" id="btn-reschedule-{{$ma->id}}_M" title="@lang('labels.reschedule')"><span class="fa fa-refresh"></span></a>');
                @endif
                @endif
                @endforeach
            });
        });

        $('#search-field').on('keyup', function () {
            var val = $(this).val();
            $('.result-container').empty();
            if (val == "") {

            } else {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{{url('getmemberorleads')}}',
                    data: {
                        nama: val
                    },
                    type: 'POST',
                    dataType: 'json',
                    success: function (result) {
                        /*console.log(result);*/

                        var innerHTML = "";
                        for (var x in result[0]) {
                            innerHTML += "<div class='col-xs-12'>";
                            innerHTML += "<div class='list-view' id='list-view-" + result[0][x].id + "M'>";
                            innerHTML += "<input type='hidden' value='" + result[0][x].name + "' name='name-user-" + result[0][x].id + "M'>";
                            innerHTML += "<input type='hidden' value='Member' name='type-user-" + result[0][x].id + "'>";
                            innerHTML += "<div class='row'>";
                            innerHTML += "<div class='col-xs-3'>";
                            if (result[0][x].photo != null) {
                                innerHTML += "<img class='photo-img' src='{{asset('images/member_photos')}}/" + result[0][x].photo + "'>";
                                innerHTML += "<input type='hidden' value='{{asset('images/member_photos')}}/" + result[0][x].photo + "' name='image-" + result[0][x].id + "M'>";
                            } else {
                                innerHTML += "<img class='photo-img' src='{{asset('images/img.png')}}'>";
                                innerHTML += "<input type='hidden' value='{{asset('images/img.png')}}' name='image-" + result[0][x].id + "M'>";
                            }
                            innerHTML += "</div>";
                            innerHTML += "<div class='col-xs-9'>";
                            innerHTML += "<h4>" + result[0][x].name + " <small style='display: block'>" + result[0][x].email + "</small></h4>"
                            innerHTML += "</div>";
                            innerHTML += "</div>";
                            innerHTML += "</div>";
                            innerHTML += "</div>";
                        }

                        for (var x in result[1]) {
                            innerHTML += "<div class='col-xs-12'>";
                            innerHTML += "<div class='list-view' id='list-view-" + result[1][x].id + "L'>";
                            innerHTML += "<input type='hidden' value='" + result[1][x].fullname + "' name='name-user-" + result[1][x].id + "L'>";
                            innerHTML += "<input type='hidden' value='Leads' name='type-user-" + result[1][x].id + "'>";
                            innerHTML += "<div class='row'>";
                            innerHTML += "<div class='col-xs-3'>";
                            innerHTML += "<img class='photo-img' src='{{asset('images/img.png')}}'>";
                            innerHTML += "<input type='hidden' value='{{asset('images/img.png')}}' name='image-" + result[1][x].id + "L'>";
                            innerHTML += "</div>";
                            innerHTML += "<div class='col-xs-9'>";
                            innerHTML += "<h4>" + result[1][x].fullname + " <small style='display: block'>" + result[1][x].phone + "</small></h4>"
                            innerHTML += "</div>";
                            innerHTML += "</div>";
                            innerHTML += "</div>";
                            innerHTML += "</div>";
                        }

                        $('.result-container').append(innerHTML);


                    },
                    error: function (xhr, textStatus, errorThrown) {
                        console.log(JSON.stringify(xhr));
                    }
                });
            }
        });
        //
        //
        //        //        Calendar New Button Function
        //        $(function () {
        //            $('.fc-toolbar .fc-center h2').before('<span id="my_left-button"> </span>');
        //            $('.fc-toolbar .fc-center h2').after('<span id="my_right-button"> </span>');
        //        });
        //
        //        $(document).on('click', '#my_left-button', function () {
        //            $('.fc-prev-button').click();
        //        });
        //
        //        $(document).on('click', '#my_right-button', function () {
        //            $('.fc-next-button').click();
        //        });
        //
        //        $(document).on('click', '.fc-day', function () {
        //
        //            $('#selected-date').text(moment($(this).attr('data-date')).format('ddd, D MMMM YYYY'));
        //            $('#btn-pop-add').click();
        //        });
        //
        //        //        END - Calendar New Button Function
    </script>

    <script>
        $('.result-container').on('click', '.list-view', function () {
            var ids = $(this).attr('id').split('-').pop();
            var id = ids.split("");

            var id_length = id.length - 1;

            var final_id = '';
            for (i = 0; i < id_length; i++) {
                final_id += id[i];
            }
            console.log(final_id);

            var view = '';
            view = '<div class="row">' +
                '<div class="col-md-3">' +
                '<img class="img-circle" src="' + $('input[name="image-' + ids + '"]').val() + '" style="width: 50px; height: 50px; object-fit: cover;">' +
                '</div>' +
                '<div class="col-xs-9">' +
                '<h4>' + $('input[name="name-user-' + ids + '"]').val() + '</h4>' +
                '</div>' +
                '</div>';

            $('input[name="user_id"]').val(final_id);
            $('#the-user').html(view);

            var choice = '';
            var button = '';
            if (id[id_length] == 'M') {
                choice = '{!! Form::select('appointment_type', $appointment_types, null, ['class' => 'form-control']) !!}';

                button = '{!! Form::submit(trans('labels.create_appointment'), ['class' => 'btn btn-primary', 'name' => 'btn-member']) !!}';
            } else if (id[id_length] == 'L') {
                choice = '{!! Form::hidden('appointment_type', 'FA') !!}';

                button = '{!! Form::submit(trans('create_appointment'), ['class' => 'btn btn-primary', 'name' => 'btn-lead']) !!}';
            }

            $('#the-choice').html(choice);
            $('#the-button').html(button);
        });
    </script>

    <script>
        $(document).ready(function () {
            $('.fc-view-container').on('click', '.btn-reschedule', function () {
                var idtypes = $(this).attr('id').split('-').pop();
                var idtype = idtypes.split('_');
                var id = idtype[0];
                var type = idtype[1];

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                $.ajax({
                    url: '{{url('getappointmentdata')}}',
                    data: {
                        appointment_id: id,
                        appointment_type: type
                    },
                    type: 'POST',
                    dataType: 'json',
                    success: function (result) {
                        console.log(result);
                        var view = '';
                        var button = '';
                        view = '<div class="row">';
                        view += '<div class="col-xs-3">';
                        if (typeof (result['user'].photo) != 'undefined' && result['user'].photo != null) {
                            view += '<img class="img-circle" src="{{asset('images/member_photos')}}/' + result['user'].photo + '" style="width: 50px; height: 50px; object-fit: cover;">';
                        } else {
                            view += '<img class="img-circle" src="{{asset('images/img.png')}}" style="width: 50px; height: 50px; object-fit: cover;">';
                        }
                        view += '</div>';
                        view += '<div class="col-xs-9">';
                        if (type == 'G') {
                            view += '<h4>' + result['user'].fullname + '</h4>';
                            view += '<p>@lang('labels.appointment_type'): FA</p>';
                            var d = new Date(result['appointment'].fa_date);
                            var t = result['appointment'].fa_hour;
                            button = '{!! Form::submit(trans('change_appointment'), ['class' => 'btn btn-primary', 'name' => 'btn-guest-reschedule']) !!}';
                        } else if (type == 'M') {
                            view += '<h4>' + result['user'].name + '</h4>';
                            view += '<p>@lang('labels.appointment_type'): ' + result['appointment'].appointment_type + '</p>';
                            var d = new Date(result['appointment'].app_date);
                            var t = result['appointment'].app_hour;
                            button = '{!! Form::submit(trans('change_appointment'), ['class' => 'btn btn-primary', 'name' => 'btn-member-reschedule']) !!}';
                        }
                        var day = ("0" + (d.getDate())).slice(-2);
                        var month = ("0" + (d.getMonth() + 1)).slice(-2);
                        var year = d.getFullYear();
                        view += '<p>@lang('labels.schedule'): ' + day + '-' + month + '-' + year + ', ' + t + '</p>';
                        view += '</div>';
                        view += '</div>';

                        $('#the-user').html(view);

                        $('input[name="appointment_id"]').val(id);
                        $('#the-button').html(button);
                    },
                    error: function (xhr, textStatus, errorThrown) {
                        console.log(JSON.stringify(xhr));
                    }
                });
            });
        });
    </script>
@endsection