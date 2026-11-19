@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <style>

    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-sm-12">
            <left>
                <a href="{{url('cf-lite/lead')}}" style="left: 25px; top: 5px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            </left>
            <center>
                <h2 class="page-title" style="margin-top: 0px;">@lang('labels.import_leads')</h2>
            </center>
        </div>
        <div class="col-md-12">
            {!! Form::open(['method' => 'POST', 'action' => 'LeadController@import_leads', 'files' => true]) !!}
            <div class="col-md-6">
                <h4>@lang('labels.import_excel') @lang('labels.lead')</h4>
                <div class="form-horizontal">
                    <div class="form-group">
                        {{--{!! Form::label('leads', trans('labels.import_excel').': ', ['class' => 'control-label']) !!}--}}
                        <div class="col-sm-12" style="margin-left: 30px;">
                            {!! Form::file('leads', null) !!}
                        </div>
                    </div>
                </div>
                {!! Form::submit(trans('labels.submit'), ['class' => 'btn btn-primary']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('other-script')
@endsection