@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/premember.css')}}" rel="stylesheet">
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <center>
                <h2 class="page-title">@lang('labels.member_type_list')</h2>
            </center>
        </div>
    </div>

    @include('cf_lite.includes.error-message')
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
                        <a href="{{url('cf-lite/payment/membership/'.$membership_type->id)}}">
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
                        <a href="{{url('cf-lite/payment/membership/'.$membership_type->id)}}">
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
            @lang('messages.there_is_no_membership_type')
        @endif
    </div>
    <div class="paging hiden rom">
        {{$membership_types->links()}}
    </div>
@endsection

@section('other-script')
@endsection
