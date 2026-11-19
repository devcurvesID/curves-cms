@extends('cf_acc.layouts.app')

@section('content')
    <div class="container">
        <div class="row">

            <div class="logo-login">
                <img src="{{asset('images/Curves_Logo_Purple_Amaze.png')}}">
            </div>

            <h3 style="text-align: center; color: rebeccapurple; font-weight: 600;">@lang('labels.accounting')</h3>

            <div class="col-md-8 col-md-offset-2 login-box">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Login
                        <span class="pull-right">
                            <a role="button" data-toggle="modal" data-target="#registrationModal">@lang('labels.registration')</a>
                        </span>
                    </div>

                    <div class="panel-body">
                        {!! Form::open(['method' => 'POST', 'action' => 'AccountingAccountController@acc_login_authentication', 'class' => 'form-horizontal']) !!}
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

        <div class="modal fade" id="registrationModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">@lang('labels.please_login_using_your_account')</h4>
                    </div>
                    {!! Form::open(['method' => 'PATCH', 'action' => 'AccountingAccountController@registration_login_check', 'class' => 'form-horizontal']) !!}
                    <div class="modal-body">
                        <div class="form-group{{ $errors->has('username') ? ' has-error' : '' }}">
                            <label for="username" class="col-md-4 control-label">@lang('labels.username')</label>

                            <div class="col-md-6">
                                <input id="reg_username" type="text" class="form-control" name="reg_username"
                                       value="" required autofocus>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('reg_password') ? ' has-error' : '' }}">
                            <label for="reg_password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="reg_password" type="password" class="form-control" name="reg_password"
                                       required>

                                @if ($errors->has('reg_password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('reg_password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Login</button>
                    </div>
                    {!! Form::close() !!}
                </div><!-- /.modal-content -->
            </div><!-- /.modal-dialog -->
        </div><!-- /.modal -->
    </div>
@endsection

@section('other-script')
    {{--<script src="{{asset('js/jquery-3_2_1.min.js')}}"></script>--}}
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('js/moment-timezone-with-data.js')}}"></script>
    <script>
        $(document).ready(function () {
            var timezone = moment.tz.guess();
            $('input[name="timezone"]').val(timezone);
        });
    </script>
@endsection