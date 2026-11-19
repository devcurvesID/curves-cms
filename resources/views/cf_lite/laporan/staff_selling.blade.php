@extends('cf_lite.layouts.staff-dashboard-template')

@section('title', 'Laporan Workout Member : ' . $club->club_name)

@section('other-style')
    <link href="{{asset('fullcalendar/fullcalendar.min.css')}}" rel="stylesheet">
    <link href="{{asset('fullcalendar/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">
    <link href="{{asset('bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">
    <link href="{{asset('select2/dist/css/select2.min.css')}}" rel="stylesheet">
    <style>
        .dt-buttons {
            float: left;
        }

        #dataTable_filter {
            float: right;
        }

        @media print {
            #content:after {
                display: block;
                content: "";
                margin-bottom: 594mm; /* must be larger than largest paper size you support */
            }
        }
    </style>
@endsection

@section('content')
    <?php
    if (isset($staff)) {

    } else {
        $staff = "";
    }

    if (isset($filter1)) {
        if ($filter1 == "0") {
            $filter1 = "";
        }
    } else {
        $filter1 = "";
    }

    if (isset($filter2)) {
        if ($filter2 == "0") {
            $filter2 = "";
        }
    } else {
        $filter2 = "";
    }
    ?>
    <div class="col-xs-12">
        <left>
            <a href="{{url('cf-lite/laporan')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
        </left>
        <center>
            <h2 class="page-title">@lang('labels.list') @lang('labels.staff_selling') : {{$club->club_name}}</h2>
        </center>
    </div>
    <div class="col-xs-12">
        <div class="row">
            <div class="col-xs-2">
                <label>@lang('labels.filter') : </label>
            </div>
            <div class="col-xs-2">
                {!! Form::select('staff', $staffs, $staff, ['class' => 'form-control', 'placeholder' => 'Pilih Staff', 'id' => 'staff']) !!}
            </div>
            <div class="col-xs-2">
                <input type="text" class="filt-date form-control" id="filter-dari"
                       placeholder="@lang('labels.from_date')" value="{{$filter1}}"/>
            </div>
            <div class="col-xs-2">
                <input type="text" class="filt-date form-control" id="filter-sampai"
                       placeholder="@lang('labels.thru_date')" value="{{$filter2}}"/>
            </div>
            <div class="col-xs-2">
                <a id="btn-search" class="btn btn-primary"><i class="fa fa-search"></i> @lang('labels.search')</a>
            </div>
            <div class="col-xs-12">
                <hr/>
            </div>
        </div>
        <table id="dataTable" class="table table-bordered table-striped" style="background: white">
            <thead>
            <tr>
                <th>@lang('labels.indexing')</th>
                <th>@lang('labels.staff')</th>
                <th>@lang('labels.lead')</th>
                <th>@lang('labels.sold')</th>
            </tr>
            </thead>
            <tbody>
            @php($count = 1)
            @foreach($list_appointment as $x)
                <tr>
                    <td>{{$count++}}</td>
                    <td>{{$x->gastatus_guestappointment->guestappointment_user->name}}</td>
                    <td>
                        {{$x->gastatus_guestappointment->guestappointment_buddyreferrallead->fullname}}
                    </td>
                    <td>
                        <?php if (empty($x->sa_date)) {
                            echo '<span style="color: red">☒</span>';
                        } else {
                            echo '<span style="color: green">☑ : ' . date('d F Y', strtotime($x->c_date)) . '</span>';
                        }
                        ?>
                    </td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
            <tr>
                <td colspan="3">@lang('labels.total')

                    <?php
                    if ($filter1 == "" || $filter1 == "0") {
                    } else {
                        echo '[' . trans('labels.from_date') . ':' . date('d F Y', strtotime($filter1)) . ']';
                    }
                    if ($filter2 == "" || $filter2 == "0") {
                    } else {
                        echo '[' . trans('labels.thru_date') . ':' . $filter2 . ']';
                    }
                    ?>
                </td>
                <td>
                    {{count($list_appointment)}}
                </td>
            </tr>
            </tfoot>
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

    <script src="{{asset('select2/dist/js/select2.js')}}"></script>
    <script>

        $('#staff').select2();
        $('#filter-dari').datetimepicker({
            format: 'YYYY-MM-DD',
        });
        $('#filter-sampai').datetimepicker({
            format: 'YYYY-MM-DD',
        });
        $('#dataTable').DataTable({
            info: false,
            dom: 'Bfrtip',
            buttons: [{
                extend: 'print',
                footer: true,
                exportOptions: {
                    columns: ':visible'
                }
            }],
        });

        $('#btn-search').on('click', function () {
            var staff = $('#staff').val();
            var filter1 = $('#filter-dari').val();
            var filter2 = $('#filter-sampai').val();
            if (staff == "") {
                staff = 0;
            }

            if (filter1 == "") {
                filter1 = 0;
            }

            if (filter2 == "") {
                filter2 = 0;
            }

            window.location = "{{url('cf-lite/laporan/penjualan-staff')}}" + "/" + staff + "/" + filter1 + "/" + filter2;
        });

        $('.dt-buttons button').prepend('<i class="fa fa-print"></i> ');
        $('.dt-buttons button').addClass('btn btn-default');
    </script>
@endsection