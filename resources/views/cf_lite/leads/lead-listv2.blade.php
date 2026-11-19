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
            <a href="{{url('cf-lite/lead/import-leads')}}" class="btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: limegreen; margin-right: 10px; margin-top: 30px;" title="@lang('labels.import_leads')"><span class="fa fa-upload" style="margin-top: 10px; color: limegreen;"></span> </a>
            <a href="{{url('cf-lite/lead/br-referrer')}}" class="btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: rebeccapurple; margin-right: 10px; margin-top: 30px; " title="@lang('labels.br_referrer')"><span class="fa fa-user-plus" style="margin-top: 10px; color: rebeccapurple;"></span></a>
            <a href="{{url('cf-lite/lead/add/regular')}}" class="btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: darkred; margin-right: 10px; margin-top: 30px; " title="@lang('labels.not') @lang('labels.buddy_referral')"><span class="fa fa-user-plus" style="margin-top: 10px; color: darkred;"></span></a>
            <a href="{{url('cf-lite/lead/add/br')}}" class="btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deepskyblue; margin-right: 10px; margin-top: 30px; " title="@lang('labels.buddy_referral')"><span class="fa fa-user-plus" style="margin-top: 10px; color: deepskyblue;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.lead_source')</h2>
            </center>
        </div>
        <div class="col-md-12">
            <div id='calendar'></div>
        </div>
        <div class="col-md-12" >

            @if($date==null)
            {!! Form::open(['method' => 'GET', 'action' => 'LeadController@lead_listv2']) !!}
            <div class="form-group" style="margin-top: 20px; margin-bottom: 0px !important;">
                <label class="radio-inline">
                    {!! Form::radio('filter_group', 'not_fa', true) !!} @lang('labels.have_not') @lang('labels.fa')
                </label>
                <label class="radio-inline">
                    {!! Form::radio('filter_group', 'fa', false) !!} @lang('labels.have_fa_before')
                </label>
                <label class="radio-inline">
                    {!! Form::radio('filter_group', 'all', false) !!} @lang('labels.all')
                </label>
                <label style="margin-left: 20px">
                {!! Form::submit(trans('labels.filter'), ['class' => 'btn btn-primary']) !!}
                </label>
            </div>
            {!! Form::close() !!}
            @else
                <div class="form-group" style="padding: 20px; margin-top: 20px; margin-bottom: 0px !important;">
                <a href="{{url('cf-lite/lead')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="fa fa-refresh" style=" margin-top: 10px; color: darkviolet;"></span></a>
                </div>
            @endif

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
                                <th>@lang('labels.staff_in_charge')</th>
                                <th>@lang('labels.edit')</th>
                                <th>@lang('labels.delete')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php($indexing = 1)
                            @foreach($leads as $lead)
                                @if(count($lead->buddyreferrallead_guestappointment) > 0)
                                    @foreach($lead->buddyreferrallead_guestappointment as $lead_ga)
                                        @if($loop->last)
                                            @if(($lead_ga->guestappointment_gastatus->status_id != 5) && ($lead_ga->guestappointment_gastatus->status_id != 6))
                                                <tr>
                                                    {!! Form::open(['method' => 'PATCH', 'action' => ['LeadController@edit_lead_ga',$lead->id]]) !!}
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
                                                    {{--@if(strtotime($lead_ga->fa_date) < strtotime(\Carbon\Carbon::today()))--}}
                                                        <td>
                                                            <div class="form-inline">
                                                                <div class="form-group">
                                                                    <label class="control-label"><span
                                                                                class="fa fa-calendar"></span><span
                                                                                style="margin: 0 10px;">:</span>
                                                                    </label>
                                                                    {!! Form::date('fa_date', $lead_ga->fa_date, ['class' => 'form-control app-date', 'id' => 'app-date-'.$lead->id]) !!}
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
                                                                    {!! Form::text('fa_hour', $lead_ga->fa_hour, ['class' => 'form-control app-clock', 'id' => 'app-clock-'.$lead->id]) !!}
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td>{!! Form::select('staff_in_charge',$staffs,$lead_ga->staff_id,['class'=>'form-control select2', 'placeholder'=>trans('labels.choose_staff')]) !!}</td>
                                                        <td>
                                                            {!! Form::submit(trans('labels.edit'), ['class' => 'btn btn-primary pull-right']) !!}
                                                        </td>
                                                        {!! Form::close() !!}
                                                        <td>
                                                            {!! Form::open(['method'=>'DELETE','action'=>['LeadController@delete_lead',$lead->id]]) !!}
                                                            {!! Form::submit(trans('labels.delete'), ['class' => 'btn btn-primary pull-right']) !!}
                                                            {!! Form::close() !!}
                                                        </td>
                                                </tr>
                                            @endif
                                        @endif
                                    @endforeach
                                @else
                                    <tr>
                                        {!! Form::open(['method' => 'POST', 'action' => ['LeadController@add_lead_ga',$lead->id]]) !!}
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
                                                    {!! Form::date('fa_date', null, ['class' => 'form-control app-date', 'id' => 'app-date-'.$lead->id]) !!}
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
                                                    {!! Form::text('fa_hour', null, ['class' => 'form-control app-clock', 'id' => 'app-clock-'.$lead->id]) !!}
                                                </div>
                                            </div>
                                        </td>
                                        <td>{!! Form::select('staff_in_charge',$staffs,null,['class'=>'form-control select2', 'placeholder'=>trans('labels.choose_staff')]) !!}</td>
                                        <td>
                                            {!! Form::submit(trans('labels.add'), ['class' => 'btn btn-primary pull-right']) !!}
                                        </td>
                                        {!! Form::close() !!}
                                        <td>
                                            {!! Form::open(['method'=>'DELETE','action'=>['LeadController@delete_lead',$lead->id]]) !!}
                                            {!! Form::submit(trans('labels.delete'), ['class' => 'btn btn-primary pull-right']) !!}
                                            {!! Form::close() !!}
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
            {!! Form::close() !!}
            {{--<div class="row">--}}
                {{--<div class="col-md-3">--}}
                    {{--<div class="paging hiden rom">--}}
                        {{--{{$leads->links()}}--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}
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

    {{--modal-cal--}}
    <div class="modal fade" id="CalendarLeadModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            {!! Form::open(['method' => 'PATCH', 'action' => 'LeadController@set_fa']) !!}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.attendance') @lang('labels.lead')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::hidden('fa_id_cal', null) !!}
                            {!! Form::label('lead_name_cal', trans('labels.name').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-6">
                                {!! Form::text('lead_name_cal', null, ['class' => 'form-control', 'disabled']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('lead_phone_cal', trans('labels.phone').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-6">
                                {!! Form::text('lead_phone_cal', null, ['class' => 'form-control', 'disabled']) !!}
                            </div>
                        </div>
                        <div class="form-group" style="margin-top: 20px; margin-bottom: 0px !important;">
                            <label class="radio-inline">
                                {!! Form::radio('result_fa', 'not_show', true, ['id' => 'not_show_rb']) !!} @lang('labels.absence')
                            </label>
                            <label class="radio-inline">
                                {!! Form::radio('result_fa', 'not_sold', false, ['id' => 'not_sold_rb']) !!} @lang('labels.no_sale')
                            </label>
                            <label class="radio-inline">
                                {!! Form::radio('result_fa', 'sold', false, ['id' => 'sold_rb']) !!} @lang('labels.sale')
                            </label>
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

    {!! Form::open(['method' => 'GET', 'action' => 'LeadController@lead_listv2', 'id' => 'form-fa-date']) !!}
    {!! Form::hidden('date_chosen', null) !!}
    {!! Form::close() !!}
@endsection

@section('other-script')
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('fullcalendar/fullcalendar.min.js')}}"></script>
    <script src="{{asset('bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>

    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $('#data-table').dataTable();

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
            /* $('#calendar').fullCalendar({
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
                    $('#form-fa-date').submit();
                    // $('#btn-pop-add').click();
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
            }); */

            $('.fc-day').css('cursor', 'pointer');

            @foreach($fas as $fa)
                $('.fa-cal-{{$fa->id}}').append('<a href="javascript:;" data-toggle="modal" data-target="#CalendarLeadModal" role="button" class="btn-cal btn" id="fa-cal-{{$fa->id}}" title="@lang('labels.reschedule')"><span class="fa fa-refresh"></span></a>');
            @endforeach

            $('.fc-day').each(function (index) {
                if ($(this).attr('data-date') == '{{date('Y-m-d', strtotime($date))}}') {
                    console.log(index + ': ' + $(this).attr('data-date'));
                    $(this).css('background', 'rgba(184, 130, 238, 0.7)');
                }
            });

            $('.fc-basicDay-button').on('click', function () {
                @foreach($fas as $fa)
                $('.fa-cal-{{$fa->id}}').append('<a href="javascript:;" data-toggle="modal" data-target="#CalendarLeadModal" role="button" class="btn-cal btn" id="fa-cal-{{$fa->id}}" title="@lang('labels.reschedule')"><span class="fa fa-refresh"></span></a>');
                @endforeach

                $('.fc-day').each(function (index) {
                    if ($(this).attr('data-date') == '{{date('Y-m-d', strtotime($date))}}') {
                        console.log(index + ': ' + $(this).attr('data-date'));
                        $(this).css('background', 'rgba(184, 130, 238, 0.7)');
                    }
                });
            });

            $('.fc-basicWeek-button').on('click', function() {
                @foreach($fas as $fa)
                $('.fa-cal-{{$fa->id}}').append('<a href="javascript:;" data-toggle="modal" data-target="#CalendarLeadModal" role="button" class="btn-cal btn" id="fa-cal-{{$fa->id}}" title="@lang('labels.reschedule')"><span class="fa fa-refresh"></span></a>');
                @endforeach

                $('.fc-day').each(function (index) {
                    if ($(this).attr('data-date') == '{{date('Y-m-d', strtotime($date))}}') {
                        console.log(index + ': ' + $(this).attr('data-date'));
                        $(this).css('background', 'rgba(184, 130, 238, 0.7)');
                    }
                });
            });

            $('.fc-prev-button').on('click', function() {
                @foreach($fas as $fa)
                $('.fa-cal-{{$fa->id}}').append('<a href="javascript:;" data-toggle="modal" data-target="#CalendarLeadModal" role="button" class="btn-cal btn" id="fa-cal-{{$fa->id}}" title="@lang('labels.reschedule')"><span class="fa fa-refresh"></span></a>');
                @endforeach

                $('.fc-day').each(function (index) {
                    if ($(this).attr('data-date') == '{{date('Y-m-d', strtotime($date))}}') {
                        console.log(index + ': ' + $(this).attr('data-date'));
                        $(this).css('background', 'rgba(184, 130, 238, 0.7)');
                    }
                });
            });

            $('.fc-next-button').on('click', function() {
                @foreach($fas as $fa)
                $('.fa-cal-{{$fa->id}}').append('<a href="javascript:;" data-toggle="modal" data-target="#CalendarLeadModal" role="button" class="btn-cal btn" id="fa-cal-{{$fa->id}}" title="@lang('labels.reschedule')"><span class="fa fa-refresh"></span></a>');
                @endforeach

                $('.fc-day').each(function (index) {
                    if ($(this).attr('data-date') == '{{date('Y-m-d', strtotime($date))}}') {
                        console.log(index + ': ' + $(this).attr('data-date'));
                        $(this).css('background', 'rgba(184, 130, 238, 0.7)');
                    }
                });
            });

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


            $('#calendar').on('click', '.btn-cal', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    fa_id: $(this).attr('id').split('-').pop()
                };
                console.log($(this).attr('id').split('-').pop());
                var type = 'POST';
                var my_url = url + 'get-fa-detail';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        $('input[name="fa_id_cal"]').val(data.fa['fa_id']);
                        $('input[name="lead_name_cal"]').val(data.fa['name']);
                        $('input[name="lead_phone_cal"]').val(data.fa['phone']);
                        if(data.fa['status_id']=='4')
                        {
                            document.getElementById("not_show_rb").checked = true;
                        }
                        else if(data.fa['status_id']=='5')
                        {
                            document.getElementById("sold_rb").checked = true;
                        }
                        else if(data.fa['status_id']=='6')
                        {
                            document.getElementById("not_sold_rb").checked = true;
                        }
                        else
                        {
                            document.getElementById("not_show_rb").checked = true;
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
