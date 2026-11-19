@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <style>

        .btn-primary,
        .voucher-list-add a,
        #edit-voucher-btn {
            background-color: #ff1493;
            color: #ffffff;
            border-color: #ff1493;
        }

        .btn-primary:hover,
        .voucher-list-add a:hover,
        #edit-voucher-btn:hover {
            color: #ff1493;
            background-color: transparent;
            border-color: #ff1493;
        }

        table th{
            color: #ff1493;
        }

    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/laporan')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">
                        @lang('labels.staff_fa_success')
                    </h2>
                    @if(isset($_GET['from_date']) && isset($_GET['thru_date']))
                    <h3>({{date('d F Y', strtotime($from_date))}} - {{date('d F Y', strtotime($thru_date))}})</h3>
                    @endif
                </center>
            </div>
        </div>
        <hr>
        <div id="section-filter">
            <div class="row">
                <div class="col-xs-12">
                {!! Form::open(['method' => 'GET', 'action' => ['ReportController@staff_fa_date_filter']]) !!}
                    <div class="col-xs-2">
                        Filter :
                    </div>
                    <div class="col-xs-3">
                        {!! Form::date('from_date', \Carbon\Carbon::now() , ['class' => 'form-control filter-date','placeholder'=>@trans('labels.from_date'), 'id'=>'filt-dari']) !!} <br/>
                    </div>
                    <div class="col-xs-3">
                        {!! Form::date('thru_date', \Carbon\Carbon::now() , ['class' => 'form-control filter-date','placeholder'=>@trans('labels.from_date'), 'id'=>'filt-sampai']) !!} <br/>
                    </div>
                    <div class="col-xs-2">
                        <button type="submit" class="btn btn-primary" id="save-btn"><i class="fa fa-search"></i> @lang('labels.search')</button>
                    </div>
                {!! Form::close() !!}
                </div>

            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="data-table">
                                <thead>
                                <tr>
                                    <th>@lang('labels.number')</th>
                                    <th>@lang('labels.staff_name')</th>
                                    <th>@lang('labels.fa_success')</th>
                                    <th>@lang('labels.fa_fail')</th>
                                    <th>@lang('labels.fa_total')</th>
                                    <th>@lang('labels.percentage')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php($indexing = 1)
                                @foreach($staff_fas as $staff)
                                    <tr>
                                        <td>{{$indexing}}</td>
                                        <td>{{$staff->name}}</td>
                                        <td>{{$staff->success_count}}</td>
                                        <td>{{$staff->fail_count}}</td>
                                        <td>{{$staff->success_count + $staff->fail_count}}</td>
                                        @if($staff->success_count + $staff->fail_count != 0)
                                        <td>{{number_format(($staff->success_count / ($staff->success_count + $staff->fail_count)) * 100 , 2)}}%</td>
                                        @else
                                        <td>0%</td>
                                        @endif
                                    </tr>
                                @php($indexing++)
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>

    <script>
        $('#data-table').dataTable();
    </script>

    <script src="{{asset('js/my_js.js')}}"></script>

@endsection