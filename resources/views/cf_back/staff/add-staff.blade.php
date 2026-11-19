@extends('cf_back.layouts.master-dashboard')

@section('title', 'Add Staff')

@section('other-style')
    <link href={{asset("vendors/iCheck/skins/flat/green.css")}} rel="stylesheet">
    <link href={{asset("vendors/select2/dist/css/select2.min.css")}} rel="stylesheet">
    <link href="{{asset('EasyAutocomplete-1_3_5\easy-autocomplete.min.css')}}" rel="stylesheet">
    <style>
        .select2 {
            width: 100% !important;
        }

        textarea {
            resize: none;
        }

        label {
            padding-left: 0px !important;
        }

        .alert {
            padding-left: 50px;
        }

        #blah {
            max-width: 100%;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="my-section">
            <div class="section-title">
                <center>
                    <h1 class="page-title">@lang('labels.add') @lang('labels.staff')</h1>
                </center>
            </div>

            @include('cf_back.error-message')
            <div class="col-lg-6 col-md-8 col-sm-10 col-lg-push-3 col-md-push-2 col-sm-push-1">
                {!! Form::open(['url' => 'cf-back/staff/', 'files' => 'true', 'id' => 'formstaff']) !!}
                <div class="col-sm-12 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('name', trans('labels.name'), ['class' => 'control-label']) !!}
                        {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => trans('labels.input').' '.trans('labels.name')]) !!}
                    </div>
                </div>
                <div class="col-sm-12 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('email', trans('labels.email').' ('.trans('labels.curves').')', ['class' => 'control-label']) !!}
                        {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => trans('labels.input').' '.trans('labels.email').' '.trans('labels.curves')]) !!}
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('password', trans('labels.password'), ['class' => 'control-label']) !!}
                        {!! Form::password('password', ['class' => 'form-control', 'placeholder' => trans('labels.input').' '.trans('labels.password')]) !!}
                        <span class="red"><small>* (Min 6 Character)</small></span>
                        @if($errors->has('password'))
                            <span class="help-block">{{$errors->first('password')}}</span>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('password_confirmation', trans('labels.repassword'), ['class' => 'control-label']) !!}
                        {!! Form::password('password_confirmation', ['class' => 'form-control', 'placeholder' => trans('labels.input').' '.trans('labels.repassword')]) !!}
                        @if($errors->has('password_confirmation'))
                            <span class="help-block">{{$errors->first('password_confirmation')}}</span>
                        @endif
                    </div>
                </div>

                <div class="col-xs-12">
                    <hr>
                    <h3>@lang('labels.club') & @lang('labels.role')</h3>
                </div>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('club_id', trans('labels.club'), ['class' => 'control-label']) !!}
                        @if(count($clubs) > 0):
                        {!! Form::select('club_id', $clubs, null, ['class' => 'form-control js-state', 'id' => 'select_club', 'placeholder' => trans('labels.choose').' '.trans('labels.club')]) !!}
                        @else
                            <p>@lang('labels.club_not_found')</p>
                        @endif
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('role_id', trans('labels.role'), ['class' => 'control-label']) !!}
                        @if(count($roles) > 0):
                        {!! Form::select('role_id', $roles, null, ['class' => 'form-control js-state', 'id' => 'select_role', 'placeholder' => trans('labels.choose').' '.trans('labels.role')]) !!}
                        @else
                            <p>@lang('labels.role_not_found')</p>
                        @endif
                    </div>
                </div>
                {{--<div class="col-sm-6 col-xs-12">--}}
                {{--<div class="form-group">--}}
                {{--{!! Form::label('frole_id', trans('labels.functional_role'), ['class' => 'control-label']) !!}--}}
                {{--@if(count($froles) > 0):--}}
                {{--{!! Form::select('frole_id', $froles, null, ['class' => 'form-control js-state', 'id' => 'select_frole', 'placeholder' => trans('labels.choose').' '.trans('labels.functional_role')]) !!}--}}
                {{--@else--}}
                {{--<p>@lang('labels.functional_role_not_found')</p>--}}
                {{--@endif--}}
                {{--</div>--}}
                {{--</div>--}}

                <div class="col-xs-12">
                    <hr>
                    <h3>@lang('labels.personal_information')</h3>
                </div>

                <div class="col-sm-12 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('email_personal', trans('labels.email').' ('.trans('labels.personal').')', ['class' => 'control-label']) !!}
                        {!! Form::email('email_personal', null, ['class' => 'form-control', 'placeholder' => trans('labels.input').' '.trans('labels.email')]) !!}
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('nik', trans('labels.nik'), ['class' => 'control-label']) !!}
                        {!! Form::text('nik', null, ['class' => 'form-control', 'placeholder' => trans('labels.input').' '.trans('labels.nik')]) !!}
                    </div>
                </div>
                <div class="col-sm-6 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('npwp', trans('labels.npwp'), ['class' => 'control-label']) !!}
                        {!! Form::text('npwp', null, ['class' => 'form-control', 'placeholder' => trans('labels.input').' '.trans('labels.npwp')]) !!}
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="form-group">
                        {!! Form::label('birth', trans('labels.birthday'), ['class' => 'control-label']) !!}
                        {!! Form::date('birth', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="form-group">
                        {!! Form::label('sex', trans('labels.gender'), ['class' => 'control-label']) !!}
                        <div class="radio">
                            <label>
                                <input name="sex" type="radio" class="flat"> @lang('labels.male')
                            </label>
                        </div>
                        <div class="radio">
                            <input name="sex" type="radio" class="flat" checked> @lang('labels.female')
                        </div>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="form-group">
                        {!! Form::label('location', trans('labels.location'), ['class' => 'control-label']) !!}
                        {!! Form::text('location', null, ['id'=>'basics','class' => 'form-control', 'placeholder' => trans('labels.choose').' '.trans('labels.location')]) !!}
                    </div>
                </div>
                {{--<div class="col-sm-4 col-xs-12">--}}
                {{--<div class="form-group">--}}
                {{--{!! Form::label('country_id', trans('labels.country'), ['class' => 'control-label']) !!}--}}
                {{--@if(count($countries) > 0):--}}
                {{--{!! Form::select('country_id', $countries, null, ['class' => 'form-control', 'placeholder' => trans('labels.choose').' '.trans('labels.country'), 'id' => 'countries']) !!}--}}
                {{--@else--}}
                {{--<p>@lang('labels.country_not_found')</p>--}}
                {{--@endif--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--<div class="col-sm-4 col-xs-12">--}}
                {{--<div class="form-group" id="province-group" style="display: none;">--}}
                {{--{!! Form::label('province_id', trans('labels.province'), ['class' => 'control-label']) !!}--}}
                {{--<div id="province-select"></div>--}}
                {{--</div>--}}
                {{--</div>--}}
                {{--<div class="col-sm-4 col-xs-12">--}}
                {{--<div class="form-group" id="city-group" style="display: none;">--}}
                {{--{!! Form::label('city_id', trans('labels.city'), ['class' => 'control-label']) !!}--}}
                {{--<div id="city-select"></div>--}}
                {{--</div>--}}
                {{--</div>--}}
                <div class="col-xs-12">
                    <div class="form-group">
                        {!! Form::label('address', trans('labels.address'), ['class' => 'control-label']) !!}
                        {!! Form::textarea('address', null, ['class' => 'form-control', 'placeholder' => trans('labels.input').' '.trans('labels.address')]) !!}
                    </div>
                </div>
                <div class="col-sm-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('postal', trans('labels.postal_code'), ['class' => 'control-label']) !!}
                        {!! Form::text('postal', null, ['class' => 'form-control','data-inputmask' => '"mask": "99999"', 'placeholder' => trans('labels.input').' '.trans('labels.postal_code')]) !!}
                    </div>
                </div>
                <div class="col-sm-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('phone', trans('labels.phone'), ['class' => 'control-label']) !!}
                        {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => trans('labels.input').' '.trans('labels.phone')]) !!}
                    </div>
                </div>
                <div class="col-sm-4 col-xs-12">
                    <div class="form-group">
                        {!! Form::label('cellphone', trans('labels.mobile_phone'), ['class' => 'control-label']) !!}
                        {!! Form::text('cellphone', null, ['class' => 'form-control', 'placeholder' => trans('labels.input').' '.trans('labels.mobile_phone')]) !!}
                    </div>
                </div>

                <div class="col-xs-12">
                    <div class="form-group">
                        {!! Form::label('photo', trans('labels.photo'), ['class' => 'control-label']) !!}
                        {!! Form::file('photo') !!}

                        <img id="blah" src="#" alt="your image"/>

                    </div>
                </div>
                <div class="col-xs-12">
                    <hr>
                </div>
                <div class="col-xs-12">
                    <div class="form-group">
                        {!! Form::label('key_tag_id', trans('labels.key_tag_id'), ['class' => 'control-label']) !!}
                        {!! Form::text('key_tag_id', null, ['class' => 'form-control', 'placeholder' => trans('labels.input').' '.trans('labels.key_tag_id')]) !!}
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="form-group">
                        {!! Form::submit('Tambah User', ['class' => 'btn btn-primary form-control']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection


@section('other-script')
    <script src={{asset("vendors/iCheck/icheck.min.js")}}></script>
    <script src={{asset("js/jquery.dataTables.min.js")}}></script>
    <script src={{asset("js/dataTables.bootstrap.min.js")}}></script>
    <script src={{asset("vendors/select2/dist/js/select2.js")}}></script>
    <script src={{asset("vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js")}}></script>
    <script src={{asset("vendors/moment/min/moment.min.js")}}></script>
    <script src={{asset("vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js")}}></script>
    <script>
        $(document).ready(function () {
            $('#bday').datetimepicker({
                format: 'YYYY/MM/DD'
            });

            $('#select_role').select2({
                placeholder: "<?php echo trans('labels.choose') . ' ' . trans('labels.role'); ?>"
            });
            $('#select_club').select2({
                placeholder: "<?php echo trans('labels.choose') . ' ' . trans('labels.club'); ?>"
            });
            $('#select_frole').select2({
                placeholder: "<?php echo trans('labels.choose') . ' ' . trans('labels.functional_role'); ?>"
            });
        });
    </script>
    <script src="{{asset('EasyAutocomplete-1_3_5\jquery.easy-autocomplete.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            var json = {!! json_encode($cities) !!};
            console.log(json);
            var options = {
                data: json,

                getValue: "city",

                list: {
                    match: {
                        enabled: true
                    },
                    showAnimation: {
                        type: "slide"
                    },
                    hideAnimation: {
                        type: "slide"
                    }
                },

                template: {
                    type: "custom",
                    method: function (value, item) {
                        return '<p>' + item.city + ', '+ item.province +'</p>' +
                            '<p>' + item.country + '</p><hr/>';
                    }
                }
            };
            $('#basics').easyAutocomplete(options);
        });
    </script>

@endsection


