@extends('cf_lite.layouts.staff-dashboard-template')

@section('title', trans('labels.fa_source_report').' : ' . Auth::user()->user_club->club_name)

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
                <a href="{{url('cf-lite/laporan')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            </left>
            <center>
                <h2 class="page-title">
                    @lang('labels.fa_source_report') : {{Auth::user()->user_club->club_name}}
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
            {!! Form::open(['method' => 'GET', 'action' => 'ReportController@fa_source_main']) !!}
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
                    <th>@lang('labels.source')</th>
                    <th>@lang('labels.sale')</th>
                    <th>
                        @if(isset($_GET['f']) && isset($_GET['t']))
                            @if(!empty($_GET['f']) && !empty($_GET['t']))
                                <a href="{{url('cf-lite/laporan/source-report/no-sale-reason?f='.$_GET['f'].'&t='.$_GET['t'])}}" title="@lang('labels.see_no_sale_reason')">@lang('labels.no_sale')</a>
                            @elseif(!empty($_GET['f']) && empty($_GET['t']))
                                <a href="{{url('cf-lite/laporan/source-report/no-sale-reason?f='.$_GET['f'].'&t=')}}" title="@lang('labels.see_no_sale_reason')">@lang('labels.no_sale')</a>
                            @else
                                <a href="{{url('cf-lite/laporan/source-report/no-sale-reason')}}" title="@lang('labels.see_no_sale_reason')">@lang('labels.no_sale')</a>
                            @endif
                        @else
                            <a href="{{url('cf-lite/laporan/source-report/no-sale-reason')}}" title="@lang('labels.see_no_sale_reason')">@lang('labels.no_sale')</a>
                        @endif
                    </th>
                </tr>
                </thead>
                <tbody>
                @php($indexing = 1)
                @foreach($sources as $source)
                    <tr>
                        <td>{{$indexing}}</td>
                        <td>{{$source->source}}</td>
                        <td>
                            @php($sale = 0)
                            @foreach($fas as $fa)
                                @if(($fa->guestappointment_buddyreferrallead->source_id == $source->id) && ($fa->guestappointment_gastatus->status_id == 5))
                                    @php($sale++)
                                @endif
                            @endforeach
                            {{$sale}}
                        </td>
                        <td>
                            @php($nosale = 0)
                            @foreach($fas as $fa)
                                @if(($fa->guestappointment_buddyreferrallead->source_id == $source->id) && ($fa->guestappointment_gastatus->status_id == 6))
                                    @php($nosale++)
                                @endif
                            @endforeach
                            {{$nosale}}
                        </td>
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