@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/appointment.css')}}" rel="stylesheet">
    <link href="{{asset('EasyAutocomplete-1_3_5\easy-autocomplete.min.css')}}" rel="stylesheet">

    <script src="{{asset('EasyAutocomplete-1_3_5\jquery.easy-autocomplete.min.js')}}"></script>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <center>
                <h2 class="page-title">@lang('labels.buddy_referral')</h2>
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
        @if(count($leads) > 0)
            @foreach($leads as $lead)
                <div class="col-md-3">
                    <a href="{{url($submit_link.$lead->id)}}">
                        <div class="guest-card guest-card-br">
                            <table>
                                <tr>
                                    <td>@lang('labels.name')</td>
                                    <td>:</td>
                                    <td>{{$lead->fullname}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('labels.phone')</td>
                                    <td>:</td>
                                    <td>{{$lead->phone}}</td>
                                </tr>
                                <tr>
                                    <td>@lang('labels.source')</td>
                                    <td>:</td>
                                    <td>
                                        @if($lead->is_br == 'BR')
                                            @lang('labels.buddy_referral')
                                        @elseif($lead->is_br == 'NotBR')
                                            {{$lead->buddyreferrallead_membersource->source}}
                                        @endif
                                    </td>
                                </tr>
                                @if($lead->is_br == 'BR')
                                    <tr>
                                        <td>@lang('labels.referral')</td>
                                        <td>:</td>
                                        <td>{{$lead->buddyreferrallead_referrer->referrer_user->name}}</td>
                                    </tr>
                                @endif
                                <tr>
                                    <td>@lang('labels.staff')</td>
                                    <td>:</td>
                                    <td>{{$lead->buddyreferrallead_user->name}}</td>
                                </tr>
                            </table>
                        </div>
                    </a>
                </div>
            @endforeach
        @else
            <span class="no_data_found">@lang('messages.there_is_no_guest')</span>
        @endif
    </div>
    <div class="paging hiden rom">
        {{$leads->links()}}
    </div>

    <div id="add-lead" class="btn btn-add-brlead" data-toggle="modal" data-target="#addBRModal" title="@lang('labels.add') @lang('labels.new_lead')"><span class="fa fa-user-plus"></span></div>

    <!-- Modal -->
    <div class="modal fade" id="addBRModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">@lang('labels.add')  @lang('labels.new_buddy_referral')</h4>
                </div>
                <div class="modal-body">
                    {!! Form::open(['action' => 'LeadController@store_br', 'method' => 'POST', 'class' => 'form-horizontal']) !!}
                    <div class="form-group">
                        {!! Form::label('fullname', trans('labels.fullname').':', ['class' => 'control-label col-md-3']) !!}
                        <div class="col-md-6">
                            {!! Form::text('fullname', null, ['class' => 'form-control', 'placeholder' => trans('labels.insert').' '.trans('labels.fullname')]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('phone', trans('labels.phone').':', ['class' => 'control-label col-md-3']) !!}
                        <div class="col-md-6">
                            {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => trans('labels.insert').' '.trans('labels.phone')]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('member_name', trans('labels.member_name').':', ['class' => 'control-label col-md-3']) !!}
                        <div class="col-md-6">
                            {!! Form::text('member_name', null, ['class' => 'form-control', 'placeholder' => trans('labels.search').' '.trans('labels.member')]) !!}
                        </div>
                    </div>
                    {!! Form::hidden('member_id', null, ['id' => 'member_id']) !!}
                    <div class="form-group">
                        <div class="col-md-offset-6 col-md-3">
                            {!! Form::submit(trans('labels.add'), ['class' => 'btn btn-primary form-control', 'name' => 'add-br']) !!}
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script type="text/javascript">
        var json = {!! json_encode($leads_json) !!};
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
                    return '<a href="{{url('cf-lite/appointment/submit/fa')}}/' + item.id +'">' +
                        '<p>' + item.fullname + '</p>' +
                        '<p>' + item.phone +'</p>' +
                        '</a>' +
                        '<hr>';
                }
            }
        };

        $('#basics').easyAutocomplete(options);
    </script>

    <script type="text/javascript">
        var member = {!! json_encode($members) !!};
        console.log(member);
        var options = {
            data: member,

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
                },
                onSelectItemEvent: function() {
                    var value = $("#member_name").getSelectedItemData().id;

                    $("#member_id").val(value).trigger("change");
                }
            },

            template: {
                type: "custom",
                method: function(value, item) {
                    return '<p>' + item.name + '</p>';
                }
            }
        };

        $('#member_name').easyAutocomplete(options);
    </script>

    <script>
        $('#member_name').parent().css('width', '100%');
    </script>
@endsection
