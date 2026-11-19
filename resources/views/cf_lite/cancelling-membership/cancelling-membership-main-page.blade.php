@extends('cf_lite.layouts.staff-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <style>
        .btn-member-stop {
            position: absolute;
            top: 0;
            right: 0;
            border-radius: 50px;
            width: 50px;
            height: 50px;
            border: 3px solid red;
        }

        .btn-member-stop:hover {
            background-color: red;
            border: 3px solid red;
        }

        .btn-member-stop:hover > span {
            color: white !important;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/dashboard')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.membership_cancellation')</h2>
            </center>
            <a href="{{url('cf-lite/cancelling-membership/request')}}" class="btn btn-default btn-member-stop" title="@lang('labels.add_cancellation_request')"><span class="fa fa-ban" style="margin-top: 6px; color: red; font-size: 20px;"></span></a>
        </div>

        <div class="col-md-12">
            <div class="col-md-6">
                {!! Form::open(['method' => 'GET', 'action' => 'CancellingMembershipController@three_days_list']) !!}
                <div class="form-inline">
                    <div class="form-group">
                        {!! Form::label('member', trans('labels.search_member').': ', ['class' => 'control-label']) !!}
                        <div class="input-group">
                            {!! Form::text('member', null, ['class' => 'form-control', 'placeholder' => trans('labels.type_member_name')]) !!}
                            <span class="input-group-btn">
                                {!! Form::button('<span class="fa fa-search"></span>', ['class' => 'btn btn-default', 'type' => 'submit', 'style' => 'padding-top: 9px; padding-bottom: 9px;']) !!}
                            </span>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>

        @if(!empty($search_members))
            <div class="col-md-12">
                <h4>@lang('labels.search_result'): "{{$_GET['member']}}"</h4>
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive" style="background-color: #fff;">
                            <table class="table table-hover table-bordered" id="data-table-search">
                                <thead>
                                <tr>
                                    <th>@lang('labels.photo')</th>
                                    <th>@lang('labels.fullname')</th>
                                    <th>@lang('labels.phone')</th>
                                    <th>@lang('labels.joined')</th>
                                    <th>@lang('labels.membership_type')</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($search_members as $member)
                                    <tr>
                                        <td>
                                            @if(!empty($member->user_memberpersonal->photo))
                                                <img class="img-circle" src="{{asset('images/member_photos/'.$member->user_memberpersonal->photo)}}" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <img class="img-circle" src="{{asset('images/img.png')}}" style="width: 50px; height: 50px; object-fit: cover;">
                                            @endif
                                        </td>
                                        <td>{{$member->name}}</td>
                                        <td>{{$member->user_memberpersonal->phone}}</td>
                                        <td>{{date('d F Y', strtotime($member->user_memberpersonal->joined))}}</td>
                                        <td>{{$member->user_memberstatus->memberstatus_membershiptype->membership_type_name}}</td>
                                        <td>
                                            @if($member->user_memberstatus->memberstatus_membershiptype->duration == '12')
                                                <a href="{{url('cf-lite/cancelling-membership/'.$member->id.'/detail')}}" class="btn btn-default btn-sm">@lang('labels.add_cancellation') <span class="fa fa-chevron-right"></span></a>
                                            @else
                                                @lang('labels.cannot_be_cancelled')
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @else
            <div class="col-md-12">
                <h4>@lang('labels.last_3_days_joined_members_list')</h4>
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive" style="background-color: #fff;">
                            <table class="table table-hover table-bordered" id="data-table">
                                <thead>
                                <tr>
                                    <th>@lang('labels.photo')</th>
                                    <th>@lang('labels.fullname')</th>
                                    <th>@lang('labels.phone')</th>
                                    <th>@lang('labels.joined')</th>
                                    <th>@lang('labels.membership_type')</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($members as $member)
                                    <tr>
                                        <td>
                                            @if(!empty($member->user_memberpersonal->photo))
                                                <img class="img-circle" src="{{asset('images/member_photos/'.$member->user_memberpersonal->photo)}}" style="width: 50px; height: 50px; object-fit: cover;">
                                            @else
                                                <img class="img-circle" src="{{asset('images/img.png')}}" style="width: 50px; height: 50px; object-fit: cover;">
                                            @endif
                                        </td>
                                        <td>{{$member->name}}</td>
                                        <td>{{$member->user_memberpersonal->phone}}</td>
                                        <td>{{date('d F Y', strtotime($member->user_memberpersonal->joined))}}</td>
                                        <td>{{$member->user_memberstatus->memberstatus_membershiptype->membership_type_name}}</td>
                                        <td><a href="{{url('cf-lite/cancelling-membership/'.$member->id.'/detail')}}" class="btn btn-default btn-sm">@lang('labels.add_cancellation') <span class="fa fa-chevron-right"></span></a></td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $('#data-table').dataTable();
        $('#data-table-search').dataTable();
    </script>
@endsection