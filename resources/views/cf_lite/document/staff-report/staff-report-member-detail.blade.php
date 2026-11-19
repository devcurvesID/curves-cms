@extends('cf_lite.layouts.staff-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">

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

        .select2 {
            width: 100%!important;
        }

    </style>

@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/staff-report')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.staff_report') ({{$member->user_club->club_name}})</h2>
            </center>
        </div>
    </div>
    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3>
                    @lang('labels.first_thirty_days')!
                    <br>
                    <small>@lang('labels.change_member_to_A_from_begining')!</small>
                </h3>
            </div>
            <div class="panel-body">
                <div class="col-md-4">
                    <div class="row">
                        <div class="col-md-4">
                            @if(!empty($member->user_memberpersonal->photo))
                                <img src="{{asset('images/member_photos/'.$member->user_memberpersonal->photo)}}" class="img-circle center-block" style="width: 60px; height: 60px; object-fit: cover;">
                            @else
                                <img src="{{asset('images/img.png')}}" class="img-circle center-block" style="width: 60px; height: 60px; object-fit: cover;">
                            @endif
                        </div>
                        <div class="col-md-8">
                            <table>
                                <tbody>
                                    <tr>
                                        <td><span class="fa fa-female" title="@lang('labels.name')"></span></td>
                                        <td>:</td>
                                        <td>{{$member->name}}</td>
                                    </tr>
                                    <tr>
                                        <td><span class="fa fa-calendar" title="@lang('labels.joined')"></span></td>
                                        <td>:</td>
                                        <td>{{date('d F Y', strtotime($member->user_memberpersonal->joined))}}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            @if(count($xfactors) > 0)
                                <h4>@lang('labels.xfactor'):</h4>
                                <ol>
                                    @foreach($xfactors as $xfactor)
                                        <li>{{$xfactor->xfactor}}</li>
                                    @endforeach
                                </ol>
                            @else
                                <h4>@lang('labels.xfactor'): -</h4>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="col-md-8" style="border-left: 1px dashed deeppink;">
                    {!! Form::open(['method' => 'PATCH', 'action' => ['StaffReportController@store_thirty_days', $member->id]]) !!}
                    <div class="table-responsive">
                        <table class="table table-condensed">
                            <thead>
                                <tr>
                                    <th>@lang('labels.sign_the_box_if_done')</th>
                                    <th>@lang('labels.result')</th>
                                </tr>
                            </thead>
                            <tbody id="thirty-days-table">
                                @foreach($thirty_days as $thirty_day)
                                    @php($thirty_check = 0)
                                    @foreach($member_thirty_days as $member_thirty_day)
                                        @if($member_thirty_day->thirty_days_id == $thirty_day->id)
                                            @php($thirty_check = 1)
                                            @php($member_thirty_result = $member_thirty_day->result)
                                        @endif
                                    @endforeach

                                    @if($thirty_check == 1)
                                        <tr>
                                            <td>
                                                <span class="fa fa-check-square"></span> <a href="javascript:;" data-toggle="modal" data-target="#editThirtyModal" class="first-thirty" id="first-thirty-{{$thirty_day->id}}" title="@lang('labels.edit')">{{$thirty_day->menu}}</a>
                                            </td>
                                            <td>{{$member_thirty_result}}</td>
                                        </tr>
                                    @elseif($thirty_check == 0)
                                        <tr>
                                            <td>
                                                <div class="checkbox">
                                                    <label>
                                                        {!! Form::checkbox('thirty_box['.$thirty_day->id.']', $thirty_day->id, false) !!} {{$thirty_day->menu}}
                                                    </label>
                                                </div>
                                            </td>
                                            <td>
                                                {!! Form::textarea('thirty_result['.$thirty_day->id.']', null, ['class' => 'form-control', 'rows' => '3']) !!}
                                            </td>
                                        </tr>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-md-6">
                        {!! Form::select('thirty_staff', $staff_list, $coach_member->coach_id, ['class' => 'form-control', 'placeholder' => '== '.trans('labels.choose_coach_whose_handle').' ==']) !!}
                    </div>
                    <div class="col-md-6">
                        {!! Form::button('<span class="fa fa-save"></span> '.trans('labels.save_changes'), ['class' => 'btn btn-default pull-right', 'type' => 'submit']) !!}
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="panel panel-default">
            <div class="panel-heading">@lang('labels.report')</div>
            <div class="panel-body">
                <div class="col-md-11 col-md-offset-1 staff-report-add">
                    <a class="btn btn-default pull-right" id="add-staff-report" href="javascript:;" title="@lang('labels.add_new_report')" data-toggle="modal" data-target="#addStaffReportModal"><span class="fa fa-plus"></span> @lang('labels.add_new_report')</a>
                </div>
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <br/>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="data-table">
                                    <thead>
                                    <tr>
                                        <th>@lang('labels.note')</th>
                                        <th>@lang('labels.month')</th>
                                        <th>@lang('labels.monthly_contact')</th>
                                        <th>@lang('labels.date')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($member_reports as $member_report)
                                        <tr>
                                            <td>{{$member_report->note}}</td>
                                            <td>{{date('M', strtotime($member_report->contact_date))}}</td>
                                            <td>{{$member_report->report}}</td>
                                            <td>
                                                <a href="javascript:;" class="edit-staff-report" id="edit-staff-report-{{$member_report->id}}" data-toggle="modal" data-target="#addStaffReportModal">
                                                    {{date('d-m-Y', strtotime($member_report->contact_date))}}
                                                </a>
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
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addStaffReportModal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'POST', 'action' => ['StaffReportController@update_staff_report', $member->id]]) !!}
                {!! Form::hidden('report_attribute', null) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.add_new_report')</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    {!! Form::label('the_date', trans('labels.staff_report_date').': ', ['class' => 'control-label col-md-3']) !!}
                                    <div class="col-md-4">
                                        {!! Form::date('the_date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('the_note', trans('labels.note').': ', ['class' => 'control-label col-md-3']) !!}
                                    <div class="col-md-6">
                                        {!! Form::textarea('the_note', null, ['class' => 'form-control', 'rows' => '2']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('the_report', trans('labels.monthly_report').': ', ['class' => 'control-label col-md-3']) !!}
                                    <div class="col-md-6">
                                        {!! Form::textarea('the_report', null, ['class' => 'form-control', 'rows' => '4']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('the_staff', trans('labels.choose_staff').': ', ['class' => 'control-label col-md-3']) !!}
                                    <div class="col-md-4">
                                        {!! Form::select('the_staff', $staff_list, $coach_member->coach_id, ['class' => 'form-control', 'placeholder' => trans('labels.choose_staff')]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" class="btn btn-primary" id="save-btn">@lang('labels.save')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editThirtyModal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'PATCH', 'action' => ['StaffReportController@update_first_thirty_days', $member->id]]) !!}
                {!! Form::hidden('thirty_days_attribute', null) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.edit_first_thirty_days_result')</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <h3 class="form-control-static" id="the_menu"></h3>
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('the_result', trans('labels.result').': ', ['class' => 'control-label col-md-3']) !!}
                                    <div class="col-md-6">
                                        {!! Form::textarea('the_result', null, ['class' => 'form-control', 'rows' => '3']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('the_thirty_staff', trans('labels.choose_staff').': ', ['class' => 'control-label col-md-3']) !!}
                                    <div class="col-md-4">
                                        {!! Form::select('the_thirty_staff', $staff_list, $coach_member->coach_id, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" class="btn btn-primary" id="save-btn">@lang('labels.edit')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>

    <script>
        $('#data-table').dataTable();
        $('#select[name="the_staff"]').select2();
        $('select[name="thirty_staff"]').select2();
    </script>

    <script type="text/javascript">
        $(document).ready(function () {
            var url = "{{url('cf-lite')}}/";

            $('#data-table').on('click', '.edit-staff-report', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    report_id: $(this).attr('id').split('-').pop()
                };

                var type = 'POST';
                var my_url = url + 'get-staff-report-detail';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        var contact_date = new Date(data.report['contact_date']);
                        var day = ("0" + contact_date.getDate()).slice(-2);
                        var month = ("0" + (contact_date.getMonth() + 1)).slice(-2);
                        var the_date = contact_date.getFullYear()+"-"+(month)+"-"+(day) ;

                        $('input[name="the_date"]').val(the_date);
                        $('textarea[name="the_note"]').val(data.report['note']);
                        $('textarea[name="the_report"]').val(data.report['report']);
                        $('#the_staff').val(data.report['staff_id']);
                        $('#addStaffReportModal .modal-title').text('@lang('labels.edit_report')');
                        $('input[name="report_attribute"]').val(data.report['id']);
                        $('#save-btn').text('@lang('labels.edit')');
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });

            $('#add-staff-report').on('click', function () {
                var contact_date = new Date();
                var day = ("0" + contact_date.getDate()).slice(-2);
                var month = ("0" + (contact_date.getMonth() + 1)).slice(-2);
                var the_date = contact_date.getFullYear()+"-"+(month)+"-"+(day) ;

                $('input[name="the_date"]').val(the_date);
                $('textarea[name="the_note"]').val('');
                $('textarea[name="the_report"]').val('');
                $('#the_staff').val('');
                $('#addStaffReportModal .modal-title').text('@lang('labels.add_new_report')');
                $('#save-btn').text('@lang('labels.save')');
            });

            $('#thirty-days-table').on('click', '.first-thirty', function () {
                var thirty_id = $(this).attr('id').split('-').pop();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    thirty_days_id: thirty_id
                };

                var type = 'POST';
                var my_url = url + 'get-thirty-days-report-detail';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        $('#the_menu').text($('#first-thirty-' + thirty_id).text());
                        $('input[name="thirty_days_attribute"]').val(data.thirty_days['id']);
                        $('textarea[name="the_result"]').val(data.thirty_days['result']);
                        $('#the_thirty_staff').val(data.thirty_days['staff_id']);
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });
        });
    </script>
@endsection