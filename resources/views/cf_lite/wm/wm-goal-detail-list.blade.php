@extends('cf_lite.layouts.staff-template')

@section('other-style')
@endsection

@section('content')
    @include('cf_lite.includes.error-message')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <center>
                <h2 class="page-title">@lang('labels.weigh_n_measure_goal_history')</h2>
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
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>@lang('labels.indexing')</th>
                            <th>@lang('labels.weigh_n_measure_date')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($indexing = 1)
                        @foreach($wm_list as $wm)
                            <tr>
                                <td>{{$indexing}}</td>
                                <td><a href="{{url('cf-lite/weigh-measure/'.$wm->id.'/detail/goal')}}">{{date('d F Y', strtotime($wm->set_goal_date))}}</a></td>
                            </tr>
                            @php($indexing++)
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('other-script')
@endsection