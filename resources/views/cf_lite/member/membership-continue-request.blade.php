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
                    <h2 class="page-title">@lang('labels.continue_membership')</h2>
                </center>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <h3>@lang('labels.member_list')</h3>
            </div>
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
                                    <th>@lang('labels.current_membership_type')</th>
                                    <th>@lang('labels.membership_end')</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($members as $member)
                                    <tr>
                                        <td># <?php echo isset($member->user_memberpersonal->key_tag_id) ? $member->user_memberpersonal->key_tag_id : ''; ?></td>
                                        <td>{{$member->name}}</td>
                                        <td>{{$member->user_memberstatus->memberstatus_membershiptype->membership_type_name}}</td>
                                        <td>{{\Carbon\Carbon::parse($member->user_memberstatus->thru_date)->format('d-m-Y')}}</td>
                                        <td>
                                            @if(count($member->user_continuemembership->where('status', 'Pending')) > 0)
                                                <dl class="dl-horizontal">
                                                    @foreach($member->user_continuemembership as $continue)
                                                        @if($continue->status == 'Pending')
                                                            <dt>{{\Carbon\Carbon::parse($continue->from_date)->format('d-m-Y')}}</dt>
                                                            <dd>{{$continue->continuemembership_membershiptype->membership_type_name}}</dd>
                                                        @endif
                                                    @endforeach
                                                </dl>
                                                <a href="{{url('cf-lite/continue-membership/'.$member->id.'/membership')}}" class="btn btn-info btn-sm">
                                                    @lang('labels.continuing_membership_again') <span class="fa fa-chevron-right"></span>
                                                </a>
                                            @else
                                                <a href="{{url('cf-lite/continue-membership/'.$member->id.'/membership')}}" class="btn btn-success btn-sm">
                                                    @lang('labels.continuing_membership') <span class="fa fa-chevron-right"></span>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        {{--<div class="row">--}}
                            {{--<div class="col-md-12">--}}
                                {{--<div class="paging hiden rom">--}}
                                    {{--{{$members->links()}}--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--</div>--}}
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
