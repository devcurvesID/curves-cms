@extends('cf_lite.layouts.staff-dashboard-template')

@section('title', 'Laporan Workout Member : ' . $club->club_name)

@section('other-style')
    <link href="{{asset('fullcalendar/fullcalendar.min.css')}}" rel="stylesheet">
    <link href="{{asset('fullcalendar/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">

    <link href="{{asset('bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">
    <style>
        .dt-buttons {
            float: left;
        }

        #dataTable_filter {
            float: right;
        }
    </style>
@endsection

@section('content')
    <div class="col-xs-12">
        <left>
            <a href="{{url('cf-lite/laporan')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
        </left>
        <center>
            <h2 class="page-title">@lang('labels.list') @lang('labels.appointment') : {{$club->club_name}}</h2>
        </center>
    </div>
    <div class="col-xs-12">
        <table id="dataTable" class="table table-bordered table-striped" style="background: white">
            <thead>
            <tr>
                <th>@lang('labels.indexing')</th>
                <th>@lang('labels.name')</th>
                <th>@lang('labels.confirm')</th>
                <th>@lang('labels.show_up')</th>
                <th>@lang('labels.sale')</th>
            </tr>
            </thead>
            <tbody>
            @php($count = 1)
            @php($counter_true_confirm = 0)
            @php( $counter_false_confirm = 0)

            @php($counter_true_showup = 0)
            @php( $counter_false_showup = 0)

            @php($counter_true_sale = 0)
            @php($counter_false_sale = 0)

            @foreach($list_appointment as $x)
                <tr>
                    <td>{{$count++}}</td>
                    <td>
                        {{$x->gastatus_guestappointment->guestappointment_buddyreferrallead->fullname}}
                    </td>
                    <td>
                        <?php if (empty($x->c_date)) {
                            $counter_false_confirm++;
                            echo '<span style="color: red">☒</span>';
                        } else {
                            $counter_true_confirm++;
                            echo '<span style="color: green">☑ : ' . date('d F Y', strtotime($x->c_date)) . '</span>';
                        }
                        ?>
                    </td>
                    <td>
                        <?php if (empty($x->s_date)) {
                            $counter_false_showup++;
                            echo '<span style="color: red">☒</span>';
                        } else {
                            $counter_true_showup++;
                            echo '<span style="color: green">☑ : ' . date('d F Y', strtotime($x->c_date)) . '</span>';
                        }
                        ?>
                    </td>
                    <td>
                        <?php if (empty($x->sa_date)) {
                            $counter_false_sale++;
                            echo '<span style="color: red">☒</span>';
                        } else {
                            $counter_true_sale++;
                            echo '<span style="color: green">☑ : ' . date('d F Y', strtotime($x->c_date)) . '</span>';
                        }
                        ?>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>


        <table class="table">
            <tr>
                <td colspan="2" rowspan="2">
                    @lang('labels.total')
                </td>
                <td style="color: green;">
                    ☑ : {{$counter_true_confirm}}
                </td>
                <td style="color: green;">
                    ☑ : {{$counter_true_showup}}
                </td>
                <td style="color: green;">
                    ☑ : {{$counter_true_sale}}
                </td>
            </tr>
            <tr>
                <td style="color: red">
                    ☒ : {{$counter_false_confirm}}
                </td>
                <td style="color: red">
                    ☒ : {{$counter_false_showup}}
                </td>
                <td style="color: red">
                    ☒ : {{$counter_false_sale}}
                </td>
            </tr>
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

        $('.dt-buttons button').prepend('<i class="fa fa-print"></i> ');
        $('.dt-buttons button').addClass('btn btn-default');
    </script>
@endsection