@extends('cf_lite.layouts.staff-dashboard-template')

@section('title', 'Laporan Workout Member : ' . $current_club->club_name)

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
            <a href="{{url('cf-lite/laporan/wo')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
        </left>
        <center>
            <h2 class="page-title">@lang('labels.list') @lang('labels.workout') : {{$current_club->club_name}}</h2>
        </center>
    </div>
    <div class="col-xs-12">
        <div class="row">
            <div class="col-xs-12">
                {!! Form::open(['method' => 'GET', 'action' => ['ReportController@wo_member_report_filter']]) !!}
                <div class="col-xs-2">
                    Filter :
                </div>
                <div class="col-xs-3">
                    @if(!empty($filterdate))
                        {!! Form::date('date', $filterdate , ['class' => 'form-control filter-date','placeholder'=>@trans('labels.from_date')]) !!} <br/>
                    @else
                        {!! Form::date('date', \Carbon\Carbon::now() , ['class' => 'form-control filter-date','placeholder'=>@trans('labels.from_date')]) !!} <br/>
                    @endif
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
                    <th>@lang('labels.origin_club')</th>
                    <th>@lang('labels.wo_time')</th>
                </tr>
                </thead>
                <tbody>
                @php($count = 1)
                @foreach($results as $result)
                        <tr>
                            <td>{{$count++}}</td>
                            <td>{{$result->name}}</td>
                            <td>{{$result->club_name}}</td>
                            <td>{{\Carbon\Carbon::parse($result->wo_time)->setTimezone(session('timezone'))->format('H:i T')}}</td>
                        </tr>
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