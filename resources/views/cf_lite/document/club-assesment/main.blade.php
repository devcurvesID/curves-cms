@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <style>
        .home-btn-title {
            font-family: 'Alegreya Sans', sans-serif;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/document')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">
                        @lang('labels.club_assessment')
                    </h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                {!! Form::open(['method' => 'GET', 'action' => 'CAController@generate_report', 'class' => 'form-horizontal']) !!}
                <div class="form-group">
                    {!! Form::label('date', trans('labels.choose_date').':  ', ['class' => 'control-label col-md-2']) !!}
                    <div class="col-md-3">
                        {!! Form::date('date', \Carbon\Carbon::now(), ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('membership_target', trans('labels.membership_target').':  ', ['class' => 'control-label col-md-2']) !!}
                    <div class="col-md-3">
                        {!! Form::number('membership_target', '200', ['class' => 'form-control']) !!}
                    </div>
                </div>
                <!-- Attrition Projection Added 24052018 -->
                <div class="form-group">
                    {!! Form::label('attrition_projection', trans('labels.member_attrition_projection').':  ', ['class' => 'control-label col-md-2']) !!}
                    <div class="col-md-3">
                        <div class="input-group">
                            {!! Form::number('attrition_projection', '0', ['class' => 'form-control']) !!}
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">@lang('labels.workout') / @lang('labels.member_experience')</div>
                    <div class="panel-body">
                        <div class="form-group">
                            {!! Form::label('in_hold_target', trans('labels.member_in_hold_target').':  ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-addon"><</span>
                                    {!! Form::number('in_hold_target', '12', ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('const_total_wo_target', trans('labels.total_const_wo_target').':  ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-addon"> > </span>
                                    {!! Form::number('const_total_wo_target', '8', ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">x @lang('labels.TAM')</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('wo_avg_target', trans('labels.average_workout_target').':  ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-addon"> > </span>
                                    {!! Form::number('wo_avg_target', '2.0', ['class' => 'form-control', 'step' => '0.1']) !!}
                                    <span class="input-group-addon">% @lang('labels.TAM')</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('wo_weekly_target', trans('labels.highest_traffic_day_target').' ('.trans('labels.monthly').'):  ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-addon"> > </span>
                                    {!! Form::number('wo_weekly_target', '33', ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">% @lang('labels.TAM')</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('attrition_target', trans('labels.attrition_target').':  ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-addon"> < </span>
                                    {!! Form::number('attrition_target', '4', ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-heading">@lang('labels.member_flag')</div>
                    <div class="panel-body">
                        <div class="form-group">
                            {!! Form::label('flag_a', 'A:  ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-addon"> > </span>
                                    {!! Form::number('flag_a', '30', ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('flag_b', 'B:  ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                <div class="input-group">
                                    {!! Form::number('flag_b', '25', ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('flag_c', 'C:  ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                <div class="input-group">
                                    {!! Form::number('flag_c', '25', ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('flag_d', 'D:  ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                <div class="input-group">
                                    {!! Form::number('flag_d', '10', ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('flag_e', 'E:  ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                <div class="input-group">
                                    {!! Form::number('flag_e', '10', ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">@lang('labels.weigh_n_measure')</div>
                    <div class="panel-body">
                        <div class="form-group">
                            {!! Form::label('wm_target', trans('labels.wm_target').':  ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-addon"> > </span>
                                    {!! Form::number('wm_target', '60', ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">% @lang('labels.TAM')</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">@lang('labels.appointment')</div>
                    <div class="panel-body">
                        <div class="form-group">
                            {!! Form::label('showup_rate_target', trans('labels.showup_rate_target').':  ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-addon"> > </span>
                                    {!! Form::number('showup_rate_target', '80', ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-default">
                    <div class="panel-heading">@lang('labels.sales')</div>
                    <div class="panel-body">
                        <div class="form-group">
                            {!! Form::label('new_sales_target', trans('labels.new_sales_target').':  ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-addon"> > </span>
                                    {!! Form::number('new_sales_target', '85', ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('ad_pp_target', trans('labels.ad_pp_target').':  ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                <div class="input-group">
                                    <span class="input-group-addon"> > </span>
                                    {!! Form::number('ad_pp_target', '90', ['class' => 'form-control']) !!}
                                    <span class="input-group-addon">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <div class="col-md-4">
                        {!! Form::submit(trans('labels.submit'), ['class' => 'btn btn-primary']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </section>
@endsection

@section('other-script')
@endsection