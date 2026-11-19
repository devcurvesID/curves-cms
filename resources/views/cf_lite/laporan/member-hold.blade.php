@extends('cf_lite.layouts.staff-dashboard-template')

@section('title', trans('labels.member_hold_report').' : ' . $club->club_name)

@section('other-style')
    <style>
        .dt-buttons {
            float: left;
        }

        #dataTable_filter {
            float: right;
        }
    </style>
@endsection

@section('content')
    <div class="col-xs-12">
        <left>
            <a href="{{url('cf-lite/laporan')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
        </left>
        <center>
            <h2 class="page-title">
                @lang('labels.member_hold_list') : {{$club->club_name}}
                @if(!empty($date))
                    <br><small>({{date('d F Y', strtotime($date))}})</small>
                @else
                    <br><small>(@lang('labels.today'): {{date('d F Y', strtotime(\Carbon\Carbon::today()))}})</small>
                @endif
            </h2>
        </center>
    </div>
    <div class="col-md-12">
        {!! Form::open(['method' => 'GET', 'action' => 'ReportController@member_hold_filter']) !!}
        <div class="form-inline">
            <div class="form-group">
                {!! Form::label('date', trans('labels.choose').' '.trans('labels.date').': ', ['class' => 'control-label']) !!}
                <div class="input-group">
                    {!! Form::date('date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
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
                <th>@lang('labels.hold_date')</th>
                <th>@lang('labels.category')</th>
                <th>@lang('labels.note')</th>
            </tr>
            </thead>
            <tbody>
            @php($indexing = 1)
            @foreach($member_holds as $member_hold)
                <tr>
                    <td>{{$indexing}}</td>
                    <td>{{$member_hold->memberhold_user->name}}</td>
                    <td>{{date('d-m-Y', strtotime($member_hold->from_date))}} @lang('labels.until') {{date('d-m-Y', strtotime($member_hold->thru_date))}}</td>
                    <td>{{$member_hold->memberhold_memberholdreason->member_hold_reason}}</td>
                    <td>
                        @if(!empty($member_hold->member_hold_note))
                            {{$member_hold->member_hold_note}}
                        @else
                            -
                        @endif
                    </td>
                </tr>
                @php($indexing++)
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
                'print',
            ]
        });

        $('.dt-buttons button').prepend('<i class="fa fa-print"></i> ');
        $('.dt-buttons button').addClass('btn btn-default');
    </script>
@endsection