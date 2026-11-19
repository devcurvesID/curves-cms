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
            <a href="{{url('cf-lite/settings')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">
                    @lang('labels.global_event_list')
                    <br>
                    <small>({{date('d F Y', strtotime($date))}})</small>
                </h2>
            </center>
        </div>
        <div class="col-md-8">
            <div id='calendar'></div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 col-md-offset-1" id="add-globalevent">
                <a class="btn btn-primary pull-right new-globalevent" href="javascript:;" data-toggle="modal" data-target="#addNewGlobalEventModal" title="@lang('labels.add_new_global_event')"><span class="fa fa-plus"></span> @lang('labels.add_new_global_event')</a>
        </div>
        <div class="col-md-12">
            @php($send_date = date('Y-m-d', strtotime($date)))
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive" style="background-color: #fff;">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                            <tr>
                                <th>@lang('labels.indexing')</th>
                                <th>@lang('labels.title')</th>
                                <th>@lang('labels.description')</th>
                                <th>@lang('labels.max_taken')</th>
                                <th>@lang('labels.point')</th>
                                <th>@lang('labels.from_date')</th>
                                <th>@lang('labels.thru_date')</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            if(count($global_events) > 0):

                                $index = 1;

                                foreach($global_events as $global_event):
                            ?>
                                    <tr>
                                        <td>{{$index}}</td>
                                        <td><a role="button" id="edit-global-event-{{$global_event->id}}" class="edit-global-event" data-toggle="modal" data-target="#addNewGlobalEventModal">{{$global_event->title}}</a></td>
                                        <td>{{$global_event->description}}</td>
                                        <td>{{$global_event->max_taken}}</td>
                                        <td>{{$global_event->point}}</td>
                                        <td>{{$global_event->from_date}}</td>
                                        <td>{{$global_event->thru_date}}</td>
                                        <td>
                                            {!! Form::open(['method' => 'DELETE' , 'style'=>'display:inline-block' , 'action' => ['GlobalEventController@delete_global_event' , $global_event->id, ]])!!}{!! Form::submit('Delete',['class' => 'btn btn-danger' , 'onclick' => 'return confirm("Anda yakin akan menghapus data ini?")']) !!} {!! Form::close() !!}
                                        </td>
                                    </tr>
                            <?php
                                $index++;
                                endforeach;
                               endif;
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Modal -->
    <div class="modal fade" id="addNewGlobalEventModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            {!! Form::open(['method' => 'PATCH', 'action' => 'GlobalEventController@store_global_event']) !!}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.add_new_global_event')</h4>
                    {{ Form::hidden('addoredit','add')}}
                    {{ Form::hidden('editid',0)}}
                    {{ Form::hidden('club_id',$id)}}
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-inline">
                                <div class="form-group">
                                    <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">@lang('labels.clubs')</a>
                                    <div class="collapse" id="collapseExample">
                                        <div class="card card-body">
                                            {!! Form::label('clubs', trans('labels.clubs').': ', ['class' => 'control-label']) !!}<br/>
                                            @foreach($clubs as $club)
                                                @if($club->id == $id)
                                                    {!! Form::checkbox('clubs[]', $club->id, true, ['class' => 'form-control club-name', 'id' => $club->id, 'onclick' => 'return false;']) !!}{{$club->club_name}} <br/>
                                                @else
                                                    {!! Form::checkbox('clubs[]', $club->id, false, ['class' => 'form-control club-name', 'id' => $club->id]) !!}{{$club->club_name}} <br/>
                                                @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div><br/>
                                <div class="form-group">
                                    {!! Form::label('title', trans('labels.title').': ', ['class' => 'control-label']) !!}<br/>
                                    {!! Form::text('title', null, ['class' => 'form-control']) !!} <br/>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('description', trans('labels.description').': ', ['class' => 'control-label']) !!}<br/>
                                    {!! Form::textarea('description', null, ['class' => 'form-control']) !!} <br/>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('max_taken', trans('labels.max_taken').': ', ['class' => 'control-label']) !!}<br/>
                                    {!! Form::text('max_taken', null, ['class' => 'form-control', 'onkeypress' => 'return numberOnlyInput(event)']) !!} <br/>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('point', trans('labels.point').': ', ['class' => 'control-label']) !!}<br/>
                                    {!! Form::text('point', null, ['class' => 'form-control', 'onkeypress' => 'return numberOnlyInput(event)']) !!} <br/>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('from_date', trans('labels.from_date').': ', ['class' => 'control-label']) !!}<br/>
                                    {!! Form::date('from_date', \Carbon\Carbon::now() , ['class' => 'form-control']) !!} <br/>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('thru_date', trans('labels.thru_date').': ', ['class' => 'control-label']) !!}<br/>
                                    {!! Form::date('thru_date', \Carbon\Carbon::now() , ['class' => 'form-control']) !!} <br/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" class="btn btn-primary" id="save-btn">@lang('labels.save')</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    <!-- End Add Modal -->

    {!! Form::open(['method' => 'GET', 'route' => ['cf-lite.event.global-event-list', $id], 'id' => 'form-global-events']) !!}
    {!! Form::hidden('date_chosen', null) !!}
    {!! Form::close() !!}
@endsection

@section('other-script')
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('fullcalendar/fullcalendar.min.js')}}"></script>

    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>

    <script type="text/javascript">
        function numberOnlyInput(event){
            var charCode = (event.which) ? event.which : event.keyCode;
            if(charCode > 31 && (charCode < 48 || charCode > 57)){
                return false;
            }
            return true;
        }
    </script>

    <script>
        var event_date;
        var url = "{{url('cf-lite')}}/";
        $(document).ready(function () {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,basicWeek,basicDay'
                },
                defaultView: 'month',
                aspectRatio: 3,
                navLinks: false, // can click day/week names to navigate views
                editable: false,
                eventLimit: false, // allow "more" link when too many events
                weekends: true, // will Hide Saturdays and Sundays
                selectable: true,
                select: function (start, end, jsEvent, view) {
                    event_date = moment(start).format();
                    $('input[name="date_chosen"]').val(event_date);
                    $('#form-global-events').submit();
                },
                events: [
                    @foreach($all_events as $global_event)
                            {
                                title: '{{$global_event->title}}',
                                start: '{{date('Y-m-d', strtotime($global_event->from_date))}}',
                                end: '{{date('Y-m-d', strtotime($global_event->thru_date . " +1 day "))}}',
                                backgroundColor: '#afeeee',
                                textColor: '#465f5f',
                                borderColor: '#7aa6a6',
                                className: 'ge-cal {{'ge-cal-'.$global_event->id}}'
                            },
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

            $('#add-globalevent').on('click', '.new-globalevent', function () {

                var idclub = {{$id}};
                $('.club-name').prop('checked',false);
                $('.club-name').each(function(){
                    if(idclub == $(this).attr('id')){
                        $(this).prop('checked',true);
                    }
                });

                $('input[name="title"]').val('');
                $('textarea[name="description"]').val('');
                $('input[name="max_taken"]').val('');
                $('input[name="point"]').val('');
                $('input[name="from_date"]').val();
                $('input[name="thru_date"]').val();
                $('#save-btn').text('@lang("labels.save")');
                $('#addNewChallenges').find(':hidden[name=addoredit]').prop('value','add');
            });

            $('#data-table').on('click', '.edit-global-event', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    globalevent_id: $(this).attr('id').split('-').pop()
                };

                var type = 'POST';
                var my_url = url + 'event/global-event-list-ajax';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        console.log($('meta[name="csrf-token"]').attr('content'));
                        $('.club-name').each(function(){
                            var id = $(this).attr('id');
                            document.getElementById(id).checked = false;
                        });
                        $('.club-name').each(function(){
                            var id = $(this).attr('id');
                            $.each(data.selectedclub,function(index, value){
                                if(id == value['id']){
                                    document.getElementById(id).checked = true;
                                }
                            });
                        });
                        $('input[name="title"]').val(data.globalevent['title']);
                        $('textarea[name="description"]').val(data.globalevent['description']);
                        $('input[name="max_taken"]').val(data.globalevent['max_taken']);
                        $('input[name="point"]').val(data.globalevent['point']);
                        $('input[name="from_date"]').val(data.globalevent['from_date']);
                        $('input[name="thru_date"]').val(data.globalevent['thru_date']);
                        $('#save-btn').text('@lang('labels.edit')');
                        $('#addNewGlobalEventModal').find(':hidden[name=addoredit]').prop('value','edit');
                        $('#addNewGlobalEventModal').find(':hidden[name=editid]').prop('value',data.globalevent['id']);

                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });

            $('input[name="search_option"]').on('click', function () {
                $('input[name="s"]').val('');
            })

        });
    </script>

@endsection
