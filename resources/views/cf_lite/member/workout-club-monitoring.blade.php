@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <style>
        body {
            -webkit-print-color-adjust: exact;
        }

        .dt-buttons {
            float: left;
        }

        #dataTable_filter {
            float: right;
        }

        .reminder-red {
            color: deeppink;
            font-weight: 600;
        }

        @media print {
            th, td {
                vertical-align: middle !important;
            }
        }
    </style>
@endsection

@section('content')
    <div class="col-xs-12">
        <left>
            <a href="{{url('cf-lite/member/member-monitoring-option')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
        </left>
        <center>
            <h2 class="page-title">@lang('labels.workout_club_monitoring')</h2>
        </center>
    </div>

    <div class="col-xs-12">
        {!! Form::open(['method' => 'GET', 'class' => 'form-inline']) !!}
        <div class="form-group">
            <p class="reminder-red" style="font-size: 20px; margin-right: 30px;">@lang('labels.filter'): </p>
        </div>
        <div class="form-group">
            {!! Form::label('from_date', trans('labels.from_date').': ', ['class' => 'control-label']) !!}
            {!! Form::date('from_date', null, ['class' => 'form-control']) !!}
            {!! Form::label('thru_date', trans('labels.until').': ', ['class' => 'control-label']) !!}
            {!! Form::date('thru_date', null, ['class' => 'form-control']) !!}
            <button type="submit" class="btn btn-default"><span class="fa fa-filter"></span> @lang('labels.set')</button>
        </div>
        {!! Form::close() !!}
        <table id="dataTable" class="table table-bordered" style="background: white;">
            <thead>
            <tr>
                <th class="text-center" style="vertical-align: middle">@lang('labels.name')</th>
                <th class="text-center" style="vertical-align: middle">@lang('labels.club')</th>
                <th class="text-center" style="vertical-align: middle">@lang('labels.total_workout_in_club')</th>
                <th class="text-center" style="vertical-align: middle">@lang('labels.action')</th>
            </tr>
            </thead>
            <tbody>
            @php($reminder = 1)
            @if(count($workout_shows->where('club_id', \Illuminate\Support\Facades\Auth::user()->club_id)) > 0)
                @php($reminder = 0)
                @php($workout_shows = $workout_shows->where('club_id', \Illuminate\Support\Facades\Auth::user()->club_id))
            @endif
            @foreach($workout_shows as $workout)
                <tr>
                    <td class="text-center">
                        <a href="{{url('cf-lite/laporan/wo-member-detail/'.$workout->id)}}" title="@lang('labels.detail')">{{$workout->name}}</a>
                    </td>
                    <td class="text-center">{{$workout->club_name}}</td>
                    <td class="text-center">{{$workout->wo_count}}</td>
                    <td class="text-center">
                        @if(!empty($workout->flag) && ($workout->flag == 'Move'))
                            <div class="reminder-red">@lang('labels.need_to_be_transfered_to') <span style="color: rebeccapurple;">{{$workout->destination}}</span></div>
                        @endif
                        @if($reminder == 1)
                                <div class="reminder-red">@lang('labels.need_to_be_transfered_to') <span style="color: rebeccapurple;">{{$workout->club_name}}</span></div>
                        @endif
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

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
                {
                    extend: 'print',
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ]
        });

        $('.dt-buttons button').prepend('<i class="fa fa-print"></i> ');
        $('.dt-buttons button').addClass('btn btn-default');
    </script>
@endsection