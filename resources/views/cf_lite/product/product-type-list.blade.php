@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <style>

        table th{
            color: #ff1493;
        }

        div.dataTables_wrapper div.dataTables_length select {
            margin: 0 10px;
        }

        .daftar-tipe-produk a,
        .pagination > .active > a,
        .pagination > .active > a:focus,
        .pagination > .active > a:hover, .pagination > .active > span,
        .pagination > .active > span:focus,
        .pagination > .active > span:hover {
            background-color: #ff1493;
            border-color: #ff1493;
        }

        .daftar-tipe-produk a:hover{
            color: #ff1493;
            background-color: transparent;
            border-color: #ff1493;
        }

        .btn-back-to{
            margin-top: 20px;
            margin-left: 20px;
        }

    </style>

@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div style="padding: 15px;">
        <div class="col-sm-12">
            <ol class="breadcrumb pull-right" style="background-color: transparent;">
                <li><a href="{{url('cf-lite/settings')}}" style="color: deeppink;">@lang('labels.setting')</a></li>
                <li><a href="{{url('cf-lite/product-setting')}}" style="color: deeppink;">@lang('labels.product_setting')</a></li>
                <li class="active" style="color: rebeccapurple;"><span>@lang('labels.product_type_list')</span></li>
            </ol>
        </div>
        <div class="my-section">
            <div class="section-title">
                <center>
                    <h1 class="page-title">@lang('labels.list') @lang('labels.product_type')</h1>
                </center>
            </div>
            @include('cf_back.flash-message')

            <div class="row">
                <a href="{{url('cf-lite/product-setting')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12 daftar-tipe-produk">
                    <a id="btn-add-type" class="btn btn-primary pull-right"><i class="fa fa-plus"></i></a>
                </div>
                <div class="col-md-12 col-sm-12 col-xs-12">
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
                                            @lang('labels.type')
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
                                    @foreach($types as $type)
                                        <tr id="{{$type->id}}" data-value="{{$type->type}}">
                                            <td>{{$type->id}}</td>
                                            <td>{{$type->type}}</td>
                                            <td>
                                                <a class="btn btn-warning btn-edit-type"><i
                                                            class="fa fa-pencil"></i> @lang('labels.edit')
                                                </a>
                                            </td>
                                            <td>
                                                {{--<a class="btn btn-danger btn-hapus"><i class="fa fa-trash"></i> @lang('labels.delete')</a>--}}

                                                <div>
                                                    {!! Form::open(['method' => 'DELETE', 'action' => ['ProductController@type_destroy', $type->id]]) !!}
                                                    {!! Form::submit(trans('labels.delete'), ['class'=>'btn btn-danger']) !!}
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
    <button type="button" id="btn-pop-add" class="hidden" data-toggle="modal" data-target="#add-type"></button>

    <!-- Modal -->
    <div class="modal fade" id="add-type" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">@lang('labels.add') @lang('labels.product_type')</h4>
                </div>
                <div class="modal-body">
                    <div>
                        {!! Form::open(['method' => 'POST', 'action' => 'ProductController@type_store']) !!}
                        <div class="form-group">
                            {!! Form::label('type', trans('labels.name').' '.trans('labels.type'), ['class' => 'control-label']) !!}
                            {!! Form::text('type', null, ['class' => 'form-control', 'placeholder' => trans('labels.insert').' '.trans('labels.product_type')]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit(trans('labels.add').' '.trans('labels.type'), ['class' => 'btn btn-primary form-control']) !!}
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--MODAL TAMBAH--}}

    {{--MODAL EDIT--}}
    <button type="button" id="btn-pop-edit" class="hidden" data-toggle="modal" data-target="#edit-type"></button>
    <div class="modal fade" id="edit-type" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">@lang('labels.edit') @lang('labels.product_type')</h4>
                </div>
                <div class="modal-body">
                    <div id="form-update-type">
                        {!! Form::model($type, ['method' => 'PATCH', 'action' => ['ProductController@type_update', $type->id]]) !!}
                        <div class="form-group">
                            {!! Form::label('type', trans('labels.name').' '.trans('labels.type'), ['class' => 'control-label']) !!}
                            {!! Form::text('type', null, ['id'=>'text-update-type','class' => 'form-control', 'placeholder' => trans('labels.type_name_placeholder')]) !!}
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
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $('#data-table').dataTable();

        $('#btn-add-type').click(function () {
            $('#btn-pop-add').click();
        });
        $('.btn-edit-type').click(function () {
            var x = $('#form-update-type > form').attr('action');
            var arr = x.split('/');
            arr.pop();
            arr.push($(this).parent().parent().attr('id'));
            var action = arr.join('/');
            $('#form-update-type > form').attr('action', action);
            $('#text-update-type').val($(this).parent().parent().attr('data-value'));
            $('#btn-pop-edit').click();
        });
    </script>
@endsection
