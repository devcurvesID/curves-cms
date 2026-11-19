@extends('cf_back.layouts.master-dashboard')

@section('title', 'Roles')

@section('other-style')
@endsection

@section('content')
    <div class="container">
        <div class="my-section">
            <div class="section-title">
                <center>
                    <h1 class="page-title">@lang('labels.list') @lang('labels.role')</h1>
                </center>
            </div>
            @include('cf_back.flash-message')


            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <a id="btn-add-role" class="btn btn-primary pull-right"><i
                                class="fa fa-plus"></i> @lang('labels.add') @lang('labels.role')</a>
                    <div class="x_panel">
                        <div class="x_content">
                            <br/>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="data-table">
                                    <thead>
                                    <tr>
                                        <th>
                                            @lang('labels.id')
                                        </th>
                                        <th>
                                            @lang('labels.role')
                                        </th>
                                        <th>
                                            @lang('labels.edit')
                                        </th>
                                        <th>
                                            @lang('labels.delete')
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($roles as $role)
                                        <tr id="{{$role->id}}" data-value="{{$role->role}}">
                                            <td>{{$role->id}}</td>
                                            <td>{{$role->role}}</td>
                                            <td>
                                                <a class="btn btn-warning btn-edit-role"><i
                                                            class="fa fa-pencil"></i> @lang('labels.edit')
                                                </a>
                                            </td>
                                            <td>
                                                <a class="btn btn-danger btn-hapus"><i
                                                            class="fa fa-trash"></i> @lang('labels.delete')</a>
                                                <div>
                                                    {!! Form::open(['method' => 'DELETE', 'action' => ['RolesController@destroy', $role->id]]) !!}
                                                    {!! Form::close() !!}
                                                </div>
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
        </div>
    </div>


    {{--MODAL TAMBAH--}}
    <button type="button" id="btn-pop-add" class="hidden" data-toggle="modal" data-target="#add-role"></button>

    <!-- Modal -->
    <div class="modal fade" id="add-role" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">@lang('labels.add') @lang('labels.role')</h4>
                </div>
                <div class="modal-body">
                    <div>
                        {!! Form::open(['url' => 'cf-back/role']) !!}
                        <div class="form-group">
                            {!! Form::label('role', trans('labels.name').' '.trans('labels.role'), ['class' => 'control-label']) !!}
                            {!! Form::text('role', null, ['class' => 'form-control', 'placeholder' => trans('labels.insert').' '.trans('labels.role')]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit(trans('labels.add').' '.trans('labels.role'), ['class' => 'btn btn-primary form-control']) !!}
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--MODAL TAMBAH--}}

    {{--MODAL EDIT--}}
    <button type="button" id="btn-pop-edit" class="hidden" data-toggle="modal" data-target="#edit-role"></button>
    <div class="modal fade" id="edit-role" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">@lang('labels.edit') @lang('labels.role')</h4>
                </div>
                <div class="modal-body">
                    <div id="form-update-role">
                        {!! Form::model($role, ['method' => 'PATCH', 'action' => ['RolesController@update', $role->id]]) !!}
                        <div class="form-group">
                            {!! Form::label('role', trans('labels.name').' '.trans('labels.role'), ['class' => 'control-label']) !!}
                            {!! Form::text('role', null, ['id'=>'text-update-role','class' => 'form-control', 'placeholder' => trans('labels.role_name_placeholder')]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit(trans('labels.edit'), ['class' => 'btn btn-primary form-control']) !!}
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--MODAL EDIT--}}
@endsection

@section('other-script')
    <script src={{asset("js/jquery.dataTables.min.js")}}></script>
    <script src={{asset("js/dataTables.bootstrap.min.js")}}></script>
    <script>
        $('#data-table').dataTable({
            bAutoWidth: false,
            aoColumns: [
                {sWidth: '10%'},
                {sWidth: '60%'},
                {sWidth: '15%'},
                {sWidth: '15%'}
            ]
        });

        $('#btn-add-role').click(function () {
            $('#btn-pop-add').click();
        });
        $('.btn-edit-role').click(function () {
            var x = $('#form-update-role > form').attr('action');
            var arr = x.split('/');
            arr.pop();
            arr.push($(this).parent().parent().attr('id'));
            var action = arr.join('/');
            $('#form-update-role > form').attr('action', action);
            $('#text-update-role').val($(this).parent().parent().attr('data-value'));
            $('#btn-pop-edit').click();
        });
    </script>
@endsection

