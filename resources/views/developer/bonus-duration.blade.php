@extends('cf_lite.layouts.app')

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="container">
        <div class="row">
            <div class="logo-login">
                <img src="{{asset('images/Curves_Logo_Purple_Amaze.png')}}">
            </div>
            <div class="col-md-8 col-md-offset-2 login-box">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('labels.bonus_duration')</div>
                    <div class="panel-body">
                        {!! Form::open(['method' => 'POST', 'action' => 'BonusDevController@store_bonus_duration', 'class' => 'form-horizontal']) !!}
                        <div class="form-group">
                            {!! Form::label('bonus', trans('labels.bonus_day').': ', ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-4">
                                <div class="input-group">
                                    {!! Form::number('bonus', 1, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">@lang('labels.day')</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('date', trans('labels.date').': ', ['class' => 'control-label col-sm-3']) !!}
                            <div class="col-sm-4">
                                {!! Form::date('date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                {!! Form::submit(trans('labels.submit'), ['class' => 'btn btn-primary pull-right']) !!}
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
