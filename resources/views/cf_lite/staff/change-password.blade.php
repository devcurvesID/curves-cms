@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <style>
        table td{
            height: 50px;
            padding: 10px;
        }
        
        table td:first-child{
            color: #ff1493;
            font-size: 20px;
        }

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
            @if((\Illuminate\Support\Facades\Auth::user()->role_id <= 4) || (\Illuminate\Support\Facades\Auth::user()->role_id == 8)
            || (\Illuminate\Support\Facades\Auth::id() == $staff->id))
                {!! Form::open(['method' => 'POST', 'action' => ['StaffController@reset_password', $staff->id] ]) !!}
                <button role="submit" class="btn btn-default pull-right"
                    style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: rebeccapurple; margin-right: 10px; margin-top: 30px; "
                    title="@lang('labels.reset_password')"><span class="fa fa-key"
                        style="color: rebeccapurple;"></span></button>
                {!! Form::close() !!}
            @endif
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/staff/'.$staff->id.'/detail')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.manage_staff')<br><small>(@lang('labels.change_password'))</small></h2>
            </center>
        </div>
    </div>

    {!! Form::open(['method' => 'PATCH', 'action' => ['StaffController@update_password', $staff->id]]) !!}
    {!! Form::hidden('attribute', $staff->id) !!}
    <div class="row">
        <div class="col-md-2 col-md-offset-1">
            @if(!empty($staff->user_memberpersonal->photo))
                <img id="image_preview" class="img-circle" src="{{asset('images/member_photos/'.$staff->user_memberpersonal->photo)}}" style="width: 150px; height: 150px; object-fit: cover;">
            @else
                <img id="image_preview" class="img-circle" src="{{asset('images/img.png')}}" style="width: 150px; height: 150px; object-fit: cover;">
            @endif
        </div>
        <div class="col-md-6 col-md-offset-1">
            <table>
                <tr>
                    <td>@lang('labels.name')</td>
                    <td>:</td>
                    <td>{{$staff->name}}</td>
                </tr>
                <tr>
                    <td>@lang('labels.email')</td>
                    <td>:</td>
                    <td>{{$staff->email}}</td>
                </tr>
                <tr>
                    <td>@lang('labels.username')</td>
                    <td>:</td>
                    <td>{{$staff->username}}</td>
                </tr>
                <tr>
                    <td>@lang('labels.old_password')</td>
                    <td>:</td>
                    <td>
                        {!! Form::password('old_password', ['class' => 'form-control']) !!}
                        <p class="form-control-static">*@lang('labels.default'): curvesindonesia</p>
                    </td>
                </tr>
                <tr>
                    <td>@lang('labels.new_password')</td>
                    <td>:</td>
                    <td>{!! Form::password('password', ['class' => 'form-control']) !!}</td>
                </tr>
                <tr>
                    <td>@lang('labels.retype_password')</td>
                    <td>:</td>
                    <td>{!! Form::password('password_confirmation', ['class' => 'form-control']) !!}</td>
                </tr>
            </table>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary pull-right']) !!}
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@section('other-script')
@endsection