@extends('cf_lite.layouts.staff-dashboard-template')

@if(!empty($last_day))
    @section('title', 'Top Weight Losers : ' . \Illuminate\Support\Facades\Auth::user()->user_club->club_name.' - '.\Carbon\Carbon::parse($last_day)->format('F Y'))
@else
    @section('title', 'Top Weight Losers : ' . \Illuminate\Support\Facades\Auth::user()->user_club->club_name.' - '.\Carbon\Carbon::now()->format('F Y'))
@endif

@section('other-style')
    <link href="{{asset('fullcalendar/fullcalendar.min.css')}}" rel="stylesheet">
    <link href="{{asset('fullcalendar/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">

    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">
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

        .bg-darker {
            background-color: #f1f1f1;
        }

        .bg-pink {
            background-color: pink;
        }

        @media print {
            th, td {
                vertical-align: middle !important;
            }

            tr td:nth-child(4), tr th:nth-child(4), tr td:nth-last-child(3), tr th:nth-last-child(3) {
                background-color: #f1f1f1 !important;
            }
        }
    </style>
@endsection

@section('content')
    <div class="col-xs-12">
        <left>
            <a href="{{url('cf-lite/laporan')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
        </left>
        <center>
            <h2 class="page-title">@lang('labels.weight_loser_list')
                : {{\Illuminate\Support\Facades\Auth::user()->user_club->club_name}}
                <br>
                <small>
                    (
                    @if(!empty($last_day))
                        {{\Carbon\Carbon::parse($last_day)->format('F Y')}}
                    @else
                        {{\Carbon\Carbon::now()->format('F Y')}}
                    @endif
                    )
                </small>
            </h2>
        </center>
    </div>
    <div class="col-xs-12">
        {!! Form::open(['method' => 'GET', 'action' => 'ReportController@top_weight_loser', 'class' => 'form-inline', 'style' => 'margin-bottom: 30px;']) !!}
        <div class="form-group">
            {!! Form::label('month', trans('labels.choose_date').': ', ['class' => 'control-label']) !!}
            {!! Form::select('month', ['01' => trans('labels.january'),
            '02' => trans('labels.february'),
            '03' => trans('labels.march'),
            '04' => trans('labels.april'),
            '05' => trans('labels.may'),
            '06' => trans('labels.june'),
            '07' => trans('labels.july'),
            '08' => trans('labels.august'),
            '09' => trans('labels.september'),
            '10' => trans('labels.october'),
            '11' => trans('labels.november'),
            '12' => trans('labels.december')], \Carbon\Carbon::now()->format('m'), ['class' => 'form-control']) !!}
            {!! Form::number('year', \Carbon\Carbon::now()->format('Y'), ['class' => 'form-control', 'min' => '1970', 'max' => '9999']) !!}
            {!! Form::submit(trans('labels.set_filter'), ['class' => 'btn btn-default']) !!}
        </div>
        {!! Form::close() !!}
    </div>
    <div class="col-xs-12">
        <table id="dataTable" class="table table-bordered" style="background: white;">
            <thead>
            <tr>
                <th class="text-center" style="vertical-align: middle">@lang('labels.name')</th>
                <th class="text-center" style="vertical-align: middle">@lang('labels.previous_measurement') (cm)</th>
                <th class="text-center" style="vertical-align: middle">@lang('labels.last_measurement') (cm)</th>
                <th class="bg-darker text-center" style="vertical-align: middle">@lang('labels.loss_total_measurement') (cm)</th>
                <th class="text-center" style="vertical-align: middle">@lang('labels.previous_weight') (kg)</th>
                <th class="text-center" style="vertical-align: middle">@lang('labels.last_weight') (kg)</th>
                <th class="bg-darker text-center" style="vertical-align: middle">@lang('labels.loss_weight') (kg)</th>
                <th class="bg-pink text-center" style="vertical-align: middle">@lang('labels.total_workout') (@lang('labels.times'))</th>
                <th class="bg-pink text-center" style="vertical-align: middle">@lang('labels.difference_day') (@lang('labels.day'))</th>
            </tr>
            </thead>
            <tbody>
            @php($total_measurement = 0)
            @php($total_weight = 0)
            @php($total_wo = 0)
            @php($total_diff_day = 0)
            @foreach($wm_users as $wm_user)
		@if($last_wm[$wm_user->user_id])
                @if(!empty($prev_wm[$wm_user->user_id]))
                    @php($prev_total_measurement = $prev_wm[$wm_user->user_id]->total_measurement)
                    @php($prev_weight = $prev_wm[$wm_user->user_id]->weight)
                @else
                    @php($prev_total_measurement = $last_wm[$wm_user->user_id]->total_measurement)
                    @php($prev_weight = $last_wm[$wm_user->user_id]->weight)
                @endif
                <tr>
                    <td>{{$user[$wm_user->user_id]->name}}</td>
                    <td class="text-right">{{$prev_total_measurement}}</td>
                    <td class="text-right">{{$last_wm[$wm_user->user_id]->total_measurement}}</td>
                    <td class="bg-darker text-right">
                        {{number_format(($last_wm[$wm_user->user_id]->total_measurement - $prev_total_measurement), 2)}}
                        @php($total_measurement = $total_measurement + ($last_wm[$wm_user->user_id]->total_measurement - $prev_total_measurement))
                    </td>
                    <td class="text-right">{{$prev_weight}}</td>
                    <td class="text-right">{{$last_wm[$wm_user->user_id]->weight}}</td>
                    <td class="bg-darker text-right">
                        {{number_format(($last_wm[$wm_user->user_id]->weight - $prev_weight), 2)}}
                        @php($total_weight = $total_weight + ($last_wm[$wm_user->user_id]->weight - $prev_weight))
                    </td>
                    <td class="bg-pink text-right">
                        {{$wo[$wm_user->user_id]}}
                        @php($total_wo = $total_wo + $wo[$wm_user->user_id])
                    </td>
                    <td class="bg-pink text-right">
                        {{$diff_days[$wm_user->user_id]}}
                        @php($total_diff_day = $total_diff_day + $diff_days[$wm_user->user_id])
                    </td>
                </tr>
		@endif
            @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-darker text-right">
                    <th colspan="2"></th>
                    <th style="text-align: right">
                        @lang('labels.sum_total_measurement')
                    </th>
                    <td>{{number_format($total_measurement, 2)}}</td>
                    <th></th>
                    <th style="text-align: right">
                        @lang('labels.sum_total_weight')
                    </th>
                    <td>{{number_format($total_weight, 2)}}</td>
                    <td class="bg-pink">{{$total_wo}}</td>
                    <td class="bg-pink">{{$total_diff_day}}</td>
                </tr>
            </tfoot>
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
                    footer: true,
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
