@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <style>
        .btn-primary{
            background-color: #ff1493;
            color: #ffffff;
            border-color: #ff1493;
        }
        .btn-primary:hover  {
            color: #ff1493;
            background-color: transparent;
            border-color: #ff1493;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/staff/'.$staff->id.'/detail')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.manage_staff')<br><small>(@lang('labels.note'))</small></h2>
            </center>
        </div>
    </div>

    <div class="row">
        <div class="col-md-10 col-md-offset-1 pm-list-add">
            <a class="btn btn-default pull-right" href="javascript:;" data-toggle="modal" data-target="#addNoteModal"
               title="@lang('labels.add_notes')"><span class="fa fa-plus"></span> @lang('labels.add_notes')</a>
        </div>
        <div class="col-md-8">
            <h3>{{$staff->name}}</h3>
            <ul class="list-group">
                @if(!empty($notes))
                    @php($color = 1)
                    @foreach($notes as $note)
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
                                <div class="col-md-2">
                                    {{\Carbon\Carbon::parse($note->created_at)->format('d-m-Y')}}
                                </div>
                                <div class="col-md-8">
                                    {{$note->note}}
                                </div>
                                <div class="col-md-1 col-md-offset-1">
                                    {!! Form::open(['method' => 'DELETE', 'action' => ['StaffController@delete_staff_note', $note->id]]) !!}
                                    {!! Form::button('<span class="pull-right fa fa-trash" role="button" style="font-size: 18px;"></span>', ['class' => 'btn-link pull-right btn-delete', 'type' => 'button', 'style' => 'float: left']) !!}
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

    <!-- Modal -->
    <div class="modal fade" id="addNoteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.add_notes')</h4>
                </div>
                {!! Form::open(['method' => 'POST', 'action' => ['StaffController@store_staff_note', $staff->id]]) !!}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="form-group">
                                {!! Form::label('note', trans('labels.note').': ', ['class' => 'control-label']) !!}
                                {!! Form::textarea('note', null, ['class' => 'form-control', 'rows' => '4']) !!}
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
@endsection