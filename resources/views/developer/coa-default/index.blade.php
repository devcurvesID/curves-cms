@extends('cf_lite.layouts.staff-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

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
                <h2 class="page-title">@lang('labels.default') @lang('labels.chart_of_account')</h2>
            </center>
        </div>
        <div class="col-md-12">
            <a class="btn btn-default pull-right" title="@lang('labels.add_chart_of_account')" data-toggle="modal" data-target="#add" role="button" style="border-radius: 50px; border-width: 3px; border-color: deeppink; height: 5rem; width: 5rem;"><span class="glyphicon glyphicon-plus" style="margin-top: 8px;color: deeppink;"></span></a>
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
                                <th>@lang('labels.chart_of_account_name')</th>
                                <th>@lang('labels.chart_of_account_abv_name')</th>
                                <th>@lang('labels.main_account')</th>
                                <th>@lang('labels.type')</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($coas as $coa)
                                <tr>
                                    <td>{{$coa->coa_number}}</td>
                                    <td>{{$coa->coa_name}}</td>
                                    <td>{{$coa->coa_abv_name}}</td>
                                    <td>{{$coa->coadefault_mainaccount->account_abv_name}}</td>
                                    <td>{{$coa->coadefault_mainaccount->account_type}}</td>
                                    <td style="text-align: center;">
                                        <a class="btn btn-info edit-btn" id="edit-{{$coa->id}}" data-toggle="modal" data-target="#edit">
                                            <span class="btn-text fa fa-edit"></span>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="add" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'POST', 'action' => 'MasterCoaDefaultController@store_coa_default']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.add_chart_of_account')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('number', trans('labels.number').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::text('number', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('name', trans('labels.chart_of_account_name').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::text('name', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('coa_abv', trans('labels.chart_of_account_abv_name').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::text('coa_abv', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('coa_main', trans('labels.main_account').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::select('coa_main', $main_accounts, null, ['class' => 'form-control select2', 'placeholder' => '-- '.trans('labels.choose').' '.trans('labels.main_account').' --']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" name="submit" id="add-bank" class="btn btn-success">@lang('labels.add')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'PATCH', 'action' => 'MasterCoaDefaultController@update_coa_default']) !!}
                {!! Form::hidden('coa_id', null) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">@lang('labels.edit_chart_of_account')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('edit_number', trans('labels.number').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::text('edit_number', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('edit_name', trans('labels.chart_of_account_name').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::text('edit_name', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('edit_coa_abv', trans('labels.chart_of_account_abv_name').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::text('edit_coa_abv', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('edit_coa_main', trans('labels.main_account').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::select('edit_coa_main', $main_accounts, null, ['class' => 'form-control select2', 'id' => 'edit_coa_main', 'placeholder' => '-- '.trans('labels.choose').' '.trans('labels.main_account').' --']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" name="submit" class="btn btn-success">@lang('labels.update')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2();
            $('#data-table').dataTable();
        });
    </script>
    <script>
        $(document).ready(function () {
            var url = "{{url('cf-lite')}}/"

            $('#data-table').on('click', '.edit-btn', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    coa_id: $(this).attr('id').split('-').pop()
                };

                var type = 'POST';
                var my_url = url + 'master/coa-default/edit-coa-default-ajax';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    success: function (data) {
                        $('input[name="coa_id"]').val(data.id);
                        $('input[name="edit_number"]').val(data.coa_number);
                        $('input[name="edit_name"]').val(data.coa_name);
                        $('input[name="edit_coa_abv"]').val(data.coa_abv_name);
                        $('select#edit_coa_main').val(data.main_account_id).trigger('change.select2');
                    },
                    error: function (data) {
                        console.log('Error : ', data);
                    }
                });
            });
        });
    </script>
@endsection