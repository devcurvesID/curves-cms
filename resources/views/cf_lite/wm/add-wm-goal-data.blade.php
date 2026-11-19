@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">
@endsection

@section('content')
    @include('cf_lite.includes.error-message')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <center>
                <h2 class="page-title">@lang('labels.add') @lang('labels.weigh_n_measure_goal')</h2>
            </center>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            @if(!empty($member->user_memberpersonal->photo))
                <img class="img-circle member-card-img" src="{{asset('images/member_photos/'.$member->user_memberpersonal->photo)}}" style="width: 150px; height: 150px; object-fit: cover;">
            @else
                <img class="img-circle member-card-img" src="{{asset('images/img.png')}}">
            @endif
        </div>
        <div class="col-md-9">
            <table>
                <tr>
                    <td><span class="fa fa-female" title="@lang('labels.name')"></span></td>
                    <td>:</td>
                    <td>{{$member->name}}</td>
                </tr>
                <tr>
                    <td><span class="fa fa-calendar" title="@lang('labels.joined')"></span></td>
                    <td>:</td>
                    <td>{{date('d F Y', strtotime($member->user_memberpersonal->joined))}}</td>
                </tr>
                <tr>
                    <td><span class="fa fa-heartbeat" title="@lang('labels.last_wm')"></span></td>
                    <td>:</td>
                    <td>
                        @if(!empty($member->user_memberstatus->last_wm))
                            {{date('d F Y', strtotime($member->user_memberstatus->last_wm))}}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <br>
    {!! Form::open(['method' => 'POST', 'action' => ['WeighAndMeasureController@store_new_wm_goal_data', $member->id]]) !!}
    <div class="row">
        <div class="col-md-3">
            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <td><span class="fa fa-calendar" title="@lang('labels.date')"></span>: </td>
                        <td>{!! Form::date('set_goal_date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}</td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-info">
                <div class="panel-heading">@lang('labels.body_composition')</div>
                <div class="panel-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('weight', trans('labels.weight').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('weight', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">@lang('labels.kg')</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('body_fat', trans('labels.body_fat').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('body_fat', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-info">
                <div class="panel-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('muscle_mass', trans('labels.muscle_mass').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('muscle_mass', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">@lang('labels.kg')</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('bone_mass', trans('labels.bone_mass').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('bone_mass', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">@lang('labels.kg')</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('bmi', trans('labels.bmi').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('bmi', null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('dci', trans('labels.dci').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('dci', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">@lang('labels.cal')</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('metabolic', trans('labels.metabolic').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('metabolic', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">@lang('labels.years_old')</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('body_water', trans('labels.body_water').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('body_water', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('visceral', trans('labels.visceral').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('visceral', null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel panel-warning">
                <div class="panel-heading">@lang('labels.size')</div>
                <div class="panel-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('chest', trans('labels.chest').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('chest', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">@lang('labels.cm')</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('waist', trans('labels.waist').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('waist', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">@lang('labels.cm')</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('abdomen', trans('labels.abdomen').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('abdomen', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">@lang('labels.cm')</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('hip', trans('labels.hip').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('hip', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">@lang('labels.cm')</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('thigh', trans('labels.thigh').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                {!! Form::text('thigh', null, ['class' => 'form-control']) !!}
                            </div>
                            {!! Form::label('total_thigh', trans('&times;2'), ['class' => 'control-label col-md-1']) !!}
                            <div class="col-md-4">
                                <div class="input-group">
                                    {!! Form::text('total_thigh', null, ['class' => 'form-control', 'readonly']) !!}
                                    <span class="input-group-addon">@lang('labels.cm')</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('arm', trans('labels.arm').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                {!! Form::text('arm', null, ['class' => 'form-control']) !!}
                            </div>
                            {!! Form::label('total_arm', trans('&times;2'), ['class' => 'control-label col-md-1']) !!}
                            <div class="col-md-4">
                                <div class="input-group">
                                    {!! Form::text('total_arm', null, ['class' => 'form-control', 'readonly']) !!}
                                    <span class="input-group-addon">@lang('labels.cm')</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">@lang('labels.xfactor')</div>
                <div class="panel-body">
                    <div class="form-group">
                        {!! Form::label('xfactor', trans('labels.xfactor').' - 1:', ['class' => 'control-label']) !!}
                        {!! Form::textarea('xfactor[1]', null, ['class' => 'form-control', 'rows' => '4']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('xfactor', trans('labels.xfactor').' - 2:', ['class' => 'control-label']) !!}
                        {!! Form::textarea('xfactor[2]', null, ['class' => 'form-control', 'rows' => '4']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary btn-lg pull-right']) !!}
    </div>
    {!! Form::close() !!}
@endsection

@section('other-script')
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('input[name="thigh"]').on('keyup', function() {
                var total_thigh = parseFloat($(this).val()) * 2;
                $('input[name="total_thigh"]').val(total_thigh);
            });

            $('input[name="arm"]').on('keyup', function() {
                var total_arm = parseFloat($(this).val()) * 2;
                $('input[name="total_arm"]').val(total_arm);
            });
        });
    </script>
@endsection