@extends('cf_lite.layouts.staff-template')

@section('other-style')
<style>
    .locker-title{
        text-align: left;
    }

    .locker-avail{
        color: #00ff45;
        height : 200px;
    }

    .locker-not-avail{
        color:#ff00ba;
        height : 200px;
    }

    .locker-not-returned{
        color:#ffba00;
        height: 200px;
    }

    .locker-avail:hover {
        color: white;
        background-color: #00ff45;
        border-color: #00ff45;
    }

    .locker-not-avail:hover {
        color: white;
        background-color: #ff00ba;
        border-color: #ff00ba;
    }

    .locker-not-returned:hover {
        color: white;
        background-color: #ffba00;
        border-color: #ffba00;
    }

    h5.rentname{
        white-space:nowrap;
        width:100%;
        overflow:hidden;
        text-overflow: ellipsis;
    }
    
</style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/dashboard')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.locker_list')</h2>
                <h3 style="margin-top: -50px;padding-bottom: 50px;">({{$club->club_name}})</h3>
            </center>
        </div>
    </div>
    <div class="row">
        @if(count($lockers) > 0)
            @php($count = 0)
            @foreach($lockers as $locker)
                @if($count%5==0)
                <div class="col-md-2 col-md-offset-1">
                @else
                <div class="col-md-2">
                @endif
                    @if($locker->is_key_returned != null && $locker->is_key_returned == "No" && $locker->isAvailable == 1)
                        {!! Form::open(['method' => 'POST', 'action' => ['LockerController@key_returned', $locker->memberlocker_id], 'id'=>'form-key-submit-'.$locker->memberlocker_id]) !!}
                        <a role="button" onclick="keyReturned({{ $locker->memberlocker_id }});">
                            <div class="thumbnail locker-not-returned" style="border-width: 15px;border-color: #ffba00;">
                                <h1 class="locker-title">{{$locker->locker_name}}</h1><br/>
                                <h5 class="locker-title rentname">{{$locker->name}}</h5>
                                <h5 class="locker-title"> ({{$locker->max_thru}})</h5>
                            </div>
                        </a>
                        {!! Form::close() !!}
                    @elseif($locker->isAvailable == 1)
                        <a href="{{url('cf-lite/locker/choose-packet',$locker->id)}}">
                            <div class="thumbnail locker-avail" style="border-width: 15px;border-color: #00ff45;">
                                <h1 class="locker-title">{{$locker->locker_name}}</h1><br/>
                                <h5 class="locker-title">@lang('labels.available')</h5>
                            </div>
                        </a>
                    @else
                        <a href="{{url('cf-lite/locker/choose-packet',$locker->id)}}">
                            <div class="thumbnail locker-not-avail" style="border-width: 15px;border-color: #ff00ba;">
                                <h1 class="locker-title">{{$locker->locker_name}}</h1><br/>
                                <h5 class="locker-title rentname">{{$locker->rent_name}}</h5>
                                <h5 class="locker-title"> ({{$locker->rent_availability}})</h5>
                            </div>
                        </a>
                    @endif
                </div>
                @php($count++)
            @endforeach
            </div>
        @endif
    </div>
@endsection

@section('other-script')
<script type="text/javascript">

    function keyReturned(value){
        var returned = confirm('Apakah kunci loker sudah dikembalikan?');

        if(returned) {
            $("#form-key-submit-"+value).submit();
        }
    }
    
</script>
@endsection