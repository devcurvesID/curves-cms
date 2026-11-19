@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/appointment.css')}}" rel="stylesheet">
    <link href="{{asset('EasyAutocomplete-1_3_5\easy-autocomplete.min.css')}}" rel="stylesheet">
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">

    <script src="{{asset('EasyAutocomplete-1_3_5\jquery.easy-autocomplete.min.js')}}"></script>

    <style>
        .select2 {
            width: 100% !important;
        }
    </style>
@endsection

@section('content')

    <div class="row">
        <div class="col-sm-12">
            <center>
                <h2 class="page-title">@lang('labels.lead_list')</h2>
            </center>
        </div>
    </div>

    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="pull-right">
            <input id="basics" placeholder="@lang('labels.search') @lang('labels.lead')">
        </div>
    </div>
    <div class="row">
        @if(count($leads) > 0)
            @foreach($leads as $lead)
                <div class="col-md-3">
                    <a href="{{url($submit_link.$lead->id)}}">
                        <div class="guest-card">
                            <table>
                                <tr>
                                    <td><i class="fa fa-user" data-toggle="tooltip" title="@lang('labels.name')"></i></td>
                                    <td>:</td>
                                    <td>{{$lead->fullname}}</td>
                                </tr>
                                <tr>
                                    <td><i class="fa fa-phone" data-toggle="tooltip" title="@lang('labels.phone')"></i></td>
                                    <td>:</td>
                                    <td>{{$lead->phone}}</td>
                                </tr>
                                <tr>
                                    <td><i class="fa fa-bolt" data-toggle="tooltip" title="@lang('labels.source')"></i></td>
                                    <td>:</td>
                                    <td>
                                        @if($lead->is_br == 'BR')
                                            @lang('labels.buddy_referral')
                                        @elseif($lead->is_br == 'NotBR')
                                            {{$lead->buddyreferrallead_membersource->source}}
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><i class="fa fa-user-secret" data-toggle="tooltip" title="@lang('labels.staff')"></i></td>
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

    <a href="../bulk/fa/lead" class="btn btn-add-brlead" data-toggle="tooltip" title="Bulk Action" style="top: 150px; background-color: #1abc9c"><span class="fa fa-tasks"></span></a>
    <div id="add-lead" class="btn btn-add-brlead" data-toggle="modal" data-target="#addLeadModal" title="@lang('labels.add') @lang('labels.new_lead')"><span class="fa fa-user-plus"></span></div>

    <!-- Modal -->
    <div class="modal fade" id="addLeadModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">@lang('labels.add')  @lang('labels.new_lead')</h4>
                </div>
                <div class="modal-body">
                    {!! Form::open(['action' => ['LeadController@store_br'], 'method' => 'POST', 'class' => 'form-horizontal']) !!}
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
                        {!! Form::label('source_id', trans('labels.source').':', ['class' => 'control-label col-md-3']) !!}
                        <div class="col-md-6">
                            {!! Form::select('source_id', $sources, null, ['class' => 'form-control', 'placeholder' => trans('labels.choose').' '.trans('labels.source')]) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-offset-6 col-md-3">
                            {!! Form::submit(trans('labels.add'), ['class' => 'btn btn-primary form-control', 'name' => 'add-lead']) !!}
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

    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script type="text/javascript">
        $('#source_id').select2();
    </script>
@endsection
