@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/appointment.css')}}" rel="stylesheet">
    <style>
        .circle-medium{
            width: 200px;
            height: 200px;
            border-radius: 50%;
            padding-top: 50px;
        }

        .btn-fa-br {
            background-color: #FFDAB9;
            border: 5px solid #e5c4a6;
            color: #fff;
        }

        .btn-fa-br:hover {
            background-color: #f9f9f9;
        }

        .btn-fa-br:hover .btn-title-apm {
            color: #e5c4a6;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <left>
                <a href="{{url('cf-lite/dashboard')}}" style="left: 25px; top: 5px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            </left>
            <center>
                <h2 class="page-title">@lang('labels.choose') @lang('labels.manual-login')</h2>
            </center>
        </div>
        <div class="col-md-12">
            <center>
                <div class="col-md-4">
                    <a class="btn btn-big-apm btn-fa-lead circle-medium" href="{{url('cf-lite/manual-login')}}">
                        <div class="fa fa-user-plus btn-icon-apm"></div>
                        <div class="btn-title-apm">@lang('labels.member_workout')</div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a class="btn btn-big-apm btn-fa-br circle-medium" data-toggle="modal" data-target="#loginModal">
                        <div class="col-xs-12">
                            <img src="{{asset('images/assets/icon kedatangan staff-11.png')}}" style="width: 40px;">
                        </div>
                        <div class="btn-title-apm">@lang('labels.staff_attendance_sign_in')</div>
                    </a>
                </div>
                <div class="col-md-4">
                    <a class="btn btn-big-apm btn-fa-br circle-medium" href="{{url('cf-lite/manual-login/sign-out')}}">
                        {{--<div class="fa fa-user-plus btn-icon-apm"></div>--}}
                        <div class="col-xs-12">
                            <img src="{{asset('images/assets/icon kepulangan staff-12.png')}}" style="width: 40px;">
                        </div>
                        <div class="btn-title-apm">@lang('labels.staff_attendance_sign_out')</div>
                    </a>
                </div>
            </center>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="loginModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">@lang('labels.staff_attendance_login')</h4>
                </div>
                {!! Form::open(['method' => 'POST', 'action' => 'ManualAttendanceController@staff_login']) !!}
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('email', trans('labels.email').':', ['class' => 'control-label col-md-2']) !!}
                            <div class="col-md-3">
                                {!! Form::text('email', null, ['class' => 'form-control']) !!}
                            </div>
                            {!! Form::label('or', trans('labels.or').':', ['class' => 'control-label col-md-2']) !!}
                            {!! Form::label('key_tag_id', trans('labels.key_tag_id').':', ['class' => 'control-label col-md-2']) !!}
                            <div class="col-md-3">
                                {!! Form::text('key_tag_id', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('password', trans('labels.password').':', ['class' => 'control-label col-md-2']) !!}
                            <div class="col-md-3">
                                {!! Form::password('password', ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('labels.close')</button>
                    {!! Form::submit(trans('labels.login'), ['class' => 'btn btn-primary']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection