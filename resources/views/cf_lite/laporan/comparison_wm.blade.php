@extends('cf_lite.layouts.staff-dashboard-template')

@section('title', 'Laporan Selisih Ukuran Tubuh : ' . $club->club_name)

@section('other-style')
<link href="{{asset('fullcalendar/fullcalendar.min.css')}}" rel="stylesheet">
<link href="{{asset('fullcalendar/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">

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

    @media print {

        th,
        td {
            vertical-align: middle !important;
        }

        tr td:nth-child(5),
        tr th:nth-child(5),
        tr td:nth-last-child(1),
        tr th:nth-last-child(1) {
            background-color: #f1f1f1 !important;
        }
    }
</style>
@endsection

@section('content')
<div class="col-xs-12">
    <left>
        <a href="{{url('cf-lite/laporan')}}" class="btn btn-default"
            style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;"
            title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left"
                style="margin-top: 10px; color: deeppink;"></span></a>
    </left>
    <center>
        <h2 class="page-title">@lang('labels.list') @lang('labels.measurement_difference')
            : {{$club->club_name}}</h2>
    </center>
</div>
<div class="col-xs-12">
    {!! Form::open(['method' => 'GET', 'action' => 'ReportController@list_comparison_wm']) !!}
    <div class="form-group" style="width: 50%;">
        <div class="input-group">
            {!! Form::select('status', ['Active' => 'Active', 'Inactive' => 'Inactive', 'Stopped' => 'Stopped','Hold' =>
            'Hold','Semua' => 'Semua'], $status, ['class' =>
            'form-control']) !!}
            <span class="input-group-btn">
                <button class="btn btn-default btn-filter-zone" type="submit" style="height: 34px;"><span
                        class="fa fa-search"></span></button>
            </span>
        </div>
    </div>
    {!! Form::close() !!}
    <table id="dataTable" class="table table-bordered" style="background: white">
        <thead>
            <tr>
                <th style="vertical-align: middle">@lang('labels.indexing')</th>
                <th style="vertical-align: middle">@lang('labels.member')</th>
                <th style="vertical-align: middle">@lang('labels.first_measurement')</th>
                <th style="vertical-align: middle">@lang('labels.last_measurement')</th>
                <th class="bg-darker" style="vertical-align: middle">@lang('labels.loss_total_measurement')</th>
                <th style="vertical-align: middle">@lang('labels.first_weight')</th>
                <th style="vertical-align: middle">@lang('labels.last_weight')</th>
                <th class="bg-darker" style="vertical-align: middle">@lang('labels.loss_weight')</th>
            </tr>
        </thead>
        <tbody>
            @php($count = 1)
            @php($count_measure = 0)
            @php($count_weight = 0)
            @foreach($list_user_wm as $x)
            <tr>
                <td>{{$count++}}</td>
                <td>{{$x[0]->weighmeasure_member->name}}</td>
                <td>{{$x[0]->total_measurement}} CM</td>
                <td>
                    @if($x[0]->id == $x[1]->id)
                    -
                    @else
                    {{$x[1]->total_measurement}} CM
                    @endif
                </td>
                <td class="bg-darker">
                    @if($x[0]->id == $x[1]->id)
                    -
                    @else
                    @php($count_measure += ($x[0]->total_measurement - $x[1]->total_measurement))
                    {{$x[0]->total_measurement - $x[1]->total_measurement}} CM
                    @endif
                </td>
                <td>{{$x[0]->weight}} KG</td>
                <td>
                    @if($x[0]->id == $x[1]->id)
                    -
                    @else
                    {{$x[1]->weight}} KG
                    @endif
                </td>
                <td class="bg-darker">

                    @if($x[0]->id == $x[1]->id)
                    -
                    @else
                    @php($count_weight += ($x[0]->weight - $x[1]->weight))
                    {{$x[0]->weight - $x[1]->weight}} KG
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>

            <tr>
                <th colspan="3" style="text-align: right">
                </th>
                <th style="text-align: right">
                    @lang('labels.sum_total_measurement')
                </th>
                <td>
                    {{$count_measure}} CM
                </td>
                <th style="text-align: right">
                </th>
                <th style="text-align: right">
                    @lang('labels.sum_total_weight')
                </th>
                <td>
                    {{$count_weight}} KG
                </td>
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

<script src="{{asset('js/moment.min.js')}}"></script>
<script src="{{asset('fullcalendar/fullcalendar.min.js')}}"></script>
<script src="{{asset('bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>
<script>
    $('.tanggal').datetimepicker({
            format: 'MM-YYYY',
            defaultDate: '{{date('Y-m-d')}}',
        });
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