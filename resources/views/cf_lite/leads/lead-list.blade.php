@extends('cf_lite.layouts.staff-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('css/appointment.css')}}" rel="stylesheet">
    <link href="{{asset('fullcalendar/fullcalendar.min.css')}}" rel="stylesheet">
    <link href="{{asset('fullcalendar/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">

    <link href="{{asset('bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">

    <link href="{{asset('css/appointment.css')}}" rel="stylesheet">
    <style>
        #if_br {
            display: none;
        }

        .circle-medium {
            width: 180px;
            height: 180px;
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

    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/dashboard')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <a href="{{url('cf-lite/lead/br-referrer')}}" class="btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: rebeccapurple; margin-right: 30px; margin-top: 30px; " title="@lang('labels.br_referrer')"><span class="fa fa-user-plus" style="margin-top: 10px; color: rebeccapurple;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.lead_source')</h2>
            </center>
        </div>
        <div class="col-md-8">
            <div id='calendar'></div>
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12" style="margin-top: 77px;">
                    <div class="row">
                        <div class="col-md-6 btn-calon-anggota">
                            <div class="row">
                                <a class="btn btn-big-apm btn-fa-lead circle-medium"
                                   href="{{url('cf-lite/lead/add/regular')}}"
                                   style="float: left;">
                                    <div class="fa fa-user-plus btn-icon-apm"></div>
                                    <div class="btn-title-apm">@lang('labels.not') @lang('labels.buddy_referral')</div>
                                </a>
                            </div>
                        </div>
                        <div class="col-md-6 btn-calon-anggota">
                            <div class="row">
                                <a class="btn btn-big-apm btn-fa-br circle-medium" href="{{url('cf-lite/lead/add/br')}}"
                                   style="float: left;">
                                    <div class="fa fa-user-plus btn-icon-apm"></div>
                                    <div class="btn-title-apm">@lang('labels.buddy_referral')</div>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            {!! Form::open(['method' => 'POST', 'action' => 'LeadController@bulk_add_leads_appointment']) !!}
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                            <tr>
                                <th>@lang('labels.indexing')</th>
                                <th>@lang('labels.fullname')</th>
                                <th>@lang('labels.phone')</th>
                                <th>@lang('labels.source')</th>
                                <th>@lang('labels.appointment_date')</th>
                                <th>@lang('labels.appointment_time')</th>
                                <th>@lang('labels.no_answer')</th>
                                <th>@lang('labels.contact')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php($indexing = 1)
                            @foreach($leads as $lead)
                                @if(count($lead->buddyreferrallead_guestappointment) > 0)
                                    @foreach($lead->buddyreferrallead_guestappointment as $lead_ga)
                                        @if($loop->last)
                                            @if($lead_ga->guestappointment_gastatus->status_id != 5)
                                                <tr>
                                                    <td>{{$indexing}}</td>
                                                    <td>
                                                        <a role="button" data-toggle="modal" data-target="#editLeadModal" class="lead-name" id="lead-name-{{$lead->id}}">{{$lead->fullname}}</a>
                                                    </td>
                                                    <td id="lead-phone-{{$lead->id}}">{{$lead->phone}}</td>
                                                    <td id="lead-source-{{$lead->id}}">
                                                        @if($lead->is_br == 'NotBR')
                                                            {{$lead->buddyreferrallead_membersource->source}}
                                                        @else
                                                            @lang('labels.buddy_referral')
                                                        @endif
                                                    </td>
                                                    @if(strtotime($lead_ga->fa_date) < strtotime(\Carbon\Carbon::today()))
                                                        <td>
                                                            <div class="form-inline">
                                                                <div class="form-group">
                                                                    <label class="control-label"><span
                                                                                class="fa fa-calendar"></span><span
                                                                                style="margin: 0 10px;">:</span>
                                                                    </label>
                                                                    {!! Form::date('fa_date['.$lead->id.']', null, ['class' => 'form-control app-date', 'id' => 'app-date-'.$lead->id, 'disabled']) !!}
                                                                    <p>(@lang('labels.last_appointment'): {{date('d-m-Y', strtotime($lead_ga->fa_date))}} {{$lead_ga->guestappointment_gastatus->gastatus_appointmentstatus->status}})</p>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <div class="form-inline">
                                                                <div class="form-group">
                                                                    <label class="control-label"><span
                                                                                class="fa fa-clock-o"></span><span
                                                                                style="margin: 0 10px;">:</span>
                                                                    </label>
                                                                    {!! Form::text('fa_hour['.$lead->id.']', null, ['class' => 'form-control app-clock', 'id' => 'app-clock-'.$lead->id, 'disabled']) !!}
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            @php
                                                                $min_contact = 0;
                                                                $max_contact = 10;
                                                                if (!empty($lead->buddyreferrallead_logcontactednewlead)):
                                                                    foreach ($lead->buddyreferrallead_logcontactednewlead as $contact_lead):
                                                                        if ($contact_lead->status == 'NotAnswered'):
                                                                            $min_contact++;
                                                                        endif;
                                                                    endforeach;
                                                                endif;
                                                            @endphp
                                                            {!! Form::number('no_answer['.$lead->id.']', $min_contact, ['class' => 'form-control', 'min' => $min_contact, 'max' => $max_contact, 'step' => '1', 'id' => 'no_answer-'.$lead->id, 'disabled']) !!}
                                                        </td>
                                                        <td>
                                                            <div class="checkbox">
                                                                <label>
                                                                    {!! Form::checkbox('fa_contact['.$lead->id.']', $lead->id, false, ['class' => 'app-contact', 'id' => 'app-contact-'.$lead->id]) !!}
                                                                </label>
                                                            </div>
                                                        </td>
                                                    @else
                                                        <td>
                                                            <span class="fa fa-calendar"></span>: {{date('d F Y', strtotime($lead_ga->fa_date))}}
                                                        </td>
                                                        <td>
                                                            <span class="fa fa-clock-o"></span>: {{date('H:i', strtotime(\Carbon\Carbon::parse($lead_ga->fa_hour)->setTimezone(Session::get('timezone'))))}}
                                                        </td>
                                                        <td>@lang('labels.confirmed')</td>
                                                        <td>@lang('labels.fa_appointment')</td>
                                                    @endif
                                                </tr>
                                            @endif
                                        @endif
                                    @endforeach
                                @else
                                    <tr>
                                        <td>{{$indexing}}</td>
                                        <td>
                                            <a role="button" data-toggle="modal" data-target="#editLeadModal" class="lead-name" id="lead-name-{{$lead->id}}">{{$lead->fullname}}</a>
                                        </td>
                                        <td id="lead-phone-{{$lead->id}}">{{$lead->phone}}</td>
                                        <td id="lead-source-{{$lead->id}}">
                                            @if($lead->is_br == 'NotBR')
                                                {{$lead->buddyreferrallead_membersource->source}}
                                            @else
                                                @lang('labels.buddy_referral')
                                            @endif
                                        </td>
                                        <td>
                                            <div class="form-inline">
                                                <div class="form-group">
                                                    <label class="control-label"><span
                                                                class="fa fa-calendar"></span><span
                                                                style="margin: 0 10px;">:</span>
                                                    </label>
                                                    {!! Form::date('fa_date['.$lead->id.']', null, ['class' => 'form-control app-date', 'id' => 'app-date-'.$lead->id, 'disabled']) !!}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="form-inline">
                                                <div class="form-group">
                                                    <label class="control-label"><span
                                                                class="fa fa-clock-o"></span><span
                                                                style="margin: 0 10px;">:</span>
                                                    </label>
                                                    {!! Form::text('fa_hour['.$lead->id.']', null, ['class' => 'form-control app-clock', 'id' => 'app-clock-'.$lead->id, 'disabled']) !!}
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            @php
                                                $min_contact = 0;
                                                $max_contact = 10;
                                                if (!empty($lead->buddyreferrallead_logcontactednewlead)):
                                                    foreach ($lead->buddyreferrallead_logcontactednewlead as $contact_lead):
                                                        if ($contact_lead->status == 'NotAnswered'):
                                                            $min_contact++;
                                                        endif;
                                                    endforeach;
                                                endif;
                                            @endphp
                                            {!! Form::number('no_answer['.$lead->id.']', $min_contact, ['class' => 'form-control', 'min' => $min_contact, 'max' => $max_contact, 'step' => '1', 'id' => 'no_answer-'.$lead->id, 'disabled']) !!}
                                        </td>
                                        <td>
                                            <div class="checkbox">
                                                <label>
                                                    {!! Form::checkbox('fa_contact['.$lead->id.']', $lead->id, false, ['class' => 'app-contact', 'id' => 'app-contact-'.$lead->id]) !!}
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                @endif
                                @php($indexing++)
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {!! Form::submit(trans('labels.submit'), ['class' => 'btn btn-primary pull-right']) !!}
            {!! Form::close() !!}
            <div class="row">
                <div class="col-md-3">
                    <div class="paging hiden rom">
                        {{$leads->links()}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editLeadModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            {!! Form::open(['method' => 'PATCH', 'action' => 'LeadController@update_lead_data']) !!}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.edit') @lang('labels.lead')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('lead_name', trans('labels.name').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-6">
                                {!! Form::hidden('lead_attribute', null) !!}
                                {!! Form::text('lead_name', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('lead_phone', trans('labels.phone').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-6">
                                {!! Form::text('lead_phone', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('lead_source', trans('labels.source').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-6">
                                {!! Form::select('lead_source', $sources, null, ['class' => 'form-control', 'placeholder' => ' == '.trans('labels.choose_source').' == ']) !!}
                                <div id="if_br">@lang('labels.buddy_referral')</div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('wa_number', trans('labels.wa_number').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-6">
                                {!! Form::text('wa_number', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('email', trans('labels.email').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-6">
                                {!! Form::email('email', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('labels.save')</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('fullcalendar/fullcalendar.min.js')}}"></script>
    <script src="{{asset('bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>

    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $('#data-table').dataTable();

        $('#data-table').on('click', '.app-contact', function () {
            var id = $(this).attr('id').split('-').pop();
            if ($(this).prop('checked') == true) {
                $('#app-date-' + id).prop('disabled', false);
                $('#app-clock-' + id).prop('disabled', false);
                $('#no_answer-' + id).prop('disabled', false);
            } else {
                $('#app-date-' + id).prop('disabled', true);
                $('#app-clock-' + id).prop('disabled', true);
                $('#no_answer-' + id).prop('disabled', true);
            }
        });

        /*$('.app-clock').datetimepicker({
            format: 'HH:mm',
            stepping: 30
        });*/

        $('#data-table').on('focus', '.app-clock', function () {
            $('#app-clock-' + $(this).attr('id').split('-').pop()).datetimepicker({
                format: 'HH:mm',
                stepping: 30
            });
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
                        @endif
                        start: '{{date('Y-m-d', strtotime($ma->app_date)).'T'.date('H:i:s', strtotime(\Carbon\Carbon::parse($ma->app_hour)->setTimezone(Session::get('timezone'))))}}',
                        className: 'ma-cal {{'ma-cal-'.$ma->id}}'
                    },
                    @endforeach
                ]
            });

            $('.fc-day').css('cursor', 'pointer');
        });
    </script>

    <script>
        $(document).ready(function () {
            var url = "{{url('cf-lite')}}/";

            $('#data-table').on('click', '.lead-name', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    lead_id: $(this).attr('id').split('-').pop()
                };

                var type = 'POST';
                var my_url = url + 'get-lead-detail';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        $('input[name="lead_attribute"]').val(data.lead['id']);
                        $('input[name="lead_name"]').val(data.lead['fullname']);
                        $('input[name="lead_phone"]').val(data.lead['phone']);
                        $('input[name="wa_number"]').val(data.lead['wa_number']);
                        $('input[name="email"]').val(data.lead['email']);

                        if (data.lead['is_br'] == 'NotBR') {
                            $('#if_br').hide();
                            $('select[name="lead_source"]').show();
                            $('select[name="lead_source"]').val(data.lead['source_id']);
                        } else if(data.lead['is_br'] == 'BR') {
                            $('#if_br').show();
                            $('select[name="lead_source"]').hide();
                        }
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });
        });
    </script>
@endsection
