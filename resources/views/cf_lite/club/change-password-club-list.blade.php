@extends('cf_lite.layouts.staff-template')

@section('other-style')
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/other-settings')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.choose_club')</h2>
            </center>
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
                        <a href="{{url('cf-lite/change-password-club-detail',$club->id)}}">
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
@endsection

@section('other-script')
@endsection