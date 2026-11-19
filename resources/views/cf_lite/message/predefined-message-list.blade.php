@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    <style>
        .list-group-item {
            margin-bottom: 10px;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/settings')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.predefined_message')</h2>
                </center>
            </div>
        </div>

        <div class="row">
            <div class="col-md-10 col-md-offset-1 pm-list-add">
                <a class="btn btn-default pull-right" href="javascript:;" data-toggle="modal" data-target="#addPredefinedMessageModal"
                   title="@lang('labels.add_predefined_message')"><span class="fa fa-plus"></span> @lang('labels.add_predefined_message')</a>
            </div>
            <div class="col-md-12">
                <div class="col-md-6">
                    <ul class="list-group">
                        @if(!empty($messages))
                            @php($color = 1)
                            @foreach($messages as $message)
                                @php
                                    if ($color == 1):
                                        $list = 'list-group-item-success';
                                    elseif ($color == 2):
                                        $list = 'list-group-item-info';
                                    elseif ($color == 3):
                                        $list = 'list-group-item-warning';
                                    elseif ($color == 4):
                                        $list = 'list-group-item-danger';
                                    endif;
                                @endphp
                                <li class="list-group-item {{$list}}">
                                    <div class="row">
                                        <div class="col-md-8">{{$message->message}}</div>
                                        <div class="col-md-1 col-md-offset-2">
                                            <a href="javascript:;" data-toggle="modal" class="btn-link pull-right edit-predefined-message" id="epm-{{$message->id}}" data-target="#editPredefinedMessageModal"><span class="pull-right fa fa-edit" style="font-size: 18px; margin-top: 4px;"></span></a>
                                        </div>
                                        <div class="col-md-1">
                                            {!! Form::open(['method' => 'DELETE', 'action' => ['PredefinedMessageController@delete_predefined_message', $message->id]]) !!}
                                            {!! Form::button('<span class="pull-right fa fa-trash" role="button" style="font-size: 18px;"></span>', ['class' => 'btn-link pull-right', 'type' => 'submit', 'style' => 'float: left']) !!}
                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                </li>
                                @php($color++)
                                @php
                                    if ($color == 5):
                                        $color = 1;
                                    endif
                                @endphp
                            @endforeach
                        @endif
                    </ul>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="paging hiden rom">
                    {{$messages->links()}}
                </div>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="addPredefinedMessageModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.add_new_predefined_message')</h4>
                </div>
                {!! Form::open(['method' => 'POST', 'action' => 'PredefinedMessageController@store_predefined_message']) !!}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('predefined_message', trans('labels.predefined_message').': ', ['class' => 'control-label']) !!}
                                {!! Form::textarea('predefined_message', null, ['class' => 'form-control', 'rows' => '3']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('labels.save')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="modal fade" id="editPredefinedMessageModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.add_new_predefined_message')</h4>
                </div>
                {!! Form::open(['method' => 'PATCH', 'action' => 'PredefinedMessageController@update_predefined_message']) !!}
                {!! Form::hidden('m_attribute', null) !!}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                {!! Form::label('predefined_message_edit', trans('labels.predefined_message').': ', ['class' => 'control-label']) !!}
                                {!! Form::textarea('predefined_message_edit', null, ['class' => 'form-control', 'rows' => '3']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('labels.save')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script>
        $(document).ready(function () {
            var url = "{{url('cf-lite')}}/";

            $('.edit-predefined-message').on('click', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    message_id: $(this).attr('id').split('-').pop()
                };

                var type = 'POST';
                var my_url = url + 'get-predefined-message-detail';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        $('input[name="m_attribute"]').val(data.message['id']);
                        $('textarea[name="predefined_message_edit"]').val(data.message['message']);
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });
        });
    </script>
@endsection
