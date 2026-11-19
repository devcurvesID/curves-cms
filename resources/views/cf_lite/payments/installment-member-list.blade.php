@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <link href="{{asset('css/appointment.css')}}" rel="stylesheet">
    <link href="{{asset('css/payment.css')}}" rel="stylesheet">
@endsection

@section('content')
    @include('cf_lite.includes.error-message')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <center>
                <h2 class="page-title">@lang('labels.member_installment_setting')</h2>
            </center>
        </div>
    </div>
    <div class="row">
        <br>
        @if(count($members) > 0)
            @foreach($members as $member)
                <div class="col-md-4">
                    <a href="{{url('cf-lite/payment/new-installment-setting/'.$member->id)}}">
                        <div class="member-debt-card">
                            <div class="row">
                                <div class="col-md-4">
                                    @if(!empty($member->user_memberpersonal->photo))
                                        <img class="img-circle member-card-img" src="{{asset('images/member_photos/'.$member->user_memberpersonal->photo)}}">
                                    @else
                                        <img class="img-circle member-card-img" src="{{asset('images/img.png')}}">
                                    @endif
                                </div>
                                <div class="col-md-8">
                                    <table>
                                        <tr>
                                            <td><span class="fa fa-female" title="@lang('labels.name')"></span></td>
                                            <td>:</td>
                                            <td>{{$member->name}}</td>
                                        </tr>
                                        <tr>
                                            <td><span class="fa fa-phone" title="@lang('labels.phone')"></span></td>
                                            <td>:</td>
                                            <td>
                                                @if(!empty($member->user_memberpersonal->cellphone))
                                                    {{$member->user_memberpersonal->cellphone}}
                                                @else
                                                    {{$member->user_memberpersonal->phone}}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><span class="fa fa-calendar" title="@lang('labels.active_until')"></span></td>
                                            <td>:</td>
                                            <td>{{date('d F Y', strtotime($member->user_memberstatus->thru_date))}}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        @else
            @lang('messages.there_is_no_debt')
        @endif
    </div>
@endsection

@section('other-script')
@endsection