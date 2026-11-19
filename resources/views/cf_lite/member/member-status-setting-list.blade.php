@extends('cf_lite.layouts.staff-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('bootstrap-toggle/css/bootstrap-toggle.min.css')}}" rel="stylesheet">

    <style>

        .page-title {
            margin-top: 0px;
        }

        .btn-filter-zone{
            padding: 9px 12px;
        }

        select[name="data-table_length"]{
            margin: 0px 10px;
        }

        .btn-member-status {
            position: absolute;
            top: 0;
            right: 0;
            border-radius: 50px;
            width: 50px;
            height: 50px;
            border: 3px solid rebeccapurple;
        }

        .btn-member-status:hover {
            background-color: rebeccapurple;
            border: 3px solid rebeccapurple;
        }

        .btn-member-status:hover > span {
            color: white !important;
        }

    </style>

    <style>
        .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20px; }
        .toggle.ios .toggle-handle { border-radius: 20px; }

        .checkbox label .toggle {
            margin-left: 0;
        }
    </style>

@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/member/list')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.member_status_setting') ({{$club->club_name}})</h2>
            </center>
            @if((Auth::user()->role_id < 6) || (Auth::user()->role_id == 8))
                <a role="button" class="btn btn-default btn-member-status" data-toggle="modal" data-target="#addDuration">
                    <span class="fa fa-user-secret" style="margin-top: 4px; color: rebeccapurple; font-size: 20px;"></span>
                </a>
            @endif
        </div>
        <div class="col-md-3">
            <div id="filter-zone">
                {!! Form::open(['method' => 'GET', 'action' => 'MemberController@member_status_setting_list']) !!}
                <div class="form-group">
                    <div class="input-group">
                        {!! Form::text('s', null, ['class' => 'form-control', 'placeholder' => trans('labels.input_search')]) !!}
                        <span class="input-group-btn">
                            <button class="btn btn-default btn-filter-zone" type="submit"><span class="fa fa-search"></span></button>
                        </span>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="col-md-12">
            {!! Form::open(['method' => 'PATCH', 'action' => 'MemberController@bulk_update_member_status_setting']) !!}
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>@lang('labels.fullname')</th>
                                    <th>@lang('labels.keytag')</th>
                                    <th>@lang('labels.vip')</th>
                                    <th>@lang('labels.membership')</th>
                                    <th>@lang('labels.from_date')</th>
                                    <th>@lang('labels.thru_date')</th>
                                    <th>@lang('labels.fa_coach')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($members as $member)
                                    <tr>
                                        <td>
                                            <div class="checkbox">
                                                <label>
                                                    {!! Form::checkbox('member['.$member->id.']', $member->id, false, ['class' => 'member-check', 'id' => 'member-check-'.$member->id]) !!}
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            {{$member->name}}
                                            @if(($member->user_memberstatus->status == 'Stopped') || ($member->user_memberstatus->status == 'Inactive'))
                                                <span class="fa fa-ban" title="@lang('labels.cancelled')" style="color: red;"></span>
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($member->user_memberpersonal->key_tag_id))
                                            {{$member->user_memberpersonal->key_tag_id}}
                                            @endif
                                        </td>
                                        <td>
                                            <div class="checkbox">
                                                @php
                                                $vip = false;
                                                if ($member->user_memberstatus->member_vip_status == 'VIP'):
                                                    $vip = true;
                                                endif;
                                                @endphp
                                                <label>
                                                    {!! Form::checkbox('vip['.$member->id.']', $member->id, $vip, ['data-toggle' => 'toggle', 'data-size' => 'mini', 'data-on' => trans('labels.vip'), 'data-off' => trans('labels.regular'), 'data-onstyle' => 'success', 'data-offstyle' => 'info', 'data-style' => 'ios', 'disabled', 'class' => 'member-vip', 'id' => 'member-vip-'.$member->id]) !!}
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            {{$member->user_memberstatus->memberstatus_membershiptype->membership_type_name}}
                                        </td>
                                        <td>
                                            {!! Form::date('old_from_date['.$member->id.']', \Carbon\Carbon::parse($member->user_memberstatus->from_date), ['class' => 'form-control hidden', 'readonly']) !!}
                                            {!! Form::date('from_date['.$member->id.']', \Carbon\Carbon::parse($member->user_memberstatus->from_date), ['class' => 'form-control member-from', 'id' => 'member-from-'.$member->id, 'readonly']) !!}
                                        </td>
                                        <td>
                                            {!! Form::date('old_thru_date['.$member->id.']', \Carbon\Carbon::parse($member->user_memberstatus->thru_date), ['class' => 'form-control hidden', 'readonly']) !!}
                                            {!! Form::date('thru_date['.$member->id.']', \Carbon\Carbon::parse($member->user_memberstatus->thru_date), ['class' => 'form-control', 'readonly']) !!}
                                        </td>
                                        <td>
                                            {!! Form::select('fa_coach['.$member->id.']', $fa_coaches, isset($member->user_memberpersonal->sales_person_id) ? $member->user_memberpersonal->sales_person_id : '', ['class' => 'form-control', 'disabled']) !!}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <br>
            <div class="form-group">
                {!! Form::submit(trans('labels.save_changes'), ['class' => 'btn btn-primary']) !!}
            </div>
            {!! Form::close() !!}
            @if(count($members) > 0)
                <div class="paging hidden rom">
                    {{$members->links()}}
                </div>
            @endif
        </div>
    </div>
    <div class="modal fade" id="addDuration" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'POST', 'action' => 'MemberController@add_membership_duration_to_clubs']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">@lang('labels.add_duration_to_all_club_members')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('duration', trans('labels.duration').' ('. trans('labels.day') .')'.': ', ['class' => 'control-label
                            col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::number('duration', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" name="submit" class="btn btn-success">@lang('labels.update')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('bootstrap-toggle/js/bootstrap-toggle.min.js')}}"></script>
    <script>
        $('#data-table').dataTable({
            'pageLength': 50
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#data-table').on('click', '.member-check', function () {
                var id = $(this).attr('id').split('-').pop();

                if ($(this).prop('checked') == true) {
                    $('#member-vip-' + id).prop('disabled', false);
                    $('input[name="from_date[' + id + ']"]').prop('readonly', false);
                    $('select[name="fa_coach[' + id + ']"]').prop('disabled', false);

//                    if ($('#member-vip-' + id).prop('checked') == true) {
                        $('input[name="thru_date[' + id + ']"]').prop('readonly', false);
//                    }
                } else {
                    $('#member-vip-' + id).prop('disabled', true);
                    $('input[name="from_date[' + id + ']"]').prop('readonly', true);
                    $('input[name="thru_date[' + id + ']"]').prop('readonly', true);
                    $('select[name="fa_coach[' + id + ']"]').prop('disabled', true);
                }
            });

            $('#data-table').on('click', '.toggle-group', function () {
                var id = $(this).parent('.toggle').children('.member-vip').attr('id').split('-').pop();

                if ($('#member-vip-' + id).prop('disabled') == false) {
                    if ($('#member-vip-' + id).prop('checked') == false) {
                        $('input[name="thru_date[' + id + ']"]').prop('readonly', false);
                    } else {
                        $('input[name="thru_date[' + id + ']"]').prop('readonly', true);
                    }
                }
            });

            $('#data-table').on('change', '.member-from', function () {
                var id = $(this).attr('id').split('-').pop();

                var old = new Date($('input[name="old_from_date[' + id + ']"]').val());
                var from_date = new Date($(this).val());
                var diff = from_date.getTime() - old.getTime();
//                var days = Math.round(diff/(1000 * 60 * 60 * 24));

                var thru_date = new Date($('input[name="old_thru_date[' + id + ']"]').val());
                var new_date = new Date(thru_date.getTime() + diff);

                var day = ("0" + new_date.getDate()).slice(-2);
                var month = ("0" + (new_date.getMonth() + 1)).slice(-2);

                var toset = new_date.getFullYear()+"-"+(month)+"-"+(day) ;

                $('input[name="thru_date[' + id + ']"]').val(toset);
            });
        });
    </script>
@endsection
