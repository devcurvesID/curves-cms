@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/appointment.css')}}" rel="stylesheet">
    <link href="{{asset('EasyAutocomplete-1_3_5\easy-autocomplete.min.css')}}" rel="stylesheet">

    <script src="{{asset('EasyAutocomplete-1_3_5\jquery.easy-autocomplete.min.js')}}"></script>
@endsection

@section('content')
    @include('cf_lite.includes.error-message')
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
        <div class="pull-right">
            <input id="basics">
        </div>
    </div>
    <div class="row">
        @if(count($members) > 0)
            @foreach($members as $member)
                <div class="col-md-4">
                    <a href="{{url($submit_link.$member->id)}}">
                        <div class="member-card {{$member_card}}">
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
                                            <td colspan="3"><span class="fa fa-user"></span> {{$member->name}}</td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                @if(!empty($member->user_memberpersonal->cellphone))
                                                    <span class="fa fa-phone-square"></span> {{$member->user_memberpersonal->cellphone}}
                                                @endif
                                            </td>
                                        </tr>
                                        <tr>
                                            @if(Session::has('appointment_type'))
                                                @if(Session::get('appointment_type') == 'WM')
                                                    <td colspan="3"><span class="fa fa-calendar" title="@lang('labels.last_w&m')"></span></td>
                                                @elseif(Session::get('appointment_type') == 'WO')
                                                    <td colspan="3"><span class="fa fa-calendar" title="@lang('labels.last_wo')"></span></td>
                                                @endif
                                            @endif
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        @else
            @lang('messages.there_is_no_guest')
        @endif
    </div>
    <div class="paging hiden rom">
        {{$members->links()}}
    </div>
@endsection

@section('other-script')
    <script type="text/javascript">
        var json = {!! json_encode($members_json) !!};
        console.log(json);
        var options = {
            data: json,

            getValue: "name",

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
                    return '<a href="{{url($submit_link)}}/' + item.id +'">' +
                        '<div class="row">' +
                        '<div class="col-md-8">' +
                        '<p>' + item.name + '</p>' +
                        '</div>' +
                        '<div class="col-md-4">' +
                        '<img class="img-circle member-card-search-img" src="{{asset('images/img.png')}}">' +
                        '</div>' +
                        '</div>' +
                        '</a>';
                }
            }
        };

        $('#basics').easyAutocomplete(options);
    </script>
@endsection
