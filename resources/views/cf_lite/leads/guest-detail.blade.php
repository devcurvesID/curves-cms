@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('select2/dist/css/select2.min.css')}}" rel="stylesheet">
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
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-sm-12">
            <center>
                <h2 class="page-title">@lang('labels.guest_detail') (@lang('labels.fa_form'))</h2>
            </center>
        </div>
    </div>

    {!! Form::model($lead, ['method' => 'PATCH', 'action' => ['LeadController@lead_update', $guest_appointment->id]]) !!}
    {!! Form::hidden('attribute', $lead->id) !!}
    <div class="row">
        <div class="col-md-3">
            <img role="button" id="image_preview" class="img-circle" src="{{asset('images/img.png')}}" style="width: 150px; height: 150px; object-fit: cover;">
        </div>
        <div class="col-md-4">
            <table>
                <tr>
                    <td><span class="fa fa-female" title="@lang('labels.fullname')"></span></td>
                    <td>:</td>
                    <td>{!! Form::text('fullname', null, ['class' => 'form-control', 'style' => 'margin-bottom: 10px;']) !!}</td>
                </tr>
                <tr>
                    <td><span class="fa fa-calendar" title="@lang('labels.birth')"></span></td>
                    <td>:</td>
                    <td>{!! Form::date('birth', !empty($lead->buddyreferrallead_guestdetail->birth) ? \Carbon\Carbon::parse($lead->buddyreferrallead_guestdetail->birth) : null, ['class' => 'form-control', 'style' => 'margin-bottom: 10px;']) !!}</td>
                </tr>
                <tr>
                    <td><span class="fa fa-envelope-square" title="@lang('labels.email')"></span></td>
                    <td>:</td>
                    <td>{!! Form::text('email', !empty($lead->buddyreferrallead_guestdetail->email) ? $lead->buddyreferrallead_guestdetail->email : null, ['class' => 'form-control', 'style' => 'margin-bottom: 10px;']) !!}</td>
                </tr>
                <tr>
                    <td><span class="fa fa-phone" title="@lang('labels.phone')"></span></td>
                    <td>:</td>
                    <td>{!! Form::text('phone', null, ['class' => 'form-control', 'style' => 'margin-bottom: 10px;']) !!}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-4">
            <h4 style="text-align: right;">@lang('labels.fa_date'): {{date('d F Y', strtotime($guest_appointment->fa_date))}}</h4>
            <h4 style="text-align: right;">@lang('labels.club'): {{$lead->buddyreferrallead_club->club_name}}</h4>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-4">
            <div class="form-inline">
                <div class="form-group">
                    {!! Form::label('occupation', trans('labels.occupation').': ', ['class' => 'control-label']) !!}
                    {!! Form::text('occupation', !empty($lead->buddyreferrallead_guestdetail->occupation) ? $lead->buddyreferrallead_guestdetail->occupation : null, ['class' => 'form-control']) !!}
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-inline">
                <div class="form-group">
                    {!! Form::label('status', trans('labels.status').': ', ['class' => 'control-label']) !!}
                    <label class="radio-inline">
                        {!! Form::radio('status', 'Single', true) !!} @lang('labels.single')
                    </label>
                    <label class="radio-inline">
                        {!! Form::radio('status', 'Married', false) !!} @lang('labels.married')
                    </label>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <h4><strong>@lang('labels.health_history')</strong></h4>
            <div class="form-group">
                @foreach($guest_attributes as $key => $value)
                    @php($chosen = 0)
                    <div class="checkbox">
                        <label>
                            @if(count($lead->lead_attribute) > 0)
                                @foreach($lead->lead_attribute as $chosen_key)
                                    @if($key == $chosen_key->id)
                                        @php($chosen = 1)
                                    @endif
                                @endforeach
                            @endif
                            @if($chosen == '1')
                                {!! Form::checkbox('guest_attribute[]', $key, true) !!} {{$value}}
                            @elseif($chosen == '0')
                                {!! Form::checkbox('guest_attribute[]', $key) !!} {{$value}}
                            @endif
                        </label>
                    </div>
                @endforeach
            </div>
        </div>
        <div class="col-md-6">
            <h4><strong>@lang('labels.weigh_n_fat')</strong></h4>
            <div class="form-horizontal">
                <div class="form-group">
                    {!! Form::label('weight', trans('labels.weight').':  ', ['class' => 'control-label col-md-4']) !!}
                    <div class="col-md-6">
                        <div class="input-group">
                            {!! Form::text('weight', null, ['class' => 'form-control']) !!}
                            <span class="input-group-addon">@lang('labels.kg')</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('body_fat', trans('labels.body_fat').':  ', ['class' => 'control-label col-md-4']) !!}
                    <div class="col-md-6">
                        <div class="input-group">
                            {!! Form::text('body_fat', null, ['class' => 'form-control']) !!}
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('visceral', trans('labels.visceral_fat').':  ', ['class' => 'control-label col-md-4']) !!}
                    <div class="col-md-6">
                        {!! Form::text('visceral', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="col-md-4">
        <div class="form-inline">
            <div class="form-group">
                {!! Form::label('no_sale_reason', trans('labels.not_join_reason').': ', ['class' => 'control-label']) !!}
                {!! Form::select('no_sale_reason', $nosalereasons, !empty($guest_appointment->guestappointment_guestnosalereason->no_sale_reason_id) ? $guest_appointment->guestappointment_guestnosalereason->no_sale_reason_id : null, ['class' => 'form-control', 'placeholder' => '-- '.trans('labels.choose_reason').' --']) !!}
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="form-inline">
            <div class="form-group">
                {!! Form::label('fa_coach', trans('labels.fa_coach').': ', ['class' => 'control-label', 'style' => 'color: deeppink; margin-left: 10px;']) !!}
                {!! Form::select('fa_coach', $fa_coaches, $guest_appointment->staff_id, ['class' => 'form-control']) !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary pull-right']) !!}
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@section('other-script')
    <script src="{{asset('select2/dist/js/select2.js')}}"></script>
    <script>
        $('#fa_coach').select2();
    </script>
@endsection