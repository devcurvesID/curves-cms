@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <style>
        .col-sm-12 .button-purple:hover {
            background-color: rebeccapurple!important;
            border-color: rebeccapurple!important;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            @if(!empty(Session::get('staff_club_choose')) && (Session::get('staff_club_choose') == 'Yes'))
                <a href="{{url('cf-lite/staff')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            @else
                <a href="{{url('cf-lite/settings')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            @endif

            {{--Reactivate Button Added 10042018--}}
                @php($staff_management_link = '')
                @foreach($access_list->where('key', 'setting_staff_management') as $key => $value)
                    @if($loop->last)
                        @php($unlock = 0)
                        @foreach(Auth::user()->user_accessmenu as $access)
                            @if($access->id == $value['id'])
                                @php($unlock = 1)
                            @endif
                        @endforeach
                        @if($unlock == 1)
                            @php($staff_management_link = 'cf-lite/staff/'.$club->id.'/inactive-staff-list')
                        @endif
                    @endif
                @endforeach

                @if(!empty($staff_management_link))
                    <a href="{{url($staff_management_link)}}" class="btn btn-default btn-back-to button-purple" title="@lang('labels.inactive_staff_list')" style="right: 0!important; left: unset!important; border-color: rebeccapurple;"><span class="fa fa-retweet" style="margin-top: 10px; color: rebeccapurple;"></span></a>
                @endif
            <center>
                <h2 class="page-title">@lang('labels.staff_list')<br><small>{{$club->club_name}} - {{ucwords($club->city)}}</small></h2>
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
        @if(count($staffs) > 0)
            @foreach($staffs as $staff)
                <div class="col-md-3">
                    <a href="{{url('cf-lite/staff/'.$staff->id.'/detail')}}">
                        <div class="thumbnail">
                            @if(!empty($staff->user_memberpersonal->photo))
                                <img src="{{asset('images/member_photos/'.$staff->user_memberpersonal->photo)}}" class="img-rounded" style="width: 100%; height: 200px; object-fit: cover;">
                            @else
                                <img src="{{asset('images/img.png')}}" class="img-rounded" style="width: 100%; height: 200px; object-fit: cover;">
                            @endif
                            <h4>{{$staff->name}}</h4>
                            <h5>(@lang('labels.role'): {{$staff->user_role->role}})</h5>
                        </div>
                    </a>
                </div>
            @endforeach
        @endif
            <div class="col-md-3">
                <a href="{{url('cf-lite/staff/'.$club->id.'/add')}}">
                    <div class="thumbnail" title="@lang('labels.add') @lang('labels.staff')">
                        <div style="width: 100%; height: 200px;">
                            <center>
                                <span class="fa fa-plus" style="font-size: 60px;"></span>
                            </center>
                        </div>
                    </div>
                </a>
            </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="pull-right paging">
                {{$staffs->links()}}
            </div>
        </div>
    </div>
@endsection

@section('other-script')
@endsection