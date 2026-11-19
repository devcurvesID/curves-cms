@extends('cf_lite.layouts.staff-dashboard-template')

@section('title', 'Laporan Workout Member : ' . $club->club_name . ' [' . $the_member->name . ']')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">

    <link href="{{asset('fullcalendar/fullcalendar.min.css')}}" rel="stylesheet">
    <link href="{{asset('fullcalendar/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">
    <style>
        .dt-buttons {
            float: left;
        }

        .dataTables_length{
            margin-left: -460px;
        }

        .member-photo, .member-photo img {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        .print-detail {
            font-size: 9px;
        }

        .my_print_padding {
            padding: 15px;
        }

        table th{
            color: #ff1493;
        }
        table, th{
            text-align: center;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <left>
                <a href="{{url('cf-lite/laporan/wo')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            </left>
            <center>
                <h2 class="page-title">@lang('labels.list') @lang('labels.workout') : {{$club->club_name}}</h2>
            </center>
        </div>
        <div class="col-xs-12">
            <div id="section-filter">
                <div class="row">
                    <div class="col-xs-12">
                    {!! Form::open(['method' => 'GET', 'action' => ['ReportController@list_wo_filter_member',$the_member->id]]) !!}
                        <div class="col-xs-2">
                            Filter :
                        </div>
                        <div class="col-xs-3">
                            {!! Form::date('from_date', \Carbon\Carbon::now() , ['class' => 'form-control filter-date','placeholder'=>@trans('labels.from_date'), 'id'=>'filt-dari']) !!} <br/>
                        </div>
                        <div class="col-xs-3">
                            {!! Form::date('thru_date', \Carbon\Carbon::now() , ['class' => 'form-control filter-date','placeholder'=>@trans('labels.from_date'), 'id'=>'filt-sampai']) !!} <br/>
                        </div>
                        <div class="col-xs-2">
                            <button type="submit" class="btn btn-primary" id="save-btn"><i class="fa fa-search"></i> @lang('labels.search')</button>
                        </div>
                    {!! Form::close() !!}
                    </div>

                </div>
            </div>
            <hr class="the_hidden"/>
            {{--<div class="input-group my-hiddenz" style="margin-bottom: 20px;">--}}
            {{--<input type="text" class="tanggal form-control">--}}
            {{--<span class="input-group-addon btn btn-default"><i class="fa fa-calendar"></i></span>--}}
            {{--</div>--}}

            <center>
                <div class="col-xs-4">
                    <div class="col-xs-12">
                        <div class="member-photo">
                            @if(!empty($the_member->user_memberpersonal->photo))
                                <img class="img-circle member-card-img"
                                     src="{{asset('images/member_photos/'.$the_member->user_memberpersonal->photo)}}">
                            @else
                                <img class="img-circle member-card-img" src="{{asset('images/img.png')}}">
                            @endif
                        </div>
                    </div>
                    <div class="col-xs-12">
                        <hr/>
                    </div>
                    <div class="col-xs-12">
                        <table id="table-keterangan" style="width: 100%">
                            <tr>
                                <td>@lang('labels.name')</td>
                                <td> :</td>
                                <td>{{$the_member->name}}</td>
                            </tr>
                            <tr>
                                <td>@lang('labels.key_tag')</td>
                                <td> :</td>
                                <td>#{{$the_member->user_memberpersonal->key_tag_id}}</td>
                            </tr>
                            <tr>
                                <td>@lang('labels.total_workout')</td>
                                <td> :</td>
                                <td>{{count($list_wo)}}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-xs-12">
                        <hr/>
                    </div>
                </div>
                <div class="col-xs-8">
                    <a id="btn-print" class="btn btn-default" style="float: right"><i class="fa fa-print"></i> @lang('labels.print')</a><br/><br/>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                            <tr>
                                <th>@lang('labels.indexing')</th>
                                <th>@lang('labels.date')</th>
                                <th>@lang('labels.club')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php($count = 1)
                            @if(count($list_wo) > 0)
                                @foreach($list_wo as $x)
                                    <tr>
                                        <td>{{$count++}}</td>
                                        <td>{{\Carbon\Carbon::parse($x->workout_date)->setTimezone(session('timezone'))->format('d F Y, H:i:s T')}}</td>
                                        <td>{{$x->workout_club->club_name}}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="2">@lang('message.no_wo_found')</td>
                                </tr>
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </center>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('fullcalendar/fullcalendar.min.js')}}"></script>
    <script src="{{asset('bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>
    
    <script>
        $('#data-table').dataTable();
    </script>

    <script>
        
        $('#btn-filter').on('click', function () {
            var filter1 = $('#filt-dari').val();
            var filter2 = $('#filt-sampai').val();
            if (filter1 == "") {
                filter1 = 0;
            }

            if (filter2 == 0) {
                filter2 = 0;
            }

            window.location = '{{url('cf-lite/laporan/wo')}}' + "/" + {{$the_member->id}} +"/" + filter1 + "/" + filter2;
        });

        $('#btn-print').on('click', function () {
            $(this).hide();
            $('.dataTables_length').hide();
            $('.dataTables_filter').hide();
            $('.dataTables_paginate').hide();
            $('.left-sidebar').hide();
            $('.nav-tabs').hide();
            $('.page-title').hide();
            $('#section-filter').hide();
            $('.the_hidden').hide();
            $('table').addClass('print-detail');
            $('.box-menu').addClass('my_print_padding');
            window.print();
            $('.box-menu').removeClass('my_print_padding');
            $('table').removeClass('print-detail');
            $('.the_hidden').show();
            $('#section-filter').show();
            $('.page-title').show();
            $('.nav-tabs').show();
            $('.left-sidebar').show();
            $('.dataTables_length').show();
            $('.dataTables_filter').show();
            $('.dataTables_paginate').show();
            $(this).show();
        });

        $('.dt-buttons button').prepend('<i class="fa fa-print"></i> ');
        $('.dt-buttons button').addClass('btn btn-default');
    </script>
@endsection