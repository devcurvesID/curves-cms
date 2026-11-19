@extends('cf_lite.layouts.app')

@section('content')
    <div class="container">
        <div class="row">

            <div class="logo-login">
                <img src="{{asset('images/Curves_Logo_Purple_Amaze.png')}}">
            </div>

            <div class="col-md-8 col-md-offset-2 login-box">
                <div class="panel panel-default">
                    <div class="panel-heading">Club Login</div>

                    <div class="panel-body">
                        {!! Form::open(['method' => 'POST', 'action' => 'ViewController@check_club_login']) !!}
                            <div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                                <label class="col-md-4 control-label">Username</label>

                                <div class="col-md-6">
                                    {!! Form::text('username', null, ['class' => 'form-control']) !!}
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
                            {{-- <select name="timezone">
                                <option value="Asia/Jakarta">Jakarta</option>
                                <option value="Asia/Makassar">Makassar</option>
                                <option value="Asia/Jayapura">Jayapura</option>
                            </select> --}}
                            {{--{!! Form::hidden('timezone', null) !!}--}}

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

{{--<script src="{{asset('js/jquery-3_2_1.min.js')}}"></script>--}}
{{--<script src="{{asset('js/moment.min.js')}}"></script>--}}
{{--<script src="{{asset('js/moment-timezone-with-data.js')}}"></script>--}}
{{--<script>--}}
    {{--$(document).ready(function () {--}}
        {{--var timezone = moment.tz.guess();--}}
        {{--$('input[name="timezone"]').val(timezone);--}}
    {{--});--}}
{{--</script>--}}
