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
                    <div class="panel-heading">@lang('labels.additional_bonus')</div>

                    <div class="panel-body">
                        {!! Form::open(['method' => 'POST', 'action' => 'BonusDevController@execute', 'class' => 'form-horizontal']) !!}
                        <div class="form-group">
                            {!! Form::label('date', trans('labels.before_date').': ', ['class' => 'control-label col-sm-2']) !!}
                            <div class="col-sm-3">
                                {!! Form::date('date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('club', trans('labels.club').': ', ['class' => 'control-label col-sm-2']) !!}
                            <div class="col-sm-3">
                                {!! Form::select('club', $clubs, null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('additional', trans('labels.additional').': ', ['class' => 'control-label col-sm-2']) !!}
                            <div class="col-sm-3">
                                <div class="input-group">
                                    {!! Form::number('additional', 1, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">@lang('labels.month')</span>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            @foreach($memberships as $membership)
                                <div class="col-sm-12">
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="membership[{{$membership->id}}]" value="{{$membership->id}}"> {{$membership->membership_type_name}}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                {!! Form::submit(trans('labels.submit'), ['class' => 'btn btn-primary pull-right']) !!}
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>

                <div class="panel panel-info">
                    <div class="panel-heading">@lang('labels.history')</div>
                    @if(!empty($logs) && (count($logs) > 0))
                        <ul class="list-group">
                            @foreach($logs as $log)
                                <li class="list-group-item">
                                    {{\Carbon\Carbon::parse($log->created_at)->format('d-m-Y')}} <span class="pull-right">{{$log->logadditionalbonus_user->name}}</span>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <div class="panel-body">
                            @lang('labels.empty')
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
