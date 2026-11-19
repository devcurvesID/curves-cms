@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/appointment.css')}}" rel="stylesheet">
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">

    <style>
        .select2 {
            width: 100% !important;
        }
    </style>
@endsection

@section('content')
    @if(Session::has('date') && Session::has('time') && Session::has('appointment_type'))
        @if(!empty($member))
            <?php
            if (Session::has('appointment_type')):
                if (Session::get('appointment_type') == 'WM'):
                    $member_card = 'member-card-wm';
                elseif (Session::get('appointment_type') == 'WO'):
                    $member_card = 'member-card-wo';
                endif;
            endif;
            ?>
            <div class="row">
                <div class="col-md-offset-3 col-md-6">
                    <div class="member-card {{$member_card}}">
                        <h1 class="page-header text-center">
                            @if(Session::get('appointment_type') == 'FA')
                                @lang('labels.fitness_assessment_appointment')
                            @elseif(Session::get('appointment_type') == 'WM')
                                @lang('labels.weigh_n_measure_appointment')
                            @elseif(Session::get('appointment_type') == 'WO')
                                @lang('labels.workout_appointment')
                            @endif
                        </h1>

                        <div class="row">
                            <div class="col-md-3">
                                @if(!empty($member->user_memberpersonal->photo))
                                    <img class="img-circle member-card-detail-img" src="{{asset('images/member_photos/'.$member->user_memberpersonal->photo)}}">
                                @else
                                    <img class="img-circle member-card-detail-img" src="{{asset('images/img.png')}}">
                                @endif
                            </div>
                            <div class="col-md-9">
                                <table>
                                    <tr>
                                        <td><span class="fa fa-user" title="@lang('labels.name')"></span></td>
                                        <td>:</td>
                                        <td>{{$member->name}}</td>
                                    </tr>
                                    <tr>
                                        <td><span class="fa fa-phone-square" title="@lang('labels.phone')"></span></td>
                                        <td>:</td>
                                        <td>
                                            @if(!empty($member->user_memberpersonal->cellphone))
                                                {{$member->user_memberpersonal->cellphone}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3"></td>
                                    </tr>
                                    <tr>
                                        <td><span class="fa fa-calendar" title="@lang('labels.appointment_date')"></span></td>
                                        <td>:</td>
                                        <td>{{date('d F Y', strtotime(Session::get('date')))}}</td>
                                    </tr>
                                    <tr>
                                        <td><span class="fa fa-clock-o" title="@lang('labels.appointment_time')"></span></td>
                                        <td>:</td>
                                        <td>{{date('H:i T', strtotime(Session::get('time')))}}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>

                        <br>
                        {!! Form::open(['url' => 'cf-lite/appointment/submit/wm-wo']) !!}
                        <div class="form-horizontal">
                            <div class="form-group">
                                {!! Form::label('staff_id', trans('labels.choose').' '.trans('labels.coach'), ['class' => 'control-label col-md-3']) !!}
                                <div class="col-md-6">
                                    {!! Form::select('staff_id', $staffs, !empty(Auth::check()) ? Auth::id() : null, ['class' => 'form-control', 'placeholder' => trans('labels.choose').' '.trans('labels.coach')]) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::textarea('note', null, ['class' => 'form-control', 'placeholder' => trans('labels.add').' '.trans('labels.note'), 'rows' => '4']) !!}
                        </div>
                        {!! Form::hidden('appointment_type', Session::get('appointment_type')) !!}
                        {!! Form::hidden('date', Session::get('date')) !!}
                        {!! Form::hidden('time', Session::get('time')) !!}
                        {!! Form::hidden('member_id', $member->id) !!}
                        <div class="form-group">
                            <div class="checkbox">
                                <label>{!! Form::checkbox('confirmed', null) !!}@lang('labels.confirmed_by_member')</label>
                            </div>
                        </div>
                        {!! Form::submit(trans('labels.submit'), ['class' => 'btn btn-primary form-control']) !!}
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>

        @endif
    @endif
@endsection

@section('other-script')
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script type="text/javascript">
        $('#staff_id').select2();
    </script>
@endsection