@extends('cf_lite.layouts.staff-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <style>

    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/guest-member/list')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.weigh_n_measure')<br><small>@lang('labels.guest_list')</small></h2>
            </center>
        </div>
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive" style="background-color: #fff; margin-top: 30px;">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                            <tr>
                                <th>@lang('labels.photo')</th>
                                <th>@lang('labels.fullname')</th>
                                <th>@lang('labels.phone')</th>
                                <th>@lang('labels.last_weigh_n_measure')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($members as $member)
                                    <tr>
                                        <td>
                                            <a href="{{url('cf-lite/guest-member/guest-weight-measure/'.$member->guest_id.'/history')}}">
                                                <img class="img-circle" src="{{asset('images/img.png')}}" style="width: 40px; height: 40px; object-fit: cover;">
                                            </a>
                                        </td>
                                        <td>
                                            <a href="{{url('cf-lite/guest-member/guest-weight-measure/'.$member->guest_id.'/history')}}">{{$member->fullname}}</a>
                                        </td>
                                        <td>
                                            {{$member->phone}}
                                            @if(!empty($member->cellphone))
                                                / {{$member->cellphone}}
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($member->wm_date))
                                                {{date('d F Y', strtotime($member->wm_date))}}
                                            @else
                                                -
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
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#data-table').dataTable();
        });
    </script>
@endsection