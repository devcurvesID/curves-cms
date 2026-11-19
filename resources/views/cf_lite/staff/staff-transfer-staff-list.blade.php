@extends('cf_lite.layouts.staff-template')

@section('other-style')
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/transfer-staff/transfer-staff-list')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
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
                    <a href="{{url('cf-lite/transfer-staff/'.$staff->id.'/detail')}}">
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