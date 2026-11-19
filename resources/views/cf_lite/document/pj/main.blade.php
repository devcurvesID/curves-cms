@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <style>
        .home-btn-title {
            font-family: 'Alegreya Sans', sans-serif;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/document')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">
                        @lang('labels.projection_sheet')
                    </h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                {!! Form::open(['method' => 'GET', 'action' => 'PJController@generate_report', 'class' => 'form-horizontal']) !!}
                <div class="form-group">
                    {!! Form::label('date', trans('labels.choose_date').':  ', ['class' => 'control-label col-md-2']) !!}
                    <div class="col-md-3">
                        {!! Form::date('date', \Carbon\Carbon::now(), ['class' => 'form-control']) !!}
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        {!! Form::submit(trans('labels.submit'), ['class' => 'btn btn-primary']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </section>
@endsection

@section('other-script')
@endsection