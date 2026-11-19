@extends('cf_lite.layouts.staff-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('fullcalendar/fullcalendar.min.css')}}" rel="stylesheet">
    <link href="{{asset('fullcalendar/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">

    <style>
        #calendar {
            max-width: 900px;
            margin: 0 auto;
        }

        @media print {
            th, td {
                vertical-align: middle !important;
            }

            tr td:nth-child(1), tr td:nth-child(6), tr td:last-child, tr th:nth-child(1), tr th:nth-child(6), tr th:last-child {
                display: none;
            }
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/dashboard')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.weigh_n_measure')<br><small>@lang('labels.member_list') (@lang('labels.all_club'))</small></h2>
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
                                    <th>@lang('labels.club')</th>
                                    <th>@lang('labels.phone')</th>
                                    <th>@lang('labels.fwo_date')</th>
                                    <th>@lang('labels.last_weigh_n_measure')</th>
                                    <th>@lang('labels.weight_difference')</th>
                                    <th>@lang('labels.measurement_difference')</th>
                                    <th>@lang('labels.body_fat_difference')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($members as $member)
                                    @php($open_gate = 0)
                                    @if(!empty($_GET['choose_date']))
                                        @if($_GET['choose_date'] == $member->wo_day)
                                            @php($open_gate = 1)
                                        @endif
                                    @else
                                        @php($open_gate = 1)
                                    @endif

                                    @if($open_gate == 1)
                                        <tr>
                                            <td>
                                                <a href="{{url('cf-lite/weigh-measure/'.$member->id.'/history')}}">
                                                    @if(!empty($member->user_memberpersonal->photo))
                                                        <img class="img-circle" src="{{asset('images/member_photos/'.$member->user_memberpersonal->photo)}}" style="width: 40px; height: 40px; object-fit: cover;">
                                                    @else
                                                        <img class="img-circle" src="{{asset('images/img.png')}}" style="width: 40px; height: 40px; object-fit: cover;">
                                                    @endif
                                                </a>
                                            </td>
                                            <td>
                                                <a href="{{url('cf-lite/weigh-measure/'.$member->id.'/history')}}">{{$member->name}}</a>
                                            </td>
                                            <td>{{$member->club_code}}</td>
                                            <td>
                                                {{$member->phone}}
                                                @if(!empty($member->cellphone))
                                                    / {{$member->cellphone}}
                                                @endif
                                            </td>
                                            {{--<td>{{\Carbon\Carbon::parse($member->user_memberstatus->from_date)->format('d-m-Y')}}</td>--}}
                                            <td>{{\Carbon\Carbon::parse($member->wo_date)->format('d-m-Y')}}</td>
                                            <td>
                                                @if(!empty($member->last_wm))
                                                    {{date('d F Y', strtotime($member->last_wm))}}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($member->weight))
                                                    {{$member->weight}} @lang('labels.kg')
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($member->tm))
                                                    {{$member->tm}} @lang('labels.cm')
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($member->body_fat))
                                                    {{$member->body_fat}} %
                                                @else
                                                    -
                                                @endif
                                            </td>
                                        </tr>
                                    @endif
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
    <script src="{{asset('js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('js/buttons.print.min.js')}}"></script>

    <script>
        $('#data-table').DataTable({
            info: false,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'print',
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function (win) {
                        $(win.document.body).find('h1').text('@lang('labels.weigh_n_measure')');
                    }
                }
            ]
        });

        $('.dt-buttons button').prepend('<i class="fa fa-print"></i> ');
        $('.dt-buttons button').addClass('btn btn-default btn-sm');
    </script>
@endsection