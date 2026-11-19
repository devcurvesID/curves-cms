@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">
    <style>
        table.table td:first-child {
            width: 150px;
            color: #ff1493;
        }

        table.table td {
            vertical-align: middle !important;
        }

        table.email-fb td:first-child,
        table.email-fb td:nth-child(4),
        table.club-open-hour td:first-child,
        table.club-open-hour td:nth-child(3),
        table.club-open-hour td:nth-child(5),
        table.club-open-hour td:nth-child(7) {
            color: #ff1493;
        }

        h4 {
            color: #663399;
        }

        .btn-primary {
            background-color: #ff1493;
            color: #ffffff;
            border-color: #ff1493;
        }

        .btn-primary:hover {
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
            <a href="{{url('cf-lite/change-password-club-list')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.club_login_password_management')</h2>
            </center>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-3 col-md-offset-1">
            @if(!empty($club->photo))
                <img role="button" id="image_preview" class="img-rounded"
                     src="{{asset('images/club_photos/'.$club->photo)}}"
                     style="width: 250px; height: 200px; object-fit: cover;">
            @else
                <img role="button" id="image_preview" class="img-rounded" src="{{asset('images/club.png')}}"
                     style="width: 250px; height: 200px; object-fit: cover;">
            @endif
            <div class="form-group hidden">
                {!! Form::file('photo', ['class' => 'file_multi_video']) !!}
            </div>
        </div>
        <div class="col-md-6">
            <table class="table">
                <tr>
                    <td>@lang('labels.club_name')</td>
                    <td>:</td>
                    <td>{{$club->club_name}}</td>
                </tr>
                <tr>
                    <td>@lang('labels.address')</td>
                    <td>:</td>
                    <td>{{ucwords($club->address)}} {{ucwords($club->city)}} {{ucwords($club->province)}} {{ucwords($club->country)}}</td>
                </tr>
                <tr>
                    <td>@lang('labels.postal')</td>
                    <td>:</td>
                    <td>{{$club->postal}}</td>
                </tr>
                <tr>
                    <td>@lang('labels.phone')</td>
                    <td>:</td>
                    <td>{{$club->phones}}</td>
                </tr>
                <tr>
                    <td>@lang('labels.status')</td>
                    <td>:</td>
                    <td>{{$club->status}}</td>
                </tr>
            </table>
        </div>
    </div>
    <hr>
    @if($new_or_update == 'update')
    {!! Form::open(['method' => 'PATCH', 'action' => ['ClubController@update_club_password',$club->id]]) !!}
    <div class="row">
        <div class="col-md-offset-2 col-md-8">
            <table class="table">
                <tr>
                    <td>@lang('labels.username')</td>
                    <td>:</td>
                    <td>{!! Form::text('username',$club->clubinfo_clublogin->username,['class'=>'form-control']) !!}</td>
                </tr>
                <tr>
                    <td>@lang('labels.old_password')</td>
                    <td>:</td>
                    <td>{!! Form::password('old_password',['class'=>'form-control']) !!}</td>
                </tr>
                <tr>
                    <td>@lang('labels.new_password')</td>
                    <td>:</td>
                    <td>{!! Form::password('password',['class'=>'form-control']) !!}</td>
                </tr>
                <tr>
                    <td>@lang('labels.retype_password')</td>
                    <td>:</td>
                    <td>{!! Form::password('password_confirmation',['class'=>'form-control']) !!}</td>
                </tr>
            </table>
            {!! Form::submit(trans('labels.update'), ['class' => 'btn btn-primary pull-right']) !!}
        </div>
    </div>
    {!! Form::close() !!}
    @else
    {!! Form::open(['method' => 'POST', 'action' => ['ClubController@store_club_password',$club->id]]) !!}
    <div class="row">
        <div class="col-md-offset-2 col-md-8">
            <table class="table">
                <tr>
                    <td>@lang('labels.username')</td>
                    <td>:</td>
                    <td>{!! Form::text('username',null,['class'=>'form-control']) !!}</td>
                </tr>
                <tr>
                    <td>@lang('labels.new_password')</td>
                    <td>:</td>
                    <td>{!! Form::password('password',['class'=>'form-control']) !!}</td>
                </tr>
                <tr>
                    <td>@lang('labels.retype_password')</td>
                    <td>:</td>
                    <td>{!! Form::password('password_confirmation',['class'=>'form-control']) !!}</td>
                </tr>
            </table>
            {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary pull-right']) !!}
        </div>
    </div>
    {!! Form::close() !!}
    @endif
@endsection

@section('other-script')
    <script src="{{asset('vendors/moment/min/moment.min.js')}}"></script>
    <script src="{{asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>
@endsection