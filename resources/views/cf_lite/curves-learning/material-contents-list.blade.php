@extends('cf_lite.layouts.staff-dashboard-template')

@section('title', 'Learning Material Content')

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
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="col-xs-12">
        <left>
            <a href="{{url('cf-lite/curves-learning/material-header')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <a data-toggle="modal" data-target="#addModal" class="btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: darkred;margin-left: 30px;margin-top: 30px;"><span class="glyphicon glyphicon-plus" style="margin-top: 7px; color:darkred;"></span></a>
            {{--<a href="{{url('cf-lite/laporan/wo-count-avg')}}" class="btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: rebeccapurple; margin-right: 30px; margin-top: 30px; " title="@lang('labels.member_average_wo_report')"><span class="glyphicon glyphicon-th-list" style="margin-top: 10px; color: rebeccapurple;"></span></a>--}}
            {{--<a href="{{url('cf-lite/laporan/wo-flag')}}" class="btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: rebeccapurple; margin-right: 30px; margin-top: 30px; " title="@lang('labels.user_flag_report')"><span class="glyphicon glyphicon-flag" style="margin-top: 10px; color: rebeccapurple;"></span></a>--}}
            {{--<a href="{{url('cf-lite/laporan/wo-time')}}" class="btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: rebeccapurple; margin-right: 30px; margin-top: 30px; " title="@lang('labels.wo_time_report')"><span class="glyphicon glyphicon-time" style="margin-top: 10px; color: rebeccapurple;"></span></a>--}}
            {{--<a href="{{url('cf-lite/laporan/wo-member-report')}}" class="btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: rebeccapurple; margin-right: 30px; margin-top: 30px; " title="@lang('labels.wo_member_report')"><span class="fa fa-calendar" style="margin-top: 10px; color: rebeccapurple;"></span></a>--}}
        </left>
        <center>
            <h2 class="page-title">@lang('labels.list') @lang('labels.material_learning') @lang('labels.content')</h2>
        </center>
    </div>
    {{--<div class="col-xs-12" style="padding-bottom: 10px">--}}
        {{--<a data-toggle="modal" data-target="#addModal" class="pull-right btn btn-info btn-anim question-add-row-btn mr-15"><span class="btn-text fa fa-plus-circle"></span></a>--}}
    {{--</div>--}}
    <div class="col-xs-12">
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-striped table-hover" style="background: white">
                <thead>
                <tr>
                    <th>@lang('labels.indexing')</th>
                    <th>@lang('labels.title')</th>
                    <th>@lang('labels.sequences')</th>
                    <th>@lang('labels.content')</th>
                    <th>@lang('labels.status')</th>
                    <th>@lang('labels.total_question')</th>
                </tr>
                </thead>
                <tbody>
                @php($count = 1)
                @foreach($contents as $content)
                    <tr>
                        <td>{{$count}}</td>
                        <td><a href="{{url('cf-lite/curves-learning/material-content-detail/'.$content->id)}}" role="button" id="material-{{$content->id}}" class="material-name">{{$content->title}}</a></td>
                        <td>{{$content->sequence}}</td>
                        <td>{{$content->content}}</td>
                        <td>{{$content->status}}</td>
                        <td>{{$content->total_question}}</td>
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
            {!! Form::open(['method' => 'POST', 'action' => ['CurvesLearningController@add_material_content',$header_id]]) !!}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.add') @lang('labels.material_learning') @lang('labels.content')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('material_content_title', trans('labels.title').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-6">
                                {!! Form::text('material_content_title', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('material_content_sequence', trans('labels.sequence').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-6">
                                {!! Form::text('material_content_sequence', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('material_content_content', trans('labels.content').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-6">
                                {!! Form::textarea('material_content_content', null, ['class' => 'form-control']) !!}
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
                        $('input[name="material_header_category_id_edit"]').val(data.materialheader['category_id']);
                        $('input[name="material_header_sequence_edit"]').val(data.materialheader['sequences']);

                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });


        });
    </script>
@endsection