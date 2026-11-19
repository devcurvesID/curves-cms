@extends('cf_lite.layouts.staff-dashboard-template')

@section('title', trans('labels.user_flag_report').' : ' . Auth::user()->user_club->club_name)

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

        .btn-print-left {
            position: absolute;
            right: 15px;
            top : -47px;
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
    <div class="row">
        <div class="col-xs-12">
            <left>
                <a href="{{url('cf-lite/laporan/wo')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            </left>
            <center>
                <h2 class="page-title">
                    @lang('labels.user_flag_report') : {{Auth::user()->user_club->club_name}}
                    @if(!empty($_GET['f']))
                        <br><small>({{date('d F Y', strtotime($_GET['f']))}} - {{date('d F Y', strtotime($_GET['t']))}})</small>
                    @else
                        <br><small>@lang('labels.last_30_days')</small>
                    @endif
                </h2>
            </center>
        </div>
        <div class="col-md-12" style="margin-bottom: 20px; padding-bottom: 20px;">
            <table>
                <th class="col-md-2 col-md-offset-2" style="color: #09d600; font-size: 30px;">
                    Flag A ({{$totalNeedA}} workout(s)) : {{$countA}} 
                </th>
                <th class="col-md-2"              style="color: #74d600; font-size: 30px;">
                    Flag B ({{$totalNeedB}} workout(s)) : {{$countB}} 
                </th>
                <th class="col-md-2"              style="color: #d6cd00; font-size: 30px;">
                    Flag C ({{$totalNeedC}} workout(s)) : {{$countC}} 
                </th>
                <th class="col-md-2"              style="color: #d66200; font-size: 30px;">
                    Flag D ({{$totalNeedD}} workout(s)) : {{$countD}} 
                </th>
                <th class="col-md-2"              style="color: #d60009; font-size: 30px;">
                    Flag E ({{$totalNeedE}} workout(s)) : {{$countE}} 
                </th>
            </table>
        </div>
        <div class="col-md-12">
            {!! Form::open(['method' => 'GET', 'action' => 'ReportController@wo_flag_list']) !!}
            <div class="form-inline">
                <div class="form-group">
                    {!! Form::label('date', trans('labels.choose').' '.trans('labels.date').': ', ['class' => 'control-label']) !!}
                    <div class="input-group">
                        {!! Form::date('f', Carbon\Carbon::now()->subDays(30), ['class' => 'form-control' , 'id' => 'from-date']) !!}
                    </div>
                    <div class="input-group">
                        <div>
                            {!! Form::date('t', Carbon\Carbon::now(), ['class' => 'form-control thru-date', 'id' => 'thru-date']) !!}
                        </div>
                    <br/>
                    </div>
                    {!! Form::label('week', trans('labels.week').': ', ['class' => 'control-label']) !!}
                    <div class="input-group">
                        {!! Form::number('weeks', '4', ['class' => 'form-control','placeholder' => @trans('labels.week'), 'min' => '1', 'id' => 'week']) !!}
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
                    <th>@lang('labels.name')</th>
                    <th>@lang('labels.wo_count')</th>
                    <th>@lang('labels.flag')</th>
                    <th>@lang('labels.membership_type')</th>
                </tr>
                </thead>
                <tbody>
                @php($indexing = 1)
                @foreach($wo_flags as $user)
                    <tr>
                        <td>{{$indexing}}</td>
                        <td>{{$user->name}}</td>
                        <td>{{$user->wo_count}}</td>
                        <td>
                            {{$user->user_flag}}
                        </td>
                        <td>{{$user->membership_type}}</td>
                    </tr>
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
    <script src="{{asset('js/moment.min.js')}}"></script>
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
        $('.dt-buttons button').addClass('btn btn-default btn-print-left');
    </script>
    <!-- <script>
        $(document).on('change','#week',function(){
            var from = document.getElementById('from-date').value;
            var week = document.getElementById('week').value;
            var day = moment(from,"YYYY-MM-DD");
            var toDay = moment(day).add(week,'weeks').format('YYYY-MM-DD');
            document.getElementById("thru-date").setAttribute("readonly", false);
            document.getElementById("thru-date").value = toDay;
            document.getElementById("thru-date").setAttribute("readonly", "readonly");
            
        })
    </script> -->
    <!-- <script>
        $(document).on('change','#from-date',function(){
            var from = document.getElementById('from-date').value;
            var week = document.getElementById('week').value;
            var day = moment(from,"YYYY-MM-DD");
            var toDay = moment(day).add(week,'weeks').format('YYYY-MM-DD');
            document.getElementById("thru-date").setAttribute("readonly", false);
            document.getElementById("thru-date").value = toDay;
            document.getElementById("thru-date").setAttribute("readonly", "readonly");
            
        })
    </script> -->
@endsection