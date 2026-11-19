@extends('layouts.template')

@section('main')
    <div class="container">
        <h1>@lang('labels.role_view_edit')</h1>

        @if($errors->any())
            <ul class="alert alert-danger">
                @foreach($errors->all() as $error)
                    <li>{{$error}}</li>
                @endforeach
            </ul>
        @endif

        {!! Form::model($role, ['method' => 'PATCH', 'action' => ['RolesController@update', $role->id]]) !!}
        <div class="form-group">
            {!! Form::label('role', trans('labels.role_nama'), ['class' => 'control-label']) !!}
            {!! Form::text('role', null, ['class' => 'form-control', 'placeholder' => trans('labels.role_nama_place')]) !!}
        </div>
        <div class="form-group">
            {!! Form::submit(trans('labels.role_view_edit'), ['class' => 'btn btn-primary form-control']) !!}
        </div>
        {!! Form::close() !!}
    </div>
@endsection
