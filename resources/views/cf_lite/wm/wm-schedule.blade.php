@extends('cf_lite.layouts.staff-template')

@section('other-style')
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/weigh-measure')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.weigh_n_measure_schedule')</h2>
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
                    <td><span class="fa fa-group" title="@lang('labels.membership_type')"></span></td>
                    <td>:</td>
                    <td>
                        {{$member->user_memberstatus->memberstatus_membershiptype->membership_type_name}}
                        <br>
                        ({{date('d F Y', strtotime($member->user_memberstatus->from_date))}} - {{date('d F Y', strtotime($member->user_memberstatus->thru_date))}})
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @if($member->user_memberstatus->wm_schedule == 'NotSet')
                {!! Form::open(['method' => 'POST', 'action' => ['WeighAndMeasureController@store_wm_schedule', $member->id]]) !!}
            @elseif($member->user_memberstatus->wm_schedule == 'Set')
                {!! Form::open(['method' => 'PATCH', 'action' => ['WeighAndMeasureController@update_wm_schedule', $member->id]]) !!}
            @endif
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <td></td>
                            <td>@lang('labels.month')</td>
                            <td>@lang('labels.date')</td>
                            <td>@lang('labels.status')</td>
                        </tr>
                    </thead>
                    <tbody>
                        @if($member->user_memberstatus->wm_schedule == 'NotSet')
                            @php
                                $date_join = \Carbon\Carbon::parse($member->user_memberstatus->from_date);

                                //FWO date Added 03042018
                                if (!empty($workouts)):
                                echo $workouts->workout_date;
                                    $date_join = \Carbon\Carbon::parse($workouts->workout_date);
                                endif;

                                $diff_months = $date_join->diffInMonths(\Carbon\Carbon::now());
                            @endphp
                            @if(count($wm_appts) == 0)
                                @for($i = $diff_months; $i < $member->user_memberstatus->memberstatus_membershiptype->duration; $i++)
                                    <tr>
                                        <td>{!! Form::checkbox('check', 'check', true, ['disabled']) !!}</td>
                                        {{--<td>{{\Carbon\Carbon::parse($member->user_memberstatus->from_date)->addMonths($i + 1)->format('F')}}</td>--}}
                                        {{--<td>{!! Form::date('wm_date['.$i.']', \Carbon\Carbon::parse($member->user_memberstatus->from_date)->addMonths($i + 1), ['class' => 'form-control']) !!}</td>--}}
                                        <td>{{\Carbon\Carbon::parse($date_join)->addMonths($i + 1)->format('F')}}</td>
                                        <td>{!! Form::date('wm_date['.$i.']', \Carbon\Carbon::parse($date_join)->addMonths($i + 1), ['class' => 'form-control']) !!}</td>
                                        <td>@lang('labels.not_set')</td>
                                    </tr>
                                @endfor
                            //Added 16042018
                            @else
                                @for($i = ($diff_months + count($wm_appts)); $i < $member->user_memberstatus->memberstatus_membershiptype->duration; $i++)
                                    <tr>
                                        <td>{!! Form::checkbox('check', 'check', true, ['disabled']) !!}</td>
                                        {{--<td>{{\Carbon\Carbon::parse($member->user_memberstatus->from_date)->addMonths($i + 1)->format('F')}}</td>--}}
                                        {{--<td>{!! Form::date('wm_date['.$i.']', \Carbon\Carbon::parse($member->user_memberstatus->from_date)->addMonths($i + 1), ['class' => 'form-control']) !!}</td>--}}
                                        <td>{{\Carbon\Carbon::parse($date_join)->addMonths($i + 1)->format('F')}}</td>
                                        <td>{!! Form::date('wm_date['.$i.']', \Carbon\Carbon::parse($date_join)->addMonths($i + 1), ['class' => 'form-control']) !!}</td>
                                        <td>@lang('labels.not_set')</td>
                                    </tr>
                                @endfor
                            @endif
                        @elseif($member->user_memberstatus->wm_schedule == 'Set')
                            @if(count($wm_appts) > 0)
                                @foreach($wm_appts as $wm_appt)
                                    <tr>
                                        <td>{!! Form::checkbox('sch['.$wm_appt->id.']', $wm_appt->id, false) !!}</td>
                                        <td>{{\Carbon\Carbon::parse($wm_appt->app_date)->format('F')}}</td>
                                        <td>{!! Form::date('wm_date['.$wm_appt->id.']', \Carbon\Carbon::parse($wm_appt->app_date), ['class' => 'form-control']) !!}</td>
                                        <td>@lang('labels.scheduled')</td>
                                    </tr>
                                @endforeach
                            @endif
                        @endif
                    </tbody>
                </table>
            </div>

            @if($member->user_memberstatus->wm_schedule == 'NotSet')
                {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary pull-right']) !!}
            @elseif($member->user_memberstatus->wm_schedule == 'Set')
                {!! Form::submit(trans('labels.update'), ['class' => 'btn btn-primary pull-right']) !!}
            @endif
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('other-script')
@endsection