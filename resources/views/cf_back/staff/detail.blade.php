@extends('cf_back.layouts.master-dashboard')

@section('title', 'Staff')

@section('other-style')
    <link href={{asset("vendors/iCheck/skins/flat/green.css")}} rel="stylesheet">
    <link href={{asset("vendors/select2/dist/css/select2.min.css")}} rel="stylesheet">
    <style>
        .preview-img {
            width: 200px;
            height: 200px;
            border-radius: 50%;
        }

        .user-name {
            font-size: 24px;
            text-transform: capitalize;
            margin-bottom: 10px;
            margin-top: 10px;
        }

        .user-tag-id {
            font-size: 14px;
        }

        .user-status {
            font-size: 14px;
            margin-bottom: 20px;
        }

        .user-tag-id:after {
            content: ' ';
            width: 125px;
            margin-top: 5px;
            margin-bottom: 5px;
            display: block;
            border-bottom: 2px solid gray;
        }

        .x_content div {
            margin: 7.5px 0px;
            font-size: 14px;
        }

    </style>
@endsection
@section('content')
    <div class="container">
        <div class="my-section">
            <div class="section-title">
                <center>
                    <h1 class="page-title"> @lang('labels.staff')</h1>
                </center>
            </div>

            <div class="col-md-10 col-md-push-1 col-sm-10 col-sm-push-1 col-xs-12 col-xs-push-0">
                <div class="row">
                    <div class="col-md-3 col-sm-4 col-xs-12">
                        <center>
                            <div class="col-xs-12">
                                <div class="preview-img my-bg"
                                     style="background-image: url({{asset('images/staff_photos/'.$user->user_staffpersonal->photo)}})">
                                </div>
                                <div class="user-name">{{$user->name}}</div>
                                <div class="user-tag-id">@lang('labels.key_tag_id') :
                                    #{{$user->user_staffpersonal->key_tag_id}}</div>
                                <div class="user-status">@lang('labels.status')
                                    : @if(isset($user->user_staffstatus))
                                    {{trans('labels.'. strtolower($user->user_staffstatus->status))}}
                                    @endif
                                </div>
                            </div>
                        </center>
                    </div>
                    <div class="col-md-9 col-sm-8 col-xs-12">


                        <ul class="nav nav-tabs">
                            <li class="active"><a data-toggle="tab"
                                                  href="#personal">@lang('labels.personal_information')</a></li>
                            <li><a data-toggle="tab" href="#osaccess">@lang('labels.os_access')</a></li>
                            <li><a data-toggle="tab" href="#certification">@lang('labels.certification')</a></li>
                            <li><a data-toggle="tab" href="#note">@lang('labels.note')</a></li>
                            <li><a data-toggle="tab" href="#work-hour">@lang('labels.work_hour')</a></li>
                        </ul>

                        <div class="tab-content">
                            <div id="personal" class="tab-pane fade in active">
                                <div class="card-personal-info">
                                    <div class="x_panel tile">
                                        <div class="x_title">
                                            <h2>@lang('labels.personal_information')</h2>
                                            <ul class="nav navbar-right panel_toolbox">
                                                <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                                </li>
                                                <li class="dropdown">
                                                    <a><i class="fa fa-pencil"></i></a>
                                                </li>
                                            </ul>
                                            <div class="clearfix"></div>
                                        </div>
                                        <div class="x_content">
                                            <div class="row">
                                                <div class="col-xs-3">
                                                    @lang('labels.nik')
                                                </div>
                                                <div class="col-xs-9">
                                                    : {{$user->user_staffpersonal->nik}}
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-xs-3">
                                                    @lang('labels.npwp')
                                                </div>
                                                <div class="col-xs-9">
                                                    : {{$user->user_staffpersonal->npwp}}
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-xs-3">
                                                    @lang('labels.birthday')
                                                </div>
                                                <div class="col-xs-9">
                                                    : {{date('d F Y', strtotime($user->user_staffpersonal->birth))}}
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-xs-3">
                                                    @lang('labels.gender')
                                                </div>
                                                <div class="col-xs-9">
                                                    :
                                                    @if($user->user_staffpersonal->sex == 'M')
                                                        @lang('labels.male')
                                                    @else
                                                        @lang('labels.female')
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-xs-3">
                                                    @lang('labels.address')
                                                </div>
                                                <div class="col-xs-9">
                                                    : {{$user->user_staffpersonal->address}}
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-xs-3">
                                                    @lang('labels.phone')
                                                </div>
                                                <div class="col-xs-9">
                                                    : {{$user->user_staffpersonal->phone}}
                                                </div>
                                            </div>

                                            <div class="row">
                                                <div class="col-xs-3">
                                                    @lang('labels.mobile_phone')
                                                </div>
                                                <div class="col-xs-9">
                                                    : {{$user->user_staffpersonal->cellphone}}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="osaccess" class="tab-pane fade">
                                <div class="x_panel tile">
                                    <div class="x_title">
                                        <h2>@lang('labels.role')</h2>
                                        <ul class="nav navbar-right panel_toolbox">
                                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                            </li>
                                            <li class="dropdown">
                                                <a><i class="fa fa-pencil"></i></a>
                                            </li>
                                        </ul>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <div class="row">
                                            <div class="col-xs-3">
                                                @lang('labels.club')
                                            </div>
                                            <div class="col-xs-9">
                                                @if(isset($user->user_club->club_name))
                                                : {{$user->user_club->club_name}}
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-xs-3">
                                                @lang('labels.role')
                                            </div>
                                            <div class="col-xs-9">
                                                @if(isset($user->user_role->role))
                                                : {{$user->user_role->role}}
                                                @endif
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-xs-3">
                                                @lang('labels.join')
                                            </div>
                                            <div class="col-xs-9">
                                                @if(isset($user->user_staffpersonal->joined))
                                                : {{date('d F Y', strtotime($user->user_staffpersonal->joined))}}
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="certification" class="tab-pane fade">
                                <h3>Menu 2</h3>
                                <p>Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque
                                    laudantium,
                                    totam rem aperiam.</p>
                            </div>
                            <div id="note" class="tab-pane fade">
                                <div class="x_panel">
                                    <div class="x_title">
                                        <h2>@lang('labels.note')</h2>
                                        <ul class="nav navbar-right panel_toolbox">
                                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                            </li>
                                            <li class="dropdown">
                                                <a><i class="fa fa-pencil"></i></a>
                                            </li>
                                        </ul>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered" id="data-table">
                                                <thead>
                                                <tr>
                                                    <th>
                                                        @lang('labels.indexing')
                                                    </th>
                                                    <th>
                                                        @lang('labels.note')
                                                    </th>
                                                    <th>
                                                        @lang('labels.member')
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                {{--<?php $count_notes = 0; ?>--}}
                                                {{--@foreach($staff_notes as $x)--}}
                                                    {{--<tr>--}}
                                                        {{--<td>{{++$count_notes}}</td>--}}
                                                        {{--<td>{{$x->notes}}</td>--}}
                                                        {{--<td>{{trans('labels.'.strtolower($x->is_member))}}</td>--}}
                                                    {{--</tr>--}}
                                                {{--@endforeach--}}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="work-hour" class="tab-pane fade">
                                <div class="x_panel tile">
                                    <div class="x_title">
                                        <h2>@lang('labels.staff_hour')</h2>
                                        <ul class="nav navbar-right panel_toolbox">
                                            <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                            </li>
                                            <li class="dropdown">
                                                <a><i class="fa fa-pencil"></i></a>
                                            </li>
                                        </ul>
                                        <div class="clearfix"></div>
                                    </div>
                                    <div class="x_content">
                                        <div class="table-responsive">
                                            <table class="table table-hover table-bordered" id="data-table">
                                                <thead>
                                                <tr>
                                                    <th>
                                                        @lang('labels.from_day')
                                                    </th>
                                                    <th>
                                                        @lang('labels.thru_day')
                                                    </th>
                                                    <th>
                                                        @lang('labels.from_hour')
                                                    </th>
                                                    <th>
                                                        @lang('labels.thru_hour')
                                                    </th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                {{--@foreach($staff_hours as $x)--}}
                                                    {{--<tr>--}}
                                                        {{--<td>{{trans('labels.'.strtolower($x->from_day))}}</td>--}}
                                                        {{--<td>{{trans('labels.'.strtolower($x->thru_day))}}</td>--}}
                                                        {{--<td>{{$x->from_hour}}</td>--}}
                                                        {{--<td>{{$x->thru_hour}}</td>--}}
                                                    {{--</tr>--}}
                                                {{--@endforeach--}}
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script src={{asset("vendors/iCheck/icheck.min.js")}}></script>
    <script src={{asset("js/jquery.dataTables.min.js")}}></script>
    <script src={{asset("js/dataTables.bootstrap.min.js")}}></script>
    <script src={{asset("vendors/select2/dist/js/select2.js")}}></script>
    <script src={{asset("vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js")}}></script>
    <script src={{asset("vendors/moment/min/moment.min.js")}}></script>
    <script src={{asset("vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js")}}></script>
    <script>
        $(document).ready(function () {
            $('#bday').datetimepicker({
                format: 'YYYY/MM/DD'
            });

            $('#select_role').select2({
                placeholder: "<?php echo trans('labels.choose') . ' ' . trans('labels.role'); ?>"
            });
            $('#select_club').select2({
                placeholder: "<?php echo trans('labels.choose') . ' ' . trans('labels.club'); ?>"
            });
            $('#countries').select2({
                placeholder: "<?php echo trans('labels.choose') . ' ' . trans('labels.country'); ?>"
            });
        });
    </script>

    <meta name="_token" content="{!! csrf_token() !!}"/>
@endsection
