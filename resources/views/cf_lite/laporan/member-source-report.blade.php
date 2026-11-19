@extends('cf_lite.layouts.staff-template')

@section('title', trans('labels.fa_source_report').' : ' . Auth::user()->user_club->club_name)

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('bootstrap-toggle/css/bootstrap-toggle.min.css')}}" rel="stylesheet">
    <link href="{{asset('select2/dist/css/select2.min.css')}}" rel="stylesheet">
    <style>
        .page-title {
            font-size: 44px;
            margin-top: 0;
            margin-bottom: 20px;
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
                    @lang('labels.member_source_report') : {{Auth::user()->user_club->club_name}}
                    @if(!empty($_GET['from_date']) && !empty($_GET['thru_date']))
                        <br><small>({{date('d F Y', strtotime($_GET['from_date']))}} - {{date('d F Y', strtotime($_GET['thru_date']))}})</small>
                    @elseif(!empty($_GET['from_date']) && empty($_GET['thru_date']))
                        <br><small>({{date('d F Y', strtotime($_GET['from_date']))}})</small>
                    @else
                        <br><small>@lang('labels.from_the_beginning')</small>
                    @endif
                </h2>
                {!! Form::open(['method' => 'GET']) !!}
                <div class="col-md-6 col-md-offset-3">
                    <div class="form-inline">
                        <div class="form-group pull-left">
                            {!! Form::label('date', trans('labels.choose'). ' ' .trans('labels.date'). ' : ', ['class' => 'control-label']) !!}
                            {!! Form::date('from_date', null, ['class' => 'form-control']) !!}
                            {!! Form::label('until', trans(' - '), ['class' => 'control-label']) !!}
                            {!! Form::date('thru_date', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-inline">
                        <div class="form-group pull-left" style="margin-top: 10px; margin-bottom: 10px;">
                            {!! Form::label('source', trans('labels.source'). ' : ', ['class' => 'control-label']) !!}
                            <select name="source" class="form-control select2" required>
                                <option value="">{{trans('labels.choose'). ' '.trans('labels.source')}}</option>
                                @foreach($sources as $source)
                                    @if($source->id == $source_temp)
                                        <option value="{{$source->id}}" selected>{{$source->source}}</option>
                                    @else
                                        <option value="{{$source->id}}">{{$source->source}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-inline">
                        <div class="form-group pull-left">
                            {!! Form::label('source', trans('labels.membership'). ' : ', ['class' => 'control-label']) !!}
                            <select name="membership" class="form-control select2">
                                <option value="">{{trans('labels.choose'). ' '.trans('labels.membership')}}</option>
                                @foreach($membership_types as $membership_type)
                                    @if($membership_type->id == $membership_temp)
                                        <option value="{{$membership_type->id}}" selected>{{$membership_type->membership_type_name}}</option>
                                    @else
                                        <option value="{{$membership_type->id}}">{{$membership_type->membership_type_name}}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-top: 10px;">
                        <button type="submit" class="btn btn-info"><span class="fa fa-search"></span> @lang('labels.search')</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </center>
        </div>
        @if(!empty($member_reports1))
            <div class="col-xs-12">
                <table id="dataTable" class="table table-bordered table-striped" style="background: white">
                    <thead>
                    <tr>
                        <th>@lang('labels.indexing')</th>
                        <th>@lang('labels.member_name')</th>
                        <th>@lang('labels.membership_type')</th>
                        <th>@lang('labels.amount_paid')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($indexing = 1)
                    @foreach($member_reports1 as $member_report)
                        <tr>
                            <td>{{$indexing}}</td>
                            <td>{{$member_report->name}}</td>
                            <td>{{$member_report->membership_type_name}}</td>
                            <td class="my_rupiah" style="text-align: right;">
                                {{$member_report->total_amount_paid}}
                            </td>
                        </tr>
                        @php($indexing++)
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif

    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script src="{{asset('js/buttons.print.min.js')}}"></script>
    <script src="{{asset('select2/dist/js/select2.js')}}"></script>
    <script>
        $(document).ready(function () {
            init_format();
            $('.select2').select2();
            $('#dataTable').DataTable();
        });

        // $('#dataTable').DataTable({
        //     info: false,
        //     dom: 'Bfrtip',
        //     buttons: [
        //         'print',
        //     ],
        //     pageLength: 100
        // });
        //
        // $('.dt-buttons button').prepend('<i class="fa fa-print"></i> ');
        // $('.dt-buttons button').addClass('btn btn-default');
        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp.' + (x));
            });
        }
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>
@endsection