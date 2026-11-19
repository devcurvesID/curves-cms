@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <style>
        .select2 {
            width: 100% !important;
            float: right;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.error-message')
    @include('cf_lite.includes.flash-message')

    <div class="row">
        <div class="col-sm-12">
            <center>
                <h2 class="page-title"
                    style="margin-top: 20px; margin-bottom: 40px; font-weight: 600;">@lang('labels.add') @lang('labels.weigh_n_measure')</h2>
            </center>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="col-md-12 wnmpp">
                @if(!empty($member->user_memberpersonal->photo))
                    <img class="img-circle member-card-img"
                         src="{{asset('images/member_photos/'.$member->user_memberpersonal->photo)}}" style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <img class="img-circle member-card-img" src="{{asset('images/img.png')}}">
                @endif
            </div>
        </div>
        <div class="col-md-9">
            <div class="col-md-12">
                <table style="font-size: 30px; margin-top: 30px;">
                    <tr>
                        <td style="height: 50px; color: #dc3c91; text-align: center;"><span class="fa fa-female" title="@lang('labels.name')"></span></td>
                        <td style="width: 50px;text-align: center;">:</td>
                        <td style="font-weight: 600;">{{$member->name}}</td>
                    </tr>
                    <tr>
                        <td style="height: 50px; color: #dc3c91; text-align: center;"><span class="fa fa-calendar" title="@lang('labels.joined')"></span></td>
                        <td style="width: 50px;text-align: center;">:</td>
                        <td style="font-weight: 600;">{{date('d F Y', strtotime($member->user_memberpersonal->joined))}}</td>
                    </tr>
                    <tr>
                        <td style="height: 50px; color: #dc3c91; text-align: center;"><span class="fa fa-heartbeat" title="@lang('labels.last_weigh_n_measure')"></span></td>
                        <td style="width: 50px;text-align: center;">:</td>
                        <td style="font-weight: 600;">
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
    </div>
    <br>
    {!! Form::open(['method' => 'POST', 'action' => ['WeighAndMeasureController@store_new_wm_data', $member->id]]) !!}
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <td>
                            <table>
                                <tr>
                                    {{--<td><span class="fa fa-calendar" title="@lang('labels.date')"></span><span>@lang('labels.date')</span>:</td>--}}
                                    <td style="font-size: 22px"><span
                                                title="@lang('labels.date')">@lang('labels.date')</span><span
                                                style="margin: 0 10px;">:</span></td>
                                    <td>{!! Form::date('wm_date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}</td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td style="text-align: right; font-size: 22px;">
                                        <span>@lang('labels.age')</span><span style="margin: 0 10px;">:</span></td>
                                    <td width="30%">
                                        @if(!empty($member->user_memberpersonal->birth))
                                            {!! Form::text('age', \Carbon\Carbon::parse($member->user_memberpersonal->birth)->age, ['class' => 'form-control']) !!}
                                        @else
                                            {!! Form::text('age', null, ['class' => 'form-control']) !!}
                                        @endif
                                    </td>
                                    <td style="font-size: 22px;"><span
                                                style="margin-left: 10px;">@lang('labels.years_old')</span></td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td style="text-align: right; font-size: 22px;">@lang('labels.height')<span
                                                style="margin: 0 10px;">:</span></td>
                                    <td width="30%">
                                        {!! Form::text('height', !empty($last_wm) ? $last_wm->height : null, ['class' => 'form-control']) !!}
                                    </td>
                                    <td style="text-align: left; font-size: 22px;"><span
                                                style="margin-left: 10px;">cm</span></td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td style="text-align: right; font-size: 22px; width: 22%;">@lang('labels.bp')<span
                                                style="margin: 0 10px;">:</span></td>
                                    <td width="18%">{!! Form::text('bp_high', null, ['class' => 'form-control']) !!}</td>
                                    <td style="text-align: left;width: 8%;"><span
                                                style="margin: 0 10px; font-size: 30px;">/</span></td>
                                    <td>{!! Form::text('bp_low', null, ['class' => 'form-control']) !!}</td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td style="font-size: 22px;"
                                    ">@lang('labels.rhr')<span style="margin: 0 10px;">:</span></td>
                                    <td>{!! Form::text('rhr', null, ['class' => 'form-control']) !!}</td>
                                </tr>
                            </table>
                        </td>
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
                            {!! Form::label('weight', trans('labels.weight'), ['class' => 'control-label col-md-6']) !!}
                            <span class="col-md-1" style="font-size: 22px;">:</span>
                            <div class="col-md-5">
                                <div class="input-group">
                                    {!! Form::text('weight', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">@lang('labels.kg')</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('body_fat', trans('labels.body_fat'), ['class' => 'control-label col-md-6']) !!}
                            <span class="col-md-1" style="font-size: 22px;">:</span>
                            <div class="col-md-5">
                                <div class="input-group">
                                    {!! Form::text('body_fat', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                        {{--<div class="form-group">
                            {!! Form::label('body_fat_left_arm', trans('labels.left_arm'), ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                <div class="input-group">
                                    {!! Form::text('body_fat_left_arm', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                            {!! Form::label('body_fat_right_arm', trans('labels.right_arm'), ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                <div class="input-group">
                                    {!! Form::text('body_fat_right_arm', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('body_fat_left_leg', trans('labels.left_leg'), ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                <div class="input-group">
                                    {!! Form::text('body_fat_left_leg', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                            {!! Form::label('body_fat_right_leg', trans('labels.right_leg'), ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                <div class="input-group">
                                    {!! Form::text('body_fat_right_leg', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>--}}

                        <hr style="margin-bottom: 15px; border-top: 2px dashed #79538f;"/>
                        <div class="form-group">
                            {!! Form::label('muscle_mass', trans('labels.muscle_mass'), ['class' => 'control-label col-md-6']) !!}
                            <span class="col-md-1" style="font-size: 22px;">:</span>
                            <div class="col-md-5">
                                <div class="input-group">
                                    {!! Form::text('muscle_mass', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">@lang('labels.kg')</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('bone_mass', trans('labels.bone_mass'), ['class' => 'control-label col-md-6']) !!}
                            <span class="col-md-1" style="font-size: 22px;">:</span>
                            <div class="col-md-5">
                                <div class="input-group">
                                    {!! Form::text('bone_mass', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">@lang('labels.kg')</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('bmi', trans('labels.bmi'), ['class' => 'control-label col-md-6']) !!}
                            <span class="col-md-1" style="font-size: 22px;">:</span>
                            <div class="col-md-5">
                                <div class="input-group">
                                    {!! Form::text('bmi', null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('dci', trans('labels.dci'), ['class' => 'control-label col-md-6']) !!}
                            <span class="col-md-1" style="font-size: 22px;">:</span>
                            <div class="col-md-5">
                                <div class="input-group">
                                    {!! Form::text('dci', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">@lang('labels.cal')</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('metabolic', trans('labels.metabolic'), ['class' => 'control-label col-md-6']) !!}
                            <span class="col-md-1" style="font-size: 22px;">:</span>
                            <div class="col-md-5">
                                <div class="input-group">
                                    {!! Form::text('metabolic', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">@lang('labels.years_old')</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('body_water', trans('labels.body_water'), ['class' => 'control-label col-md-6']) !!}
                            <span class="col-md-1" style="font-size: 22px;">:</span>
                            <div class="col-md-5">
                                <div class="input-group">
                                    {!! Form::text('body_water', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('visceral', trans('labels.visceral'), ['class' => 'control-label col-md-6']) !!}
                            <span class="col-md-1" style="font-size: 22px;">:</span>
                            <div class="col-md-5">
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
                            {!! Form::label('chest', trans('labels.chest'), ['class' => 'control-label col-md-6']) !!}
                            <span class="col-md-1" style="font-size: 22px;">:</span>
                            <div class="col-md-5">
                                <div class="input-group">
                                    {!! Form::text('chest', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">@lang('labels.cm')</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('waist', trans('labels.waist'), ['class' => 'control-label col-md-6']) !!}
                            <span class="col-md-1" style="font-size: 22px;">:</span>
                            <div class="col-md-5">
                                <div class="input-group">
                                    {!! Form::text('waist', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">@lang('labels.cm')</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('abdomen', trans('labels.abdomen'), ['class' => 'control-label col-md-6']) !!}
                            <span class="col-md-1" style="font-size: 22px;">:</span>
                            <div class="col-md-5">
                                <div class="input-group">
                                    {!! Form::text('abdomen', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">@lang('labels.cm')</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('hip', trans('labels.hip'), ['class' => 'control-label col-md-6']) !!}
                            <span class="col-md-1" style="font-size: 22px;">:</span>
                            <div class="col-md-5">
                                <div class="input-group">
                                    {!! Form::text('hip', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">@lang('labels.cm')</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('thigh', trans('labels.thigh'), ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                {!! Form::text('thigh', null, ['class' => 'form-control']) !!}
                            </div>
                            {!! Form::label('total_thigh', trans('&times;2'), ['class' => 'control-label col-md-1']) !!}
                            <div class="col-md-5">
                                <div class="input-group">
                                    {!! Form::text('total_thigh', null, ['class' => 'form-control', 'readonly']) !!}
                                    <span class="input-group-addon">@lang('labels.cm')</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('arm', trans('labels.arm'), ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                {!! Form::text('arm', null, ['class' => 'form-control']) !!}
                            </div>
                            {!! Form::label('total_arm', trans('&times;2'), ['class' => 'control-label col-md-1']) !!}
                            <div class="col-md-5">
                                <div class="input-group">
                                    {!! Form::text('total_arm', null, ['class' => 'form-control', 'readonly']) !!}
                                    <span class="input-group-addon">@lang('labels.cm')</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{--<div class="panel-footer">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('total_measurement', trans('labels.total'), ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <div class="input-group">
                                    {!! Form::text('total_measurement', null, ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">@lang('labels.cm')</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>--}}
            </div>
        </div>
        <div class="col-md-4">
            <div class="panel panel-default">
                <div class="panel-heading">@lang('labels.workout_history')</div>
                <div class="panel-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('three_times_a_week', trans('labels.three_times_a_week'), ['class' => 'control-label col-md-7']) !!}
                            <div class="col-md-5">
                                @if($total_wo_monthly < 12)
                                    @php($wo_his_no = 'checked')
                                    @php($wo_his_yes = 'disabled')
                                @else
                                    @php($wo_his_yes = 'checked')
                                    @php($wo_his_no = 'disabled')
                                @endif
                                <label class="radio-inline">
                                    <input type="radio" name="three_times_a_week" id="ttaw-1"
                                           value="Yes" {{$wo_his_yes}}> @lang('labels.yes')
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" name="three_times_a_week" id="ttaw-2" value="No"
                                            {{$wo_his_no}}> @lang('labels.no')
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('total_wo_per_month', trans('labels.total_wo_per_month'), ['class' => 'control-label col-md-7']) !!}
                            <div class="col-md-5">
                                <div class="input-group">
                                    {!! Form::text('total_wo_per_month', $total_wo_monthly, ['class' => 'form-control', 'readonly']) !!}
                                    <span class="input-group-addon">&times;</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="panel panel-success">
                <div class="panel-heading">@lang('labels.recommendation'):</div>
                <div class="panel-body">
                    <div class="checkbox">
                        <label>
                            {!! Form::textarea('recommendation', null, ['class' => 'form-control']) !!}
                        </label>
                    </div>
                    <hr>
                    <div class="checkbox">
                        <label>
                            {!! Form::checkbox('pro_workout', 'pro_workout') !!} @lang('labels.profesional_workout')
                        </label>
                    </div>
                    <hr>
                    @foreach($wm_attributes as $key => $value)
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('wm_attribute[]', $key) !!} {{$value}}
                            </label>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-primary">
                <div class="panel-heading">@lang('labels.next_weigh_n_measure')</div>
                <div class="panel-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <div class="col-md-6">
                                <div class="checkbox">
                                    <label>
                                        {!! Form::checkbox('set_next_wm', 'set_next_wm', false, ['id' => 'set_next_wm_toggle']) !!} @lang('labels.set_next_wm')
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('next_wm_date', trans('labels.date'), ['class' => 'control-label col-md-2']) !!}
                            <div class="col-md-4">
                                {!! Form::date('next_wm_date', \Carbon\Carbon::today()->addMonth(1), ['class' => 'form-control', 'disabled']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('next_wm_hour', trans('labels.hour'), ['class' => 'control-label col-md-2']) !!}
                            <div class="col-md-4">
                                {!! Form::text('next_wm_hour', null, ['class' => 'form-control', 'disabled']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-offset-6 col-md-6">
            <div class="form-horizontal pull-right">
                <div class="form-group">
                    {!! Form::label('measured_by', trans('labels.measurer').': ', ['class' => 'control-label col-md-4']) !!}
                    <div class="col-md-8">
                        @if(!empty(Session::get('staff_session')))
                            {!! Form::select('measured_by', $measurers, Session::get('staff_session'), ['class' => 'form-control']) !!}
                        @else
                            {!! Form::select('measured_by', $measurers, Auth::id(), ['class' => 'form-control']) !!}
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary btn-lg pull-right']) !!}
            </div>
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@section('other-script')
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script>
        $('#measured_by').select2();
    </script>
    <script>
        $(document).ready(function () {
            $('input[name="next_wm_hour"]').datetimepicker({
                format: 'HH:mm'
            });

            $('input[name="thigh"]').on('keyup', function () {
                var total_thigh = parseFloat($(this).val()) * 2;
                $('input[name="total_thigh"]').val(total_thigh);
            });

            $('input[name="arm"]').on('keyup', function () {
                var total_arm = parseFloat($(this).val()) * 2;
                $('input[name="total_arm"]').val(total_arm);
            });

            if ($('#set_next_wm_toggle').prop('checked') == true) {
                $('input[name="next_wm_date"]').prop('disabled', false);
                $('input[name="next_wm_hour"]').prop('disabled', false);
            }

            $('#set_next_wm_toggle').on('click', function () {
                if ($(this).prop('checked') == true) {
                    $('input[name="next_wm_date"]').prop('disabled', false);
                    $('input[name="next_wm_hour"]').prop('disabled', false);
                } else {
                    $('input[name="next_wm_date"]').prop('disabled', true);
                    $('input[name="next_wm_hour"]').prop('disabled', true);
                }
            });
        });
    </script>
@endsection