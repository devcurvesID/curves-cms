@extends('cf_lite.layouts.staff-template')

@section('other-style')
@endsection

@section('content')
@include('cf_lite.includes.flash-message')
@include('cf_lite.includes.error-message')
    {!! Form::open(['method' => 'POST', 'action' => 'ChartOfAccountController@generate_coa']) !!}
        {!! Form::text('jubilant', null, ['class' => 'form-control']) !!}
        {!! Form::submit() !!}
    {!! Form::close() !!}
@endsection

@section('other-script')

@endsection