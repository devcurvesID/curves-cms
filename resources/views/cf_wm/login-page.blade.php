@extends('cf_lite.layouts.app')

@section('content')
    <div class="container">
        <div class="row">

            <div class="logo-login">
                <img src="{{asset('images/Curves_Logo_Purple_Amaze.png')}}">
            </div>

            <h3 style="text-align: center; color: rebeccapurple; font-weight: 600;">@lang('labels.weigh_n_measure')</h3>

            <div class="col-md-8 col-md-offset-2 login-box">
                <div class="panel panel-default">
                    <div class="panel-heading">Login</div>

                    <div class="panel-body">
                        {!! Form::open(['method' => 'POST', 'action' => 'WMLiteController@login_authentication', 'class' => 'form-horizontal']) !!}
                            <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                                <label for="username" class="col-md-4 control-label">@lang('labels.username')</label>

                                <div class="col-md-6">
                                    <input id="username" type="text" class="form-control" name="username"
                                           value="{{ old('username') }}" required autofocus>

                                    @if ($errors->has('email'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                                <label for="password" class="col-md-4 control-label">Password</label>

                                <div class="col-md-6">
                                    <input id="password" type="password" class="form-control" name="password"
                                           required>

                                    @if ($errors->has('password'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                    @endif
                                </div>
                            </div>

                            {!! Form::hidden('timezone', null) !!}

                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4 login-submit">
                                    <button type="submit" class="btn btn-primary">
                                        Login
                                    </button>
                                </div>
                            </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery-3_2_1.min.js')}}"></script>
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('js/moment-timezone-with-data.js')}}"></script>
    <script>
        $(document).ready(function () {
            var timezone = moment.tz.guess();
            $('input[name="timezone"]').val(timezone);
        });
    </script>
@endsection