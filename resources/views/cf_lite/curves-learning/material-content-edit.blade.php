@extends('cf_lite.layouts.staff-dashboard-template')

@section('title', 'Learning Material Content Detail')

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
            <a href="{{url('cf-lite/curves-learning/material-content/'.$content->clmaterialcontent_clmaterialheader->id)}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            </left>
        <center>
            <h2 class="page-title">@lang('labels.list') @lang('labels.material_learning') @lang('labels.content') @lang('labels.detail')</h2>
        </center>
    </div>
    <div class="col-xs-12">
        {!! Form::open(['method'=>'PATCH','action'=>['CurvesLearningController@edit_material_content_detail',$content->id]]) !!}
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('material_content_title', trans('labels.title').': ', ['class' => 'control-label col-md-3']) !!}
                <div class="col-md-6">
                    {!! Form::text('material_content_title', $content->title, ['class' => 'form-control']) !!}
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('material_content_content', trans('labels.content').': ', ['class' => 'control-label col-md-3']) !!}
                <div class="col-md-6">
                    {!! Form::textarea('material_content_content', $content->content, ['class' => 'form-control', 'rows'=>8]) !!}
                </div>
            </div>
        </div>
        <div>
            <button type="submit" class="btn btn-primary">@lang('labels.save')</button>
        </div>
        {!! Form::close() !!}
    </div>
    <div class="col-xs-12"><hr></div>
    <div class="col-xs-12">
        {!! Form::open(['method'=>'PATCH','action'=>['CurvesLearningController@edit_material_content_question',$content->id]]) !!}
        <div class="row">
            <button type="button" class="pull-right btn btn-info btn-anim question-add-row-btn mr-15" title="@lang('labels.add_purchase')">
                <i class="icon-rocket"></i>
                <span class="btn-text fa fa-plus-circle"></span>
            </button>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th>@lang('labels.number')</th>
                <th>@lang('labels.question')</th>
                <th><span class="fa fa-trash-o"></span></th>
            </tr>
            </thead>
            <tbody id="question-body">

                @php($count=0)
                @if(count($questions)>0)
                    @foreach($questions as $question)
                        <tr class="cs-pp-item" id="1">
                        @php($count++)
                        <td>
                            <div>{{$count}}</div>
                        </td>
                        <td>
                            {!! Form::text('question['.$count.']', $question->question, ['class' => 'form-control quest', 'id'=>'q-'.$count]) !!}
                            {!! Form::hidden('id['.$count.']', $question->id) !!}
                            {!! Form::hidden('status['.$count.']',0,['id'=>'s-'.$count]) !!}
                            {{--0-> nothing 1->edit--}}
                        </td>
                        <td>
                            @if($count>1)
                                <a class="btn btn-danger btn-anim question-delete-btn" id="d-{{$question->id}}"><i class="icon-rocket"></i><span class="btn-text fa fa-trash"></span></a>
                            @endif
                        </td>
                        </tr>
                    @endforeach
                @else
                    <tr class="cs-pp-item" id="1">
                    @php($count++)
                    <td>
                        <div>{{$count}}</div>
                    </td>
                    <td>
                        {!! Form::text('question[1]', null, ['class' => 'form-control quest', 'id'=>'q-1']) !!}
                        {!! Form::hidden('id[1]', 0) !!}
                        {{--id=0->add--}}
                        {!! Form::hidden('status[1]',0,['id'=>'s-1']) !!}
                        {{--0-> nothing 1->edit--}}
                    </td>
                    <td></td>
                    </tr>
                @endif
                {!! Form::hidden('counter', $count) !!}

            </tbody>
        </table>
        <div>
            <button type="submit" class="btn btn-primary">@lang('labels.save')</button>
        </div>
        {!! Form::close() !!}
    </div>
    <div class="col-xs-12"><hr></div>
    <div class="col-xs-12">
        {!! Form::open(['method'=>'PATCH','files'=>'true','action'=>['CurvesLearningController@upload_material_content_file',$content->id]]) !!}
        <div class="row">
            <button type="button" class="pull-right btn btn-info btn-anim file-add-row-btn mr-15" title="@lang('labels.add_purchase')">
                <i class="icon-rocket"></i>
                <span class="btn-text fa fa-plus-circle"></span>
            </button>
        </div>
        <table class="table">
            <thead>
            <tr>
                <th>@lang('labels.number')</th>
                <th>@lang('labels.file')</th>
                <th>@lang('labels.previous_file')</th>
                <th><span class="fa fa-trash-o"></span></th>
            </tr>
            </thead>
            <tbody id="file-body">
            @php($fcount=0)
            @if(count($supports)>0)
                @foreach($supports as $support)
                    <tr class="cs-pp-item" id="1">
                        @php($fcount++)
                        <td>
                            <div>{{$fcount}}</div>
                        </td>
                        <td>
                            {!! Form::file('cl_file['.$fcount.']', ['class' => 'file_multi_video up_file', 'id'=>'f-'.$fcount, 'accept'=>'image/* audio/* video/*']) !!}
                            {{--{!! Form::hidden('init_file['.$fcount.']',1,['id'=>'initf-'.$fcount]) !!}--}}
                            {{--<input type="file" name="cl_file[{{$fcount}}]" class="file_multi_video up_file" id="f-{{$fcount}}" accept="image/* audio/* video/*" value="{{public_path('curves_learning/'.$support->file_name)}}">--}}
                            {!! Form::hidden('file_id['.$fcount.']', $support->id) !!}
                            {!! Form::hidden('file_status['.$fcount.']',0,['id'=>'fs-'.$fcount]) !!}
                            {{--0-> nothing 1->edit--}}
                            {{--<img role="button" id="image_preview" class="img-circle center-block" src="{{asset('images/curves_learning/'.$support->file_name)}}" style="width: 150px; height: 150px; object-fit: cover;">--}}
                        </td>
                        <td>{{$support->file_name}}</td>
                        <td>
                            @if($fcount>1)
                                <a class="btn btn-danger btn-anim file-delete-btn" id="df-{{$support->id}}"><i class="icon-rocket"></i><span class="btn-text fa fa-trash"></span></a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            @else
                <tr class="cs-pp-item" id="1">
                    @php($fcount++)
                    <td>
                        <div>{{$fcount}}</div>
                    </td>
                    <td>
                        {!! Form::file('cl_file[1]', ['class' => 'file_multi_video up_file', 'accept'=>'image/* audio/* video/*']) !!}
                        {{--<input type="file" name="cl_file[1]" class="file_multi_video up_file" accept=".jpeg">--}}
                        {!! Form::hidden('file_id[1]', 0) !!}
                        {{--id=0->add--}}
                        {!! Form::hidden('file_status[1]',0,['id'=>'fs-1']) !!}
                        {{--0-> nothing 1->edit--}}
                        {{--<img role="button" id="image_preview" class="img-circle center-block" src="{{asset('images/img.png')}}" style="width: 150px; height: 150px; object-fit: cover;">--}}
                    </td>
                    <td>-</td>
                    <td></td>
                </tr>
            @endif
            {!! Form::hidden('fcounter', $fcount) !!}
            </tbody>
        </table>
        <div>
            <button type="submit" class="btn btn-primary">@lang('labels.save')</button>
        </div>
        {!! Form::close() !!}
    </div>
    <div class="col-xs-12">
        {!! Form::open(['method'=>'DELETE', 'action'=>['CurvesLearningController@delete_material_content',$content->id]]) !!}
        <a role="button" class="btn btn-danger btn-anim cs-master-add-btn mr-15 btn-delete-content pull-right"
           title="@lang('labels.delete')"><span>@lang('labels.delete') @lang('labels.this_content')</span>
        </a>
        {{--<button type="submit" class="btn btn-danger pull-right">@lang('labels.delete') @lang('labels.this_content')</button>--}}
        {!! Form::close() !!}
    </div>

    {!! Form::open(['method'=>'DELETE','action'=>['CurvesLearningController@delete_material_content_question',$content->id],'id'=>'deleteform']) !!}
    {!! Form::hidden('deleteid',null,['id'=>'deleteid']) !!}
    {!! Form::close() !!}

    {!! Form::open(['method'=>'DELETE','action'=>['CurvesLearningController@delete_material_content_file',$content->id],'id'=>'deletefileform']) !!}
    {!! Form::hidden('deletefid',null,['id'=>'deletefid']) !!}
    {!! Form::close() !!}

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

    {{--<script>--}}
        {{--$(document).on('change', '.file_multi_video', function (evt) {--}}
            {{--var $source = $('#image_preview');--}}
            {{--$source[0].src = URL.createObjectURL(this.files[0]);--}}
            {{--$source.load();--}}
            {{--console.log('test images');--}}
        {{--});--}}
    {{--</script>--}}

    <script>
        $(document).ready(function () {
            $('.question-add-row-btn').on('click', function () {
                var counter = parseInt($('input[name="counter"]').val());
                counter++;
                var html = '<tr class="cs-pp-item" id="' + counter + '">';
                html += '<td>';
                html += counter.toString();
                html += '</td>';
                html += '<td>';
                html += '<input type="text" name="question['+ counter +']" id="q-'+counter+'" class="form-control quest">';
                html += '<input type="hidden" name="id['+ counter +']" class="form-control" value="0">';
                // id=0->add
                html += '<input type="hidden" name="status['+ counter +']" class="form-control" value="0" id="s-'+counter+'">';
                // 0->nothing 1->edit
                html += '</td>';
                html += '<td>';
                html += '<a class="btn btn-danger btn-anim question-delete-row-btn" id="row-del-' + counter + '"><i class="icon-rocket"></i><span class="btn-text fa fa-trash"></span></a>';
                html += '</td>';
                html += '</tr>';

                $('#question-body').append(html);
                $('input[name="counter"]').val(counter);
                $(".select2").select2();
            });

            $('.file-add-row-btn').on('click', function () {
                var counter = parseInt($('input[name="fcounter"]').val());
                counter++;
                var html = '<tr class="cs-pp-item" id="fr-' + counter + '">';
                html += '<td>';
                html += counter.toString();
                html += '</td>';
                html += '<td>';
                html += '<input type="file" name="cl_file['+ counter +']" id="f-'+counter+'" class="up_file">';
                html += '<input type="hidden" name="file_id['+ counter +']" class="form-control" value="0">';
                // id=0->add
                html += '<input type="hidden" name="file_status['+ counter +']" class="form-control" value="0" id="fs-'+counter+'">';
                // 0->nothing 1->edit
                html += '</td>';
                html += '<td>-</td>';
                html += '<td>';
                html += '<a class="btn btn-danger btn-anim file-delete-row-btn" id="row-del-' + counter + '"><i class="icon-rocket"></i><span class="btn-text fa fa-trash"></span></a>';
                html += '</td>';
                html += '</tr>';

                $('#file-body').append(html);
                $('input[name="fcounter"]').val(counter);
                $(".select2").select2();
            });

            $('#question-body').on('click', '.question-delete-row-btn', function () {
                var counter = parseInt($('input[name="counter"]').val());
                $('#' + $(this).attr('id').split('-').pop()).remove();
                $('input[name="counter"]').val(counter);
            });

            $('#file-body').on('click', '.file-delete-row-btn', function () {
                var counter = parseInt($('input[name="fcounter"]').val());
                $('#fr-' + $(this).attr('id').split('-').pop()).remove();
                $('input[name="fcounter"]').val(counter);
            });

            $('#question-body').on('change','.quest',function () {
                var counter = $(this).attr('id').split('-').pop();
                document.getElementById('s-'+counter).valueOf().value = 1;
            });

            $('#question-body').on('click', '.question-delete-btn', function () {
                var questionid = $(this).attr('id').split('-').pop();
                document.getElementById('deleteid').valueOf().value = questionid;
                $('#deleteform').submit();
            });


            $('#file-body').on('click', '.file-delete-btn', function () {
                var fileid = $(this).attr('id').split('-').pop();
                document.getElementById('deletefid').valueOf().value = fileid;
                $('#deletefileform').submit();
            });

        });
    </script>
    <script src={{asset("bootbox/bootbox.min.js")}}></script>
    <script>
        $(document).ready(function () {
            $(document).on('click', '.btn-delete-content', function () {
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
                            theform.submit();
                        }
                    }
                });
            });
        });
    </script>
@endsection