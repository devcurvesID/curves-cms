@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <style>
        .home-btn-title{
            font-family: 'Alegreya Sans', sans-serif;
        }

        .value-base {
            display: block;
        }

        .percent-base {
            display: none;
        }

        #menuutama {
            padding-top: 10px;
            padding-bottom: 10px;
            padding-left: 30px;
            padding-right: 30px;
        }
    </style>

    <style>
        .home-btn-title {
            font-family: 'Alegreya Sans', sans-serif;
        }

        .value-base {
            display: block;
        }

        .percent-base {
            display: none;
        }

        div.dataTables_wrapper div.dataTables_length select {
            margin: 0 10px;
        }

        table th{
            color: #ff1493;
        }

        .dl-horizontal dt {
            width: 90px;
        }

        .dl-horizontal dd {
            margin-left: 110px;
        }

    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/continue-membership')}}" style="left: 25px; top: 5px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.continue_membership_request_list')</h2>
                </center>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="data-table-2">
                                <thead>
                                <tr>
                                    <th>@lang('labels.keytag')</th>
                                    <th>@lang('labels.name')</th>
                                    <th>@lang('labels.new_membership')</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($members as $member)
                                    <tr>
                                        <td>#{{$member->continuemembership_user->user_memberpersonal->key_tag_id}}</td>
                                        <td>{{$member->continuemembership_user->name}}</td>
                                        <td>
                                            <dl class="dl-horizontal">
                                                <dt>{{\Carbon\Carbon::parse($member->from_date)->format('d-m-Y')}}</dt>
                                                <dd>{{$member->continuemembership_membershiptype->membership_type_name}}</dd>
                                            </dl>
                                        </td>
                                        <td>
                                            <a href="{{url('cf-lite/continue-membership/cancel/'.$member->id)}}" class="btn btn-danger btn-sm">@lang('labels.submit_cancellation') <span class="fa fa-chevron-right"></span> </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="paging hidden rom">
                                    {{$members->links()}}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>

    <script>
        $('#data-table-2').dataTable();
    </script>
@endsection
