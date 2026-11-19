@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/premember.css')}}" rel="stylesheet">
    <style>
        .member-photo {
            width: 150px;
            height: 150px;
            object-fit: cover;
        }

        td {
            padding-bottom: 15px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <center>
                <h2 class="page-title">@lang('labels.membership_changing') (@lang('labels.upgrade'))<br><small>(@lang('labels.choose_membership'))</small></h2>
            </center>
        </div>
    </div>
    @include('cf_lite.includes.error-message')

    <div class="row" style="margin-bottom: 20px;">
        <div class="col-md-3">
            @if(!empty($member->user_memberpersonal->photo))
                <img class="img-circle center-block member-photo" src="{{asset('images/member_photos/'.$member->user_memberpersonal->photo)}}">
            @else
                <img class="img-circle center-block member-photo" src="{{asset('images/img.png')}}">
            @endif
        </div>
        <div class="col-md-6">
            <table>
                <tr>
                    <td><span class="fa fa-female" title="@lang('labels.name')"></span> </td>
                    <td>:</td>
                    <td>{{$member->name}}</td>
                </tr>
                <tr>
                    <td><span class="fa fa-group" title="@lang('labels.membership_type')"></span> </td>
                    <td>:</td>
                    <td style="color: deeppink; font-weight: 600;">{{$member->user_memberstatus->memberstatus_membershiptype->membership_type_name}}</td>
                </tr>
                <tr>
                    <td><span class="fa fa-calendar-o" title="@lang('labels.end_date')"></span> </td>
                    <td>:</td>
                    <td>{{\Carbon\Carbon::parse($member->user_memberstatus->thru_date)->format('d F Y')}} (@lang('labels.membership_end'))</td>
                </tr>
            </table>
        </div>
    </div>

    <div class="row">
        @if(count($membership_types) > 0)
            @foreach($membership_types as $membership_type)
                @if($membership_type->is_vip == 'general')
                    @php
                        $membership_type_card = 'membership-regular';
                        $membership_payment_card = 'fa-money';
                        $membership_type_logo = 'fa-female';
                        $membership_labels = 'labels.regular';

                        if ($membership_type->membership_type == 'Student'):
                            $membership_type_card = 'membership-student';
                            $membership_type_logo = 'fa-graduation-cap';
                            $membership_labels = 'labels.student';
                        elseif ($membership_type->membership_type == 'Corporate'):
                            $membership_type_card = 'membership-corporate';
                            $membership_type_logo = 'fa-group';
                            $membership_labels = 'labels.corporate';
                        endif;

                        if ($membership_type->payment_type == 'AD'):
                            $membership_payment_card = 'fa-audio-description';
                        elseif ($membership_type->payment_type == 'Prepaid'):
                            $membership_payment_card = 'fa-credit-card';
                        endif;
                    @endphp
                    <div class="col-md-4">
                        <a href="{{url('cf-lite/payment/membership-changing/'.$membership_type->id)}}">
                            <div class="membership-type-card {{$membership_type_card}}">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h2 class="text-center"><span class="fa {{$membership_type_logo}}"></span></h2>
                                        <h3 class="text-center" style="font-size: 20px;">{{trans($membership_labels)}}</h3>
                                        <h2 class="pull-left" style="margin-top: 0;"><span class="fa {{$membership_payment_card}}"></span></h2>
                                    </div>
                                    <div class="col-md-8">
                                        <table>
                                            <tr>
                                                <td><h1 style="font-size: 20px">{{$membership_type->membership_type_name}}</h1></td>
                                            </tr>
                                            <tr>
                                                <td><span class="fa fa-calendar-o" title="@lang('labels.duration')" style="margin-top: 20px;"></span> {{$membership_type->duration}} @lang('labels.month')</td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @elseif($membership_type->is_vip == 'vip')
                    <div class="col-md-4">
                        <a href="{{url('cf-lite/payment/membership-changing/'.$membership_type->id)}}">
                            <div class="membership-type-card" style="background-color: gold">
                                <div class="row">
                                    <div class="col-md-4">
                                        <h2 class="text-center">@lang('labels.vip')</h2>
                                        <h3 class="text-center" style="font-size: 20px;"></h3>
                                        <h2 class="pull-left" style="margin-top: 0;">@lang('labels.free')</h2>
                                    </div>
                                    <div class="col-md-8">
                                        <table>
                                            <tr>
                                                <td><h1 style="font-size: 20px">{{$membership_type->membership_type_name}}</h1></td>
                                            </tr>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                @endif
            @endforeach
        @else
            @lang('messages.membership_type_changing_not_available')
        @endif
    </div>
    <div class="paging hiden rom">
        {{ $membership_types->links()}}
    </div>
@endsection

@section('other-script')
@endsection
