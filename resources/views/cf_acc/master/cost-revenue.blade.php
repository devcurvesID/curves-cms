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
        <div class="col-md-12">
            <a href="{{url('cf-acc/dashboard')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.mst_cost_revenue')</h2>
            </center>
        </div>
        <div class="col-md-12">
            <div class="btn btn-default pull-right" data-toggle="modal" data-target="#addaccountinfo" role="button" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: mediumvioletred; margin-right: 10px;" title="@lang('labels.new_account_info')">
                <span class="fa fa-plus-circle" style="margin-top: 10px; color: mediumvioletred;"></span>
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
                                    <td>@lang('labels.indexing')</td>
                                    <td>@lang('labels.account_info')</td>
                                    <td>@lang('labels.transaction_info')</td>
                                    <td></td>
                                </tr>
                            </thead>
                            <tbody>
                                @php($indexing = 1)
                                @foreach($cost_revenue as $cr)
                                    <tr>
                                        <td>{{ $indexing }}</td>
                                        <td><a role="button" class="edit-cost" id="{{ $cr->id }}" data-toggle="modal" data-target="#editcostrevenue">{{$cr->account_info}}</a></td>
                                        <td>{{ $cr->default_transaction_info }}</td>
                                        <td>
                                            <a class="btn btn-danger cost-delete-btn" style="text-align: center;">
                                                <span class="btn-text fa fa-trash"></span>
                                            </a>
                                            <div>
                                                {!! Form::open(['method' => 'DELETE', 'action' => ['AccountingMasterController@delete_cost_revenue',$cr->id]]) !!}
                                                {!! Form::submit(trans('labels.delete'), ['class' => 'btn btn-xs btn-danger hidden']) !!}
                                                {!! Form::close() !!}
                                            </div>
                                        </td>
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

    {{--Modal Add--}}
    <div class="modal fade" id="addaccountinfo" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'POST', 'action' => 'AccountingMasterController@store_cost_revenue', 'id' => 'form-cost-revenue']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.add_new_cost_revenue')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('account_info', trans('labels.account_info').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-6">
                                {!! Form::text('account_info', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('transaction_info', trans('labels.transaction_info').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-6">
                                {!! Form::textarea('transaction_info', null, ['class' => 'form-control', 'id' => 'transaction-info', 'style' => 'height: 130px; resize:none;']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('coa', trans('labels.chart_of_account').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-6">
                                <select name="coa" class="form-control select2" id="coa">
                                    <option value="">@lang('labels.choose') @lang('labels.chart_of_account')</option>
                                    @foreach($coas as $coa)
                                        <option value="{{$coa->id}}">{{ $coa->coa_abv_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" name="submit" id="add-account-info" class="btn btn-success">@lang('labels.add')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    {{--Modal Edit--}}
    <div class="modal fade" id="editcostrevenue" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'PATCH', 'action' => 'AccountingMasterController@update_cost_revenue']) !!}
                {!! Form::hidden('cost_id', null) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">@lang('labels.edit_cost_revenue')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('edit_account_info', trans('labels.account_info').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-6">
                                {!! Form::text('edit_account_info', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('edit_transaction_info', trans('labels.transaction_info').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-6">
                                {!! Form::textarea('edit_transaction_info', null, ['class' => 'form-control', 'id' => 'edit-transaction-info', 'style' => 'height: 130px; resize:none;']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('edit_coa', trans('labels.chart_of_account').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-6">
                                <select name="edit_coa" class="form-control select2" id="edit_coa">
                                    <option value="">@lang('labels.choose') @lang('labels.chart_of_account')</option>
                                    @foreach($coas as $coa)
                                        <option value="{{$coa->id}}">{{ $coa->coa_abv_name }}</option>
                                    @endforeach
                                </select>
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
            var url = "{{url('cf-acc/accounting')}}/"

            $('#data-table').on('click', '.edit-cost', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    cost_id: $(this).attr('id')
                };

                var type = 'POST';
                var my_url = url + 'cost-revenue-ajax';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    success: function (data) {
                        $('input[name="cost_id"]').val(data.cost.id);
                        $('input[name="edit_account_info"]').val(data.cost.account_info);
                        $('textarea#edit-transaction-info').val(data.cost.default_transaction_info);
                        $('select').val(data.cost.coa_id).trigger('change.select2');
                    },
                    error: function (data) {
                        console.log('Error : ', data);
                    }
                });
            });
        })
    </script>
    <script src={{asset("bootbox/bootbox.min.js")}}></script>
    <script>
        $(document).ready(function () {
            $(document).on('click', '.cost-delete-btn', function () {
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