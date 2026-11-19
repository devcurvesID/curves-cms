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
    <div class="row">
        <div class="col-xs-12">
            <left>
                @if(isset($_GET['f']) && isset($_GET['t']))
                    @if(!empty($_GET['f']) && !empty($_GET['t']))
                        <a href="{{url('cf-lite/laporan/source-report?f='.$_GET['f'].'&t='.$_GET['t'])}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                    @elseif(!empty($_GET['f']) && empty($_GET['t']))
                        <a href="{{url('cf-lite/laporan/source-report?f='.$_GET['f'].'&t=')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                    @else
                        <a href="{{url('cf-lite/laporan/source-report')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                    @endif
                @else
                    <a href="{{url('cf-lite/laporan/source-report')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                @endif
            </left>
            <center>
                <h2 class="page-title">
                    @lang('labels.fa_no_sale_reason_recap') : {{Auth::user()->user_club->club_name}}
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
            {!! Form::open(['method' => 'GET', 'action' => 'ReportController@fa_source_nosale_reason']) !!}
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
                    <th>@lang('labels.source')</th>
                    @foreach($nosale_reasons as $nosale_reason)
                        <th>{{$nosale_reason->nosale_reason}}</th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @foreach($sources as $source)
                    <tr>
                        <td>{{$source->source}}</td>
                        @foreach($nosale_reasons as $nosale_reason)
                            <td>
                                @php($nosale = 0)
                                @foreach($fas as $fa)
                                    @if(($fa->guestappointment_buddyreferrallead->source_id == $source->id) && ($fa->guestappointment_gastatus->status_id == 6))
                                        @if(!empty($fa->guestappointment_guestnosalereason) && ($fa->guestappointment_guestnosalereason->no_sale_reason_id == $nosale_reason->id))
                                            @php($nosale++)
                                        @endif
                                    @endif
                                @endforeach
                                {{$nosale}}
                            </td>
                        @endforeach
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