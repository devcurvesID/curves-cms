@extends('cf_lite.layouts.staff-template')

@section('other-style')
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <left>
                <a href="{{url('cf-lite/manual-login/choose')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px; margin-top: 30px;s" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            </left>
            <center>
                <h2 class="page-title">@lang('labels.staff_attend_list')
                    <br>
                    <small>
                        @if(!empty($club))
                            {{$club->club_name}} - {{ucwords($club->city)}}
                        @else
                            @lang('labels.choose') @lang('labels.club')
                        @endif
                    </small>
                </h2>
            </center>
        </div>
    </div>
{{--    <div class="row">--}}
{{--        <div class="col-md-6">--}}
{{--            <div id="filter-zone">--}}
{{--                {!! Form::open(['method' => 'GET', 'action' => 'ManualAttendanceController@staff_login_list']) !!}--}}
{{--                <div class="form-inline">--}}
{{--                    <div class="form-group">--}}
{{--                        {!! Form::label('clubs', trans('labels.choose').' '.trans('labels.club').': ', ['class' => 'control-label']) !!}--}}
{{--                        {!! Form::select('clubs', $clubs, !empty($club) ? $club->club_name : null, ['class' => 'form-control']) !!}--}}
{{--                        {!! Form::submit(trans('labels.find'), ['class' => 'btn btn-primary']) !!}--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                {!! Form::close() !!}--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}
    <div class="row">
        @if(count($staffs) > 0)
            @foreach($staffs as $staff)
                <div class="col-md-3">
                    @php($sign = 0)
                    @if(count($staff->user_staffattendance) > 0)
                        @foreach($staff->user_staffattendance as $attend)
                            @if($attend->sign_out == null)
                                @php($sign = 1)
                            @endif
                        @endforeach
                    @endif

                    @if($sign == 0)
                        <div class="thumbnail">
                            @if(!empty($staff->user_memberpersonal->photo))
                                <img src="{{asset('images/member_photos/'.$staff->user_memberpersonal->photo)}}" class="img-rounded" style="width: 100%; height: 200px; object-fit: cover; filter: grayscale(100%);">
                            @else
                                <img src="{{asset('images/img.png')}}" class="img-rounded" style="width: 100%; height: 200px; object-fit: cover; filter: grayscale(100%);">
                            @endif
                            <h4>{{$staff->name}}</h4>
                            <h5>(@lang('labels.role'): {{$staff->user_role->role}})</h5>
                        </div>
                    @elseif($sign == 1)
                        <a class="signout-staff" id="signout-staff-{{$staff->id}}" role="button" data-toggle="modal" data-target="#signoutModal">
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
                    @endif
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

    <!-- Modal -->
    <div class="modal fade" id="signoutModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">@lang('labels.staff_attendance_sign_out')</h4>
                </div>
                {!! Form::open(['method' => 'POST', 'action' => 'ManualAttendanceController@staff_logout', 'id' => 'form-staff-sign-out']) !!}
                {!! Form::hidden('staff_so_id', null) !!}
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('password', trans('labels.password').':', ['class' => 'control-label col-md-2']) !!}
                            <div class="col-md-3">
                                {!! Form::password('password', ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('labels.close')</button>
                    {!! Form::submit(trans('labels.sign_out'), ['class' => 'btn btn-primary']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script>
        $('.signout-staff').on('click', function () {
            var id = $(this).attr('id').split('-').pop();
            $('input[name="staff_so_id"]').val(id);
        });
    </script>
@endsection