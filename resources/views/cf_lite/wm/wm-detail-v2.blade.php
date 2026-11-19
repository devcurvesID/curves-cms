@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <style>
        .select2 {
            width: 100% !important;
            float: right;
        }

        .profile {
            margin-top: 17px;
        }

        .profile table td:first-child,
        table.table table td:first-child {
            color: #ff1493;
        }

        .profile table td {
            height: 30px;
            padding: 10px;
            font-size: 20px;
        }

        table.table table td {
            height: 84px;
            padding: 10px;
            font-size: 20px;
        }

        .detail-komposisi-tubuh td {
            height: 50px;
            padding: 10px;
        }

        .detail-komposisi-tubuh td:first-child {
            padding-left: 0;
            font-size: 20px;
            font-weight: 600;
            width: 230px;
        }

        .detail-komposisi-tubuh td:nth-child(2),
        .detail-ukuran td:nth-child(2) {
            font-size: 20px;
            width: 15px;
        }

        .detail-komposisi-tubuh td:nth-child(3) {
            font-size: 40px;
            color: #6752a5;
            border-bottom: 2px solid #b74c7e;
            width: 60px;
            text-align: right;
            padding-right: 10px;
        }

        .detail-komposisi-tubuh td:nth-child(4),
        .detail-ukuran td:nth-child(4) {
            font-size: 20px;
            font-weight: 600;
        }

        .detail-ukuran td {
            height: 61px;
            padding: 10px;
        }

        .detail-ukuran td:first-child {
            padding-left: 0;
            font-size: 20px;
            font-weight: 600;
            width: 190px;
        }

        .detail-ukuran td:nth-child(3) {
            font-size: 40px;
            color: #6752a5;
            border-bottom: 2px solid #b74c7e;
            width: 150px;
            text-align: right;
            padding-right: 10px;
        }

        .detail-ukuran td span {
            font-size: 20px;
            color: #414042;
        }

        .detail-riwayat-latihan td {
            height: 50px;
            padding: 10px;
        }

        .detail-riwayat-latihan td span {
            margin-right: 10px;
        }

        .detail-riwayat-latihan td:first-child {
            padding-left: 0;
            font-size: 20px;
            font-weight: 600;
            width: 235px;
        }

        .detail-riwayat-latihan td:nth-child(2) {
            font-size: 20px;
            width: 7px;
        }

        .detail-riwayat-latihan td:nth-child(3),
        .detail-riwayat-latihan td:nth-child(4) {
            font-weight: 600;
        }

        .detail-rekomendasi td {
            height: 50px;
            padding: 10px;
        }

        .detail-rekomendasi .panel-primary {
            height: auto;
        }

        .detail-result th {
            color: #dc3c91;
            font-size: 20px;
        }

        .detail-result td {
            height: 50px;
            padding: 10px !important;
        }

        .detail-result td:first-child {
            padding-left: 0px !important;
            font-size: 20px;
            font-weight: 600;
        }

        .detail-result td:nth-child(2),
        .detail-result td:nth-child(3),
        .detail-result td:nth-child(4) {
            font-size: 35px;
            color: #6752a5;
            padding-right: 10px;
        }

        .detail-result td:nth-child(2) span,
        .detail-result td:nth-child(3) span,
        .detail-result td:nth-child(4) span {
            font-size: 20px;
            font-weight: 600;
            color: #414042;
        }

        #print-btn, #print-btn:link {
            position: absolute;
            top: 135px;
            right: 50px;
            color: rebeccapurple;
            background-color: transparent;
            border: 2px solid rebeccapurple;
        }

        #print-btn:hover {
            color: #fff;
            background-color: rebeccapurple;
        }

    </style>


@endsection

@section('content')
    @include('cf_lite.includes.error-message')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/weigh-measure/'.$wm->weighmeasure_member->id.'/history')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.weigh_n_measure')</h2>
            </center>
        </div>
    </div>
    @php
        $member = $wm->weighmeasure_member;
    @endphp
    <div class="row">
        <a href="{{url('cf-lite/weigh-measure/'.$wm->id.'/printout')}}" class="btn btn-primary btn-lg" id="print-btn" title="@lang('labels.print_preview')">
            <span class="fa fa-print"></span> <span class="fa fa-eye"></span>
        </a>
        <div class="col-md-2 col-md-offset-1">
            @if(!empty($member->user_memberpersonal->photo))
                <img class="img-circle member-card-img"
                     src="{{asset('images/member_photos/'.$member->user_memberpersonal->photo)}}"
                     style="width: 150px; height: 150px; object-fit: cover;">
            @else
                <img class="img-circle member-card-img" src="{{asset('images/img.png')}}">
            @endif
        </div>
        <div class="col-md-9 profile">
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
    {!! Form::model($wm, ['method' => 'PATCH', 'action' => ['WeighAndMeasureController@update_wm_data', $wm->id]]) !!}
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
                                    <td>{!! Form::date('wm_date', \Carbon\Carbon::parse($wm->wm_date), ['class' => 'form-control']) !!}</td>
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
                                    <td width="30%">{!! Form::text('height', null, ['class' => 'form-control']) !!}</td>
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
        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6">
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

                <div class="col-md-6">
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
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-success">
                        <div class="panel-heading">@lang('labels.result')</div>
                        <div class="panel-body">
                            <div class="table-responsive">
                                <table class="table table-condensed">
                                    <thead>
                                    <tr>
                                        <th>@lang('labels.category')</th>
                                        <th>@lang('labels.last_measurement')</th>
                                        <th>@lang('labels.current_measurement')</th>
                                        <th>@lang('labels.difference')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <tr>
                                        <td>@lang('labels.weight')</td>
                                        <td>
                                            @if(!empty($prev_wm->weight))
                                                @php($prev_weight = $prev_wm->weight)
                                                {{$prev_wm->weight}} <span>@lang('labels.kg')</span>
                                            @else
                                                @php($prev_weight = 0)
                                                -
                                            @endif
                                        </td>
                                        <td>{{$wm->weight}} <span>@lang('labels.kg')</span></td>
                                        <td>{{number_format(($wm->weight - $prev_weight), 1)}} <span>@lang('labels.kg')</span></td>
                                    </tr>
                                    <tr>
                                        <td>@lang('labels.total_measurement')</td>
                                        <td>
                                            @if(!empty($prev_wm->total_measurement))
                                                @php($prev_totalmeasurement = $prev_wm->total_measurement)
                                                {{$prev_wm->total_measurement}} <span>@lang('labels.cm')</span>
                                            @else
                                                @php($prev_totalmeasurement = 0)
                                                -
                                            @endif
                                        </td>
                                        <td>{{$wm->total_measurement}} <span>@lang('labels.cm')</span></td>
                                        <td>{{number_format(($wm->total_measurement - $prev_totalmeasurement), 1)}}
                                            <span>@lang('labels.cm')</span></td>
                                    </tr>
                                    <tr>
                                        <td>@lang('labels.body_fat')</td>
                                        <td>
                                            @if(!empty($prev_wm->body_fat))
                                                @php($prev_bodyfat = $prev_wm->body_fat)
                                                {{$prev_wm->body_fat}} <span>%</span>
                                            @else
                                                @php($prev_bodyfat = 0)
                                                -
                                            @endif
                                        </td>
                                        <td>{{$wm->body_fat}} <span>%</span></td>
                                        <td>{{number_format(($wm->body_fat - $prev_bodyfat), 1)}} <span>%</span></td>
                                    </tr>
                                    <tr>
                                        <td>@lang('labels.body_water')</td>
                                        <td>
                                            @if(!empty($prev_wm->total_hydration))
                                                @php($prev_totalhydration = $prev_wm->total_hydration)
                                                {{$prev_wm->total_hydration}} <span>%</span>
                                            @else
                                                @php($prev_totalhydration = 0)
                                                -
                                            @endif
                                        </td>
                                        <td>{{$wm->total_hydration}} <span>%</span></td>
                                        <td>{{number_format(($wm->total_hydration - $prev_totalhydration), 1)}} <span>%</span></td>
                                    </tr>
                                    </tbody>
                                </table>
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
                    <table>
                        <tr>
                            <td>@lang('labels.three_times_a_week')</td>
                            <td>:</td>
                            <td>
                                @if($wm->three_times_a_week == 'Yes')
                                    <span class="fa fa-circle"></span> @lang('labels.yes')
                                @else
                                    <span class="fa fa-circle-o"></span> @lang('labels.yes')
                                @endif
                            </td>
                            <td>
                                @if($wm->three_times_a_week == 'No')
                                    <span class="fa fa-circle"></span> @lang('labels.no')
                                @else
                                    <span class="fa fa-circle-o"></span> @lang('labels.no')
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>@lang('labels.total_wo_per_month')</td>
                            <td>:</td>
                            <td colspan="2">{{$wm->total_wo_per_month}}</td>
                        </tr>
                    </table>
                    <hr>
                    <table>
                        <tr>
                            <td><strong>@lang('labels.health_history')</strong></td>
                            <td>:</td>
                            <td><strong>@lang('labels.change'):</strong></td>
                        </tr>
                    </table>
                    <hr>
                    <table>
                        <tr>
                            <td><strong>@lang('labels.xfactor')</strong></td>
                            <td>:</td>
                            <td>
                                @if(count($member->member_xfactor) > 0)
                                    <ul>
                                        @foreach($member->member_xfactor as $xfactor)
                                            <li>{{$xfactor->xfactor}}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </td>
                        </tr>
                    </table>
                    <hr>
                    <table>
                        <tr>
                            <td><strong>@lang('labels.members_feeling')</strong></td>
                            <td>:</td>
                            <td>&nbsp;</td>
                        </tr>
                    </table>
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
                            @if($wm->pro_workout == 'No')
                                {!! Form::checkbox('pro_workout', 'pro_workout') !!} @lang('labels.profesional_workout')
                            @elseif($wm->pro_workout == 'Yes')
                                {!! Form::checkbox('pro_workout', 'pro_workout', true) !!} @lang('labels.profesional_workout')
                            @endif
                        </label>
                    </div>
                    <hr>
                    @foreach($wm_attributes as $key => $value)
                        @php($wmattr_open = 0)
                        @foreach($wm->weighmeasure_weighmeasureattribute as $wmattr)
                            @if($key == $wmattr->wm_attribute_id)
                                @php($wmattr_open = 1)
                            @endif
                        @endforeach
                        <div class="checkbox">
                            <label>
                                @if($wmattr_open == 1)
                                    {!! Form::checkbox('wm_attribute[]', $value->id, true) !!} {{$value->attribute_name}}
                                @elseif($wmattr_open == 0)
                                    {!! Form::checkbox('wm_attribute[]', $value->id, false) !!} {{$value->attribute_name}}
                                @endif
                            </label>
                        </div>
                    @endforeach
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
                        {!! Form::select('measured_by', $measurers, null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                {!! Form::submit(trans('labels.save_changing'), ['class' => 'btn btn-primary btn-lg pull-right']) !!}
            </div>
        </div>
    </div>
    {!! Form::close() !!}

@endsection

@section('other-script')
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script>
        $('#measured_by').select2();
    </script>
@endsection