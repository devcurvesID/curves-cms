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

        @media print {
            tr th:first-child, tr th:last-child, tr td:first-child, tr td:last-child {
                display: none;
            }
        }
    </style>

@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/cancelling-membership')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.inactive_member') <br><small>({{\Illuminate\Support\Facades\Auth::user()->user_club->club_name}})</small></h2>
            </center>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h3>@lang('labels.need_to_inactivated_member')</h3>
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
                                <th>@lang('labels.membership_end_date')</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($pending_members))
                                @foreach($pending_members as $pending_member)
                                    <tr>
                                        <td>
                                            @if(!empty($pending_member->user_memberpersonal->photo))
                                                <img class="img-circle" src="{{asset('images/member_photos/'.$pending_member->user_memberpersonal->photo)}}" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                {{--<a href="#">--}}
                                                <img class="img-circle" src="{{asset('images/img.png')}}" title="@lang('labels.photo')" style="width: 40px; height: 40px; object-fit: cover;">
                                                {{--</a>--}}
                                            @endif
                                        </td>
                                        <td id="name-col-{{$pending_member->id}}">{{$pending_member->name}} </td>
                                        <td>
                                            @if(!empty($pending_member->user_memberpersonal->phone))
                                            {{$pending_member->user_memberpersonal->phone}}
                                            @endif
                                            @if(!empty($pending_member->user_memberpersonal->cellphone))
                                                /{{$pending_member->user_memberpersonal->cellphone}}
                                            @endif
                                        </td>
                                        <td>{{\Carbon\Carbon::parse($pending_member->user_memberstatus->thru_date)->format('d-m-Y')}}</td>
                                        <td>
                                            <a href="javascript:;" class="btn btn-danger nonactivate-btn" id="nonactivate-btn-{{$pending_member->id}}" data-toggle="modal" data-target="#nonactivate-modal">@lang('labels.nonactivate') <span class="fa fa-chevron-right"></span> </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Modal -->
            <div class="modal fade" id="nonactivate-modal" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">@lang('labels.nonactivate_member')</h4>
                        </div>
                        {!! Form::open(['method' => 'POST', 'action' => 'CancellingMembershipController@inactivate_member']) !!}
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-horizontal">
                                        {!! Form::hidden('user_id', null, ['id' => 'user_id']) !!}
                                        <div class="form-group">
                                            {!! Form::label('name', trans('labels.name').': ', ['class' => 'control-label col-md-3']) !!}
                                            <div class="col-md-4">
                                                <p class="form-control-static name-text"></p>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('cancellation_reason', trans('labels.reason').': ', ['class' => 'control-label col-md-3']) !!}
                                            <div class="col-md-4">
                                                {!! Form::select('cancellation_reason', $cancellation_reasons, null, ['class' => 'form-control']) !!}
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('another_cancellation_reason', trans('labels.another_reason').': ', ['class' => 'control-label col-md-3']) !!}
                                            <div class="col-md-6">
                                                {!! Form::textarea('another_cancellation_reason', null, ['class' => 'form-control', 'rows' => '4']) !!}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('labels.close')</button>
                            <button type="submit" class="btn btn-primary">@lang('labels.save')</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <h3>@lang('labels.stopped_member_list')</h3>
        </div>
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="data-table-2">
                            <thead>
                            <tr>
                                <th>@lang('labels.photo')</th>
                                <th>@lang('labels.fullname')</th>
                                <th>@lang('labels.phone')</th>
                                <th>@lang('labels.reason')</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($cancelled_members))
                                @foreach($cancelled_members as $cancelled_member)
                                    <tr>
                                        <td>
                                            @if(!empty($cancelled_member->user_memberpersonal->photo))
                                                <img class="img-circle" src="{{asset('images/member_photos/'.$cancelled_member->user_memberpersonal->photo)}}" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                {{--<a href="#">--}}
                                                <img class="img-circle" src="{{asset('images/img.png')}}" title="@lang('labels.photo')" style="width: 40px; height: 40px; object-fit: cover;">
                                                {{--</a>--}}
                                            @endif
                                        </td>
                                        <td>{{$cancelled_member->name}} </td>
                                        <td>
                                            @if(!empty($cancelled_member->user_memberpersonal->phone))
                                            {{$cancelled_member->user_memberpersonal->phone}}
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($inactive_reasons[$cancelled_member->id]))
                                                @if(!empty($inactive_reasons[$cancelled_member->id]->reason_id))
                                                    <p>{{$inactive_reasons[$cancelled_member->id]->inactivereason_reason->nosale_reason}}</p>
                                                @endif
                                                <p>{{$inactive_reasons[$cancelled_member->id]->another_reason}}</p>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{url('cf-lite/continue-membership/'.$cancelled_member->id.'/reactivate')}}" class="btn btn-warning">@lang('labels.reactivate') <span class="fa fa-chevron-right"></span> </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
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
        $('#data-table').dataTable();
        $('#data-table-2').DataTable({
            info: false,
            dom: 'Bfrtip',
            buttons: [
                {
                    extend: 'print',
                    footer: true,
                    exportOptions: {
                        columns: ':visible'
                    },
                    customize: function (win) {
                        $(win.document.body).find('h1').text('@lang('labels.stopped_member_list') ({{\Carbon\Carbon::now()->format('d-m-Y')}})');
                    }
                }
            ]
        });
        $('.dt-buttons button').prepend('<i class="fa fa-print"></i> ');
        $('.dt-buttons button').addClass('btn btn-default');
    </script>

    <script>
        $(document).ready(function () {
            $('#data-table').on('click', '.nonactivate-btn', function () {
                var id = $(this).attr('id').split('-').pop();

                $('#user_id').val(id);
                $('.name-text').text($('#name-col-' + id).text());
            });
        });
    </script>
@endsection
