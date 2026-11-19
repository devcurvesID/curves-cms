@extends('cf_back.layouts.master-dashboard')

@section('title', 'Edit Member')

@section('other-style')
    <link href={{asset("vendors/iCheck/skins/flat/green.css")}} rel="stylesheet">
    <link href={{asset("vendors/select2/dist/css/select2.min.css")}} rel="stylesheet">
    <style>
        .select2 {
            width: 100% !important;
        }

        textarea {
            resize: none;
        }

        label {
            padding-left: 0px !important;
        }

        .preview-img {
            width: 200px;
            height: 200px;
            border-radius: 50%;
        }

        .preview-img:hover .inner-img {
            background-color: rgba(0, 0, 0, .5);
            cursor: pointer;
        }

        .preview-img:hover .inner-img i {
            opacity: 1;
        }

        .inner-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            background-color: rgba(0, 0, 0, .0);
            text-align: center;
            color: white;
            margin: 0px auto;
            overflow: hidden;
            transition: 0.5s;
            display: table;
        }

        .inner-img i {
            transition: 0.5s;
            opacity: 0;
            display: table-cell;
            vertical-align: middle;
            font-size: 18px;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="my-section">

            @include('cf_back.error-message')

            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#personal">@lang('labels.personal_information')</a></li>
                <li><a data-toggle="tab" href="#mkt_pref">@lang('labels.marketing_preferences')</a></li>
                <li><a data-toggle="tab" href="#family_addon">@lang('labels.family_addon')</a></li>
                <li><a href="{{url('member/'.$user->id.'/edit/status')}}">@lang('labels.status')</a></li>
                <li><a href="{{url('member/'.$user->id.'/measurement')}}">@lang('labels.measurement')</a></li>
                <li><a href="{{url('member/'.$user->id.'/workout')}}">@lang('labels.workout')</a></li>
            </ul>

            {!! Form::model($user->user_memberpersonal, ['method' => 'PATCH', 'action' => ['MemberController@update', $user->id], 'id' => 'formcountry', 'files' => 'true']) !!}
            <div class="tab-content">
                <div id="personal" class="tab-pane fade in active">

                    <div class="section-title">
                        <center>
                            <h1 class="page-title">
                                @lang('labels.edit') @lang('labels.user') (@lang('labels.member'))<br/>
                                <div class="page-subtitle" style="margin-top: 15px;">@lang('labels.personal_information')</div>
                            </h1>

                        </center>
                    </div>
                    @include('cf_back.flash-message')
                    <div class="col-lg-6 col-md-8 col-sm-10 col-lg-push-3 col-md-push-2 col-sm-push-1" style="margin-top: 30px;">
                        @if(isset($user))
                            {!! Form::hidden('attribute', $user->id) !!}
                        @endif
                        <div class="col-sm-12 col-xs-12">
                            {!! Form::label('joined', trans('labels.joined').': '.date('d F Y', strtotime($user->user_memberpersonal->joined)), ['class' => 'control-label']) !!}
                        </div>
                        <div class="col-sm-9 col-xs-12">
                            <div class="row">
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        {!! Form::label('name', trans('labels.name'), ['class' => 'control-label']) !!}
                                        {!! Form::text('name', $user->name, ['class' => 'form-control', 'placeholder' => trans('labels.input').' '.trans('labels.name')]) !!}
                                    </div>
                                </div>
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        {!! Form::label('nik', trans('labels.nik'), ['class' => 'control-label']) !!}
                                        {!! Form::text('nik', null, ['class' => 'form-control', 'placeholder' => trans('labels.input').' '.trans('labels.nik')]) !!}
                                    </div>
                                </div>
                                <div class="col-xs-12">
                                    <div class="form-group">
                                        {!! Form::label('birth', trans('labels.birthday'), ['class' => 'control-label']) !!}
                                        {!! Form::text('birth', null, ['class' => 'form-control', 'id' => 'bday']) !!}
                                    </div>
                                </div>
                                <div class="col-xs-12 hidden">
                                    <div class="form-group">
                                        {!! Form::label('photo', 'Ganti Foto:', ['class' => 'control-label']) !!}
                                        {!! Form::file('photo', null) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-sm-3 col-xs-12">
                            <center>
                                <div class="preview-img my-bg"
                                     style="background-image: url({{asset('memberphotos/'.$user->user_memberpersonal->photo)}})">
                                    <div class="inner-img">
                                        <i>@lang('labels.edit')</i>
                                        <i class="fa fa-camera"></i>
                                        <i>@lang('labels.photo')</i>
                                    </div>
                                </div>
                            </center>
                            {{--<img src="{{asset('staffphotos/'.$user->user_staffpersonal->photo)}}"--}}
                            {{--style="width: 150px; height: auto;">--}}
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group">
                                {!! Form::label('sex', 'Jenis Kelamin:', ['class' => 'control-label']) !!}
                                <div class="radio">
                                    <label>{!! Form::radio('sex', 'M',false, ['class' => 'flat']) !!} @lang('labels.male')</label>
                                </div>
                                <div class="radio">
                                    <label>{!! Form::radio('sex', 'F',false, ['class' => 'flat']) !!} @lang('labels.female')</label>
                                </div>
                            </div>
                        </div>
                        {{--<div class="col-sm-4 col-xs-12">--}}
                            {{--<div class="form-group">--}}
                                {{--{!! Form::label('country_id', trans('labels.country'), ['class' => 'control-label']) !!}--}}
                                {{--@if(count($countries) > 0):--}}
                                {{--{!! Form::select('country_id', $countries, null, ['class' => 'form-control', 'placeholder' => 'Pilih Negara', 'id' => 'countries']) !!}--}}
                                {{--@else--}}
                                    {{--<p>@lang('labels.country_not_found')</p>--}}
                                {{--@endif--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="col-sm-4 col-xs-12">--}}
                            {{--<div class="form-group" id="province-group">--}}
                                {{--{!! Form::label('province_id', trans('labels.province'), ['class' => 'control-label']) !!}--}}
                                {{--<div id="province-select">--}}
                                    {{--@if(count($provinces) > 0)--}}
                                        {{--{!! Form::select('province_id', $provinces, null, ['class' => 'form-control', 'placeholder' => 'Pilih Provinsi', 'id' => 'provincies']) !!}--}}
                                    {{--@else--}}
                                        {{--<p>@lang('labels.province_not_found')</p>--}}
                                    {{--@endif--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        {{--<div class="col-sm-4 col-xs-12">--}}
                            {{--<div class="form-group" id="city-group">--}}
                                {{--{!! Form::label('city_id', trans('labels.city'), ['class' => 'control-label']) !!}--}}
                                {{--<div id="city-select">--}}
                                    {{--@if(count($cities) > 0)--}}
                                        {{--{!! Form::select('city_id', $cities, null, ['class' => 'form-control', 'placeholder' => 'Pilih Kota', 'id' => 'cities']) !!}--}}
                                    {{--@else--}}
                                        {{--<p>@lang('labels.city_not_found')</p>--}}
                                    {{--@endif--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                        <div class="col-xs-12">
                            <div class="form-group">
                                {!! Form::label('address', trans('labels.address'), ['class' => 'control-label']) !!}
                                {!! Form::textarea('address', null, ['class' => 'form-control', 'placeholder' => trans('labels.input').' '.trans('labels.address')]) !!}
                            </div>
                        </div>
                        <div class="col-sm-4 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('postal', trans('labels.postal_code'), ['class' => 'control-label']) !!}
                                {!! Form::text('postal', null, ['class' => 'form-control','data-inputmask' => '"mask": "99999"', 'placeholder' => trans('labels.input').' '.trans('labels.postal_code')]) !!}
                            </div>
                        </div>
                        <div class="col-sm-4 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('phone', trans('labels.phone'), ['class' => 'control-label']) !!}
                                {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => trans('labels.input').' '.trans('labels.phone')]) !!}
                            </div>
                        </div>
                        <div class="col-sm-4 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('cellphone', trans('labels.mobile_phone'), ['class' => 'control-label']) !!}
                                {!! Form::text('cellphone', null, ['class' => 'form-control', 'placeholder' => trans('labels.input').' '.trans('labels.mobile_phone')]) !!}
                            </div>
                        </div>


                        <div class="col-xs-12">
                            <hr>
                            <h3>@lang('labels.club')</h3>
                        </div>

                        <div class="col-sm-12 col-xs-12">
                            <div class="form-group">
                                {!! Form::label('club_id', 'Pilih Club', ['class' => 'control-label']) !!}
                                @if(count($clubs) > 0):
                                {!! Form::select('club_id', $clubs, $user->club_id, ['class' => 'form-control', 'placeholder' => 'Pilih Klub']) !!}
                                @else
                                    <p>@lang('labels.club_not_found')</p>
                                @endif
                            </div>
                        </div>

                        {{--<div class="col-sm-12 col-xs-12">--}}
                            {{--<div class="form-group">--}}
                                {{--{!! Form::label('source_id', 'Sumber: '.$user->user_memberpersonal->memberpersonal_membersource->source, ['class' => 'control-label']) !!}--}}
                            {{--</div>--}}
                        {{--</div>--}}

                        {{--@if($user->user_memberpersonal->source_id == '1'):--}}
                            {{--<div class="col-sm-12 col-xs-12">--}}
                                {{--<div class="form-group">--}}
                                    {{--{!! Form::label('br_id', 'Buddy Referral: '.$user->user_memberfromguest->memberfromguest_brl->first_name.' '.$user->user_memberfromguest->memberfromguest_brl->sur_name, ['class' => 'control-label']) !!}--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--@endif--}}

                        <div class="col-xs-12">
                            <hr>
                            <div class="form-group">
                                {!! Form::label('key_tag_id', trans('labels.key_tag_id'), ['class' => 'control-label']) !!}
                                {!! Form::text('key_tag_id', null, ['class' => 'form-control', 'placeholder' => trans('labels.input').' '.trans('labels.key_tag_idphp ')]) !!}
                            </div>
                        </div>
                        <div class="col-xs-12">
                            <div class="form-group">
                                {!! Form::submit(trans('labels.edit').' '.trans('labels.data').' '.trans('labels.user'), ['class' => 'btn btn-primary form-control', 'name' => 'personal_data']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                {{--<div id="mkt_pref" class="tab-pane fade">--}}
                    {{--<h3>@lang('labels.marketing_preferences')</h3>--}}
                    {{--<br>--}}
                    {{--<div class="col-xs-12" style="margin-top: 30px;">--}}
                        {{--@if(count($mktprefs) > 0)--}}
                            {{--@foreach($mktprefs as $mktpref)--}}
                                {{--<div class="checkbox">--}}
                                    {{--<label>--}}
                                        {{--@if($user->member_mktpref->contains($mktpref->id))--}}
                                            {{--{!! Form::checkbox('mktpref[]', $mktpref->id, null, ['checked' => 'checked', 'class' => 'flat']) !!} {{$mktpref->m_preferences}}--}}
                                        {{--@else--}}
                                            {{--{!! Form::checkbox('mktpref[]', $mktpref->id, null, ['class' => 'flat'] )!!} {{$mktpref->m_preferences}}--}}
                                        {{--@endif--}}
                                    {{--</label>--}}
                                {{--</div>--}}
                            {{--@endforeach--}}
                        {{--@else--}}
                            {{--<p>No Marketing Preferences Found</p>--}}
                        {{--@endif--}}
                    {{--</div>--}}
                    {{--<div class="col-xs-12">--}}
                        {{--<div class="form-group">--}}
                            {{--<br/>--}}
                            {{--<br/>--}}
                            {{--{!! Form::submit(trans('labels.edit').' '.trans('labels.marketing_preferences'), ['class' => 'btn btn-primary form-control', 'name' => 'marketing_preference']) !!}--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
                {{--<div id="family_addon" class="tab-pane fade">--}}
                    {{--<h3>@lang('labels.family_addon')</h3>--}}
                    {{--<br>--}}
                    {{--<div class="col-lg-6 col-md-8 col-sm-10 col-lg-push-3 col-md-push-2 col-sm-push-1" style="margin-top: 30px;">--}}
                        {{--<div class="form-group">--}}
                            {{--{!! Form::label('family_id', trans('labels.family_addon'), ['class' => 'control-label']) !!}--}}
                            {{--@if(!empty(count($members)))--}}
                                {{--@if(!empty($user->user_familyaddon))--}}
                                    {{--{!! Form::select('family_id', $members, $user->user_familyaddon->family_id, ['class' => 'form-control', 'id' => 'select_member', 'placeholder' => trans('labels.choose').' '.trans('labels.family_addon')]) !!}--}}
                                {{--@else--}}
                                    {{--{!! Form::select('family_id', $members, null, ['class' => 'form-control', 'id' => 'select_member', 'placeholder' => trans('labels.choose').' '.trans('labels.family_addon')]) !!}--}}
                                {{--@endif--}}
                            {{--@else--}}
                                {{--<p>Belum ada Member yang terdaftar</p>--}}
                            {{--@endif--}}
                        {{--</div>--}}
                        {{--<div class="form-group">--}}
                            {{--{!! Form::label('siblings', trans('labels.siblings'), ['class' => 'control-label']) !!}--}}
                            {{--@if(!empty($user->user_familyaddon))--}}
                                {{--{!! Form::text('siblings', $user->user_familyaddon->siblings, ['class' => 'form-control', 'placeholder' => trans('labels.input').' '.trans('labels.siblings')]) !!}--}}
                            {{--@else--}}
                                {{--{!! Form::text('siblings', null, ['class' => 'form-control', 'placeholder' => trans('labels.input').' '.trans('labels.siblings')]) !!}--}}
                            {{--@endif--}}
                        {{--</div>--}}
                    {{--</div>--}}
                    {{--<div class="col-xs-12">--}}
                        {{--<div class="form-group">--}}
                            {{--{!! Form::submit(trans('labels.edit').' '.trans('labels.family_addon'), ['class' => 'btn btn-primary form-control', 'name' => 'family_addon']) !!}--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection


@section('other-script')
    <script src={{asset("vendors/iCheck/icheck.min.js")}}></script>
    <script src={{asset("js/jquery.dataTables.min.js")}}></script>
    <script src={{asset("js/dataTables.bootstrap.min.js")}}></script>
    <script src={{asset("vendors/select2/dist/js/select2.js")}}></script>
    <script src={{asset("vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js")}}></script>
    <script src={{asset("vendors/moment/min/moment.min.js")}}></script>
    <script src={{asset("vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js")}}></script>
    <script>
        $(document).ready(function () {
            $('#select_role').select2({
                placeholder: "<?php echo trans('labels.choose') . ' ' . trans('labels.role'); ?>"
            });
            $('#select_club').select2({
                placeholder: "<?php echo trans('labels.choose') . ' ' . trans('labels.club'); ?>"
            });
            $('#countries').select2({
                placeholder: "<?php echo trans('labels.choose') . ' ' . trans('labels.country'); ?>"
            });
            $('#provincies').select2({
                placeholder: "<?php echo trans('labels.choose') . ' ' . trans('labels.country'); ?>"
            });
            $('#cities').select2({
                placeholder: "<?php echo trans('labels.choose') . ' ' . trans('labels.country'); ?>"
            });
            $('#select_member').select2({
                placeholder: "<?php echo trans('labels.choose') . ' ' . trans('labels.family_addon'); ?>"
            });

            $('.preview-img').on('click', function () {
                $('#photo').click();
            });
        });
    </script>

    <meta name="_token" content="{!! csrf_token() !!}"/>
    <script>
        $(document).ready(function () {
            $('#bday').datetimepicker({
                format: 'YYYY/MM/DD'
            });


            var url = "/curvesindonesia/public/";
            $('#countries').on('change', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                var formData = {
                    country_id: $('#countries').val()
                };
                var type = 'POST';
                var my_url = url + 'getprovince';
                var wew = "";

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        var province = '<select class="form-control js-state" id="provinces" name="province_id" placeholder="<?php echo trans('labels.club_province_placeholder'); ?>"><option selected><?php echo trans('labels.choose') . ' ' . trans('labels.province')?></option>';
                        $.each(data, function (i, item) {
                            province += '<option value="' + i + '">' + item + '</option>'
                        });
                        province += '</select>';
                        $('#province-select').html(province);
                        $('#province-group').show();
                        $('#provinces').select2({
                            placeholder: "<?php echo trans('labels.province'); ?>"
                        });
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });

            $(document).on('change', '#provinces', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                var formData = {
                    province_id: $('#provinces').val()
                };

                var type = 'POST';
                var my_url = url + 'getcities';
                var wew = "";

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        var city = '<select class="form-control js-state" id="cities" name="city_id" placeholder="<?php echo trans('labels.club_city_placeholder'); ?>"><option selected><?php echo trans('labels.choose') . ' ' . trans('labels.city')?></option>';
                        $.each(data, function (i, item) {
                            city += '<option value="' + i + '">' + item + '</option>'
                        });

                        city += '</select>';

                        $('#city-select').html(city);
                        $('#city-group').show();
                        $('#cities').select2({
                            placeholder: "<?php echo trans('labels.city'); ?>"
                        });
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });
        });
    </script>
@endsection


