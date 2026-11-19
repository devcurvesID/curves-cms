@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">

    <style>
        table.table td:first-child {
            width: 150px;
            color: #ff1493;
        }

        table.table td {
            vertical-align: middle !important;
        }

        table.email-fb td:first-child,
        table.email-fb td:nth-child(4),
        table.club-open-hour td:first-child,
        table.club-open-hour td:nth-child(3),
        table.club-open-hour td:nth-child(5),
        table.club-open-hour td:nth-child(7) {
            color: #ff1493;
        }

        table.email-fb td,
        table.club-open-hour td,
        table.club-open-hour-add td {
            padding: 10px;
        }

        h4 {
            color: #663399;
        }

        .btn-primary,
        .voucher-list-add a,
        #edit-voucher-btn {
            background-color: #ff1493;
            color: #ffffff;
            border-color: #ff1493;
        }

        .btn-primary:hover,
        .voucher-list-add a:hover,
        #edit-voucher-btn:hover {
            color: #ff1493;
            background-color: transparent;
            border-color: #ff1493;
        }

        table.club-open-hour-add td:first-child,
        table.club-open-hour-add td:nth-child(3),
        table.club-open-hour-add td:nth-child(5),
        table.club-open-hour-add td:nth-child(7) {
            color: #663399;
        }

    </style>

    <style>
        #tax-config-btn {
            position: absolute;
            right: 30px;
            top: 215px;
        }

        .select2 {
            width: 100%;
        }
    </style>

    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {display:none;}

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: deeppink;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    {!! Form::model($club, ['method' => 'PATCH', 'action' => ['ClubController@update_club_detail', $club->id], 'id' => 'form-club-detail']) !!}
    {!! Form::hidden('club_attribute', $club->id) !!}
    {{-- {!! Form::hidden('club_accurateId', $club->accurate_id, ['id'=> 'club_accurateId']) !!} --}}
    {{-- {!! Form::hidden('club_accurateBranch', $club->accurate_branch, ['id'=> 'club_accurateBranch']) !!} --}}

    <div class="row">
        <div class="col-md-12">
            <a href="{{url('cf-lite/settings')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
        </div>
        <div class="col-md-3 col-md-offset-1">
            @if(!empty($club->photo))
                <img role="button" id="image_preview" class="img-rounded"
                     src="{{asset('images/club_photos/'.$club->photo)}}"
                     style="width: 250px; height: 200px; object-fit: cover;">
            @else
                <img role="button" id="image_preview" class="img-rounded" src="{{asset('images/club.png')}}"
                     style="width: 250px; height: 200px; object-fit: cover;">
            @endif
            <div class="form-group hidden">
                {!! Form::file('photo', ['class' => 'file_multi_video']) !!}
            </div>
        </div>
        <div class="col-md-6">
            <table class="table">
                <tr>
                    <td>@lang('labels.club_name')</td>
                    <td>:</td>
                    <td>{!! Form::text('club_name', null, ['class' => 'form-control']) !!}</td>
                </tr>
                <tr>
                    <td>@lang('labels.club_code')</td>
                    <td>:</td>
                    <td>{!! Form::text('club_code', !empty($club->club_code) ? $club->club_code : sprintf('%03d', $club->id), ['class' => 'form-control']) !!}</td>
                </tr>
                <tr>
                    <td>@lang('labels.address')</td>
                    <td>:</td>
                    <td>{!! Form::text('address', (ucwords($club->address).', '.ucwords($club->city).', '.ucwords($club->province).', '.ucwords($club->country)), ['class' => 'form-control', 'id' => 'autocomplete', 'onFocus' => 'geolocate()']) !!}</td>
                </tr>
                <tr>
                    <td>@lang('labels.postal')</td>
                    <td>:</td>
                    <td>{!! Form::text('postal', null, ['class' => 'form-control']) !!}</td>
                </tr>
                <tr>
                    <td>@lang('labels.phone')</td>
                    <td>:</td>
                    <td>{!! Form::text('phones', null, ['class' => 'form-control']) !!}</td>
                </tr>
                <tr>
                    <td>@lang('labels.status')</td>
                    <td>:</td>
                    <td>{{$club->status}}</td>
                </tr>
                @if((\Illuminate\Support\Facades\Auth::user()->role_id < 3) || (\Illuminate\Support\Facades\Auth::user()->role_id == 8))
                    <tr>
                        <td>@lang('labels.jubilant')</td>
                        <td>:</td>
                        <td>
                            {!! Form::select('jubilant', $jubilants, $club->jubilant_id, ['class' => 'form_control select2', 'placeholder' => trans('labels.choose_jubilant')]) !!}
                        </td>
                    </tr>
                @else
                    {!! Form::hidden('jubilant', $club->jubilant_id) !!}
                @endif
                <tr>
                    <td>@lang('labels.bank')</td>
                    <td>:</td>
                    <td>
                        <select name="bank[]" id="bank" class="form-control select2" multiple="multiple">
                            @if(!empty($clubbanks))
                                @foreach($clubbanks as $cb)
                                    <option selected="selected" value="{{ $cb->id }}">{{ $cb->bank_abv }} - {{ $cb->bank_account_number }}</option>
                                @endforeach
                            @endif
                            @foreach($banks as $bank)
                                @php( $gate = true)
                                @foreach($clubbanks as $cb)
                                    @if($bank->id == $cb->id)
                                        @php( $gate = false)
                                    @endif
                                @endforeach
                                @if($gate)
                                    @if($bank->bank_chartofaccount->jubilant_id == $club->jubilant_id)
                                        <option value="{{ $bank->id }}">{{ $bank->bank_abv }} - {{ $bank->bank_account_number }}</option>
                                    @endif
                                @endif
                            @endforeach
                        </select>
                    </td>
                </tr>

                <!-- ACCURATE -->

                {{-- <tr>
                    <td>Accurate </td>
                    <td>:</td>
                    <td>
                        <select name="accurate_id" id="accurate_id" class="form-control select2">
                            <option value="">No Accurate Club</option>
                            @if($accurateDB)
                                @foreach($accurateDB as $accu)
                                @if ($accu['id'] == $club->accurate_id)
                                    <option value="{{ $accu['id'] }}" selected>{{ $accu['alias'] }}</option>
                                @endif
                                    <option value="{{ $accu['id'] }}">{{ $accu['alias'] }}</option>
                                @endforeach
                            @endif
                        </select>
                        <br>
                        <span style="font-size:14px">(leave blank if club has no equivalent club in Accurate)</span>
                    </td>
                </tr>

                <tr id="accurateBranches" class="hidden">
                    <td>Branch </td>
                    <td>:</td>
                    <td>
                        <select name="accurate_branch" id="accurate_branch" class="form-control select2">

                        </select>
                    </td>
                </tr> --}}

                <!-- / ACCURATE -->

                <tr>
                    <td>@lang('labels.headquarter')</td>
                    <td>:</td>
                    <td>
                        <div class="switch">
                            <label>
                                @if($club->hq)
                                    <input name="hq" class="hq" type="checkbox" id="status" checked="checked">
                                @else
                                    <input name="hq" class="hq" type="checkbox" id="status">
                                @endif
                                <span class="slider round"></span>
                            </label>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-md-2">
            <a id="tax-config-btn" target="_blank" href="{{url('cf-lite/club/'.$club->id.'/tax-config')}}" class="btn btn-primary btn-md" title="@lang('labels.tax_config')">@lang('labels.tax') <span class="fa fa-wrench"></span></a>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="row">
                <table class="email-fb">
                    <tr>
                        <td>{!! Form::label('email', trans('labels.email'), ['class' => 'control-label']) !!}</td>
                        <td>:</td>
                        <td>{!! Form::text('email', null, ['class' => 'form-control']) !!}</td>
                        <td>{!! Form::label('email', trans('labels.facebook'), ['class' => 'control-label']) !!}</td>
                        <td>:</td>
                        <td>{!! Form::text('facebook', null, ['class' => 'form-control']) !!}</td>
                        <td>
                            <button type="button" name="submit-info" class="pull-right btn btn-default"><span
                                        class="fa fa-save"></span></button>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <h4>@lang('labels.club_open_hour')</h4>
            <div class="col-md-12">
                <div id="club-open-hour-box">
                    @if(count($club->clubinfo_clubopenhour) > 0)
                        @php($sequence_start = 'a')
                        @foreach($club->clubinfo_clubopenhour as $club_open_hour)
                            <div class="form-inline" id="form-inline-{{$club_open_hour->id}}">
                                {!! Form::hidden('schedule_id['.$club_open_hour->id.']', $club_open_hour->id) !!}
                                <table class="club-open-hour">
                                    <tr>
                                        <td>{!! Form::label('days', trans('labels.choose_day'), ['class' => 'control-label']) !!}</td>
                                        <td>{!! Form::select('from_day['.$club_open_hour->id.']', ['Monday' => 'Monday', 'Tuesday' => 'Tuesday', 'Wednesday' => 'Wednesday', 'Thursday' => 'Thursday', 'Friday' => 'Friday', 'Saturday' => 'Saturday'], $club_open_hour->from_day, ['class' => 'form-control']) !!}</td>
                                        <td>{!! Form::label('to_days', trans('labels.to'), ['class' => 'control-label']) !!}</td>
                                        <td>{!! Form::select('thru_day['.$club_open_hour->id.']', ['Monday' => 'Monday', 'Tuesday' => 'Tuesday', 'Wednesday' => 'Wednesday', 'Thursday' => 'Thursday', 'Friday' => 'Friday', 'Saturday' => 'Saturday'], $club_open_hour->thru_day, ['class' => 'form-control']) !!}</td>
                                        <td><label class="control-label"><span class="fa fa-clock-o"></span></label>
                                        </td>
                                        <td>{!! Form::text('from_hour['.$club_open_hour->id.']', \Carbon\Carbon::parse($club_open_hour->from_hour)->setTimezone(Session::get('timezone'))->format('H:i'), ['class' => 'form-control hour']) !!}</td>
                                        <td>{!! Form::label('to_hours', trans('labels.to'), ['class' => 'control-label']) !!}</td>
                                        <td>{!! Form::text('thru_hour['.$club_open_hour->id.']', \Carbon\Carbon::parse($club_open_hour->thru_hour)->setTimezone(Session::get('timezone'))->format('H:i'), ['class' => 'form-control hour']) !!}</td>
                                        <td>{!! Form::button(trans('labels.delete'), ['class' => 'btn btn-danger btn-delete-schedule', 'id' => 'delete-schedule-'.$club_open_hour->id]) !!}</td>
                                    </tr>
                                </table>
                            </div>
                        @endforeach
                    @else
                        @php($sequence_start = 'b')
                        <div class="form-inline" id="form-inline-a">
                            {!! Form::hidden('schedule_id[a]', 'a') !!}
                            <table class="club-open-hour">
                                <tr>
                                    <td>{!! Form::label('days', trans('labels.choose_day'), ['class' => 'control-label']) !!}</td>
                                    <td>{!! Form::select('from_day[a]', ['Monday' => 'Monday', 'Tuesday' => 'Tuesday', 'Wednesday' => 'Wednesday', 'Thursday' => 'Thursday', 'Friday' => 'Friday', 'Saturday' => 'Saturday'], 'Monday', ['class' => 'form-control']) !!}</td>
                                    <td>{!! Form::label('to_days', trans('labels.to'), ['class' => 'control-label']) !!}</td>
                                    <td>{!! Form::select('thru_day[a]', ['Monday' => 'Monday', 'Tuesday' => 'Tuesday', 'Wednesday' => 'Wednesday', 'Thursday' => 'Thursday', 'Friday' => 'Friday', 'Saturday' => 'Saturday'], 'Friday', ['class' => 'form-control']) !!}</td>

                                    <td><label class="control-label"><span class="fa fa-clock-o"></span></label></td>
                                    <td>{!! Form::text('from_hour[a]', '10:00', ['class' => 'form-control hour']) !!}</td>
                                    <td>{!! Form::label('to_hours', trans('labels.to'), ['class' => 'control-label']) !!}</td>
                                    <td>{!! Form::text('thru_hour[a]', '12:00', ['class' => 'form-control hour']) !!}</td>
                                </tr>
                            </table>
                        </div>
                    @endif
                </div>
                {!! Form::hidden('sequence', $sequence_start) !!}
                <button id="add-new-schedule" type="button" class="btn btn-primary btn-lg"
                        title="@lang('labels.add') @lang('labels.schedule')">
                    <span class="fa fa-plus"></span>
                </button>
            </div>
            <div class="col-md-10">
                <button type="button" name="submit-schedule" class="pull-right btn btn-default"><span
                            class="fa fa-save"></span></button>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <h4>@lang('labels.club_service_fee')</h4>
        </div>
        <div class="col-md-6">
            <button type="button" name="submit-csf" class="pull-right btn btn-default"><span class="fa fa-save"></span>
            </button>
            <button type="button" class="pull-right btn btn-primary" id="add-new-csf"
                    title="@lang('labels.add') @lang('labels.membership_type_fee')" style="margin-right: 20px;"><span class="fa fa-plus"></span>
            </button>
        </div>
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>@lang('labels.membership_type')</th>
                        <th>@lang('labels.service_fee') (@lang('labels.currency'))</th>
                        <th>@lang('labels.monthly_fee') (@lang('labels.currency'))</th>
                        <th>@lang('labels.service_fee_discount') (%)</th>
                        <th>@lang('labels.service_fee_discount') (@lang('labels.currency'))</th>
                        <th>@lang('labels.agreement_renewal')</th>
                        <th>@lang('labels.taxable')</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="csf-box">
                    @if(count($csfs) > 0)
                        @foreach($csfs as $csf)
                            <tr id="csf-row-{{$csf->id}}">
                                {!! Form::hidden('csf_id['.$csf->id.']', $csf->id) !!}
                                <td>
                                    {{$csf->clubservicefee_membershiptype->membership_type_name}}
                                    {!! Form::hidden('membership_type['.$csf->id.']', $csf->membership_type_id) !!}
                                </td>
                                <td>{!! Form::text('sf_amount['.$csf->id.']', $csf->sf_amount, ['class' => 'form-control']) !!}</td>
                                <td>{!! Form::text('mf_amount['.$csf->id.']', $csf->mf_amount, ['class' => 'form-control']) !!}</td>
                                <td>{!! Form::text('pre_pay_discount['.$csf->id.']', $csf->pre_pay_discount, ['class' => 'form-control']) !!}</td>
                                <td>{!! Form::text('pre_pay_discount_value['.$csf->id.']', $csf->pre_pay_discount_value, ['class' => 'form-control']) !!}</td>
                                <td>{!! Form::select('agreement_renewal['.$csf->id.']', $agreement_renewals, $csf->agreement_renewal_type_id, ['class' => 'form-control']) !!}</td>
                                <td>
                                    <div class="checkbox">
                                        <label>
                                            {!! Form::checkbox('is_taxable['.$csf->id.']', 'on', ($csf->is_taxable == 'Yes') ? true : false) !!}
                                        </label>
                                    </div>
                                </td>
                                <td>
                                    {!! Form::button(trans('labels.delete'), ['class' => 'btn btn-danger csf-delete', 'id' => 'csf-delete-'.$csf->id]) !!}
                                </td>
                            </tr>
                        @endforeach
                    @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            {!! Form::submit(trans('labels.save_all'), ['class' => 'pull-right btn btn-primary btn-lg']) !!}
            <a href="{{url('cf-lite/club/'.$club->id)}}"
               class="pull-right btn btn-default btn-lg" style="margin-right: 20px;">@lang('labels.reset')</a>
        </div>
    </div>
    {!! Form::hidden('attribute', null) !!}
    {!! Form::close() !!}
@endsection

@section('other-script')
    <script src="{{asset('vendors/moment/min/moment.min.js')}}"></script>
    <script src="{{asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2();
        });
    </script>
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
        function nextChar(c) {
            return String.fromCharCode(c.charCodeAt(0) + 1);
        }

        $('#add-new-schedule').on('click', function () {
            var sequence = $('input[name="sequence"]').val();
            var view = '';
            view = '<div class="form-inline" id="form-inline-' + sequence + '">' +
                '<input name="schedule_id[' + sequence + ']" type="hidden" value="' + sequence + '">' +
                '<table class="club-open-hour-add">' +
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
                '<td>' +
                '<label class="control-label">' +
                '<span class="fa fa-clock-o"></span>' +
                '</label></td>' +
                '<td><input class="form-control hour" name="from_hour[' + sequence + ']" type="text" value="10:00"></td>' +
                '<td><label class="control-label">@lang('labels.to')</label></td>' +
                '<td><input class="form-control hour" name="thru_hour[' + sequence + ']" type="text" value="12:00"></td>' +
                '<td>' +
                '<button class="btn btn-danger btn-delete-schedule" id="delete-schedule-' + sequence + '" type="button">@lang('labels.delete')</button>' +
                '</td>' +
                '</tr>' +
                '</table>' +
                '</div>';

            $('#club-open-hour-box').append(view);
            $('input[name="sequence"]').val(nextChar(sequence));
        });
    </script>
    <script>
        $('#add-new-csf').on('click', function () {
            var sequence = $('input[name="sequence"]').val();
            var csf_view = '';
            csf_view = '<tr id="csf-row-' + sequence + '">' +
                '<input name="csf_id[' + sequence + ']" type="hidden" value="' + sequence + '">' +
                '<td>' +
                '<select name="membership_type[' + sequence + ']" class="form-control">' +
                '@foreach($membership_types as $key => $value)<option value="{{$key}}">{{$value}}</option>@endforeach' +
                '</select>' +
                '</td>' +
                '<td><input class="form-control" name="sf_amount[' + sequence + ']" type="text"></td>' +
                '<td><input class="form-control" name="mf_amount[' + sequence + ']" type="text"></td>' +
                '<td><input class="form-control" name="pre_pay_discount[' + sequence + ']" type="text"></td>' +
                '<td><input class="form-control" name="pre_pay_discount_value[' + sequence + ']" type="text"></td>' +
                '<td>' +
                '<select name="agreement_renewal[' + sequence + ']" class="form-control">' +
                '@foreach($agreement_renewals as $key => $value)<option value="{{$key}}">{{$value}}</option>@endforeach' +
                '</select>' +
                '</td>' +
                '<td>' +
                '<div class="checkbox">' +
                '<label><input name="is_taxable[' + sequence + ']" type="checkbox" value="on"></label>' +
                '</div>' +
                '</td>' +
                '<td><button class="btn btn-danger csf-delete" id="csf-delete-' + sequence + '" type="button">@lang('labels.delete')</button></td>' +
                '</tr>';

            $('#csf-box').append(csf_view);
            $('input[name="sequence"]').val(nextChar(sequence));
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#club-open-hour-box').on('focus', '.hour', function () {
                $(this).datetimepicker({
                    format: 'HH:mm',
                    stepping: 30
                });
            });

            $('#club-open-hour-box').on('click', '.btn-delete-schedule', function () {
                var id = $(this).attr('id').split('-').pop();
                $('#form-inline-' + id).remove();
            });

            $('#csf-box').on('click', '.csf-delete', function () {
                var id = $(this).attr('id').split('-').pop();
                $('#csf-row-' + id).remove();
            });

            $('button[name="submit-info"]').on('click', function () {
                $('input[name="attribute"]').val('info');
                $('#form-club-detail').submit();
            });

            $('button[name="submit-schedule"]').on('click', function () {
                $('input[name="attribute"]').val('schedule');
                $('#form-club-detail').submit();
            });

            $('button[name="submit-csf"]').on('click', function () {
                $('input[name="attribute"]').val('csf');
                $('#form-club-detail').submit();
            });

            /* $('#accurate_id').on('change', function () {
                if($(this).val() != ''){
                    //getBranches($(this).val())
                }else{
                    //hide branch dropdown
                    $('#accurateBranches').removeClass('hidden');
                    $('#accurateBranches').addClass('hidden');
                }
            }); */


            /* $('#accurate_branch').on('change', function () {
                //console.log($(this).val());
            });

            if ($('#club_accurateBranch').val() != '') {
                //getBranches($('#club_accurateId').val())
            } */
        });

        function getBranches($accuId){
            $.ajax({
                url: "http://163.47.8.52/curves/cf-lite/club/branches/" + $accuId, //live
                //url: "http://localhost:8000/cf-lite/club/branches/" + $accuId, //dev
                type: 'GET',
                dataType: 'json', // added data type
                beforeSend: function() {
                    var opt = '<option value=""></option>';
                    $('#accurate_branch').append(opt);
                    $('#accurate_branch').val('');
                },
                success: function(res) {
                    $("#accurate_branch option").remove();
                    if(res.length > 0){
                        var opt = '<option value="">Select Branch</option>';
                        $('#accurate_branch').append(opt);
                        $("#accurate_branch").select2({ width: '200px' });
                        var club_accurateBranch = $('#club_accurateBranch').val();
                        res.forEach(element => {
                            if(club_accurateBranch == element.id){
                                opt = '<option value="'+ element.id +'" selected>'+ element.name +'</option>';
                            }else{
                                opt = '<option value="'+ element.id +'">'+ element.name +'</option>';
                            }
                            $('#accurate_branch').append(opt);
                        });

                        $('#accurateBranches').removeClass('hidden');
                    }else{
                        var opt = '<option value=""></option>';
                        $('#accurate_branch').append(opt);
                        $('#accurate_branch').val('');
                        $('#accurateBranches').removeClass('hidden');
                        $('#accurateBranches').addClass('hidden');
                    }
                }
            });
        }
    </script>
    <script>
        $(document).ready(function () {
            $.fn.select2.amd.define('select2/selectAllAdapter', [
                'select2/utils',
                'select2/dropdown',
                'select2/dropdown/attachBody'
            ], function (Utils, Dropdown, AttachBody) {

                function SelectAll() {
                }

                SelectAll.prototype.render = function (decorated) {
                    var self = this,
                        $rendered = decorated.call(this),
                        $selectAll = $(
                            '<button class="btn btn-xs btn-success" type="button" style="margin-left:6px;"><i class="fa fa-check-square-o"></i> @lang('labels.select_all')</button>'
                        ),
                        $unselectAll = $(
                            '<button class="btn btn-xs btn-danger" type="button" style="margin-left:6px;"><i class="fa fa-square-o"></i> @lang('labels.unselect_all')</button>'
                        ),
                        $btnContainer = $('<div style="margin-top:3px;">').append($selectAll).append($unselectAll);
                    if (!this.$element.prop("multiple")) {
                        // this isn't a multi-select -> don't add the buttons!
                        return $rendered;
                    }
                    $rendered.find('.select2-dropdown').prepend($btnContainer);
                    $selectAll.on('click', function (e) {
                        var $results = $rendered.find('.select2-results__option[aria-selected=false]');
                        $results.each(function () {
                            self.trigger('select', {
                                data: $(this).data('data')
                            });
                        });
                        self.trigger('close');
                    });
                    $unselectAll.on('click', function (e) {
                        var $results = $rendered.find('.select2-results__option[aria-selected=true]');
                        $results.each(function () {
                            self.trigger('unselect', {
                                data: $(this).data('data')
                            });
                        });
                        self.trigger('close');
                    });
                    return $rendered;
                };

                return Utils.Decorate(
                    Utils.Decorate(
                        Dropdown,
                        AttachBody
                    ),
                    SelectAll
                );

            });

            $('#bank').select2({
                dropdownAdapter: $.fn.select2.amd.require('select2/selectAllAdapter')
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('select[name="jubilant"]').on('change', function() {
                console.log('masuk riki');

                html = '';
                        @foreach($banks as $bank)
                var jubilant = '{{$bank->bank_chartofaccount->jubilant_id}}';

                if (jubilant == $(this).val()) {
                    html += '<option value="{{ $bank->id }}">{{ $bank->bank_abv }} - {{ $bank->bank_account_number }}</option>';
                }
                @endforeach

                $('#bank').html(html);
            });
        });
    </script>
@endsection
