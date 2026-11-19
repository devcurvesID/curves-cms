@extends('cf_acc.layouts.app')

@section('other-styles')
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
@endsection

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
                        @lang('labels.registration')
                    </div>

                    <div class="panel-body">
                        @include('cf_lite.includes.error-message')
                        {!! Form::open(['method' => 'POST', 'action' => 'AccountingAccountController@store_registration', 'class' => 'form-horizontal']) !!}
                            <div class="form-group">
                                {!! Form::label('name', trans('labels.name'), ['class' => 'col-md-4 control-label']) !!}
                                <div class="col-md-6">
                                    {!! Form::text('name', null, ['class' => 'form-control']) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('username', trans('labels.username'), ['class' => 'col-md-4 control-label']) !!}
                                <div class="col-md-6">
                                    {!! Form::text('username', null, ['class' => 'form-control']) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('password', trans('labels.password'), ['class' => 'col-md-4 control-label']) !!}
                                <div class="col-md-6">
                                    {!! Form::password('password', ['class' => 'form-control']) !!}
                                </div>
                            </div>

                            <div class="form-group">
                                {!! Form::label('password_confirmation', trans('labels.confirm_password'), ['class' => 'col-md-4 control-label']) !!}
                                <div class="col-md-6">
                                    {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <!-- OLD -->
                            <!--
                            <div class="form-group">
                                {{-- {!! Form::label('clubs', trans('labels.choose_club'), ['class' => 'col-md-4 control-label']) !!} --}}
                                <div class="col-md-6">
                                    {{--<select multiple="multiple" name="clubs[]" id="clubs" class="form-control select2">--}}
                                        {{--<option value="val">Select All</option>--}}
                                        {{--@foreach($clubs as $key => $value)--}}
                                            {{--<option value="{{$key}}">{{$value}}</option>--}}
                                        {{--@endforeach--}}
                                    {{--</select>--}}
                                    {{-- {!! Form::select('clubs[]', $clubs, null, ['class' => 'form-control select2', 'multiple', 'id' => 'clubs']) !!} --}}
                                </div>
                            </div>
                            -->
                            <!-- END OF OLD -->
                            <!-- NEW 17 Desember 2019 -->
                            <div class="form-group">
                                {!! Form::label('jubilants', trans('labels.choose_jubilant'), ['class' => 'col-md-4 control-label']) !!}
                                <div class="col-md-6">
                                    {!! Form::select('jubilants[]', $jubilants, null, ['class' => 'form-control select2', 'multiple', 'id' => 'jubilants']) !!}
                                </div>
                            </div>
                            <!-- END OF NEW 17 Desember 2019 -->
                            <div class="form-group">
                                <div class="col-md-8 col-md-offset-4 login-submit">
                                    <button type="submit" class="btn btn-primary">
                                        @lang('labels.save')
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
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $.fn.select2.amd.define('select2/selectAllAdapter', [
                'select2/utils',
                'select2/dropdown',
                'select2/dropdown/attachBody'
            ], function (Utils, Dropdown, AttachBody) {

                function SelectAll() { }
                SelectAll.prototype.render = function (decorated) {
                    var self = this,
                        $rendered = decorated.call(this),
                        $selectAll = $(
                            '<button class="btn btn-xs btn-success" type="button" style="margin-left:6px;"><i class="fa fa-check-square-o"></i> @lang('labels.select_all')</button>'
                        ),
                        $unselectAll = $(
                            '<button class="btn btn-xs btn-danger" type="button" style="margin-left:6px;"><i class="fa fa-square-o"></i> @lang('labels.unselect_all')</button>'
                        ),
                        $btnContainer = $('<div style="margin-top:3px;">').append($selectAll).append($unselectAll);
                    if (!this.$element.prop("multiple")) {
                        // this isn't a multi-select -> don't add the buttons!
                        return $rendered;
                    }
                    $rendered.find('.select2-dropdown').prepend($btnContainer);
                    $selectAll.on('click', function (e) {
                        var $results = $rendered.find('.select2-results__option[aria-selected=false]');
                        $results.each(function () {
                            self.trigger('select', {
                                data: $(this).data('data')
                            });
                        });
                        self.trigger('close');
                    });
                    $unselectAll.on('click', function (e) {
                        var $results = $rendered.find('.select2-results__option[aria-selected=true]');
                        $results.each(function () {
                            self.trigger('unselect', {
                                data: $(this).data('data')
                            });
                        });
                        self.trigger('close');
                    });
                    return $rendered;
                };

                return Utils.Decorate(
                    Utils.Decorate(
                        Dropdown,
                        AttachBody
                    ),
                    SelectAll
                );

            });

            $('#jubilants').select2({
//                placeholder: 'Select',
                dropdownAdapter: $.fn.select2.amd.require('select2/selectAllAdapter')
            });
        });
    </script>
@endsection