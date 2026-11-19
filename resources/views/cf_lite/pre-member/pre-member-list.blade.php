@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/premember.css')}}" rel="stylesheet">
    <link href="{{asset('EasyAutocomplete-1_3_5\easy-autocomplete.min.css')}}" rel="stylesheet">

    <script src="{{asset('EasyAutocomplete-1_3_5\jquery.easy-autocomplete.min.js')}}"></script>
@endsection

@section('content')

    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/dashboard')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.pre_member_list')</h2>
            </center>
        </div>
    </div>

    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="pull-right">
            <input id="basics">
        </div>
    </div>
    <div class="row">
        @if(count($premembers) > 0)
            @foreach($premembers as $premember)
                <div class="col-md-3">
                    <div class="premember-card">
                        <div class="row">
                            <div class="col-md-8">
                                <table>
                                    <tr>
                                        <td><span class="fa fa-female" title="@lang('labels.name')"></span></td>
                                        <td>:</td>
                                        <td>{{$premember->fullname}}</td>
                                    </tr>
                                    <tr>
                                        <td><span class="fa fa-phone" title="@lang('labels.phone')"></span></td>
                                        <td>:</td>
                                        <td>{{$premember->phone}}</td>
                                    </tr>
                                    <tr>
                                        <td><span class="fa fa-bolt" title="@lang('labels.source')"></span></td>
                                        <td>:</td>
                                        <td>
                                            @if($premember->is_br == 'BR')
                                                @lang('labels.buddy_referral')
                                            @elseif($premember->is_br == 'NotBR')
                                                {{$premember->buddyreferrallead_membersource->source}}
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><span class="fa fa-user-secret" title="@lang('labels.staff')"></span></td>
                                        <td>:</td>
                                        <td>{{$premember->buddyreferrallead_user->name}}</td>
                                    </tr>
                                </table>
                            </div>
                            <div class="col-md-4">
                                <a href="{{url('cf-lite/pre-member/'.$premember->id.'/membership')}}" class="btn premember-membership-btn">
                                    <span class="fa fa-newspaper-o" title="@lang('labels.choose') @lang('labels.membership_type')"></span>
                                </a>
                            </div>
                        </div>
                        <br>
                        <div class="row">
                            <div class="col-md-12">
                                <a href="{{url('cf-lite/pre-member/'.$premember->id)}}" class="pull-right">
                                    <span class="fa fa-female"></span> @lang('labels.see_detail') <span class="fa fa-hand-o-right"></span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        @else
            @lang('messages.there_is_no_pre_member')
        @endif
    </div>
    <div class="paging hiden rom">
        {{$premembers->links()}}
    </div>
@endsection

@section('other-script')
    <script type="text/javascript">
        var json = {!! json_encode($premembers_json) !!};
        console.log(json);
        var options = {
            data: json,

            getValue: "fullname",

            list: {
                match: {
                    enabled: true
                },
                showAnimation: {
                    type: "slide"
                },
                hideAnimation: {
                    type: "slide"
                }
            },

            template: {
                type: "custom",
                method: function(value, item) {
                    return '<a href="{{url('cf-lite/pre-member')}}/' + item.id +'">' +
                        '<p>' + item.fullname + '</p>' +
                        '<p>' + item.phone +'</p>' +
                        '</a>' +
                        '<hr>';
                }
            }
        };

        $('#basics').easyAutocomplete(options);
    </script>
@endsection
