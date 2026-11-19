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

    </style>

@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/document')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.member_list') ({{$club->club_name}})</h2>
            </center>
        </div>
        <div class="col-md-3">
            <div id="filter-zone">
                {!! Form::open(['method' => 'GET', 'action' => 'StaffReportController@staff_report_choose_member']) !!}
                <div class="form-group">
                    <div class="input-group">
                        {!! Form::select('status', ['Active' => 'Active', 'Inactive' => 'Inactive', 'Stopped' => 'Stopped','Hold' => 'Hold','Semua' => 'Semua'], $status, ['class' =>
                        'form-control']) !!}
                    </div>
                </div>
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
                                    <th>@lang('labels.last_contact')</th>
                                    <th>@lang('labels.coach')</th>
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
                                            <a href="{{url('cf-lite/staff-report/'.$member->id)}}" target="_blank" title="@lang('labels.edit')">
                                                {{$member->name}}
                                                @if($member->user_memberstatus->status == 'Stopped')
                                                    <span class="fa fa-ban" title="@lang('labels.cancelled')" style="color: red;"></span>
                                                @endif
                                            </a>
                                        </td>
                                        <td>{{$member->user_memberpersonal->phone}}</td>
                                        <td>
                                            @if(!empty($member->member_staffreport))
                                                @foreach($member->member_staffreport as $staff_report)
                                                    @if($loop->last)
                                                        {{date('d-m-Y', strtotime($staff_report->contact_date))}}
                                                    @endif
                                                @endforeach
                                            @else
                                                @lang('labels.not_contacted_yet')
                                            @endif
                                        </td>
                                        <td>
                                            {{ $member->memberuser_coachmember()->exists() ? $member->memberuser_coachmember->coachmember_coachuser->name :  '' }}
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
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $('#data-table').dataTable();
    </script>
@endsection
