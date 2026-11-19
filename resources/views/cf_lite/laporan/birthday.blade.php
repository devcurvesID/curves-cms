@extends('cf_lite.layouts.staff-dashboard-template')

@section('title', 'Laporan Tanggal Lahir Member '.$club->club_name)

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('fullcalendar/fullcalendar.min.css')}}" rel="stylesheet">
    <link href="{{asset('fullcalendar/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">

    <link href="{{asset('bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">
    <style>
        .fc-right, .fc-time {
            display: none !important;
        }

        #dataTable_filter, .dataTables_paginate{
            float: right;
        }

        .dt-buttons{
            float: left;
        }
    </style>
    <style>
        .walk-in-btn {
            width: 125px;
            height: 30px;
            top: -20px;
            right: 15px;
            background-color: rebeccapurple;
            border-radius: 25px;
            text-align: center;
            
            
            color: #fff;
            margin-bottom: 20px;
            margin-left: 20px;
        }

        .walk-in-btn:hover {
            border: 1px solid rebeccapurple;
            background-color: transparent;
            color: rebeccapurple;
        }

        .select2 {
            width: 100% !important;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <left>
                <a href="{{url('cf-lite/laporan')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            </left>
            <center>
                <h2 class="page-title">@lang('labels.list') @lang('labels.birthday') : {{$club->club_name}}</h2>
            </center>
        </div>
    </div>
    <div class="row">
        <div class="col-md-1">
            <a href="{{url('cf-lite/laporan/ulang-tahun-staff')}}"><div class="walk-in-btn" role="button" style="font-size: 20px;" title="@lang('labels.staff')">STAFF</div></a>
        </div>
        <div class="col-md-1">
            <a href="{{url('cf-lite/laporan/ulang-tahun')}}"><div class="walk-in-btn" role="button" style="font-size: 20px;" title="@lang('labels.member')">MEMBER</div></a>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6 col-xs-12">
            {{--<div class="input-group my-hiddenz" style="margin-bottom: 20px;">--}}
            {{--<input type="text" class="tanggal form-control">--}}
            {{--<span class="input-group-addon btn btn-default"><i class="fa fa-calendar"></i></span>--}}
            {{--</div>--}}
            <table id="dataTable" class="table table-bordered table-striped" style="background: white">
                <thead>
                <tr>
                    <th>@lang('labels.number')</th>
                    <th>@lang('labels.name')</th>
                    <th>@lang('labels.birth')</th>
                    <th>@lang('labels.age')</th>
                </tr>
                </thead>
                <tbody>
                @php($count = 1)
                @foreach($members as $x)
                    @if($x->user_memberpersonal->birth != "")
                        <tr>
                            <td>{{$count++}}</td>
                            <td>{{$x->name}}</td>
                            @php($dob = date('Y-n-d', strtotime($x->user_memberpersonal->birth)))
                            <td>{{date('F d, Y', strtotime($dob))}}</td>
                            <td>{{date_diff(date_create($dob), date_create('today'))->y}}</td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
        </div>
        <div class="col-sm-6 col-xs-12 my-hiddenz">
            <div id='calendar'></div>
        </div>
    </div>
@endsection

@section('other-script')

    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('js/buttons.print.min.js')}}"></script>

    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('fullcalendar/fullcalendar.min.js')}}"></script>
    <script src="{{asset('bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script>
        $('.tanggal').datetimepicker({
            format: 'MM-YYYY',
            defaultDate: '{{date('Y-m-d')}}',
        });

        $(document).ready(function () {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,basicWeek,basicDay'
                },
                navLinks: false, // can click day/week names to navigate views
                editable: false,
                eventLimit: true, // allow "more" link when too many events
                weekends: true, // will Hide Saturdays and Sundays
                selectable: false,
                events: [
                        @foreach($members as $x)
                    {
                        title: '{{$x->name}}',
                        start: '{{date('Y')}}-{{date('m-d', strtotime($x->user_memberpersonal->birth)).'T'.date('H:i:s', strtotime($x->user_memberpersonal->birth))}}',
                    },
                    @endforeach
                ]
            });

            $('#dataTable').DataTable({
                info: false,
                dom: 'Bfrtip',
                buttons: [
                    'print',
                ]
            });

            $('.dt-buttons button').prepend('<i class="fa fa-print"></i> ');
            $('.dt-buttons button').addClass('btn btn-default');
        });
    </script>
@endsection