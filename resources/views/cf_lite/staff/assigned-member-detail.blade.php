@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">

    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">
    <style>
        .home-btn-title {
            font-family: 'Alegreya Sans', sans-serif;
        }

        .home-btn span{
            color: #ffffff;
        }

        h3.home-btn-title{
            color: #ffffff;
            margin: 20px 20px 10px 20px;
        }

        .main-btn:hover .home-btn span,
        .main-btn:hover h3.home-btn-title{
            color: #666666;
        }

        .select2 {
            width: 25% !important;
            float: left;
        }

        .label-success {
            font-size: 12px;
            background-color: rebeccapurple;
            margin-left: 40px;
        }

        .img-table {
            width: 40px !important;
            height: 40px !important;
        }

    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/auto-assign-member')}}" style="left: 25px; top: 5px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.coach_assigned_members')</h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3">
                @if(!empty($staff->user_memberpersonal->photo))
                    <img src="{{asset('images/member_photos/'.$staff->user_memberpersonal->photo)}}" class="img-circle" style="object-fit: cover;">
                @else
                    <img src="{{asset('images/img.png')}}" class="img-circle" style="object-fit: cover;">
                @endif
            </div>
            <div class="col-sm-6">
                <h4>{{$staff->name}} <span class="label label-success">{{$staff->user_role->role}}</span></h4>
                <dl>
                    <dt>@lang('labels.member_detail'):</dt>
                    @for($i = 'A'; $i <= 'E'; $i++)
                        <dd>{{$i}}: {{count($members->where('flag', $i))}} @lang('labels.person')</dd>
                    @endfor
                </dl>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-sm-6">
                <h4>@lang('labels.member_list')</h4>
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="member-list">
                                <thead>
                                <tr>
                                    <th>@lang('labels.photo')</th>
                                    <th>@lang('labels.name')</th>
                                    <th>@lang('labels.information')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($members as $member)
                                    <tr>
                                        <td>
                                            @if(!empty($member->photo))
                                                <img src="{{asset('images/member_photos/'.$member->photo)}}" class="img-circle img-table center-block" style="object-fit: cover;">
                                            @else
                                                <img src="{{asset('images/img.png')}}" class="img-circle img-table center-block" style="object-fit: cover;">
                                            @endif
                                        </td>
                                        <td>{{$member->memberpersonal_user->name}}</td>
                                        <td>
                                            <p>{{$member->flag}} <span class="fa fa-flag"></span> <span class="label label-default">{{$member->memberpersonal_user->user_memberstatus->status}}</span></p>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-sm-6">
                <h4>@lang('labels.other_member')</h4>
                {!! Form::open(['method' => 'POST', 'action' => ['SettingController@update_auto_assign', $staff->id], 'id' => 'other-member-form']) !!}
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="other-list">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>@lang('labels.photo')</th>
                                    <th>@lang('labels.name')</th>
                                    <th>@lang('labels.information')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($othermembers as $other_member)
                                    <tr>
                                        <td>
                                            {!! Form::checkbox('member['.$other_member->id.']', $other_member->id, false) !!}
                                        </td>
                                        <td>
                                            @if(!empty($other_member->user_memberpersonal->photo))
                                                <img src="{{asset('images/member_photos/'.$other_member->user_memberpersonal->photo)}}" class="img-circle img-table center-block" style="object-fit: cover;">
                                            @else
                                                <img src="{{asset('images/img.png')}}" class="img-circle img-table center-block" style="object-fit: cover;">
                                            @endif
                                        </td>
                                        <td>{{$other_member->name}}</td>
                                        <td>
                                            <p>{{$other_member->user_memberpersonal->flag}} <span class="fa fa-flag"></span> <span class="label label-default">{{$other_member->user_memberstatus->status}}</span></p>
                                            <p>@lang('labels.coach'): {{$other_member->memberuser_coachmember->coachmember_coachuser->name}}</p>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <button type="button" id="add-to-list-btn" class="btn btn-default btn-lg" title="@lang('labels.add_to_member_list')"><span class="fa fa-angle-double-left"></span></button>
                {!! Form::close() !!}
            </div>
        </div>
    </section>
@endsection

@section('other-script')
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            "use strict";
            var count = 0;
            $('#member-list').dataTable({
                'order': [[2, 'desc'], [1, 'asc']]
            });

            var table = $('#other-list').dataTable({
                'order': [[2, 'desc'], [1, 'asc']]
            });

            $('#add-to-list-btn').on('click', function () {
                var $form = $('#other-member-form');

                // Iterate over all checkboxes in the table
                table.$('input[type="checkbox"]').each(function(){
                    //If checkbox exist in DOM
                    if($.contains(document, this)){
                        // If checkbox is checked
                        if(this.checked){
                            count++;
                        }
                    }
                    // If checkbox doesn't exist in DOM
                    if(!$.contains(document, this)){
                        // If checkbox is checked
                        if(this.checked){
                            // Create a hidden element
                            $form.append(
                                $('<input>')
                                    .attr('type', 'hidden')
                                    .attr('name', this.name)
                                    .val(this.value)
                            );
                            count++;
                        }
                    }
                });

                if (count > 0) {
                    $form.submit();
                } else {
                    alert('@lang('messages.nothing_has_chosen')');
                }
            });
        });
    </script>
@endsection