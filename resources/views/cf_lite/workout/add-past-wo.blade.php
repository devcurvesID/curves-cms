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

        .fc-state-highlight {background:red!important;}

        .fc-state-highlight-delete {background:lawngreen!important;opacity: 1;}

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
            <a href="{{url('cf-lite/manual-login')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">
                    @lang('labels.global_event_list')
                    <br>
                    <small>({{date('d F Y', strtotime($date))}})</small>
                </h2>
            </center>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div id='calendar'></div>
        </div>
    {!! Form::open(['method' => 'POST', 'route' => ['cf-lite.manual-login.store-past-wo'], 'id' => 'form-global-events']) !!}
    <div class="col-md-4" id="time-choose"></div>
    {!! Form::hidden('id', $user->id) !!}
    {!! Form::hidden('date_chosen', null, ['id' => 'chosen_date']) !!}
    {!! Form::hidden('date_chosen_delete', null, ['id' => 'chosen_date']) !!}
    {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary btn-sm', 'style' => 'margin-top: 30px;']) !!}
    {!! Form::close() !!}
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('fullcalendar/fullcalendar.min.js')}}"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.js"></script>

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

        function cekWaktu($id) {
            console.log($id);
            console.log($('#time-'+$id).val());
            $jm = $('#time-'+$id).val().toString().substr(0,2);
            $mnt = $('#time-'+$id).val().toString().substr(3,2);

            $jmInt = parseInt($('#time-'+$id).val().toString().substr(0,2));
            $mntInt = parseInt($('#time-'+$id).val().toString().substr(3,2));

            if($mnt[1] != '-'){ //berarti ud ngisi 4 angka
                if($jmInt > 19) $jm = '19';
                else if($jmInt < 9) $jm = '09';

                if($jm == '19') $mnt = '00';
                else if($mntInt >= 0 && $mntInt <= 29) $mnt = '00';
                else $mnt = '30';

                $('#time-'+$id).val($jm + ':' + $mnt);
            }
        }
    </script>

    <script>
        var event_date;
        var url = "{{url('cf-lite')}}/";
        var dates = [];
        var datesdel = [];
        var today = new Date();
        var dd = today.getDate();
        var mm = today.getMonth()+1; //January is 0!
        var yyyy = today.getFullYear();

        if(dd<10) {
            dd = '0'+dd
        } 

        if(mm<10) {
            mm = '0'+mm
        } 

        today = yyyy + '-' + mm + '-' + dd;
        $(document).ready(function () {
            
            // check if this day has an event before
            function IsDateHasEvent(date) {
                var allEvents = [];
                allEvents = $('#calendar').fullCalendar('clientEvents');
                var event = $.grep(allEvents, function (v) {
                    return +v.start === +date;
                });
                return event.length > 0;
            }

            $('#calendar').fullCalendar({
                dayClick: function (date, jsEvent, view) {
                    if (!IsDateHasEvent(date)) {
                        if(!$(this).hasClass("fc-state-highlight") && !$(this).hasClass("disabled") && !$(this).hasClass("fc-state-highlight-delete") ){
                            $(this).addClass("fc-state-highlight");
                        }else{
                            $(this).removeClass("fc-state-highlight");
                        }
                    }
                    else {
                        if(!$(this).hasClass("fc-state-highlight-delete") && !$(this).hasClass("disabled") ){
                            $(this).addClass("fc-state-highlight-delete");
                        }else{
                            $(this).removeClass("fc-state-highlight-delete");
                        }
                    }
                    
                },
                dayRender: function (date, cell) {
                    if($(cell).hasClass("fc-future")){
                        $("td[data-date="+date.format('YYYY-MM-DD')+"]").addClass('disabled');
                    }
                },
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month'
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
                    
                    if(!$("td[data-date="+start.format('YYYY-MM-DD')+"]").hasClass("disabled") && !IsDateHasEvent(start) ){
                        var index = dates.indexOf(event_date);
                        if(index > -1){
                            dates.splice(index,1);
                            $("#"+event_date).remove();
                        }else{
                            $temp = "<div class='form-group' id='"+event_date+"'>";
                            $temp += "<label>Time For "+event_date+"</label>";
                            $temp += "<input type='text' class='form-control time' name=time["+event_date+"] id='time-"+event_date+"' onkeyup=cekWaktu('"+event_date+"')>";
                            $temp += "</div>";
                            $( "#time-choose" ).append( $temp );
                            $('.time').mask("99:99", {placeholder:"--:--"});
                            dates.push(event_date);
                        }

                        $('input[name="date_chosen"]').val(dates.join(" "));
                    }else if(!$("td[data-date="+start.format('YYYY-MM-DD')+"]").hasClass("disabled") && IsDateHasEvent(start) ){
                        var index = datesdel.indexOf(event_date);
                        if(index > -1){
                            datesdel.splice(index,1);
                        }else{
                            datesdel.push(event_date);
                        }

                        $('input[name="date_chosen_delete"]').val(datesdel.join(" "));
                    }
                },
                events: [
                    @foreach ($userWo as $wo )
                        @if(!empty($wo->workout_club->club_code))
                            @php($club_code = $wo->workout_club->club_code)
                        @else
                            @php($club_code = $wo->workout_club->club_name)
                        @endif
                        {
                            start:'{{date("Y-m-d",strtotime($wo->workout_date))}}',
                            end:'{{date("Y-m-d",strtotime($wo->workout_date))}}',
                            rendering:'background',
                            className:'fc-state-highlight unclickable',
                        },
                        {
                            title:'{{$club_code}}',
                            start:'{{date("Y-m-d",strtotime($wo->workout_date))}}',
                            backgroundColor: '#ffffff',
                            textColor: '#414042'
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

            $('.fc-next-button').on('click', function () {
                $('.fc-day').each(function (index) {
                    if ($(this).attr('data-date') == '{{date('Y-m-d', strtotime($date))}}') {
                        console.log(index + ': ' + $(this).attr('data-date'));
                        $(this).css('background', 'rgba(184, 130, 238, 0.7)');
                    }
                });
            });

            $('.fc-prev-button').on('click', function () {
                var collect = $('input[name="date_chosen"]').val().split(' ');
                $.each(collect, function (ind, value) {
                    $('.fc-day').each(function (index) {
                        if ($(this).attr('data-date') == value) {
                            $(this).addClass('fc-state-highlight');
                            console.log($(this).attr('data-date'));
                        }
                    });
                });
                var collectdel = $('input[name="date_chosen_delete"]').val().split(' ');
                $.each(collectdel, function (ind, value) {
                    $('.fc-day').each(function (index) {
                        if ($(this).attr('data-date') == value) {
                            $(this).addClass('fc-state-highlight-delete');
                            console.log($(this).attr('data-date'));
                        }
                    });
                });
            });

            $('.fc-next-button').on('click', function () {
                var collect = $('input[name="date_chosen"]').val().split(' ');
                $.each(collect, function (ind, value) {
                    $('.fc-day').each(function (index) {
                        if ($(this).attr('data-date') == value) {
                            $(this).addClass('fc-state-highlight');
                            console.log($(this).attr('data-date'));
                        }
                    });
                });
                var collectdel = $('input[name="date_chosen_delete"]').val().split(' ');
                $.each(collectdel, function (ind, value) {
                    $('.fc-day').each(function (index) {
                        if ($(this).attr('data-date') == value) {
                            $(this).addClass('fc-state-highlight-delete');
                            console.log($(this).attr('data-date'));
                        }
                    });
                });
            });
        });
    </script>

@endsection