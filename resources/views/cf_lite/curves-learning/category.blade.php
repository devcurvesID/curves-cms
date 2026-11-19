@extends('cf_lite.layouts.staff-dashboard-template')

@section('title', 'Curves Learning')

@section('other-style')

    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">

    <link href="{{asset('css/appointment.css')}}" rel="stylesheet">
    <link href="{{asset('fullcalendar/fullcalendar.min.css')}}" rel="stylesheet">
    <link href="{{asset('fullcalendar/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">

    <style>
        .dt-buttons {
            float: left;
        }

        #dataTable_filter {
            float: right;
        }

        .dataTables_length {
            margin-left: -460px;
        }

        .btn-primary{
            color: #ffffff;
            border-color: #ff1493;
            background-color:transparent;
            border-radius: 50px;
            width: 50px; height: 50px;
            border-width: 3px;
            border-color: deeppink;
            margin-right: 30px;
            margin-top: 30px;
        }

        .btn-primary:hover {
            color: #333;
            background-color: #ffd4f9;
            border-color: deeppink;
            background-color: pink;
        }

        .btn-primary:focus{
            background-color: pink;
            border-color: deeppink;
        }

        table th {
            color: #ff1493;
        }

        table, th {
            text-align: center;
        }

    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="col-xs-12">
        <left>
            <a href="{{url('cf-lite/curves-learning/choose-menus')}}" class="btn btn-default btn-back-to" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <button type="button" data-toggle="modal" data-target="#addModal" class="btn btn-primary pull-right"><span class="glyphicon glyphicon-plus" style="color: deeppink;"></span></button>
        </left>
        <center>
            <h2 class="page-title">@lang('labels.list') @lang('labels.category')</h2>
        </center>
    </div>
    <div class="col-xs-12">
        {{--<div class="row">--}}
            {{--<button type="button" class="pull-right btn btn-info btn-anim cl-category-add-btn" data-toggle="modal"--}}
                    {{--data-target="#addModal" title="@lang('labels.add_category')" style="margin-right: 15px">--}}
                {{--<span class="btn-text fa fa-plus-circle"></span>--}}
            {{--</button>--}}
        {{--</div>--}}
        <hr/>
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-striped table-hover" style="background: white">
                <thead>
                <tr>
                    <th>@lang('labels.indexing')</th>
                    <th>@lang('labels.category')</th>
                    <th>@lang('labels.action')</th>
                </tr>
                </thead>
                <tbody>
                @php($indexing = 1)
                @foreach($categories as $c)
                    <tr>
                        <td>{{ $indexing }}</td>
                        <td><a role="button" class="edit-btn-text" id="{{$c->id}}" data-toggle="modal"
                               data-target="#editModal">{{$c->name}}</a></td>
                        <td>
                            <div>
                                {!! Form::open(['method' => 'DELETE','id'=>'formdeletes-'.$c->id, 'action' => ['CurvesLearningController@delete_category', $c->id]]) !!}
                                <a role="button" id="btndelete-{{$c->id}}" class="btn btn-danger btn-anim cs-master-add-btn mr-15 btn-delete"
                                   title="@lang('labels.delete')"><span>@lang('labels.delete')</span>
                                </a>
                                {{--<div class="col-xs-2">--}}
                                    {{--<button type="submit" class="btn btn-warning" id="save-btn"><i class="fa fa-trash"></i> @lang('labels.delete')</button>--}}
                                    {{--</a>--}}
                                {{--</div>--}}
                                {!! Form::close() !!}
                            </div>
                        </td>
                    </tr>
                    @php($indexing++)
                @endforeach
                </tbody>
            </table>
        </div>

        {{--Modal--}}
        <div id="addModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="addModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h5 class="modal-title" id="addModalLabel">@lang('labels.add_new_category')</h5>
                    </div>
                    {!! Form::open(['method' => 'POST','id'=>'form-add-cat', 'action' => 'CurvesLearningController@store_category']) !!}
                    <div class="modal-body">
                        <div class="form-wrap">
                            <div class="form-group">
                                {!! Form::label('category_name', trans('labels.category_name').' :', ['class' => 'control-label mb-10 text-left']) !!}
                                {!! Form::text('category_name', null, ['class' => 'form-control', 'id'=>'category_name']) !!}
                                <div id="message"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info" id="addBtn">@lang('labels.save')</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>

        <div id="editModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="editModalLabel"
             aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                        <h5 class="modal-title" id="editModalLabel">@lang('labels.edit_category')</h5>
                    </div>
                    {!! Form::open(['method' => 'PATCH','id'=>'form-edit-cat', 'action' => 'CurvesLearningController@edit_category']) !!}
                    {!! Form::hidden('category_id', null) !!}
                    <div class="modal-body">
                        <div class="form-wrap">
                            <div class="form-group">
                                {!! Form::label('update_category_name', trans('labels.category_name').' :', ['class' => 'control-label mb-10 text-left']) !!}
                                {!! Form::text('update_category_name', null, ['class' => 'form-control category_name', 'id' => 'update_name']) !!}
                                <div id="message"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-info" id="editBtn">@lang('labels.save')</button>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('other-script')

    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('js/dataTables.buttons.min.js')}}"></script>;
    <script src="{{asset('js/buttons.print.min.js')}}"></script>

    <script>

        $('#dataTable').DataTable({
        });
    </script>
    <script>
        $(document).ready(function () {
            var url = "{{url('cf-lite')}}/";

            $('#addModal').on('click', '#addBtn', function () {
                if ($('#category_name').val() != "") {
                    $('#form-add-cat').submit();
                } else if ($('#category_name').val() == "") {
                    $('#message').html('@lang("labels.field_empty")').css('color', 'red');
                }
            });

            $('#editModal').on('click', '#editBtn', function () {
                if ($('#update_name').val() != "") {
                    console.log($('#category_id'));
                    $('#form-edit-cat').submit();
                } else if ($('#update_name').val() == "") {
                    $('#message').html('@lang("labels.field_empty")').css('color', 'red');
                }
            });

            $('.edit-btn-text').on('click', function () {

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    cat_id: $(this).attr('id')
                };

                var type = 'POST';
                var my_url = url + 'curves-learning/category/ajax';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        $('input[name="category_id"]').val(data.cate_id.id);
                        $('#update_name').val(data.cate_id.name);
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });
        });
    </script>
    <script src={{asset("bootbox/bootbox.min.js")}}></script>
    <script>
        $(document).ready(function () {
            $(document).on('click', '.btn-delete', function () {
                var theform = $(this).parent('form');
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