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
            <a href="{{url('cf-lite/staff')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.manager_club_setting')</h2>
            </center>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            @if(!empty($user->user_memberpersonal->photo))
                <img src="{{asset('images/member_photos/'.$user->user_memberpersonal->photo)}}" class="img-circle" style="width: 150px; height: 150px; object-fit: cover;">
            @else
                <img src="{{asset('images/img.png')}}" class="img-circle" style="width: 150px; height: 150px; object-fit: cover;">
            @endif
        </div>
        <div class="col-md-6">
            <h5>{{$user->name}}</h5>
            <h5>({{$user->user_role->role}})</h5>
        </div>
    </div>

    <div class="row">
        {!! Form::open(['method' => 'POST', 'action' => ['StaffController@store_manager_club_setting', $user->id]]) !!}
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                            <tr>
                                <th></th>
                                <th>@lang('labels.club')</th>
                                <th>@lang('labels.address')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($clubs as $club)
                                <tr>
                                    <td>
                                        @if(count($user->manager_club) > 0)
                                            @php($checked_club = 0)
                                            @foreach($user->manager_club as $manager)
                                                @if($manager->id == $club->id)
                                                    @php($checked_club = 1)
                                                @endif
                                            @endforeach

                                            @if($checked_club == 1)
                                                {!! Form::checkbox('club['.$club->id.']', $club->id, true) !!}
                                            @else
                                                {!! Form::checkbox('club['.$club->id.']', $club->id, false) !!}
                                            @endif
                                        @else
                                            {!! Form::checkbox('club['.$club->id.']', $club->id, false) !!}
                                        @endif
                                    </td>
                                    <td>{{$club->club_name}}</td>
                                    <td>{{$club->address}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary pull-right', 'style' => 'margin-top: 20px;']) !!}
        </div>
        {!! Form::close() !!}
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $('#data-table').dataTable({
            'pageLength': 100
        });
    </script>
@endsection