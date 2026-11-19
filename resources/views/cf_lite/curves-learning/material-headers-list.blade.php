@extends('cf_lite.layouts.staff-dashboard-template')

@section('title', 'Learning Material')

@section('other-style')

    {{--DATA TABLE--}}
    <meta name="_new_token" content="{!! csrf_token() !!}"/>
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">

    <link href="{{asset('css/appointment.css')}}" rel="stylesheet">
    <link href="{{asset('fullcalendar/fullcalendar.min.css')}}" rel="stylesheet">
    <link href="{{asset('fullcalendar/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <style>
        .dt-buttons {
            float: left;
        }

        #dataTable_filter {
            float: right;
        }

        .dataTables_length{
            margin-left: -460px;
        }


        table th{
            color: #ff1493;
        }

        table, th{
            text-align: center;
        }

        .select2 {
            width: 100% !important;
        }
    </style>

@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="col-xs-12">
        <left>
            <a href="{{url('cf-lite/dashboard')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <a data-toggle="modal" data-target="#addModal" class="btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: darkred;margin-left: 30px;margin-top: 30px;"><span class="glyphicon glyphicon-plus" style="margin-top: 7px; color: darkred;"></span></a>
            {{--<a href="{{url('cf-lite/laporan/wo-count-avg')}}" class="btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: rebeccapurple; margin-right: 30px; margin-top: 30px; " title="@lang('labels.member_average_wo_report')"><span class="glyphicon glyphicon-th-list" style="margin-top: 10px; color: rebeccapurple;"></span></a>--}}
            {{--<a href="{{url('cf-lite/laporan/wo-flag')}}" class="btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: rebeccapurple; margin-right: 30px; margin-top: 30px; " title="@lang('labels.user_flag_report')"><span class="glyphicon glyphicon-flag" style="margin-top: 10px; color: rebeccapurple;"></span></a>--}}
            {{--<a href="{{url('cf-lite/laporan/wo-time')}}" class="btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: rebeccapurple; margin-right: 30px; margin-top: 30px; " title="@lang('labels.wo_time_report')"><span class="glyphicon glyphicon-time" style="margin-top: 10px; color: rebeccapurple;"></span></a>--}}
            {{--<a href="{{url('cf-lite/laporan/wo-member-report')}}" class="btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: rebeccapurple; margin-right: 30px; margin-top: 30px; " title="@lang('labels.wo_member_report')"><span class="fa fa-calendar" style="margin-top: 10px; color: rebeccapurple;"></span></a>--}}
        </left>
        <center>
            <h2 class="page-title">@lang('labels.list') @lang('labels.material_learning')</h2>
        </center>
    </div>
    <div class="col-xs-12">
        {{--<div class="row">--}}
            {{--<div class="col-xs-12">--}}
                {{--{!! Form::open(['method' => 'GET', 'action' => ['ReportController@list_wo_filter']]) !!}--}}
                {{--<div class="col-xs-2">--}}
                    {{--Filter :--}}
                {{--</div>--}}
                {{--<div class="col-xs-3">--}}
                    {{--{!! Form::date('from_date', \Carbon\Carbon::now() , ['class' => 'form-control filter-date','placeholder'=>@trans('labels.from_date'), 'id'=>'filt-dari']) !!} <br/>--}}
                {{--</div>--}}
                {{--<div class="col-xs-3">--}}
                    {{--{!! Form::date('thru_date', \Carbon\Carbon::now() , ['class' => 'form-control filter-date','placeholder'=>@trans('labels.from_date'), 'id'=>'filt-sampai']) !!} <br/>--}}
                {{--</div>--}}
                {{--<div class="col-xs-2">--}}
                    {{--<button type="submit" class="btn btn-primary" id="save-btn"><i class="fa fa-search"></i> @lang('labels.search')</button>--}}
                    {{--</a>--}}
                {{--</div>--}}
                {{--{!! Form::close() !!}--}}
            {{--</div>--}}

        {{--</div>--}}

        {{--<div class="input-group my-hiddenz" style="margin-bottom: 20px;">--}}
        {{--<input type="text" class="tanggal form-control">--}}
        {{--<span class="input-group-addon btn btn-default"><i class="fa fa-calendar"></i></span>--}}
        {{--</div>--}}
        {{--<div class="row col-xs-12" style="padding-bottom: 10px">--}}
            {{--<a data-toggle="modal" data-target="#addModal" class="pull-right btn btn-info btn-anim question-add-row-btn mr-15" ><span class="btn-text fa fa-plus-circle"></span></a>--}}
        {{--</div>--}}
        <div class="row table-responsive col-xs-12">
            <table id="dataTable" class="table table-bordered table-striped table-hover" style="background: white">
                <thead>
                <tr>
                    <th>@lang('labels.indexing')</th>
                    <th>@lang('labels.name')</th>
                    <th>@lang('labels.category')</th>
                    <th>@lang('labels.sequences')</th>
                    <th></th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @php($count = 1)
                @foreach($materials as $material)
                    <tr>
                        <td>{{$count}}</td>
                        <td><a role="button" id="material-{{$material->id}}" data-toggle="modal" data-target="#editModal" class="material-name">{{$material->name}}</a></td>
                        <td>{{$material->clmaterialheader_clcategory->name}}</td>
                        <td>{{$material->sequences}}</td>
                        <td>
                            {!! Form::open(['method'=>'GET','action'=>['CurvesLearningController@view_material_content',$material->id]]) !!}
                            <div class="col-xs-2">
                                <button type="submit" class="btn btn-primary" id="save-btn"><i class="fa fa-search"></i> @lang('labels.detail') @lang('labels.content')</button>
                            </div>
                            {!! Form::close() !!}
                        </td>
                        <td>
                            {!! Form::open(['method'=>'DELETE','action'=>['CurvesLearningController@delete_material_header',$material->id], 'id'=>'formdeletes-'.$material->id]) !!}
                            <div class="col-xs-2">
                                <a role="button" class="btn btn-danger btn-delete-submit btn-delete" id="btndelete-{{$material->id}}"><i class="fa fa-trash"></i> @lang('labels.delete')</a>
                                {{--<button type="submit" class="btn btn-danger btn-delete-submit btn-delete"><i class="fa fa-trash"></i> @lang('labels.delete')</button>--}}
                            </div>
                            {!! Form::close() !!}
                        </td>
                        @php($count++)
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            {!! Form::open(['method' => 'POST', 'action' => 'CurvesLearningController@add_material_header']) !!}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.add') @lang('labels.material_learning')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('material_header_name', trans('labels.name').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-6">
                                {!! Form::text('material_header_name', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('material_header_category', trans('labels.category').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-6">
                                {!! Form::select('material_header_category',$categories, null, ['class' => 'form-control select2', 'placeholder' => trans('labels.choose_category')]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('material_header_sequence', trans('labels.material_sequence').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-6">
                                {!! Form::text('material_header_sequence', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('labels.save')</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

    {{--editmodal--}}
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            {!! Form::open(['method' => 'PATCH', 'action'=>'CurvesLearningController@edit_material_header']) !!}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.edit') @lang('labels.material_learning')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::hidden('material_header_id_edit',null) !!}
                            {!! Form::label('material_header_name', trans('labels.name').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-6">
                                {!! Form::text('material_header_name_edit', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('material_header_category', trans('labels.category').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-6">
                                {!! Form::select('material_header_category_edit',$categories, null, ['class' => 'form-control select2']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('material_header_sequence', trans('labels.material_sequence').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-6">
                                {!! Form::text('material_header_sequence_edit', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('labels.save')</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>

@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('js/dataTables.buttons.min.js')}}"></script>

    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('fullcalendar/fullcalendar.min.js')}}"></script>
    <script src="{{asset('bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>

    <script>
        $('#dataTable').DataTable({
            info: false,
            dom: 'Bfrtip',
            buttons: [

            ]
        });
    </script>

    <script>
        $(document).ready(function () {
            "use strict";
            $('#dataTable').DataTable();
            $(".select2").select2();
        });
    </script>

    <script>
        $(document).ready(function () {
            var url = "{{url('cf-lite')}}/";

            $('#dataTable').on('click', '.material-name', function () {
                console.log('text');
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_new_token"]').attr('content')
                    }
                });

                var formData = {
                    materialheader_id: $(this).attr('id').split('-').pop()
                };

                var type = 'POST';
                var my_url = url + 'curves-learning/material-header/ajax';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        $('input[name="material_header_id_edit"]').val(data.materialheader['id']);
                        $('input[name="material_header_name_edit"]').val(data.materialheader['name']);
                        $('select[name="material_header_category_id_edit"]').val(data.materialheader['category_id']);
                        // $('select[name="material_header_category_id_edit"]').text(data.category);
                        $('#select2-material_header_category_id_edit-container').text(data.category);
                        $('input[name="material_header_sequence_edit"]').val(data.materialheader['sequences']);

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
            $('#dataTable').on('click', '.btn-delete', function () {
                var theform = $(this).parents('form');
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
                            // $('#delete_header').submit();
                            theform.submit();
                        }
                    }
                });
            });
        });
    </script>

@endsection