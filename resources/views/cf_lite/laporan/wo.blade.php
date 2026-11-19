@extends('cf_lite.layouts.staff-dashboard-template')

@section('title', 'Laporan Workout Member : ' . $club->club_name)

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
        .dt-buttons {
            float: left;
        }

        #dataTable_filter {
            float: right;
        }

        .dataTables_length{
            margin-left: -460px;
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
    <div class="col-xs-12">
        <left>
            <a href="{{url('cf-lite/laporan')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <a href="{{url('cf-lite/laporan/wo-count-avg')}}" class="btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: rebeccapurple; margin-right: 30px; margin-top: 30px; " title="@lang('labels.member_average_wo_report')"><span class="glyphicon glyphicon-th-list" style="margin-top: 10px; color: rebeccapurple;"></span></a>
            <a href="{{url('cf-lite/laporan/wo-flag')}}" class="btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: rebeccapurple; margin-right: 30px; margin-top: 30px; " title="@lang('labels.user_flag_report')"><span class="glyphicon glyphicon-flag" style="margin-top: 10px; color: rebeccapurple;"></span></a>
            <a href="{{url('cf-lite/laporan/wo-time')}}" class="btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: rebeccapurple; margin-right: 30px; margin-top: 30px; " title="@lang('labels.wo_time_report')"><span class="glyphicon glyphicon-time" style="margin-top: 10px; color: rebeccapurple;"></span></a>
            <a href="{{url('cf-lite/laporan/wo-member-report')}}" class="btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: rebeccapurple; margin-right: 30px; margin-top: 30px; " title="@lang('labels.wo_member_report')"><span class="fa fa-calendar" style="margin-top: 10px; color: rebeccapurple;"></span></a>
            </left>
        <center>
            <h2 class="page-title">@lang('labels.list') @lang('labels.workout') : {{$club->club_name}}</h2>
        </center>
    </div>
    <div class="col-xs-12">
        <div class="row">
            <div class="col-xs-12">
                {!! Form::open(['method' => 'GET', 'action' => ['ReportController@list_wo_filter']]) !!}
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
                    </a>
                </div>
                {!! Form::close() !!}
            </div>

        </div>
        <hr/>
        {{--<div class="input-group my-hiddenz" style="margin-bottom: 20px;">--}}
        {{--<input type="text" class="tanggal form-control">--}}
        {{--<span class="input-group-addon btn btn-default"><i class="fa fa-calendar"></i></span>--}}
        {{--</div>--}}
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-striped table-hover" style="background: white">
                <thead>
                <tr>
                    <th>@lang('labels.indexing')</th>
                    <th>@lang('labels.name')</th>
                    <th>@lang('labels.phone')</th>
                    <th>@lang('labels.wo_count')</th>
                    <th>@lang('labels.status')</th>
                </tr>
                </thead>
                <tbody>
                @php($count = 1)
                @foreach($list_wo as $x)
                    @if($x->total_wo > 0)
                        <tr>
                            <td>{{$count++}}</td>
                            <td><a role="button" href="{{url('cf-lite/laporan/wo-member-detail',$x->id)}}">{{$x->name}}</a></td>
                            <td>{{isset($x->user_memberpersonal->phone) ? $x->user_memberpersonal->phone : ' -- '}}</td>
                            <td>{{$x->total_wo}}</td>
                            <td>{{$x->user_memberstatus->status}}</td>
                        </tr>
                    @endif
                @endforeach
                </tbody>
            </table>
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

        $('#dataTable').DataTable({
            info: false,
            dom: 'Bfrtip',
            buttons: [
                'print',
            ]
        });

        $('#btn-filter').on('click', function () {
            var filter1 = $('#filt-dari').val();
            var filter2 = $('#filt-sampai').val();
            if (filter1 == "") {
                filter1 = 0;
            }

            if (filter2 == 0) {
                filter2 = 0;
            }

            window.location = '{{url('cf-lite/laporan/wo')}}' + "/" + filter1 + "/" + filter2;
        });

        $('.dt-buttons button').prepend('<i class="fa fa-print"></i> ');
        $('.dt-buttons button').addClass('btn btn-default');
    </script>
@endsection
