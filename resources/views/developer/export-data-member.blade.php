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
            <a href="{{url('cf-lite/settings')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.export') @lang('labels.member_data')</h2>
            </center>
        </div>
        <div class="row">
            <div class="col-md-12 form-inline">
                {!! Form::open(['method' => 'GET']) !!}
                <div class="col-md-3">
                    {!! Form::label('clubs', trans('labels.choose_club'), ['class' => 'control-label']) !!} :
                </div>
                <div class="col-md-3">
                    {!! Form::select('clubs', $clubs, null, ['class' => 'form-control select2', 'placeholder' => trans('labels.choose_club')]) !!}
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
                                    <th>@lang('labels.name')</th>
                                    <th>@lang('labels.address')</th>
                                    <th>@lang('labels.phone')</th>
                                    <th>@lang('labels.join_date')</th>
                                    <th>@lang('labels.total_wo')</th>
                                    <th>@lang('labels.membership_type')</th>
                                    <th>@lang('labels.status')</th>
                                    <th>@lang('labels.flag')</th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(!empty($member_data))
                                @foreach($member_data as $data)
                                    <tr>
                                        <td>{{ $data->name }}</td>
                                        <td>{{ $data->address == null ? '-' : $data->address }}</td>
                                        <td>{{ $data->cellphone == null ? $data->phone : $data->cellphone }}</td>
                                        <td>{{ $data->join_date }}</td>
                                        <td>{{ $data->total_wo }}</td>
                                        <td>{{ $data->membership_type_name }}</td>
                                        <td>
                                            @if(($data->status == 'Active') || ($data->status == 'Hold'))
                                                {{ $data->status }}
                                            @else
                                                Inactive
                                            @endif
                                        </td>
                                        <td>{{ $data->flag }}</td>
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
                    title: '@lang('labels.member_data') - ({{\Carbon\Carbon::parse(\Carbon\Carbon::now())->format('d-m-Y')}})',
                }]
            });
        });
    </script>
@endsection