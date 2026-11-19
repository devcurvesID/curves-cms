@extends('cf_lite.layouts.staff-dashboard-template')

@section('title', trans('labels.fa_source').' : ' . Auth::user()->user_club->club_name)

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('bootstrap-toggle/css/bootstrap-toggle.min.css')}}" rel="stylesheet">

    <style>
        .dt-buttons {
            float: left;
        }

        #dataTable_filter {
            float: right;
        }
    </style>

    <style>
        #menuutama {
            padding-left: 30px;
            padding-right: 30px;
        }

        .page-title {
            font-size: 44px;
            margin-top: 0;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-xs-12">
            <left>
                <a href="{{url('cf-lite/laporan/wo')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            </left>
            <center>
                <h2 class="page-title">
                    @lang('labels.member_average_wo_report') : {{Auth::user()->user_club->club_name}}
                    @if(!empty($_GET['f']) && !empty($_GET['t']))
                        <br><small>({{date('d F Y', strtotime($_GET['f']))}} - {{date('d F Y', strtotime($_GET['t']))}})</small>
                    @elseif(!empty($_GET['f']) && empty($_GET['t']))
                        <br><small>({{date('d F Y', strtotime($_GET['f']))}})</small>
                    @else
                        <br><small>@lang('labels.from_the_beginning')</small>
                    @endif
                </h2>
            </center>
        </div>
        <div class="col-md-12">
            {!! Form::open(['method' => 'GET', 'action' => 'ReportController@wo_count_average_list']) !!}
            <div class="form-inline">
                <div class="form-group">
                    {!! Form::label('date', trans('labels.choose').' '.trans('labels.date').': ', ['class' => 'control-label']) !!}
                    {!! Form::date('f', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                    {!! Form::label('until', trans(' - '), ['class' => 'control-label']) !!}
                    <div class="input-group">
                        {!! Form::date('t', null, ['class' => 'form-control']) !!}
                        <span class="input-group-btn">
                            <button type="submit" class="btn btn-default"><span class="fa fa-search"></span> @lang('labels.search')</button>
                        </span>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        <div class="col-xs-12">
            <table id="dataTable" class="table table-bordered table-striped" style="background: white">
                <thead>
                <tr>
                    <th>@lang('labels.indexing')</th>
                    <th>@lang('labels.day')</th>
                    <th>@lang('labels.date')</th>
                    <th>@lang('labels.wo_count')</th>
                    <th>@lang('labels.active_member_count')</th>
                    <th>@lang('labels.workout_percent')</th>
                    <th>@lang('labels.running_workout_average_weekly')</th>
                </tr>
                </thead>
                <tbody>
                @php($indexing = 1)
                @foreach($wo_count_avgs as $wca)
                    @if($wca['day'] != 'Minggu')
                    <tr>
                        <td>{{$indexing}}</td>
                        <td>{{$wca['day']}}</td>
                        <td>{{$wca['date']}}</td>
                        <td>{{$wca['countWo']}}</td>
                        <td>{{$wca['activeMember']}}</td>
                        <td>{{$wca['woPercent']}}%</td>
                        <td>{{$wca['avgPercent']}}%</td>
                    </tr>
                    @else
                    <tr>
                        <td>{{$indexing}}</td>
                        <td>{{$wca['day']}}</td>
                        <td>{{$wca['date']}}</td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    @endif
                    @php($indexing++)
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
    <script>
        $('#dataTable').DataTable({
            info: false,
            dom: 'Bfrtip',
            buttons: [
                'print',
            ],
            pageLength: 100
        });

        $('.dt-buttons button').prepend('<i class="fa fa-print"></i> ');
        $('.dt-buttons button').addClass('btn btn-default');
    </script>
@endsection