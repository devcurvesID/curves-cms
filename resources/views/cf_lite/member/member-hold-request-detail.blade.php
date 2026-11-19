@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <style>
        .home-btn-title{
            font-family: 'Alegreya Sans', sans-serif;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <center>
                    <h2 class="page-title">@lang('labels.member_hold_request_detail')</h2>
                </center>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-2">
                @if(!empty($member_hold->memberhold_user->user_memberpersonal->photo))
                    <img class="img-rounded" src="{{asset('images/member_photos/'.$member_hold->memberhold_user->user_memberpersonal->photo)}}" style="width: 120px; height: 125px; object-fit: cover;">
                @else
                    <img class="img-rounded" src="{{asset('images/img.png')}}" style="width: 120px; height: 125px; object-fit: cover;">
                @endif
            </div>
            <div class="col-md-9">
                <div class="panel panel-default">
                    <div class="panel-body">
                        {!! Form::model($member_hold, ['method' => 'PATCH', 'action' => ['MemberHoldController@update_member_hold_request', $member_hold->id]]) !!}
                        <div class="form-horizontal">
                            <div class="form-group">
                                {!! Form::label('name', trans('labels.name').': ', ['class' => 'control-label col-md-3']) !!}
                                <div class="col-md-8">
                                    <p class="form-control-static">{{$member_hold->memberhold_user->name}}</p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('from_date', trans('labels.date').': ', ['class' => 'control-label col-md-3']) !!}
                                <div class="col-md-3">
                                    @if($member_hold->status == 'OnHold')
                                        {!! Form::date('from_date', null, ['class' => 'form-control', 'readonly']) !!}
                                    @else
                                        {!! Form::date('from_date', null, ['class' => 'form-control']) !!}
                                    @endif
                                </div>
                                <span class="form-control-static col-md-1">@lang('labels.until')</span>
                                <div class="col-md-3">
                                    {!! Form::date('thru_date', null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('category', trans('labels.category').': ', ['class' => 'control-label col-md-3']) !!}
                                <div class="col-md-8">
                                    <p class="form-control-static">{{$member_hold->memberhold_memberholdreason->member_hold_reason}}</p>
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('member_hold_note', trans('labels.note').': ', ['class' => 'control-label col-md-3']) !!}
                                <div class="col-md-6">
                                    {!! Form::textarea('member_hold_note', null, ['class' => 'form-control', 'rows' => '3']) !!}
                                </div>
                            </div>
                            @if(($member_hold->status == 'Pending') || ($member_hold->status == 'OnHold'))
                                <div class="form-group">
                                    <div class="col-md-10">
                                        {!! Form::submit(trans('labels.save_changes'), ['class' => 'btn btn-primary btn-sm pull-right']) !!}
                                    </div>
                                </div>
                            @endif
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('other-script')
@endsection