@extends('cf_lite.layouts.staff-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <style>

        .page-title {
            margin-top: 0px;
        }

        .btn-filter-zone{
            padding: 9px 12px;
        }

    </style>

@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/message')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.member_message_note') ({{Auth::user()->user_club->club_name}})</h2>
            </center>
        </div>
        <div class="col-md-12">
            <div id="filter-zone">
                {!! Form::open(['method' => 'GET', 'action' => 'MemberController@member_message_note_filter']) !!}
                <div class="form-group">
                    <label class="radio-inline">
                        {!! Form::radio('search_option', 'empty_note', true) !!} @lang('labels.empty_note')
                    </label>
                    <label class="radio-inline">
                        {!! Form::radio('search_option', 'ongoing', false) !!} @lang('labels.ongoing_note')
                    </label>
                    <label class="radio-inline">
                        {!! Form::radio('search_option', 'expired', false) !!} @lang('labels.expired')
                    </label>
                    {!! Form::submit(trans('labels.apply_filter'), ['class' => 'btn btn-default', 'style' => 'margin-left: 20px;']) !!}
                </div>
                <div class="form-group">
                    <div class="col-md-3">
                        <div class="input-group">
                            {!! Form::text('s', null, ['class' => 'form-control', 'placeholder' => trans('labels.input_search')]) !!}
                            <span class="input-group-btn">
                            <button class="btn btn-default btn-filter-zone" type="submit"><span class="fa fa-search"></span></button>
                        </span>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="col-md-12">
            <h3>
                @lang('labels.search'):
                @if(!empty($_GET['s']))
                    @lang('labels.keyword') "{{$_GET['s']}}"
                @else
                    @if(!empty($_GET['search_option']))
                        {{trans('labels.'.$_GET['search_option'])}}
                    @endif
                @endif
            </h3>
        </div>
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                                <tr>
                                    <th>@lang('labels.photo')</th>
                                    <th>@lang('labels.fullname')</th>
                                    <th>@lang('labels.phone')</th>
                                    <th>@lang('labels.message')</th>
                                    <th>@lang('labels.status')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($members as $member)
                                    <tr>
                                        <td>
                                            @if(!empty($member->user_memberpersonal->photo))
                                                <img class="img-circle" src="{{asset('images/member_photos/'.$member->user_memberpersonal->photo)}}" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                <img class="img-circle" src="{{asset('images/img.png')}}" title="@lang('labels.take') @lang('labels.photo')" style="width: 40px; height: 40px; object-fit: cover;">
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($member->user_membermessagenote))
                                                @if(strtotime($member->user_membermessagenote->thru_date) >= strtotime(\Carbon\Carbon::now()))
                                                    <a role="button" id="edit-msg-note-{{$member->id}}" class="edit-msg-note" data-toggle="modal" data-target="#addMessageNoteModal">
                                                        {{$member->name}}
                                                    </a>
                                                @endif
                                            @else
                                                <span id="name-text-{{$member->id}}">{{$member->name}}</span>
                                            @endif
                                        </td>
                                        <td>{{$member->user_memberpersonal->phone}}</td>
                                        <td>
                                            @if(!empty($member->user_membermessagenote))
                                                <p>{{$member->user_membermessagenote->notes}}</p>
                                            @else
                                                -
                                            @endif

                                            @if(!empty($member->user_membermessagenote->predefined_message))
                                                <label class="label label-info" title="{{$member->user_membermessagenote->membermessagenote_predefinedmessage->message}}" style="text-overflow: ellipsis;">{{$member->user_membermessagenote->membermessagenote_predefinedmessage->message}}</label>
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($member->user_membermessagenote))
                                                @if(strtotime($member->user_membermessagenote->thru_date) >= strtotime(\Carbon\Carbon::now()))
                                                    {{date('d-m-Y', strtotime($member->user_membermessagenote->from_date))}} @lang('labels.until') {{date('d-m-Y', strtotime($member->user_membermessagenote->thru_date))}}
                                                @else
                                                    <a role="button" id="msg-note-{{$member->id}}" class="btn btn-info msg-note" data-toggle="modal" data-target="#addMessageNoteModal">
                                                        <label class="label label-default" title="@lang('labels.add_new_message_note')">@lang('labels.expired')</label>
                                                    </a>
                                                @endif
                                            @else
                                                <a role="button" id="msg-note-{{$member->id}}" class="btn btn-info msg-note" data-toggle="modal" data-target="#addMessageNoteModal">
                                                    @lang('labels.add_note') <span class="fa fa-envelope"></span>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @if(count($members) > 0)
                <div class="paging hiden rom">
                    {{$members->links()}}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addMessageNoteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            {!! Form::open(['method' => 'PATCH', 'action' => 'MemberController@update_member_message_note']) !!}
            {!! Form::hidden('attribute', null) !!}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.message_note')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('from_date', trans('labels.date').': ', ['class' => 'control-label col-md-2']) !!}
                            <div class="col-md-4">
                                {!! Form::date('from_date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                            </div>
                            {!! Form::label('thru_date', trans('labels.until'), ['class' => 'control-label col-md-1']) !!}
                            <div class="col-md-4">
                                {!! Form::date('thru_date', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('lead_name', trans('labels.name').': ', ['class' => 'control-label col-md-2']) !!}
                            <div class="col-md-6">
                                <p class="form-control-static member-name"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('predefined_message', trans('labels.predefined_message').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-6">
                                {!! Form::select('predefined_message', $predefined_message_list, null, ['class' => 'form-control', 'placeholder' => trans('labels.choose_predefined_message').'...']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('notes', trans('labels.message_note').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-6">
                                {!! Form::textarea('notes', null, ['class' => 'form-control', 'rows' => '3']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" class="btn btn-primary" id="save-btn">@lang('labels.save')</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $('#data-table').dataTable();
    </script>

    <script>
        $(document).ready(function () {
            var url = "{{url('cf-lite')}}/";

            $('#data-table').on('click', '.edit-msg-note', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    member_id: $(this).attr('id').split('-').pop()
                };

                var type = 'POST';
                var my_url = url + 'get-member-message-note-detail';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        $('input[name="attribute"]').val(data.member['id']);
                        $('input[name="from_date"]').val(data.message['from_date']);
                        $('input[name="thru_date"]').val(data.message['thru_date']);
                        $('.member-name').text(data.member['name']);
                        $('textarea[name="notes"]').val(data.message['notes']);
                        $('select[name="predefined_message"]').val(data.message['predefined_message']);
                        $('#save-btn').text('@lang('labels.edit')');
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });

            $('#data-table').on('click', '.msg-note', function () {
                var id = $(this).attr('id').split('-').pop();

                $('input[name="attribute"]').val(id);
                $('.member-name').text($('#name-text-' + id).text());
                $('#save-btn').text('@lang('labels.save')');
                $('input[name="from_date"]').val('');
                $('input[name="thru_date"]').val('');
                $('textarea[name="notes"]').val('');
                $('select[name="predefined_message"]').val('');
            });

            $('input[name="search_option"]').on('click', function () {
                $('input[name="s"]').val('');
            })
        });
    </script>
@endsection
