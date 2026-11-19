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
            <a href="{{url('cf-lite/cancelling-membership')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.membership_cancellation') <br><small>({{$club->club_name}})</small></h2>
            </center>
        </div>
        <div class="col-md-3">
            <div id="filter-zone">
                {!! Form::open(['method' => 'GET', 'action' => 'CancellingMembershipController@cancellation_request']) !!}
                <div class="form-group">
                    <label class="radio-inline">
                        {!! Form::radio('search_option', 'name', true, ['id' => 'search-option-1']) !!} @lang('labels.name')
                    </label>
                    <label class="radio-inline">
                        {!! Form::radio('search_option', 'keytag', false, ['id' => 'search-option-2']) !!} @lang('labels.key_tag')
                    </label>
                </div>
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
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                            @if(!empty($members))
                                @foreach($members as $member)
                                    @php($access = 1)
                                    @if(count($member->user_refundsubmission) > 0)
                                        @foreach($member->user_refundsubmission as $rs)
                                            @if($rs->refund_status == 'Pending')
                                                @php($access = 0)
                                            @endif
                                        @endforeach
                                    @endif

                                    @if($access == 1)
                                        <tr>
                                            <td>
                                                @if(!empty($member->user_memberpersonal->photo))
                                                    <img class="img-circle" src="{{asset('images/member_photos/'.$member->user_memberpersonal->photo)}}" style="width: 40px; height: 40px; object-fit: cover;">
                                                @else
                                                    {{--<a href="#">--}}
                                                    <img class="img-circle" src="{{asset('images/img.png')}}" title="@lang('labels.photo')" style="width: 40px; height: 40px; object-fit: cover;">
                                                    {{--</a>--}}
                                                @endif
                                            </td>
                                            <td>{{$member->name}} </td>
                                            <td>{{$member->user_memberpersonal->phone}}</td>
                                            <td>
                                                <a href="{{url('cf-lite/cancelling-membership/request/'.$member->id)}}" class="btn btn-warning">@lang('labels.create_termination_request') <span class="fa fa-chevron-right"></span> </a>
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @if(!empty($members) && (count($members) > 0))
                <div class="paging hiden rom">
                    {{$members->links()}}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $('#data-table').dataTable();
    </script>
@endsection
