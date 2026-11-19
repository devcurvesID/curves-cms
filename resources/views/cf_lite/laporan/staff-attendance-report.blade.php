@extends('cf_lite.layouts.staff-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css" rel="stylesheet">

    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <style>
        .select2 {
            width: 100% !important;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/laporan')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.staff_attendance_report')</h2>
            </center>
        </div>
        <div class="row">
            <div class="col-md-12 form-inline">
                {!! Form::open(['method' => 'GET']) !!}
                <div class="col-md-1">
                    {!! Form::label('selected_date', trans('labels.choose_date'), ['class' => 'control-label']) !!} :
                </div>
                <div class="col-md-2">
                    {!! Form::date('selected_date', $selected_date, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary" id="save-btn"><i class="fa fa-search"></i> @lang('labels.search')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive" style="background-color: #fff;">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                                <tr>
                                    <th>@lang('labels.number')</th>
                                    <th>@lang('labels.staff_name')</th>
                                    <th>@lang('labels.keytag')</th>
                                    <th>@lang('labels.date')</th>
                                    <th>@lang('labels.hour_sign_in')</th>
                                    <th>@lang('labels.hour_sign_out')</th>
                                    <th>@lang('labels.workhour_total')</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(!empty($attendances))
                                @php($counter = 0)
                                @foreach($attendances as $attend)
                                    @php($counter++)
                                    <tr>
                                        <td>{{ $counter }}</td>
                                        <td>{{ $attend->staffattendance_user->name }}</td>
                                        <td>{{ !empty($attend->staffattendance_user->user_memberpersonal) ? $attend->staffattendance_user->user_memberpersonal->key_tag_id : '' }}</td>
                                        <td>{{ Carbon\Carbon::parse($selected_date)->format('Y-m-d') }}</td>
                                        <td>{{ Carbon\Carbon::parse($attend->sign_in)->timezone('Asia/Jakarta')->format('H:i:s') }}</td>
                                        <td>{{ Carbon\Carbon::parse($attend->sign_out)->timezone('Asia/Jakarta')->format('H:i:s') }}</td>
                                        <td>{{ Carbon\Carbon::parse($attend->sign_out)->diff(Carbon\Carbon::parse($attend->sign_in))->format('%H:%i:%s') }}</td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>

    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2();
            $('#data-table').DataTable({
                dom: 'Bfrtip',
                bPaginate: false,
                fixedHeader: true,
                buttons: [{
                    extend: 'excel',
                    title: '@lang('labels.staff_attendance_report') - ({{\Carbon\Carbon::parse($selected_date)->format('d-m-Y')}})',
                }]
            });
        });
    </script>
@endsection