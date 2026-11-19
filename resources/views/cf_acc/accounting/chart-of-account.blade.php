@extends('cf_acc.layouts.acc-dashboard-template')

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
            <a href="{{url('cf-lite/dashboard')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.chart_of_account')</h2>
            </center>
        </div>
        <div class="col-md-12">
            <div class="btn btn-default pull-right" data-toggle="modal" data-target="#addCoa" role="button" style="border-radius: 50px; width: 65px; height: 65px; border-width: 3px; border-color: limegreen; margin-right: 10px;" title="@lang('labels.add_chart_of_account')">
                <span class="fa fa-plus-circle" style="font-size: 60px; margin-top: -6px; margin-left: -8px; color: limegreen;"></span>
            </div>
        </div>
        {!! Form::open(['method'=>'GET']) !!}
        <div class="col-md-6">
            {!! Form::select('selected_jubilant', $jubilants, $selectedJubilant == 0 ? null : $selectedJubilant ,['class'=>'form-control','id'=>'coa-select']) !!}
        </div>
        <div class="col-md-6">
            {!! Form::submit('Filter',['class'=>'btn btn-primary']) !!}
        </div>
        {!! Form::close() !!}
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive" style="background-color: #fff;">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                            <tr>
                                {{--<th>@lang('labels.indexing')</th>--}}
                                <th>@lang('labels.number')</th>
                                <th>@lang('labels.chart_of_account_name')</th>
                                <th>@lang('labels.chart_of_account_abv_name')</th>
                                <th>@lang('labels.main_account')</th>
                                <th>@lang('labels.type')</th>
                                <th></th>

                            </tr>
                            </thead>
                            <tbody>
                            {{--@php($indexing = 1)--}}
                            @if(!empty($coas))
                                @foreach($coas as $coa)
                                    <tr>
                                        {{--<td>{{$indexing}}</td>--}}
                                        <td><a role="button" class="edit-coa" id="{{$coa->id}}" data-toggle="modal" data-target="#editCoa">{{$coa->coa_number}}</a></td>
                                        <td>{{$coa->coa_name}}</td>
                                        <td>{{$coa->coa_abv_name}}</td>
                                        <td>{{$coa->chartofaccount_mainaccount->account_abv_name}}</td>
                                        <td>{{$coa->chartofaccount_mainaccount->account_type}}</td>
                                        <td>
                                            <a class="btn btn-danger coa-delete-btn">
                                                <span class="btn-text fa fa-trash"></span>
                                            </a>
                                            <div>
                                                {!! Form::open(['method' => 'DELETE', 'action' => ['ChartOfAccountController@delete_chart_of_account',$coa->id]]) !!}
                                                {!! Form::submit(trans('labels.delete'), ['class' => 'btn btn-xs btn-danger hidden']) !!}
                                                {!! Form::close() !!}
                                            </div>
                                        </td>
                                    </tr>
                                    {{--@php($indexing++)--}}
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addCoa" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'POST', 'action' => 'ChartOfAccountController@store_chart_of_account', 'id' => 'form-chart-of-account']) !!}
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
                                {!! Form::select('coa_main', $main_accounts, null, ['class' => 'form-control select3', 'placeholder' => '-- '.trans('labels.choose').' '.trans('labels.main_account').' --']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('jubilant_id', trans('labels.choose_jubilant').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::select('jubilant_id', $jubilants, $selectedJubilant == 0 ? null : $selectedJubilant, ['class' => 'form-control select3']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" name="submit" id="add-chart-of-account" class="btn btn-success">@lang('labels.add')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="modal fade" id="editCoa" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'PATCH', 'action' => 'ChartOfAccountController@update_chart_of_account']) !!}
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
                                {!! Form::select('edit_coa_main', $main_accounts, null, ['class' => 'form-control select3', 'id' => 'select-edit-main', 'placeholder' => '-- '.trans('labels.choose').' '.trans('labels.main_account').' --']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('edit_jubilant_id', trans('labels.choose_jubilant').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::select('edit_jubilant_id', $jubilants, $selectedJubilant, ['class' => 'form-control select3','id'=>'select-edit-jubilant']) !!}
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
            $('.select3').select2();
            $('#coa-select').select2();
            $('#data-table').dataTable({
                "order": [[ 0, "asc" ]]
            });
        });

    </script>
    <script>
        $(document).ready(function () {
            var url = "{{url('cf-acc/accounting')}}/"

            $('#data-table').on('click', '.edit-coa', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    coa_id: $(this).attr('id')
                };

                var type = 'POST';
                var my_url = url + 'chart-of-account-ajax';

                $.ajax({
                   type: type,
                   url: my_url,
                   data: formData,
                   success: function (data) {
                       $('input[name="coa_id"]').val(data.coa.id);
                       $('input[name="edit_number"]').val(data.coa.coa_number);
                       $('input[name="edit_name"]').val(data.coa.coa_name);
                       $('input[name="edit_coa_abv"]').val(data.coa.coa_abv_name);
                       $('input[name="edit_jubilant_id"]').val(data.coa.jubilant_id);
                       $('#select-edit-main').val(data.coa.main_account_id).trigger('change.select2');
                       $('#select-edit-jubilant').val(data.coa.jubilant_id).trigger('change.select2');
                   },
                   error: function (data) {
                       console.log('Error : ', data);
                   }
                });
            });
        });
    </script>
    <script src={{asset("bootbox/bootbox.min.js")}}></script>
    <script>
        $(document).ready(function () {
            $(document).on('click', '.coa-delete-btn', function () {
                var theform = $(this).siblings('div').children('form');
                bootbox.confirm({
                    message: "@lang('messages.are_you_sure')",
                    buttons: {
                        confirm: {
                            label: '@lang('labels.yes')',
                            className: 'btn-danger'
                        },
                        cancel: {
                            label: '@lang('labels.no')',
                        }
                    },
                    callback: function (result) {
                        if (result == false) {
                        } else {
                            theform.submit();
                        }
                    }
                });
            });
        });
    </script>
@endsection