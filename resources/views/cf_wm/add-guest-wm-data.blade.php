@extends('cf_wm.layout.wm-template')

@section('other-style')
    <link href="{{asset('bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">

    <style>
        .btn-back-to {
            left: 25px !important;
            top: 15px !important;
        }

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
            <br/>
            <a href="{{url('cf-wm/wm-member-scan')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title"
                    style="margin-top: 20px; margin-bottom: 40px; font-weight: 600;">@lang('labels.add') @lang('labels.guest_membership') @lang('labels.weigh_n_measure')</h2>
            </center>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="col-md-12 wnmpp">
                <img class="img-circle member-card-img" src="{{asset('images/img.png')}}">
            </div>
        </div>
        <div class="col-md-9">
            <div class="col-md-12">
                <table style="font-size: 30px; margin-top: 30px;">
                    <tr>
                        <td style="height: 50px; color: #dc3c91; text-align: center;"><span class="fa fa-female" title="@lang('labels.name')"></span></td>
                        <td style="width: 50px;text-align: center;">:</td>
                        @if(!empty($guest))
                            <td style="font-weight: 600;">{{$guest->gmmember_guest->fullname}}</td>
                        @endif
                    </tr>
                    <tr>
                        <td style="height: 50px; color: #dc3c91; text-align: center;"><span class="fa fa-calendar" title="@lang('labels.joined')"></span></td>
                        <td style="width: 50px;text-align: center;">:</td>
                        @if(!empty($guest))
                            <td style="font-weight: 600;">{{date('d F Y', strtotime($guest->from_date))}}</td>
                        @endif
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <br>
    @if(!empty($guest))
        {!! Form::open(['method' => 'POST', 'action' => ['WMLiteController@store_guest_add_wm', $guest->id]]) !!}
    @endif
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <td>
                            <table>
                                <tr>
                                    <td style="font-size: 22px">
                                        <span title="@lang('labels.date')">@lang('labels.date')</span><span style="margin: 0 10px;">:</span>
                                    </td>
                                    <td>{!! Form::date('wm_date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}</td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td style="text-align: right; font-size: 22px;">
                                        <span>@lang('labels.age')</span><span style="margin: 0 10px;">:</span>
                                    </td>
                                    <td width="30%">
                                        {!! Form::text('age', (!empty($last_wm->age)) ? $last_wm->age : null, ['class' => 'form-control']) !!}
                                    </td>
                                    <td style="font-size: 22px;">
                                        <span style="margin-left: 10px;">@lang('labels.years_old')</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td style="text-align: right; font-size: 22px;">
                                        @lang('labels.height')<span style="margin: 0 10px;">:</span>
                                    </td>
                                    <td width="30%">
                                        {!! Form::text('height', !empty($last_wm) ? $last_wm->height : null, ['class' => 'form-control']) !!}
                                    </td>
                                    <td style="text-align: left; font-size: 22px;">
                                        <span style="margin-left: 10px;">cm</span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td style="text-align: right; font-size: 22px; width: 22%;">
                                        @lang('labels.bp')<span style="margin: 0 10px;">:</span>
                                    </td>
                                    <td width="18%">{!! Form::text('bp_high', null, ['class' => 'form-control']) !!}</td>
                                    <td style="text-align: left;width: 8%;">
                                        <span style="margin: 0 10px; font-size: 30px;">/</span>
                                    </td>
                                    <td>{!! Form::text('bp_low', null, ['class' => 'form-control']) !!}</td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td style="font-size: 22px;">
                                        @lang('labels.rhr')<span style="margin: 0 10px;">:</span>
                                    </td>
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
                                @if(!empty($guest))
                                    @if($total_wo_monthly < 12)
                                        @php($wo_his_no = 'checked')
                                        @php($wo_his_yes = 'disabled')
                                    @else
                                        @php($wo_his_yes = 'checked')
                                        @php($wo_his_no = 'disabled')
                                    @endif
                                @endif
                                <label class="radio-inline">
                                    @if(!empty($guest))
                                        <input type="radio" name="three_times_a_week" id="ttaw-1"
                                           value="Yes" {{$wo_his_yes}}> @lang('labels.yes')
                                    @endif
                                </label>
                                <label class="radio-inline">
                                    @if(!empty($guest))
                                        <input type="radio" name="three_times_a_week" id="ttaw-2" value="No"
                                            {{$wo_his_no}}> @lang('labels.no')
                                    @endif
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('total_wo_per_month', trans('labels.total_wo_per_month'), ['class' => 'control-label col-md-7']) !!}
                            <div class="col-md-5">
                                <div class="input-group">
                                    @if(!empty($guest))
                                        {!! Form::text('total_wo_per_month', $total_wo_monthly, ['class' => 'form-control', 'readonly']) !!}
                                        <span class="input-group-addon">&times;</span>
                                    @endif
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

    <!-- Modal -->
    <div class="modal fade" id="guestModal" role="dialog" aria-labelledby="addModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'GET', 'id' => 'choose-guest-form']) !!}
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group" style="padding-left: 25px; padding-right: 25px;">
                            {!! Form::label('guest', trans('labels.guest').':', ['class' => 'control-label mb-10 text-left']) !!}
                            <select name="guest" class="form-control select2" id="guest-select">
                                <option value="">@lang('labels.choose') @lang('labels.guest_membership')</option>
                                @foreach($gmm as $gm)
                                    <option value="{{$gm->id}}">{{$gm->gmmember_guest->fullname}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script>
        $('.select2').select2();
        $('#measured_by').select2();
    </script>
    <script type="text/javascript">
        $(window).on('load',function(){
            @if(!empty($guest))
                $('#guestModal').modal('hide');
            @else
                $("#guestModal").modal('show');
            @endif;
            $('#guest-select').val("");
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#guestModal').on('change', "#guest-select", function () {
                $('#choose-guest-form').submit();
            })

            $('input[name="thigh"]').on('keyup', function () {
                var total_thigh = parseFloat($(this).val()) * 2;
                $('input[name="total_thigh"]').val(total_thigh);
            });

            $('input[name="arm"]').on('keyup', function () {
                var total_arm = parseFloat($(this).val()) * 2;
                $('input[name="total_arm"]').val(total_arm);
            });
        });
    </script>
@endsection