@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">
    <style>
        #locationField, #controls {
            position: relative;
            /*width: 480px;*/
        }

        /*#autocomplete {*/
        /*position: absolute;*/
        /*top: 0px;*/
        /*left: 0px;*/
        /*width: 99%;*/
        /*}*/

        #address td {
            font-size: 10pt;
        }

        #locationField {
            height: 20px;
            margin-bottom: 2px;
        }

        #table-staff-detail td,
        #table-staff-detail-add td{
            height: 50px;
            padding: 0px 9px;
            font-size: 20px;
        }

        #table-staff-detail td:first-child,
        #staff-functional-role label,
        #form-staff-detail button span[class="fa fa-save"],
        #staff-working-hour-box #table-staff-detail td label{
            color: #ec088f;
        }

        #table-staff-detail-add td:first-child,
        #table-staff-detail-add td:nth-child(3),
        #table-staff-detail-add td:nth-child(5),
        #table-staff-detail-add td:nth-child(7){
            color: #77619c;
        }

        button#add-new-working-hour {
            background-color: #fabbde;
            border-color: #fabbde;
            margin-left: 10px;
        }

        button#add-new-working-hour:hover {
            background-color: #ff98d1;
            border-color: #ff98d1;
        }

        #staff-functional-role label{
            font-size: 20px;
        }

        input[value="Simpan"]{
            background-color: #fabbde;
            border-color: #fabbde;
        }

        input[value="Simpan"]:hover{
            background-color: #ff98d1;
            border-color: #ff98d1;
        }

        #note-box {
            position: absolute;
            top: 250px;
            right: 0;
            width: 250px;
            height: auto;
            background-color: peachpuff;
            color: rebeccapurple;
        }

        .note-header {
            z-index: 2;
            color: #fff;
            background-color: rebeccapurple;
            border-color: rebeccapurple;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-sm-12">
            @if(!empty(Session::get('staff_club_choose')) && (Session::get('staff_club_choose') == 'Yes'))
                <a href="{{url('cf-lite/staff/'.$staff->user_club->id)}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            @else
                <a href="{{url('cf-lite/settings')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            @endif
            <a href="{{url('cf-lite/certification/staff-certification-detail/'.$staff->id)}}" class="btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: rebeccapurple; margin-right: 30px; margin-top: 30px; " title="@lang('labels.staff_certification')"><span class="glyphicon glyphicon-book" style="margin-top: 10px; color: rebeccapurple;"></span></a>

            @if((\Illuminate\Support\Facades\Auth::user()->role_id <= 4) || (\Illuminate\Support\Facades\Auth::user()->role_id == 8) || (\Illuminate\Support\Facades\Auth::id() == $staff->id))
                <a href="{{url('cf-lite/staff/'.$staff->id.'/manage-password')}}" class="btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: rebeccapurple; margin-right: 10px; margin-top: 30px; " title="@lang('labels.manage_password')"><span class="fa fa-key" style="margin-top: 10px; color: rebeccapurple;"></span></a>
            @endif

            @if(\Illuminate\Support\Facades\Auth::user()->role_id <= 5)
                <a href="{{url('cf-lite/staff/'.$staff->id.'/manage-note')}}" class="btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: rebeccapurple; margin-right: 10px; margin-top: 30px; " title="@lang('labels.manage_note')"><span class="fa fa-edit" style="margin-top: 10px; color: rebeccapurple;"></span></a>
            @endif
            <a href="{{url('cf-lite/coach-target/setting-coach-target/'.$staff->id)}}" class="btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: rebeccapurple; margin-right: 10px; margin-top: 30px; " title="@lang('labels.coach_target')"><span class="glyphicon glyphicon-arrow-right" style="margin-top: 10px; color: rebeccapurple;"></span></a>

            <center>
                <h2 class="page-title">@lang('labels.staff_detail')<br><small>{{$staff->user_club->club_name}} - {{ucwords($staff->user_club->city)}}</small></h2>
            </center>
        </div>
    </div>

    {!! Form::model($staff->user_memberpersonal, ['method' => 'PATCH', 'action' => ['StaffController@staff_update', $staff->id], 'files' => 'true', 'id' => 'form-staff-detail']) !!}
    {!! Form::hidden('staff_attribute', $staff->id) !!}
    <div class="row">
        <div class="col-md-2 col-md-offset-1">
            @if(!empty($staff->user_memberpersonal->photo))
                <img role="button" id="image_preview" class="img-circle"
                     src="{{asset('images/member_photos/'.$staff->user_memberpersonal->photo)}}"
                     style="width: 150px; height: 150px; object-fit: cover;">
            @else
                <img role="button" id="image_preview" class="img-circle" src="{{asset('images/img.png')}}"
                     style="width: 150px; height: 150px; object-fit: cover;">
            @endif
            <div class="form-group hidden">
                {!! Form::file('photo', ['class' => 'file_multi_video']) !!}
            </div>
        </div>
        <div class="col-md-3">
            <table id="table-staff-detail">
                <tr>
                    <td><span class="fa fa-female" title="@lang('labels.name')"></span></td>
                    <td>:</td>
                    <td>{!! Form::text('name', $staff->name, ['class' => 'form-control']) !!}</td>
                </tr>
                <tr>
                    <td><span class="fa fa-briefcase" title="@lang('labels.role')"></span></td>
                    <td>:</td>
                    <td>
                        @if((\Illuminate\Support\Facades\Auth::id() != $staff->id) && (\Illuminate\Support\Facades\Auth::user()->role_id != $staff->role_id))
                            @if((Auth::user()->role_id < 5) || (Auth::user()->role_id == 8))
                                {!! Form::select('role', $roles, $staff->role_id, ['class' => 'form-control']) !!}
                            @else
                                {{$staff->user_role->role}}
                            @endif
                        @else
                            {{$staff->user_role->role}}
                        @endif
                    </td>
                </tr>
                <tr>
                    <td><span class="fa fa-calendar" title="@lang('labels.birth')"></span></td>
                    <td>:</td>
                    <td>{!! Form::date('birth', null, ['class' => 'form-control']) !!}</td>
                </tr>
                <tr>
                    <td><span class="fa fa-sign-in" title="@lang('labels.login_email')"></span></td>
                    <td>:</td>
                    <td>{!! Form::text('email', $staff->email, ['class' => 'form-control']) !!}</td>
                </tr>
                <tr>
                    <td><span class="fa fa-envelope-square" title="@lang('labels.personal_email')"></span></td>
                    <td>:</td>
                    <td>{!! Form::text('email_personal', !empty($staff->user_staffpersonal->email_personal) ? $staff->user_staffpersonal->email_personal : null, ['class' => 'form-control']) !!}</td>
                </tr>
                <tr>
                    <td><span class="fa fa-calendar-o" title="@lang('labels.join')"></span></td>
                    <td>:</td>
                    <td>
                        {!! Form::date('joined', !empty($staff->user_memberpersonal->joined) ? \Carbon\Carbon::parse($staff->user_memberpersonal->joined) : null, ['class' => 'form-control']) !!}
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-md-3">
            <table id="table-staff-detail">
                <tr>
                    <td>
                        <span>{!! Form::label('key_tag_id', trans('labels.key_tag').'#', ['class' => 'control-label']) !!}</span>
                    </td>
                    <td>:</td>
                    <td>{!! Form::text('key_tag_id', null, ['class' => 'form-control']) !!}</td>
                </tr>
                <tr>
                    <td><span>{!! Form::label('nik', trans('labels.nik'), ['class' => 'control-label']) !!}</span></td>
                    <td>:</td>
                    <td>{!! Form::text('nik', null, ['class' => 'form-control']) !!}</td>
                </tr>
                <tr>
                    <td><span>{!! Form::label('npwp', trans('labels.npwp'), ['class' => 'control-label']) !!}</span>
                    </td>
                    <td>:</td>
                    <td>{!! Form::text('npwp', !empty($staff->user_staffpersonal->npwp) ? $staff->user_staffpersonal->npwp : null, ['class' => 'form-control']) !!}</td>
                </tr>
                <tr>
                    <td><span>{!! Form::label('staff_code', trans('labels.code'), ['class' => 'control-label']) !!}</span>
                    </td>
                    <td>:</td>
                    <td>{!! Form::text('staff_code', !empty($staff->user_staffcode->code) ? $staff->user_staffcode->code : null, ['class' => 'form-control upper-class','maxlength'=>'3']) !!}</td>
                </tr>
            </table>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-7 col-md-offset-1">
            <div class="row">
                <div class="col-md-12">
                    <table id="table-staff-detail">
                        <tr>
                            <td>
                                <span>{!! Form::label('address', trans('labels.address'), ['class' => 'control-label']) !!}</span>
                            </td>
                            <td>:</td>
                            <td colspan="4"
                                id="locationField">{!! Form::text('address', null, ['class' => 'form-control', 'id' => 'autocomplete', 'onFocus' => 'geolocate()']) !!}</td>
                        </tr>
                        <tr>
                            <td>
                                <span>{!! Form::label('phone', trans('labels.phone'), ['class' => 'control-label']) !!}</span>
                            </td>
                            <td>:</td>
                            <td>{!! Form::text('phone', null, ['class' => 'form-control']) !!}</td>
                            <td style="color: #ec088f;">
                                <span>{!! Form::label('cellphone', trans('labels.cellphone'), ['class' => 'control-label']) !!}</span>
                            </td>
                            <td>:</td>
                            <td>{!! Form::text('cellphone', null, ['class' => 'form-control']) !!}</td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12">
                    <table id="table-staff-detail">
                        <tr>
                            <td>
                                <span>{!! Form::label('postal', trans('labels.postal'), ['class' => 'control-label']) !!}</span>
                            </td>
                            <td>:</td>
                            <td>{!! Form::text('postal', null, ['class' => 'form-control']) !!}</td>
                        </tr>
                        <tr>
                            <td colspan="3">
                                <button type="button" name="submit-info" class="btn btn-default pull-right"><span
                                            class="fa fa-save"></span>
                                </button>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">

        <div class="col-md-11 col-md-offset-1">
            <div style="font-size: 25px; font-weight: 600; margin: 10px 0;">@lang('labels.staff_hour')</div>


            <div id="staff-working-hour-box" style="margin: 15px 0;">
                @if(count($staff->user_staffworkinghour) > 0)
                    @php($sequence_start = 'a')
                    @foreach($staff->user_staffworkinghour as $staff_working_hour)
                        <div class="form-inline" id="form-inline-{{$staff_working_hour->id}}">
                            {!! Form::hidden('working_hour_id['.$staff_working_hour->id.']', $staff_working_hour->id) !!}
                            <table id="table-staff-detail">
                                <tr>
                                    <td>{!! Form::label('days', trans('labels.choose_day'), ['class' => 'control-label']) !!}</td>
                                    <td>{!! Form::select('from_day['.$staff_working_hour->id.']', ['Monday' => 'Monday', 'Tuesday' => 'Tuesday', 'Wednesday' => 'Wednesday', 'Thursday' => 'Thursday', 'Friday' => 'Friday', 'Saturday' => 'Saturday'], $staff_working_hour->from_day, ['class' => 'form-control']) !!}</td>
                                    <td>{!! Form::label('to_days', trans('labels.to'), ['class' => 'control-label']) !!}</td>
                                    <td>{!! Form::select('thru_day['.$staff_working_hour->id.']', ['Monday' => 'Monday', 'Tuesday' => 'Tuesday', 'Wednesday' => 'Wednesday', 'Thursday' => 'Thursday', 'Friday' => 'Friday', 'Saturday' => 'Saturday'], $staff_working_hour->thru_day, ['class' => 'form-control']) !!}</td>
                                    <td><label class="control-label"><span class="fa fa-clock-o"></span></label></td>
                                    <td>{!! Form::text('from_hour['.$staff_working_hour->id.']', \Carbon\Carbon::parse($staff_working_hour->from_hour)->setTimezone(Session::get('timezone'))->format('H:i'), ['class' => 'form-control hour']) !!}</td>
                                    <td>{!! Form::label('to_hours', trans('labels.to'), ['class' => 'control-label']) !!}</td>
                                    <td>{!! Form::text('thru_hour['.$staff_working_hour->id.']', \Carbon\Carbon::parse($staff_working_hour->thru_hour)->setTimezone(Session::get('timezone'))->format('H:i'), ['class' => 'form-control hour']) !!}</td>
                                    <td>{!! Form::button(trans('labels.delete'), ['class' => 'btn btn-danger btn-delete-working-hour', 'id' => 'delete-working-hour-'.$staff_working_hour->id]) !!}</td>
                                </tr>
                            </table>
                        </div>
                    @endforeach
                @else
                    @php($sequence_start = 'b')
                    <div class="form-inline" id="form-inline-a">
                        {!! Form::hidden('working_hour_id[a]', 'a') !!}
                        <table id="table-staff-detail">
                            <tr>
                                <td>{!! Form::label('days', trans('labels.choose_day'), ['class' => 'control-label']) !!}</td>
                                <td>{!! Form::select('from_day[a]', ['Monday' => 'Monday', 'Tuesday' => 'Tuesday', 'Wednesday' => 'Wednesday', 'Thursday' => 'Thursday', 'Friday' => 'Friday', 'Saturday' => 'Saturday'], 'Monday', ['class' => 'form-control']) !!}</td>
                                <td>{!! Form::label('to_days', trans('labels.to'), ['class' => 'control-label']) !!}</td>
                                <td>{!! Form::select('thru_day[a]', ['Monday' => 'Monday', 'Tuesday' => 'Tuesday', 'Wednesday' => 'Wednesday', 'Thursday' => 'Thursday', 'Friday' => 'Friday', 'Saturday' => 'Saturday'], 'Friday', ['class' => 'form-control']) !!}</td>
                                <td><label class="control-label"><span class="fa fa-clock-o"></span></label></td>
                                <td>{!! Form::text('from_hour[a]', '10:00', ['class' => 'form-control hour']) !!}</td>
                                <td>{!! Form::label('to_hours', trans('labels.to'), ['class' => 'control-label']) !!}</td>
                                <td>{!! Form::text('thru_hour[a]', '13:00', ['class' => 'form-control hour']) !!}</td>
                            </tr>
                        </table>
                    </div>
                @endif
            </div>
            {!! Form::hidden('sequence', $sequence_start) !!}
            <button id="add-new-working-hour" type="button" class="btn btn-primary btn-lg"
                    title="@lang('labels.add') @lang('labels.working_hour')">
                <span class="fa fa-plus"></span>
            </button>
        </div>
        <div class="col-md-10 col-md-offset-1">
            <div class="col-md-12">
                <button type="button" name="submit-working-hour" class="pull-right btn btn-default"
                        style="margin-right: 15px;">
                    <span class="fa fa-save"></span>
                </button>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div id="staff-functional-role" class="col-md-11 col-md-offset-1">
            <div style="font-size: 25px; font-weight: 600; margin: 10px 0;">@lang('labels.staff_functional_role')</div>
            <div class="form-group">
                @foreach($functional_roles as $key => $value)
                    @php($chosen = 0)
                    <div class="checkbox">
                        <label>
                            @if(count($staff->staff_functionalrole) > 0)
                                @foreach($staff->staff_functionalrole as $chosen_key)
                                    @if($key == $chosen_key->id)
                                        @php($chosen = 1)
                                    @endif
                                @endforeach
                            @endif
                            @if($chosen == '1')
                                {!! Form::checkbox('functional_role[]', $key, true) !!} {{$value}}
                            @elseif($chosen == '0')
                                {!! Form::checkbox('functional_role[]', $key) !!} {{$value}}
                            @endif
                        </label>
                    </div>
                @endforeach
            </div>

            <div class="col-md-10 col-md-offset-1">
                <div class="col-md-12">
                    <button type="button" name="submit-staff-functional-role" class="pull-right btn btn-default"
                            style="margin-right: 15px;">
                        <span class="fa fa-save"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div style="font-size: 25px; font-weight: 600; margin: 10px 0;">@lang('labels.staff_position_history')</div>
            <table class="table table-hover table-bordered">
                <thead>
                    <tr>
                        <th>@lang('labels.month_year')</th>
                        <th>@lang('labels.club')</th>
                        <th>@lang('labels.role')</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transferHistories as $th)
                        <tr>
                            <td>{{\Carbon\Carbon::parse($th->from_date)->format('F Y')}}</td>
                            <td>{{$th->transferhistory_club->club_name}}</td>
                            <td>{{$th->transferhistory_role->role}}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary pull-right']) !!}
        </div>
    </div>
    {!! Form::hidden('attribute', null) !!}
    {!! Form::close() !!}

    @php($note_gate = 0)
    @if((\Illuminate\Support\Facades\Auth::user()->role_id <= 4) || (\Illuminate\Support\Facades\Auth::user()->role_id == 8))
        @php($gate = 0)
        @if((\Illuminate\Support\Facades\Auth::user()->role_id < 4) || (\Illuminate\Support\Facades\Auth::user()->role_id == 8))
            @php($gate = 1)
        @else
            {{--club manager -> role not available yet--}}
            @php($gate = 1)
        @endif

        @if($gate == 1)
            {!! Form::open(['method' => 'DELETE', 'action' => ['StaffController@set_staff_inactive', $staff]]) !!}
            {{--{!! Form::button('<span class="fa fa-power-off"></span>  '.trans('labels.nonactivate_staff'), ['class' => 'btn btn-delete btn-danger', 'id' => 'set-inactive-btn']) !!}--}}
            <a href="javascript:;" data-toggle="modal" data-target="#inactiveModal" class="btn btn-danger"><span class="fa fa-power-off"></span> @lang('labels.nonactivate_staff')</a>

            <!-- Modal -->
            <div class="modal fade" id="inactiveModal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">@lang('labels.nonactivate_staff')</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-horizontal">
                                        <div class="form-group">
                                            {!! Form::label('inactive_type', trans('labels.type').': ', ['class' => 'control-label col-md-2']) !!}
                                            <div class="col-md-10">
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="inactive_type" value="Permission" checked>
                                                        @lang('labels.cuti')
                                                    </label>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="inactive_type" value="Resign">
                                                        @lang('labels.resign')
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('reason', trans('labels.reason').': ', ['class' => 'control-label col-md-2']) !!}
                                            <div class="col-md-6">
                                                {!! Form::textarea('reason', null, ['class' => 'form-control', 'rows' => '4']) !!}
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('from_date', trans('labels.date').': ', ['class' => 'control-label col-md-2']) !!}
                                            <div class="col-md-4">
                                                {!! Form::date('from_date', null, ['class' => 'form-control']) !!}
                                            </div>
                                            {!! Form::label('thru_date', trans('labels.until'), ['class' => 'control-label col-md-1']) !!}
                                            <div class="col-md-4">
                                                {!! Form::date('thru_date', null, ['class' => 'form-control']) !!}
                                                <p class="help-block">*@lang('messages.set_empty_for_resign_choice')</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default"
                                    data-dismiss="modal">@lang('labels.close')</button>
                            <button type="submit" class="btn btn-primary">@lang('labels.save')</button>
                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        @endif

        @php($note_gate = 1)
    @endif

    @if((\Illuminate\Support\Facades\Auth::user()->role_id == 5) && ($staff->role_id > 5) && ($staff->role_id != 8))
        @php($note_gate = 1)
    @endif

    @if($note_gate == 1)
        @if(count($staff->user_staffnote->where('status', 'Ongoing')) > 0)
            <ul class="list-group" id="note-box">
                <li class="list-group-item note-header">
                    @lang('labels.note') <span class="pull-right" id="close-note" role="button" title="close">&times;</span>
                </li>
                @foreach($staff->user_staffnote->where('status', 'Ongoing') as $note)
                    <li class="list-group-item">
                        {{$note->note}}
                    </li>
                @endforeach
            </ul>
        @endif
    @endif
@endsection

@section('other-script')
    <script src="{{asset('vendors/moment/min/moment.min.js')}}"></script>
    <script src="{{asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script>
        var placeSearch, autocomplete;

        var componentForm = {
            street_number: 'short_name',
            route: 'long_name',
            locality: 'long_name',
            administrative_area_level_1: 'short_name',
            country: 'long_name',
            postal_code: 'short_name'
        };

        function initAutocomplete() {
            // Create the autocomplete object, restricting the search to geographical
            // location types.
            autocomplete = new google.maps.places.Autocomplete(
                /** @type {!HTMLInputElement} */(document.getElementById('autocomplete')),
                {types: ['geocode']});

            // When the user selects an address from the dropdown, populate the address
            // fields in the form.
            autocomplete.addListener('place_changed', fillInAddress);
        }

        function fillInAddress() {
            // Get the place details from the autocomplete object.
            var place = autocomplete.getPlace();

            for (var component in componentForm) {
                document.getElementById(component).value = '';
                document.getElementById(component).disabled = false;
            }

            // Get each component of the address from the place details
            // and fill the corresponding field on the form.
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType = place.address_components[i].types[0];
                if (componentForm[addressType]) {
                    var val = place.address_components[i][componentForm[addressType]];
                    document.getElementById(addressType).value = val;
                }
            }
        }

        function geolocate() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    var geolocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    var circle = new google.maps.Circle({
                        center: geolocation,
                        radius: position.coords.accuracy
                    });
                    autocomplete.setBounds(circle.getBounds());
                });
            }
        }
    </script>
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAx42dzcUZAXbWOYbCUV8-sEyN9_ljWzcs&libraries=places&callback=initAutocomplete"
            async defer></script>

    <script>
        $('#image_preview').on('click', function () {
            $('.file_multi_video').click();
        });

        $(document).on('change', '.file_multi_video', function (evt) {
            var $source = $('#image_preview');
            $source[0].src = URL.createObjectURL(this.files[0]);
            $source.load();
        });
    </script>

    <script>
        function nextChar(c) {
            return String.fromCharCode(c.charCodeAt(0) + 1);
        }

        $('#add-new-working-hour').on('click', function () {
            var sequence = $('input[name="sequence"]').val();
            var view = '';
            view = '<div class="form-inline" id="form-inline-' + sequence + '">' +
                '<input name="working_hour_id[' + sequence + ']" type="hidden" value="' + sequence + '">' +
                '<table id="table-staff-detail-add">' +
                    '<tr>' +
                '<td><label class="control-label">@lang('labels.choose_day')</label></td>' +
                '<td><select class="form-control" name="from_day[' + sequence + ']">' +
                '<option value="Monday" selected="selected">Monday</option>' +
                '<option value="Tuesday">Tuesday</option>' +
                '<option value="Wednesday">Wednesday</option>' +
                '<option value="Thursday">Thursday</option>' +
                '<option value="Friday">Friday</option>' +
                '<option value="Saturday">Saturday</option>' +
                '</select></td>' +
                '<td><label class="control-label">@lang('labels.to')</label></td>' +
                '<td><select class="form-control" name="thru_day[' + sequence + ']">' +
                '<option value="Monday">Monday</option>' +
                '<option value="Tuesday">Tuesday</option>' +
                '<option value="Wednesday">Wednesday</option>' +
                '<option value="Thursday">Thursday</option>' +
                '<option value="Friday" selected="selected">Friday</option>' +
                '<option value="Saturday">Saturday</option>' +
                '</select></td>' +
                '<td><label class="control-label">' +
                '<span class="fa fa-clock-o"></span>' +
                '</label></td>' +
                '<td><input class="form-control hour" name="from_hour[' + sequence + ']" type="text" value="10:00"></td>' +
                '<td><label class="control-label">@lang('labels.to')</label></td>' +
                '<td><input class="form-control hour" name="thru_hour[' + sequence + ']" type="text" value="13:00"></td>' +
                '<td><button class="btn btn-danger btn-delete-schedule" id="delete-schedule-' + sequence + '" type="button">@lang('labels.delete')</button></td>' +
                '</tr>' +
                '</table>' +
                '</div>' +
                '</div>';

            $('#staff-working-hour-box').append(view);
            $('input[name="sequence"]').val(nextChar(sequence));
        });
    </script>

    <script>
        $('#staff-working-hour-box').on('focus', '.hour', function () {
            $(this).datetimepicker({
                format: 'HH:mm'
            });
        });

        $('#staff-working-hour-box').on('click', '.btn-delete-working-hour', function () {
            var id = $(this).attr('id').split('-').pop();
            $('#form-inline-' + id).remove();
        });

        $('button[name="submit-info"]').on('click', function () {
            $('input[name="attribute"]').val('info');
            $('#form-staff-detail').submit();
        });

        $('button[name="submit-working-hour"]').on('click', function () {
            $('input[name="attribute"]').val('working-hour');
            $('#form-staff-detail').submit();
        });

        $('button[name="submit-staff-functional-role"]').on('click', function () {
            $('input[name="attribute"]').val('sfr');
            $('#form-staff-detail').submit();
        });

        $('#close-note').on('click', function () {
            $('#note-box').hide();
        });
    </script>

    <script>
        $('#form-staff-detail').on('submit',function () {
            $('.upper-class').val($('.upper-class').val().toUpperCase());
        });
    </script>
@endsection