@extends('cf_lite.layouts.staff-dashboard-template')

@section('title', trans('labels.report_absent_member').' : ' . $club->club_name)

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">
@endsection

@section('content')
    <div class="col-xs-12">
        <left>
            <a href="{{url('cf-lite/laporan')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
        </left>
        <center>
            <h2 class="page-title">
                @lang('labels.absent_member_list') : {{$club->club_name}}
                @if(!empty($absence))
                    <br>
                    <small>(@lang('labels.since'): {{date('d F Y', strtotime(\Carbon\Carbon::now()->subDays($absence)))}})</small>
                @endif
            </h2>
        </center>
    </div>
    <div class="col-md-12">
        {!! Form::open(['method' => 'GET']) !!}
        <div class="form-inline">
            <div class="form-group">
                {!! Form::label('absence', trans('labels.absence').' ('.trans('labels.day').'): ', ['class' => 'control-label']) !!}
                <div class="input-group">
                    {!! Form::number('absence', !empty($absence) ? $absence : '7', ['class' => 'form-control']) !!}
                    <span class="input-group-addon">@lang('labels.day')</span>
                </div>
                {!! Form::submit(trans('labels.search'), ['class' => 'btn btn-default']) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    <div class="col-xs-12">
        <div class="x_panel">
            <div class="x_content">
                <br/>
                <div class="table-responsive">
                    <table id="datable_1" class="table table-bordered table-striped" style="background: white">
                        <thead>
                        <tr>
                            <th>@lang('labels.indexing')</th>
                            <th>@lang('labels.name')</th>
                            <th>@lang('labels.phone')</th>
                            <th>@lang('labels.last_workout')</th>
                            <th>@lang('labels.membership_type')</th>
                            <th>@lang('labels.not_workout_duration') (@lang('labels.day'))</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($members as $member)
                            @if(!empty($member->workout_date))
                                <tr>
                                    <td></td>
                                    <td>{{$member->name}}</td>
                                    <td>{{$member->user_memberpersonal->phone}}</td>
                                    <td>{{date('d-m-Y', strtotime($member->workout_date))}}</td>
                                    <td>{{$member->user_memberstatus->memberstatus_membershiptype->membership_type}}</td>
                                    @if(empty($absence))
                                        @if(\Carbon\Carbon::parse($member->workout_date)->diffInDays(\Carbon\Carbon::today(),false)<7)
                                            <td>{{7}}</td>
                                        @else
                                            <td>{{\Carbon\Carbon::parse($member->workout_date)->diffInDays(\Carbon\Carbon::today(),false)}}</td>
                                        @endif
                                    @else
                                        @if(\Carbon\Carbon::parse($member->workout_date)->diffInDays(\Carbon\Carbon::today(),false)<$absence)
                                            <td>{{$absence}} @lang('labels.day')</td>
                                        @else
                                            <td>{{\Carbon\Carbon::parse($member->workout_date)->diffInDays(\Carbon\Carbon::today(),false)}}</td>
                                        @endif
                                    @endif
                                </tr>
                            @endif
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>

    <script>
        $(document).ready(function () {
            var t = $('#datable_1').DataTable({
                'iDisplayLength': 10,
                'lengthMenu': [[10, 50, 100, -1], [10, 50, 100, '@lang('labels.all')']],
                "columnDefs": [ {
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                } ]
            });

            t.on( 'order.dt search.dt', function () {
                t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                    cell.innerHTML = i+1;
                } );
            } ).draw();
        });
    </script>
@endsection