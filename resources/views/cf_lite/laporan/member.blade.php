@extends('cf_lite.layouts.staff-dashboard-template')

@if(isset($filter))
    @section('title', trans('labels.report_member_status').$club->club_name . ' [' . $filter . ']')
@else
    @section('title', trans('labels.report_member_status').$club->club_name)
@endif

@section('other-style')
    <link href="{{asset('fullcalendar/fullcalendar.min.css')}}" rel="stylesheet">
    <link href="{{asset('fullcalendar/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">

    <link href="{{asset('bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/iCheck/skins/flat/purple.css')}}" rel="stylesheet">
    <style>
        .fc-right, .fc-time {
            display: none !important;
        }

        #dataTable_filter, .dataTables_paginate {
            float: right;
        }

        .dt-buttons {
            float: left;
        }
    </style>
@endsection

@section('content')
    <div class="col-xs-12">
        <left>
            <a href="{{url('cf-lite/laporan')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
        </left>
        <center>
            <h2 class="page-title">@lang('labels.list') @lang('labels.member') : {{$club->club_name}}</h2>
        </center>
    </div>
    <div class="col-xs-12">
        <div class="section-filter">
            <div class="form-group">
                <div class="row">

                    <div class="col-xs-2">
                        @lang('labels.status') :
                    </div>
                    <div class="col-xs-2">
                        <input name="status" type="radio" class="flat" value="semua"
                        <?php
                            if (isset($filter)) {
                                if ($filter == "semua") {
                                    echo 'checked';
                                }
                            } else {
                                echo 'checked';
                            }
                            ?>> @lang('labels.all')
                    </div>
                    <div class="col-xs-2">
                        <input name="status" type="radio" class="flat" value="active"
                        <?php
                            if (isset($filter)) {
                                if ($filter == "active") {
                                    echo 'checked';
                                }
                            }
                            ?>
                        > @lang('labels.active')
                    </div>
                    <div class="col-xs-2">
                        <input name="status" type="radio" class="flat" value="inactive"
                        <?php
                            if (isset($filter)) {
                                if ($filter == "inactive") {
                                    echo 'checked';
                                }
                            }
                            ?>
                        > @lang('labels.inactive')
                    </div>
                </div>
            </div>
        </div>
        {{--<div class="input-group my-hiddenz" style="margin-bottom: 20px;">--}}
        {{--<input type="text" class="tanggal form-control">--}}
        {{--<span class="input-group-addon btn btn-default"><i class="fa fa-calendar"></i></span>--}}
        {{--</div>--}}
        <table id="dataTable" class="table table-bordered table-striped" style="background: white">
            <thead>
            <tr>
                <th>@lang('labels.indexing')</th>
                <th>@lang('labels.keytag')</th>
                <th>@lang('labels.name')</th>
                <th>@lang('labels.address')</th>
                <th>@lang('labels.phone')</th>
                <th>@lang('labels.status')</th>
            </tr>
            </thead>
            <tbody>
            @php($indexing = 1)
            @foreach($list_members as $x)
                <tr>
                    <td>{{$indexing}}</td>
                    <td>{{isset($x->user_memberpersonal) ? $x->user_memberpersonal->key_tag_id : ''}}</td>
                    <td>{{$x->name}}</td>
                    <td>{{isset($x->user_memberpersonal) ? $x->user_memberpersonal->address : ''}}, {{isset($x->user_memberpersonal) ? $x->user_memberpersonal->postal : '' }}</td>
                    <td>{{isset($x->user_memberpersonal) ? $x->user_memberpersonal->phone : ''}} / {{isset($x->user_memberpersonal) ? $x->user_memberpersonal->cellphone : '' }}</td>
                    <td>{{isset($x->user_memberpersonal) ? $x->user_memberstatus->status : '' }}</td>
                </tr>
            @php($indexing++)
            @endforeach
            </tbody>
        </table>
    </div>
@endsection

@section('other-script')

    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('js/buttons.print.min.js')}}"></script>

    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('fullcalendar/fullcalendar.min.js')}}"></script>
    <script src="{{asset('bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script src="{{asset('vendors/iCheck/icheck.min.js')}}"></script>
    <script>
        $('.tanggal').datetimepicker({
            format: 'MM-YYYY',
            defaultDate: '{{date('Y-m-d')}}',
        });

        $('#dataTable').DataTable({
            info: false,
            dom: 'Bfrtip',
            buttons: [
                'print',
            ]
        });
        $(document).ready(function () {
            if ($("input.flat")[0]) {
                $(document).ready(function () {
                    $('input.flat').iCheck({
                        checkboxClass: 'icheckbox_flat-purple',
                        radioClass: 'iradio_flat-purple'
                    });
                });
            }
        });

        $(".flat").on("ifChecked", function () {
            var x = $(this).val();
            if (x == 'semua') {
                window.location = "{{url('cf-lite/laporan/member')}}";
            } else {
                window.location = "{{url('cf-lite/laporan/member')}}" + "/" + x;
            }
        });

        $('.dt-buttons button').prepend('<i class="fa fa-print"></i> ');
        $('.dt-buttons button').addClass('btn btn-default');
    </script>
@endsection
