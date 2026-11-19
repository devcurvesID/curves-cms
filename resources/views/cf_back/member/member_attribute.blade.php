@extends('cf_back.layouts.master-dashboard')

@section('title', 'Member')

@section('other-style')
@endsection

@section('content')
    <div class="container">
        <div class="my-section">
            <div class="section-title">
                <center>
                    <h1 class="page-title">@lang('labels.list') @lang('labels.member_attribute')</h1>
                </center>
            </div>
            @include('cf_back.flash-message')
            <?php $x = null ?>

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <a id="btn-add" class="btn btn-primary" style="float: right"><i
                                class="fa fa-plus"></i> @lang('labels.add') @lang('labels.attribute')</a>
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
                                            @lang('labels.attribute')
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
                                    @foreach($attributes as $x)
                                        <tr id="{{$x->id}}" data-value="{{$x->attribute}}">
                                            <td>{{$x->id}}</td>
                                            <td>{{$x->attribute}}</td>
                                            <td>
                                                <a class="btn btn-warning btn-edit"><i
                                                            class="fa fa-pencil"></i> @lang('labels.edit')</a>
                                            </td>
                                            <td>
                                                <a class="btn btn-danger btn-hapus"><i
                                                            class="fa fa-trash"></i> @lang('labels.delete')</a>
                                                <div>
                                                    {!! Form::open(['method' => 'DELETE', 'action' => ['MemberController@destroy_attribute', $x->id]]) !!}
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
    <button type="button" id="btn-pop-add" class="hidden" data-toggle="modal" data-target="#pop-add"></button>

    <!-- Modal -->
    <div class="modal fade" id="pop-add" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">@lang('labels.add') @lang('labels.attribute')</h4>
                </div>
                <div class="modal-body">
                    <div>
                        {!! Form::open(['url' => 'cf-back/member-attribute']) !!}
                        <div class="form-group">
                            {!! Form::label('attribute', trans('labels.attribute').' '.trans('labels.attribute'), ['class' => 'control-label']) !!}
                            {!! Form::text('attribute', null, ['class' => 'form-control', 'placeholder' => trans('labels.insert').' '.trans('labels.attribute')]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit(trans('labels.add'), ['class' => 'btn btn-primary form-control']) !!}
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--MODAL TAMBAH--}}

    {{--MODAL EDIT--}}
    @if(count($attributes) > 0)
        <button type="button" id="btn-pop-edit" class="hidden" data-toggle="modal" data-target="#pop-edit"></button>
        <div class="modal fade" id="pop-edit" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">@lang('labels.edit') @lang('labels.attribute')</h4>
                    </div>
                    <div class="modal-body">
                        <div id="form-update">
                            {!! Form::model($x, ['method' => 'PATCH', 'action' => ['MemberController@update_attribute', $x->id]]) !!}
                            <div class="form-group">
                                {!! Form::label('attribute', trans('labels.attribute'), ['class' => 'control-label']) !!}
                                {!! Form::text('attribute', null, ['id'=>'text-update','class' => 'form-control', 'placeholder' => trans('labels.imput'). ' ' .trans('labels.attribute')]) !!}
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
    @endif
    {{--MODAL EDIT--}}
@endsection

@section('other-script')
    <script src={{asset("js/jquery.dataTables.min.js")}}></script>
    <script src={{asset("js/dataTables.bootstrap.min.js")}}></script>
    <script>
        $('#btn-add').click(function () {
            $('#btn-pop-add').click();
        });

        $('#data-table').dataTable({
            bAutoWidth: false,
            aoColumns: [
                {sWidth: '10%'},
                {sWidth: '60%'},
                {sWidth: '15%'},
                {sWidth: '15%'}
            ]
        });


        $('.btn-edit').click(function () {
            var x = $('#form-update > form').attr('action');
            var arr = x.split('/');
            arr.pop();
            arr.push($(this).parent().parent().attr('id'));
            var action = arr.join('/');
            $('#form-update > form').attr('action', action);
            $('#text-update').val($(this).parent().parent().attr('data-value'));
            $('#btn-pop-edit').click();
        });
    </script>
@endsection

