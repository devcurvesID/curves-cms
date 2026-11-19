@extends('cf_lite.layouts.staff-template')

@section('other-style')
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <ol class="breadcrumb pull-right" style="background-color: transparent;">
                <li><a href="{{url('cf-lite/settings')}}" style="color: deeppink;">@lang('labels.setting')</a></li>
                <li><a href="{{url('cf-lite/product-setting')}}" style="color: deeppink;">@lang('labels.product_setting')</a></li>
                <li class="active" style="color: rebeccapurple;"><span>@lang('labels.choose_club')</span></li>
            </ol>
        </div>
        <div class="col-sm-12">
            <a href="{{url('cf-lite/product-setting')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.product_inventory') (@lang('labels.choose_club'))</h2>
            </center>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div id="filter-zone">
                {!! Form::open(['method' => 'GET']) !!}
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
                        <a href="{{url('cf-lite/product-inventory/'.$club->id)}}">
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
@endsection

@section('other-script')
@endsection