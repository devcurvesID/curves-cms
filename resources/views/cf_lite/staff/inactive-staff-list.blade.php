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

@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/staff/'.$club->id)}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.inactive_staff_list')</h2>
            </center>
        </div>

        <div class="col-md-12">
            <h3>@lang('labels.cuti')</h3>
        </div>
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped" id="data-table-cuti">
                            <thead>
                            <tr>
                                <th>@lang('labels.photo')</th>
                                <th>@lang('labels.name')</th>
                                <th>@lang('labels.reason')</th>
                                <th>@lang('labels.date')</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($permission_members as $permission_member)
                                <tr>
                                    <td>
                                        @if(!empty($permission_member->staffinactive_user->user_memberpersonal->photo))
                                            <img class="img-circle" src="{{asset('images/member_photos/'.$permission_member->staffinactive_user->user_memberpersonal->photo)}}" style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            {{--<a href="#">--}}
                                            <img class="img-circle" src="{{asset('images/img.png')}}" title="@lang('labels.photo')" style="width: 40px; height: 40px; object-fit: cover;">
                                            {{--</a>--}}
                                        @endif
                                    </td>
                                    <td>{{$permission_member->staffinactive_user->name}}</td>
                                    <td>
                                        @if(!empty($permission_member->reason))
                                            {{$permission_member->reason}}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{\Carbon\Carbon::parse($permission_member->from_date)->format('d-m-Y')}} @lang('labels.until') {{\Carbon\Carbon::parse($permission_member->thru_date)->format('d-m-Y')}}</td>
                                    <td>
                                        {!! Form::open(['method' => 'POST', 'action' => ['StaffController@activate_permission_member', $permission_member->id]]) !!}
                                        {!! Form::button(trans('labels.permission_finished'), ['class' => 'btn btn-success btn-delete']) !!}
                                        {!! Form::close() !!}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <h3>@lang('labels.resign')</h3>
        </div>
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered table-striped" id="data-table-resign">
                            <thead>
                            <tr>
                                <th>@lang('labels.photo')</th>
                                <th>@lang('labels.name')</th>
                                <th>@lang('labels.last_club')</th>
                                <th>@lang('labels.reason')</th>
                                <th>@lang('labels.date')</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($resigned_members as $resigned_member)
                                <tr>
                                    <td>
                                        @if(!empty($resigned_member->staffinactive_user->user_memberpersonal->photo))
                                            <img class="img-circle" src="{{asset('images/member_photos/'.$resigned_member->staffinactive_user->user_memberpersonal->photo)}}" style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            {{--<a href="#">--}}
                                            <img class="img-circle" src="{{asset('images/img.png')}}" title="@lang('labels.photo')" style="width: 40px; height: 40px; object-fit: cover;">
                                            {{--</a>--}}
                                        @endif
                                    </td>
                                    <td>{{$resigned_member->staffinactive_user->name}}</td>
                                    <td>{{$resigned_member->staffinactive_user->user_club->club_name}}</td>
                                    <td>
                                        @if(!empty($resigned_member->reason))
                                            {{$resigned_member->reason}}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>{{\Carbon\Carbon::parse($resigned_member->from_date)->format('d-m-Y')}}</td>
                                    <td>
                                        <a href="javascript:;" class="btn btn-info reactivate-btn" data-toggle="modal" data-target="#reactivateModal" id="reactivate-btn-{{$resigned_member->id}}">@lang('labels.reactivate_staff')</a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="reactivateModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'POST', 'action' => 'StaffController@reactivate_resigned_member']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.reactivate') @lang('labels.in') {{$club->club_name}}</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-horizontal">
                                {!! Form::hidden('reactivate_id', null, ['id' => 'the-staff']) !!}
                                {!! Form::hidden('club', $club->id) !!}
                                <div class="form-group">
                                    {!! Form::label('role', trans('labels.role').': ', ['class' => 'control-label col-md-2']) !!}
                                    <div class="col-md-6">
                                        {!! Form::select('role', $roles, null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('email', trans('labels.email').': ', ['class' => 'control-label col-md-2']) !!}
                                    <div class="col-md-6">
                                        {!! Form::text('email', null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('username', trans('labels.username').': ', ['class' => 'control-label col-md-2']) !!}
                                    <div class="col-md-6">
                                        {!! Form::text('username', null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('password', trans('labels.password').': ', ['class' => 'control-label col-md-2']) !!}
                                    <div class="col-md-6">
                                        {!! Form::password('password', ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('password_confirmation', trans('labels.repassword').': ', ['class' => 'control-label col-md-2']) !!}
                                    <div class="col-md-6">
                                        {!! Form::password('password_confirmation', ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('from_date', trans('labels.date_active').': ', ['class' => 'control-label col-md-2']) !!}
                                    <div class="col-md-6">
                                        {!! Form::date('from_date', \Carbon\Carbon::now(), ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('labels.activate')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $('#data-table-cuti').dataTable();
        $('#data-table-resign').dataTable();

        $(document).ready(function () {
            $('#data-table-resign').on('click', '.reactivate-btn', function () {
                var id = $(this).attr('id').split('-').pop();
                $('#the-staff').val(id);
            });
        });
    </script>
@endsection