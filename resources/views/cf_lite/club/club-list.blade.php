@extends('cf_lite.layouts.staff-template')

@section('other-style')
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/settings')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.club_list')</h2>
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
                {{--Modified 06122019--}}
                <?php
                $club_gate = 1;
                if(Auth::user()->user_club->jubilant_id != $club->jubilant_id):
                    if(Auth::user()->user_club->club_jubilant->type == 'Franchise'):
                        $club_gate = 0;
                    endif;
                endif;
                //echo $club->club_name .' || ';
                if($club_gate > 0):
                ?>
                    <div class="col-md-3">
                        <a href="{{url('cf-lite/club/'.$club->id)}}">
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
                <?php endif; ?>
            @endforeach
        @endif
        <div class="col-md-3">
            <a href="{{url('cf-lite/club/add')}}">
                <div class="thumbnail" title="@lang('labels.add') @lang('labels.club')">
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
                {{$clubs->links()}}
            </div>
        </div>
    </div>
@endsection

@section('other-script')
@endsection
