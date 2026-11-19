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
                <h2 class="page-title">@lang('labels.weigh_n_measure')<br><small>@lang('labels.member_list')</small></h2>
            </center>
        </div>
        <div class="col-md-offset-4 col-md-8">
            <div id='calendar'></div>
        </div>
        <div class="col-md-12" style="margin-top: 30px;">
            <div class="col-md-6">
                {!! Form::open(['method' => 'GET', 'action' => 'WeighAndMeasureController@wm_member_list', 'class' => 'form-inline']) !!}
                <div class="form-group">
                    {!! Form::label('choose_date', trans('labels.choose_date').': ', ['class' => 'control-label']) !!}
                    <div class="input-group">
                        {!! Form::selectRange('choose_date', 1, 31, null, ['class' => 'form-control']) !!}
                        <span class="input-group-btn">
                            <button class="btn btn-default" type="submit">@lang('labels.set_filter')</button>
                        </span>
                    </div>
                    <a href="{{url('cf-lite/weigh-measure')}}" class="btn btn-info" title="@lang('labels.reset_date')"><span class="fa fa-undo"></span></a>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        @if(isset($_GET['choose_date']) && !empty($_GET['choose_date']))
            @php($the_chosen_date = $_GET['choose_date'])
            <div class="col-md-12">
                <h4 style="font-weight: 500;">@lang('labels.filter'): @lang('labels.fwo_date') {{$_GET['choose_date']}}</h4>
            </div>
        @else
            @php($the_chosen_date = trans('labels.all'))
        @endif
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
                                    <th>@lang('labels.fwo_date')</th>
                                    <th>@lang('labels.last_weigh_n_measure')</th>
                                    <th>@lang('labels.wm_schedule')</th>
                                    <th>@lang('labels.add')</th>
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
                                                @if($member->wm_schedule == 'NotSet')
                                                    @lang('labels.not_set_yet') <a href="{{url('cf-lite/weigh-measure/'.$member->id.'/schedule')}}" class="btn btn-default btn-sm" title="@lang('labels.set_wm_schedule')"><span class="fa fa-cog"></span></a>
                                                @elseif($member->wm_schedule == 'Set')
                                                    @lang('labels.have_been_set') <a href="{{url('cf-lite/weigh-measure/'.$member->id.'/schedule')}}" class="btn btn-default btn-sm" title="@lang('labels.view_wm_schedule')"><span class="fa fa-calendar"></span></a>
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{url('cf-lite/weigh-measure/'.$member->id.'/add')}}" target="_blank" class="btn btn-default btn-sm" title="@lang('labels.add_new_weigh_n_measure')"><span class="fa fa-plus"></span></a>
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
                        $(win.document.body).find('h1').text('@lang('labels.weigh_n_measure')(@lang('labels.fwo_date'): {{$the_chosen_date}})');
                    }
                }
            ]
        });

        $('.dt-buttons button').prepend('<i class="fa fa-print"></i> ');
        $('.dt-buttons button').addClass('btn btn-default btn-sm');
    </script>

    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('fullcalendar/fullcalendar.min.js')}}"></script>

    <script>
        var appointment_date;
        $(document).ready(function () {
            $('#calendar').fullCalendar({
                header: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'month,basicWeek,basicDay'
                },
                defaultView: 'month',
                aspectRatio: 2,
                navLinks: false, // can click day/week names to navigate views
                editable: false,
                eventLimit: true, // allow "more" link when too many events
                weekends: true, // will Hide Saturdays and Sundays
                selectable: true,
                select: function (start, end, jsEvent, view) {
//                    appointment_date = moment(start).format();
//                    $('input[name="date_chosen"]').val(appointment_date);
//                    $('#form-attendance-date').submit();
                },
                events: [
                        @foreach($mas as $ma)
                            {
                                title: '{{$ma->memberappointment_user->name}}',
                                start: '{{date('Y-m-d', strtotime($ma->app_date))}}',
                                backgroundColor: '#ffe4e1',
                                textColor: '#665b5a',
                                borderColor: '#ffb6ae',
                                className: 'ma-cal {{'ma-cal-'.$ma->id}}'
                            },
                        @endforeach
                ]
            });

            $('.fc-day').css('cursor', 'pointer');
        });
    </script>
@endsection