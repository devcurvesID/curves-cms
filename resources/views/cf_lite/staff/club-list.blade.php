@extends('cf_lite.layouts.staff-template')

@section('other-style')
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/settings')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.choose_club')</h2>
            </center>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div id="filter-zone">
                {!! Form::open(['method' => 'GET', 'action' => 'MemberController@member_list_search']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
    <div class="row">
        @if(count($clubs) > 0)
            @foreach($clubs as $club)
                @php($club_gate = true)
                @if(\Illuminate\Support\Facades\Auth::user()->user_club->jubilant_id != $club->jubilant_id)
                    @if(\Illuminate\Support\Facades\Auth::user()->user_club->club_jubilant->type == 'Franchise')
                        @php($club_gate = false)
                    @endif
                @endif

            @if($club_gate)
                <div class="col-md-3">
                    <a href="{{url('cf-lite/staff/'.$club->id)}}">
                        <div class="thumbnail">
                            @if(!empty($club->photo))
                                <img src="{{asset('images/club_photos/'.$club->photo)}}" class="img-rounded" style="width: 100%; height: 200px; object-fit: cover;">
                            @else
                                <img src="{{asset('images/club.png')}}" class="img-rounded" style="width: 100%; height: 200px; object-fit: cover;">
                            @endif
                            <h4>{{$club->club_name}}</h4>
                            <h5>({{ucwords($club->city).', '.ucwords($club->province)}})</h5>
                        </div>
                    </a>
                </div>
            @endif
            @endforeach
        @endif
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right paging">
                {{$clubs->links()}}
            </div>
        </div>
    </div>
    @if(\Illuminate\Support\Facades\Auth::user()->role_id < 4)
        <hr>
        <div class="row">
            <h3>@lang('labels.club_manager_list')</h3>
            @if(count($managers) > 0)
                @foreach($managers as $manager)
                    <div class="col-md-3">
                        <a href="{{url('cf-lite/staff/'.$manager->id.'/manager-club-setting')}}">
                            <div class="thumbnail" style="height: 300px;">
                                @if(!empty($manager->user_memberpersonal->photo))
                                    <img src="{{asset('images/member_photos/'.$manager->user_memberpersonal->photo)}}" class="img-rounded" style="width: 100%; height: 200px; object-fit: cover;">
                                @else
                                    <img src="{{asset('images/img.png')}}" class="img-rounded" style="width: 100%; height: 200px; object-fit: cover;">
                                @endif
                                <h4>{{$manager->name}}</h4>
                                <h5>({{ucwords($manager->user_club->club_name)}})</h5>
                                @if(count($manager->manager_club) > 0)
                                    <h5>@lang('labels.see_more') <span class="fa fa-chevron-right"></span></h5>
                                @endif
                            </div>
                        </a>
                    </div>
                @endforeach
            @endif
        </div>
    @endif
@endsection

@section('other-script')
@endsection