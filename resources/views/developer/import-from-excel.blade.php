@extends('cf_lite.layouts.app')

@section('content')
    <div class="container">
        <div class="row">

            <div class="logo-login">
                <img src="{{asset('images/Curves_Logo_Purple_Amaze.png')}}">
            </div>

            <div class="col-md-8 col-md-offset-2 login-box">
                <div class="panel panel-default">
                    <div class="panel-heading">@lang('labels.import_excel_nosale_file')</div>

                    <div class="panel-body">
                        {!! Form::open(['method' => 'POST', 'action' => 'ExcelDevController@read_excel', 'files' => 'true']) !!}
                        <div class="form-group">
                            {!! Form::file('data', null) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit(trans('labels.submit'), ['class' => 'btn btn-default']) !!}
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
