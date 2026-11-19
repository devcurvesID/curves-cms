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
                <h2 class="page-title">@lang('labels.master_jubilant')</h2>
            </center>
        </div>
        <div class="col-md-12">
            <a class="btn btn-default pull-right" title="@lang('labels.add_jubilant')" data-toggle="modal" data-target="#add" role="button" style="border-radius: 50px; border-width: 3px; border-color: deeppink; height: 5rem; width: 5rem;"><span class="glyphicon glyphicon-plus" style="margin-top: 8px;color: deeppink;"></span></a>
        </div>
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive" style="background-color: #fff;">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                            <tr>
                                <th>@lang('labels.indexing')</th>
                                <th>@lang('labels.jubilant_name')</th>
                                <th>@lang('labels.type')</th>
                                <th>@lang('labels.action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php($indexing = 1)
                            @foreach($jubilants as $jubilant)
                                <tr>
                                    <td>{{$indexing}}</td>
                                        <td>{{$jubilant->jubilant_name}}</td>
                                        <td>{{$jubilant->type}}</td>
                                        <td style="text-align: center;">
                                            <a class="btn btn-info edit-btn" id="edit-{{$jubilant->id}}" data-toggle="modal" data-target="#edit">
                                                <i class="icon-rocket"></i>
                                                <span class="btn-text fa fa-edit"></span>
                                            </a>
                                            <a class="btn btn-danger delete-btn">
                                                <i class="icon-rocket"></i>
                                                <span class="btn-text fa fa-trash"></span>
                                            </a>
                                            <div>
                                                {!! Form::open(['method' => 'DELETE', 'action' => ['JubilantController@delete_jubilant',$jubilant->id]]) !!}
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

    <div class="modal fade" id="add" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'POST', 'action' => 'JubilantController@store_jubilant']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.add_jubilant')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('jubilant_name', trans('labels.jubilant_name').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::text('jubilant_name', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('type', trans('labels.type').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <select name="type" class="form-control">
                                    <option value="Main">@lang('labels.main')</option>
                                    <option value="Franchise">@lang('labels.franchise')</option>
                                </select>
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
                {!! Form::open(['method' => 'PATCH', 'action' => 'JubilantController@update_jubilant']) !!}
                {!! Form::hidden('jubilant_id', null) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">@lang('labels.edit_jubilant')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('edit_jubilant_name', trans('labels.bank_name').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::text('edit_jubilant_name', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('edit_type', trans('labels.type').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <select name="edit_type" id="type" class="form-control">
                                    <option value="Main">@lang('labels.main')</option>
                                    <option value="Franchise">@lang('labels.franchise')</option>
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
            var url = "{{url('cf-lite')}}/"

            $('#data-table').on('click', '.edit-btn', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    jubilant_id: $(this).attr('id').split('-').pop()
                };

                var type = 'POST';
                var my_url = url + 'master/jubilant/edit-jubilant-ajax';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    success: function (data) {
                        $('input[name="jubilant_id"]').val(data.id);
                        $('input[name="edit_jubilant_name"]').val(data.jubilant_name);
                        $('select#type').val(data.type);
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
            $(document).on('click', '.delete-btn', function () {
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