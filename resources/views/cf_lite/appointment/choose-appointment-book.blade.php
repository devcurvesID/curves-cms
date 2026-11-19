@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/appointment.css')}}" rel="stylesheet">
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/dashboard')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.choose') @lang('labels.appointment_date')</h2>
            </center>
        </div>
        <div class="col-md-12">
            {!! Form::label('appointment_date', trans('labels.choose').' '.trans('labels.date'), ['class' => 'control-label']) !!}
            {!! Form::date('appointment_date', null, ['class' => 'form-control']) !!}
        </div>
    </div>
@endsection

@section('other-script')
    <script>
        $('#appointment_date').on('change', function () {
            var url = '{{url()->current()}}';
            window.location.href=url + '/' + $(this).val();
        });
    </script>
@endsection