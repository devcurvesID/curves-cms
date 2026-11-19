@extends('cf_lite.layouts.staff-template')

@section('other-style')
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/dashboard')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span
                        class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.product_request') - @lang('labels.club_list')</h2>
            </center>
        </div>
    </div>
    <div class="row">
        @if(count($clubs) > 0)
            @foreach($clubs as $club)
                <div class="col-md-3">
                    <a href="{{url('cf-lite/product-request/'.$club->id.'/stock')}}">
                        <div class="thumbnail">
                            @if(!empty($club->photo))
                                <img src="{{asset('images/club_photos/'.$club->photo)}}" class="img-rounded"
                                     style="width: 100%; height: 200px; object-fit: cover;">
                            @else
                                <img src="{{asset('images/club.png')}}" class="img-rounded"
                                     style="width: 100%; height: 200px; object-fit: cover;">
                            @endif
                            <h4>{{$club->club_name}} <span class="pull-right"><strong>{{ ($club->hq) ? '(HQ)' : '' }}</strong></span></h4>
                            <h5>({{ucwords($club->city).', '.ucwords($club->province)}})</h5>
                        </div>
                    </a>
                </div>
            @endforeach
        @endif
    </div>
@endsection

@section('other-script')
@endsection