@extends('cf_lite.layouts.staff-template')

@section('other-style')
@endsection

@section('content')
    @include('cf_lite.includes.error-message')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <center>
                <h2 class="page-title">@lang('labels.weigh_n_measure')</h2>
            </center>
        </div>
    </div>
    @php
        $member = $wm->weighmeasuregoal_member;
    @endphp
    <div class="row">
        {{--<a href="{{url('cf-lite/weigh-measure/'.$wm->id.'/printout')}}" class="btn btn-primary btn-lg" style="position: absolute; top: 50px; right: 30px">
            <span class="fa fa-print"></span>
        </a>--}}
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
    <div class="row">
        <div class="col-md-3">
            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <td><span class="fa fa-calendar" title="@lang('labels.date')"></span>: </td>
                        <td>{{date('d F Y', strtotime($wm->set_goal_date))}}</td>
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
                    <table>
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

        <div class="col-md-8">
            <div class="row">
                <div class="col-md-6">
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
                                    <td>{{$wm->thigh}} &times;2 = {{$wm->thigh * 2}}</td>
                                    <td>@lang('labels.cm')</td>
                                </tr>
                                <tr>
                                    <td>@lang('labels.arm')</td>
                                    <td>:</td>
                                    <td>{{$wm->arm}} &times;2 = {{$wm->arm * 2}}</td>
                                    <td>@lang('labels.cm')</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">@lang('labels.xfactor')</div>
                        <div class="panel-body">
                            @if(count($wm->weighmeasuregoal_member->member_xfactor) > 0)
                                <ol>
                                    @foreach($wm->weighmeasuregoal_member->member_xfactor as $xfactor)
                                        <li>{{$xfactor->xfactor}}</li>
                                    @endforeach
                                </ol>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('other-script')
@endsection