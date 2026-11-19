@extends('cf_lite.layouts.staff-template')

@section('other-style')
<style>
    .locker-title{
        text-align: center;
    }

    .locker-avail{
        color: #00ff45;
        height : 200px;
    }

    .locker-not-avail{
        color:#ff00ba;
        height : 200px;
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
            <a href="{{url(/*'cf-lite/locker/club-locker-list'*/'cf-lite/locker/locker-setting-choice')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.locker_list')</h2>
                <h3 style="margin-top: -50px;padding-bottom: 50px;">({{$club->club_name}})</h3>
            </center>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2">
            <a href="javascript:;" data-toggle="modal" data-target="#addNewLocker">
                <div class="thumbnail" style="border-width: 15px;">
                    <div style="width: 90%; height: 125px;">
                        <center>
                            <span class="fa fa-plus" style="font-size: 60px;vertical-align: middle;color: gray;"></span>
                        </center>
                    </div>
                </div>
            </a>
        </div>
        <div class="col-md-10 ">
        @if(count($lockers) > 0)
            @php($count = 0)
            @foreach($lockers as $locker)
                @if($count%5==0)
                <div class="col-md-2 col-md-offset-1">
                @else
                <div class="col-md-2">
                @endif
                    @if($locker->isAvailable == 1)
                        {!! Form::open(['method' => 'DELETE' ,'id'=>'formdeletes-'.$locker->id ,'action' => ['LockerController@delete_locker' , $locker->id, ]])!!}
                        <a role="button" id="btndelete-{{$locker->id}}" class="thumbnail locker-avail btn-delete" style="border-width: 15px;border-color: #00ff45;width: 100%;">
                            <h1 class="locker-title ">{{$locker->locker_name}}</h1><br/>
                            <h5 class="locker-title ">@lang('labels.available')</h5>
                        </a>
                        {!! Form::close() !!}
                    @else
                        <div class="thumbnail locker-not-avail" style="border-width: 15px;border-color: #ff00ba;">
                            <h1 class="locker-title ">{{$locker->locker_name}}</h1><br/>
                            <h5 class="locker-title rentname">{{$locker->rent_name}}<br/>({{$locker->rent_availability}})</h5>
                        </div>
                    @endif
                </div>
                @php($count++)
            @endforeach
            </div>
        @endif
        </div>
        <!-- Add Modal -->
        <div class="modal fade" id="addNewLocker" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                {!! Form::open(['method' => 'POST', 'action' => ['LockerController@store_lockers',$club->id]]) !!}
                {!! Form::hidden('club_id',$club->id) !!}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true" >&times;</span></button>
                        <h4 class="modal-title">@lang('labels.add_new_locker')</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-inline">
                                    <div class="form-group">
                                        {!! Form::label('locker_name', trans('labels.locker_name').': ', ['class' => 'control-label']) !!}<br/>
                                        {!! Form::select('locker_name_start', $locker_start, null, ['class' => 'form-control']) !!}
                                        {!! Form::number('number_start', null, ['class' => 'form-control']) !!} <br/><br/>
                                        {!! Form::select('locker_name_end', $locker_start, null, ['class' => 'form-control']) !!}
                                        {!! Form::number('number_end', null, ['class' => 'form-control']) !!} <br/>
                                    </div><br/>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal">@lang('labels.close')</button>
                        <button type="submit" class="btn btn-primary" id="save-btn">@lang('labels.save')</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <!-- End Add Modal -->
    </div>
@endsection

@section('other-script')
@endsection