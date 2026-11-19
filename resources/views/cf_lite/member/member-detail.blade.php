@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <style>
        #locationField, #controls {
            position: relative;
            /*width: 480px;*/
        }

        #autocomplete {
            position: absolute;
            top: 0px;
            left: 0px;
            width: 99%;
        }

        #address td {
            font-size: 10pt;
        }

        #locationField {
            height: 20px;
            margin-bottom: 2px;
        }

        #vip-box {
            width: 135px;
            height: 75px;
            padding: 5px;
            padding-left: 15px;
            padding-right: 15px;
            border: 2px solid deeppink;
            border-radius: 7px;
            color: deeppink;
            position: absolute;
            top: 0;
            right: 0;
            text-align: center;
        }

        #vip-box h3 {
            font-weight: 600;
            font-size: 40px;
            margin-top: 10px;
        }

        .btn-purple, .btn-purple:link, .btn-purple:visited, .btn-purple:active {
            background-color: rebeccapurple;
            color: white;
            border-color: rebeccapurple;
        }

        .btn-purple:hover {
            border-color: rebeccapurple;
            color: rebeccapurple;
            background-color: transparent;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/member/list')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.member_detail')</h2>
            </center>

            @if($member->user_memberstatus->member_vip_status == 'VIP')
                <div id="vip-box"><h3>@lang('labels.vip')</h3></div>
            @endif
        </div>
    </div>

    {!! Form::model($member->user_memberpersonal, ['method' => 'PATCH', 'action' => ['MemberController@member_update', $member->id], 'files' => 'true']) !!}
    {!! Form::hidden('attribute', $member->id) !!}
    <div class="row">
        <div class="col-md-3">
            @if(!empty($member->user_memberpersonal->photo))
                <img role="button" id="image_preview" class="img-circle center-block" src="{{asset('images/member_photos/'.$member->user_memberpersonal->photo)}}" style="width: 150px; height: 150px; object-fit: cover;">
            @else
                <img role="button" id="image_preview" class="img-circle center-block" src="{{asset('images/img.png')}}" style="width: 150px; height: 150px; object-fit: cover;">
            @endif
            <div class="form-group hidden">
                {!! Form::file('photo', ['class' => 'file_multi_video']) !!}
            </div>
            <a href="{{url('cf-lite/member/'.$member->id.'/album')}}" class="btn btn-sm btn-default center-block" style="margin-top: 25px;">@lang('labels.photo_album') <span class="fa fa-chevron-circle-right"></span></a>
        </div>
        <div class="col-md-4">
            <table>
                <tr>
                    <td><span class="fa fa-female" title="@lang('labels.name')"></span></td>
                    <td>:</td>
                    <td>{!! Form::text('name', $member->name, ['class' => 'form-control', 'style' => 'margin-bottom: 10px']) !!}</td>
                </tr>
                <tr>
                    <td><span class="fa fa-calendar" title="@lang('labels.birth')"></span></td>
                    <td>:</td>
                    <td>{!! Form::date('birth', null, ['class' => 'form-control', 'style' => 'margin-bottom: 10px']) !!}</td>
                </tr>
                <tr>
                    <td><span class="fa fa-envelope-square" title="@lang('labels.email')"></span></td>
                    <td>:</td>
                    <td>{!! Form::text('email', $member->email, ['class' => 'form-control', 'style' => 'margin-bottom: 10px']) !!}</td>
                </tr>
                <tr>
                    <td><span class="fa fa-calendar-o" title="@lang('labels.join')"></span></td>
                    <td>:</td>
                    @if(isset($member->user_memberpersonal->joined))
                        <td>{{date('d F Y', strtotime($member->user_memberpersonal->joined))}}</td>
                    @else
                        <td>-</td>
                    @endif
                </tr>
                <tr>
                    <td><span class="fa fa-group" title="@lang('labels.membership_type')"></span></td>
                    <td>:</td>
                    <td>{{$member->user_memberstatus->memberstatus_membershiptype->membership_type_name}}<br>({{date('d F Y', strtotime($member->user_memberstatus->from_date))}} - {{date('d F Y', strtotime($member->user_memberstatus->thru_date))}})</td>
                </tr>
            </table>
        </div>
        <div class="col-md-4">
            <div class="form-horizontal">
                <div class="form-group">
                    {!! Form::label('key_tag_id', trans('labels.key_tag').' #:', ['class' => 'control-label col-md-6']) !!}
                    <div class="col-md-6">
                        {!! Form::text('key_tag_id', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('nik', trans('labels.nik').' :', ['class' => 'control-label col-md-6']) !!}
                    <div class="col-md-6">
                        {!! Form::text('nik', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
                @if((\Illuminate\Support\Facades\Auth::user()->role_id < 6) || (\Illuminate\Support\Facades\Auth::user()->role_id == 8))
                    @if($member->role_id == '7')
                        <div class="form-group">
                            <div class="col-md-12">
                                <button type="button" class="btn btn-purple pull-right" data-toggle="modal" data-target="#convertModal"><span class="fa fa-exchange"></span> <span class="fa fa-user"></span> @lang('labels.convert_to_staff')</button>
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('address', trans('labels.address').': ', ['class' => 'control-label col-md-2']) !!}
                <div class="col-md-6" id="locationField">
                    {!! Form::text('address', null, ['class' => 'form-control', 'id' => 'autocomplete', 'onFocus' => 'geolocate()']) !!}
                </div>
                {!! Form::label('postal', trans('labels.postal').': ', ['class' => 'control-label col-md-2']) !!}
                <div class="col-md-2">
                    {!! Form::text('postal', null, ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('phone', trans('labels.phone').': ', ['class' => 'control-label col-md-2']) !!}
                <div class="col-md-2">
                    {!! Form::text('phone', null, ['class' => 'form-control']) !!}
                </div>
                {!! Form::label('cellphone', trans('labels.cellphone').': ', ['class' => 'control-label col-md-2']) !!}
                <div class="col-md-2">
                    {!! Form::text('cellphone', null, ['class' => 'form-control']) !!}
                </div>
                {!! Form::label('wa_number', trans('labels.wa_number').': ', ['class' => 'control-label col-md-2']) !!}
                <div class="col-md-2">
                    {!! Form::text('wa_number', !empty($member->user_memberpersonal->memberpersonal_buddyreferrallead->wa_number) ? $member->user_memberpersonal->memberpersonal_buddyreferrallead->wa_number : null, ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('occupation', trans('labels.occupation'), ['class' => 'control-label col-md-2']) !!}
                <div class="col-md-2">
                    {!! Form::text('occupation', !empty($member->user_memberpersonal->memberpersonal_buddyreferrallead->buddyreferrallead_guestdetail->occupation) ? $member->user_memberpersonal->memberpersonal_buddyreferrallead->buddyreferrallead_guestdetail->occupation : null, ['class' => 'form-control']) !!}
                </div>
                {!! Form::label('office', trans('labels.office_phone_number').': ', ['class' => 'control-label col-md-2']) !!}
                <div class="col-md-2">
                    {!! Form::text('office', null, ['class' => 'form-control']) !!}
                </div>
                {!! Form::label('ignore_wa', trans('labels.ignore_wa').': ', ['class' => 'control-label col-md-2']) !!}
                <div class="col-md-2">
                    {!! Form::checkbox('ignore_wa', 'ignored',$member->user_memberstatus->ignore_wa, ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('marriage_status', trans('labels.status').': ', ['class' => 'control-label col-md-1']) !!}
                <div class="col-md-4">
                    @if(!empty($member->user_memberpersonal->memberpersonal_buddyreferrallead->buddyreferrallead_guestdetail))
                        @if($member->user_memberpersonal->memberpersonal_buddyreferrallead->buddyreferrallead_guestdetail->status == 'Single')
                            <label class="radio-inline">
                                {!! Form::radio('marriage_status', 'Single', true) !!} @lang('labels.single')
                            </label>
                            <label class="radio-inline">
                                {!! Form::radio('marriage_status', 'Married', false) !!} @lang('labels.married')
                            </label>
                        @elseif($member->user_memberpersonal->memberpersonal_buddyreferrallead->buddyreferrallead_guestdetail->status == 'Married')
                            <label class="radio-inline">
                                {!! Form::radio('marriage_status', 'Single', false) !!} @lang('labels.single')
                            </label>
                            <label class="radio-inline">
                                {!! Form::radio('marriage_status', 'Married', true) !!} @lang('labels.married')
                            </label>
                        @endif
                    @else
                        <label class="radio-inline">
                            {!! Form::radio('marriage_status', 'Single', true) !!} @lang('labels.single')
                        </label>
                        <label class="radio-inline">
                            {!! Form::radio('marriage_status', 'Married', false) !!} @lang('labels.married')
                        </label>
                    @endif
                </div>
            </div>
        </div>
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('tshirt_size', trans('labels.tshirt_size').': ', ['class' => 'control-label col-md-2']) !!}
                <div class="col-md-2">
                    {!! Form::select('tshirt_size', $tshirt_sizes, null, ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <h4>@lang('labels.emergency_contact')</h4>
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('family_name', trans('labels.family_nearby').': ', ['class' => 'control-label col-md-2']) !!}
                <div class="col-md-2">
                    {!! Form::text('family_name', !empty($member->user_emergencycontact) ? $member->user_emergencycontact->family_name : null, ['class' => 'form-control']) !!}
                </div>
                {!! Form::label('relation', trans('labels.relationship').': ', ['class' => 'control-label col-md-2']) !!}
                <div class="col-md-2">
                    {!! Form::text('relation', !empty($member->user_emergencycontact) ? $member->user_emergencycontact->relation : null, ['class' => 'form-control']) !!}
                </div>
                {!! Form::label('emergency_contact_number', trans('labels.emergency_contact').': ', ['class' => 'control-label col-md-2']) !!}
                <div class="col-md-2">
                    {!! Form::text('emergency_contact_number', !empty($member->user_emergencycontact) ? $member->user_emergencycontact->emergency_contact_number : null, ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <h4>@lang('labels.doctor_contact')</h4>
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('doctor_name', trans('labels.doctor_name').': ', ['class' => 'control-label col-md-2']) !!}
                <div class="col-md-2">
                    {!! Form::text('doctor_name', !empty($member->user_memberhealthhistory) ? $member->user_memberhealthhistory->doctor_name : null, ['class' => 'form-control']) !!}
                </div>
                {!! Form::label('doctor_contact_number', trans('labels.doctor_contact_number').': ', ['class' => 'control-label col-md-2']) !!}
                <div class="col-md-2">
                    {!! Form::text('doctor_contact_number', !empty($member->user_memberhealthhistory) ? $member->user_memberhealthhistory->doctor_contact_number : null, ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>
        <br>
        <h5><strong>@lang('labels.health_history')</strong></h5>
        <div class="form-group">
            @foreach($member_attributes as $key => $value)
                @php($chosen = 0)
                <div class="checkbox">
                    <label>
                        @if(count($member->member_attribute) > 0)
                            @foreach($member->member_attribute as $chosen_key)
                                @if($key == $chosen_key->id)
                                    @php($chosen = 1)
                                @endif
                            @endforeach
                        @endif
                        @if($chosen == '1')
                            {!! Form::checkbox('member_attribute[]', $key, true) !!} {{$value}}
                        @elseif($chosen == '0')
                            {!! Form::checkbox('member_attribute[]', $key) !!} {{$value}}
                        @endif
                    </label>
                </div>
            @endforeach
        </div>
        <br>
        <div class="col-md-4">
            <div class="form-group">
                {!! Form::label('health_comment', trans('labels.comment').': ', ['class' => 'control-label']) !!}
                {!! Form::textarea('health_comment', !empty($member->user_memberhealthhistory) ? $member->user_memberhealthhistory->comment : null, ['class' => 'form-control', 'rows' => '4']) !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary pull-right']) !!}
        </div>
    </div>
    {!! Form::close() !!}

    @if((\Illuminate\Support\Facades\Auth::user()->role_id < 6) || (\Illuminate\Support\Facades\Auth::user()->role_id == 8))
        @if($member->role_id == '7')
            <!-- Modal -->
            <div id="convertModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="convertModalLabel" aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                            <h5 class="modal-title" id="convertModalLabel">@lang('labels.convert_to_staff')</h5>
                        </div>
                        {!! Form::open(['method' => 'POST', 'action' => ['MemberController@convert_member_into_staff', $member->id], 'class' => 'form-horizontal', 'id' => 'convert-form-'.$member->id]) !!}
                        <div class="modal-body">
                            <div class="form-wrap">
                                <div class="form-group">
                                    {!! Form::label('convert_date', trans('labels.by_date').':', ['class' => 'control-label col-md-3']) !!}
                                    <div class="col-md-4">
                                        {!! Form::date('convert_date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" id="convert-btn-{{$member->id}}" class="btn btn-primary convert-btn">@lang('labels.submit')</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        @endif
    @endif
@endsection

@section('other-script')
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
                navigator.geolocation.getCurrentPosition(function(position) {
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
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAx42dzcUZAXbWOYbCUV8-sEyN9_ljWzcs&libraries=places&callback=initAutocomplete" async defer></script>
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
        $(document).ready(function () {
            $(document).on('click', '.convert-btn', function () {
                var id = $(this).attr('id').split('-').pop();
                $('#convertModal').modal('toggle');
                bootbox.confirm({
                    message: "@lang('messages.are_you_sure')",
                    buttons: {
                        confirm: {
                            label: '@lang('labels.yes')',
                            className: 'btn-success'
                        },
                        cancel: {
                            label: '@lang('labels.no')',
                            className: 'btn-danger'
                        }
                    },
                    callback: function (result) {
                        if (result == false) {
                        } else {
                            $('#convert-form-' + id).submit();
                        }
                    }
                });
            });
        });
    </script>
@endsection
