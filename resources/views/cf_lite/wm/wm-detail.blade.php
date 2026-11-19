@extends('cf_lite.layouts.staff-template')

@section('other-style')

    <style>

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
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <td>
                            <table>
                                <tr>
                                    <td><span class="fa fa-calendar" title="@lang('labels.date')"></span></td>
                                    <td>:</td>
                                    <td>{{date('d F Y', strtotime($wm->wm_date))}}</td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td>@lang('labels.age')</td>
                                    <td>:</td>
                                    <td style=" font-size: 40px; color: #715c94; font-weight: 600;">{{$wm->age}}</td>
                                    <td>@lang('labels.years_old')</td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td>@lang('labels.height')</td>
                                    <td>:</td>
                                    <td style="font-size: 40px; color: #715c94; font-weight: 600;">{{$wm->height}}</td>
                                    <td>@lang('labels.cm')</td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td>@lang('labels.bp')</td>
                                    <td>:</td>
                                    <td style=" font-size: 40px; color: #715c94; font-weight: 600;">{{$wm->bp_high}}</td>
                                    <td> /</td>
                                    <td style=" font-size: 40px; color: #715c94; font-weight: 600;">{{$wm->bp_low}}</td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <table>
                                <tr>
                                    <td>@lang('labels.rhr')</td>
                                    <td>:</td>
                                    <td style=" font-size: 40px; color: #715c94; font-weight: 600;">{{$wm->rhr}}</td>
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
                <div class="col-md-6 detail-komposisi-tubuh">
                    <div class="panel panel-info">
                        <div class="panel-heading">@lang('labels.body_composition')</div>
                        <div class="panel-body">
                            <table class="detail-table">
                                <tr>
                                    <td>@lang('labels.weight')</td>
                                    <td>:</td>
                                    <td>{{$wm->weight}}</td>
                                    <td>@lang('labels.kg')</td>
                                </tr>
                                <tr>
                                    <td>@lang('labels.body_fat')</td>
                                    <td>:</td>
                                    <td>{{$wm->body_fat}}</td>
                                    <td>%</td>
                                </tr>
                            </table>
                            <hr>
                            <table>
                                <tr>
                                    <td>@lang('labels.muscle_mass')</td>
                                    <td>:</td>
                                    <td>{{$wm->muscle_mass}}</td>
                                    <td>@lang('labels.kg')</td>
                                </tr>
                                <tr>
                                    <td>@lang('labels.bone_mass')</td>
                                    <td>:</td>
                                    <td>{{$wm->bone_mass}}</td>
                                    <td>@lang('labels.kg')</td>
                                </tr>
                                <tr>
                                    <td>@lang('labels.bmi')</td>
                                    <td>:</td>
                                    <td>{{$wm->bmi}}</td>
                                    <td></td>
                                </tr>
                                <tr>
                                    <td>@lang('labels.dci')</td>
                                    <td>:</td>
                                    <td>{{$wm->dci}}</td>
                                    <td>@lang('labels.cal')</td>
                                </tr>
                                <tr>
                                    <td>@lang('labels.metabolic')</td>
                                    <td>:</td>
                                    <td>{{$wm->metabolic}}</td>
                                    <td>@lang('labels.years_old')</td>
                                </tr>
                                <tr>
                                    <td>@lang('labels.body_water')</td>
                                    <td>:</td>
                                    <td>{{$wm->body_water}}</td>
                                    <td>%</td>
                                </tr>
                                <tr>
                                    <td>@lang('labels.visceral')</td>
                                    <td>:</td>
                                    <td>{{$wm->visceral}}</td>
                                    <td></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 detail-ukuran">
                    <div class="panel panel-warning">
                        <div class="panel-heading">@lang('labels.size')</div>
                        <div class="panel-body">
                            <table>
                                <tr>
                                    <td>@lang('labels.chest')</td>
                                    <td>:</td>
                                    <td>{{$wm->chest}}</td>
                                    <td>@lang('labels.cm')</td>
                                </tr>
                                <tr>
                                    <td>@lang('labels.waist')</td>
                                    <td>:</td>
                                    <td>{{$wm->waist}}</td>
                                    <td>@lang('labels.cm')</td>
                                </tr>
                                <tr>
                                    <td>@lang('labels.abdomen')</td>
                                    <td>:</td>
                                    <td>{{$wm->abdomen}}</td>
                                    <td>@lang('labels.cm')</td>
                                </tr>
                                <tr>
                                    <td>@lang('labels.hip')</td>
                                    <td>:</td>
                                    <td>{{$wm->hip}}</td>
                                    <td>@lang('labels.cm')</td>
                                </tr>
                                <tr>
                                    <td>@lang('labels.thigh')</td>
                                    <td>:</td>
                                    <td>{{$wm->thigh}} <span>&times;2 =</span> {{$wm->thigh * 2}}</td>
                                    <td>@lang('labels.cm')</td>
                                </tr>
                                <tr>
                                    <td>@lang('labels.arm')</td>
                                    <td>:</td>
                                    <td>{{$wm->arm}} <span>&times;2 =</span> {{$wm->arm * 2}}</td>
                                    <td>@lang('labels.cm')</td>
                                </tr>
                                <tr>
                                    <td>@lang('labels.total')</td>
                                    <td>:</td>
                                    <td>{{$wm->total_measurement}}</td>
                                    <td>@lang('labels.cm')</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td>&nbsp;</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="col-md-12 detail-result">
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
                                        <td>{{$wm->weight - $prev_weight}} <span>@lang('labels.kg')</span></td>
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
                                        <td>{{$wm->total_measurement - $prev_totalmeasurement}}
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
                                        <td>{{$wm->body_fat - $prev_bodyfat}} <span>%</span></td>
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
                                        <td>{{$wm->total_hydration - $prev_totalhydration}} <span>%</span></td>
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
            <div class="col-md-12 detail-riwayat-latihan">
                <div class="row">
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
                </div>
            </div>

            <div class="col-md-12 detail-rekomendasi">
                <div class="row">
                    <div class="panel panel-primary">
                        <div class="panel-heading">@lang('labels.recommendation')</div>
                        <div class="panel-body">
                            <div style="height: 50px; padding: 10px;">
                                @if($wm->pro_workout == 'Yes')
                                    <span class="fa fa-circle" style="margin-right: 10px;"></span><span
                                            style="font-size: 20px;">@lang('labels.profesional_workout')</span>
                                @else
                                    <span class="fa fa-circle-o" style="margin-right: 10px;"></span><span
                                            style="font-size: 20px;">@lang('labels.profesional_workout')</span>
                                @endif
                            </div>
                            <table>
                                @php($total_attribute = count($wm_attributes))
                                @php($total_row = ceil($total_attribute / 5))
                                @for($i = 0; $i < $total_row; $i++)
                                    @foreach($wm_attributes->slice(($i * 5), ($i + 5)) as $wm_attribute)

                                        @php($indicator = 0)
                                        @foreach($wm->weighmeasure_weighmeasureattribute as $wma_key)
                                            @if($wma_key->id == $wm_attribute->id)
                                                @php($indicator = 1)
                                            @endif
                                        @endforeach
                                        @if($indicator == '1')
                                            <tr>
                                                <td><span class="fa fa-circle"></span> {{$wm_attribute->attribute_name}}
                                                </td>
                                            </tr>
                                        @else
                                            <tr>
                                                <td>
                                                    <span class="fa fa-circle-o"></span> {{$wm_attribute->attribute_name}}
                                                </td>
                                            </tr>
                                        @endif
                                    @endforeach
                                @endfor
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel panel-nasihat">
                <div style="padding: 0px 15px;">
                    <h4 style="color: #dc3c91; font-size: 24px; font-weight: 600;">@lang('labels.advice') :</h4>
                </div>
                <div class="panel-body">
                {!! nl2br(e($wm->recommendation)) !!}
                </div>
            </div>

        </div>

    </div>

@endsection

@section('other-script')
@endsection