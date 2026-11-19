@extends('cf_lite.layouts.staff-dashboard-template')

@section('title', 'Laporan Penjualan Loker : ' . $club_name)

@section('other-style')

    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">
    <style>
        .dt-buttons {
            float: left;
        }

        #dataTable_filter {
            float: right;
        }

        .dataTables_length{
            margin-left: -460px;
        }

        table th{
            color: #ff1493;
        }

        table, th{
            text-align: center;
        }

        .dt-button{
            border-color:deeppink;
            color: deeppink;
        }
    </style>
@endsection

@section('content')
    <div class="col-xs-12">
        <left>
            <a href="{{url('cf-lite/financial-management')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
        </left>
        <center>
            <h2 class="page-title">@lang('labels.locker_sales_report') : {{$club_name}}</h2>
        </center>
    </div>
    <div class="col-xs-12">
        {!! Form::open(['method' => 'GET']) !!}
        <div class="row">
            <div class="col-xs-12">
                <div class="col-xs-2">
                    Club :
                </div>
                <div class="col-xs-6">
                    <select class="form-control" name="club_id">
                        <option value="0">@lang('labels.all_club')</option>
                        @foreach($clubs as $club)
                            <option value="{{$club->id}}" {{$club_id == $club->id ? 'selected' : '' }}>{{$club->club_name}}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-xs-12">
                <div class="col-xs-2">
                    Filter :
                </div>
                <div class="col-xs-3">
                    {!! Form::date('from_date', $from_date , ['class' => 'form-control filter-date','placeholder'=>@trans('labels.from_date'), 'id'=>'filt-dari']) !!} <br/>
                </div>
                <div class="col-xs-3">
                    {!! Form::date('thru_date', $thru_date , ['class' => 'form-control filter-date','placeholder'=>@trans('labels.from_date'), 'id'=>'filt-sampai']) !!} <br/>
                </div>
                <div class="col-xs-2">
                    <button type="submit" class="btn btn-primary" id="save-btn"><i class="fa fa-search"></i> @lang('labels.search')</button>
                    </a>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
        <hr/>
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-striped table-hover" style="background: white">
                <thead>
                <tr>
                    <th>@lang('labels.date')</th>
                    <th>@lang('labels.name') @lang('labels.locker')</th>
                    <th>@lang('labels.club_name')</th>
                    <th>@lang('labels.member_name')</th>
                    <th>@lang('labels.duration')</th>
                    <th>@lang('labels.amount')</th>
                    <th>@lang('labels.payment_type')</th>
                </tr>
                </thead>
                <tbody>
                @foreach($lockers as $locker)
                    <tr>
                        <td>{{Carbon\Carbon::parse($locker->payment_date)->format('d-m-Y')}}</td>
                        <td>{{$locker->lockerpayment_memberlocker->memberlocker_lockerinfo->locker_name}}</td>
                        <td>{{$locker->lockerpayment_memberlocker->memberlocker_lockerinfo->lockerinfo_club->club_name}}</td>
                        <td>{{$locker->lockerpayment_memberlocker->memberlocker_user->name}}</td>
                        <td>{{$locker->day_duration}} @lang('labels.days')</td>
                        <td class="my_rupiah">{{$locker->price}}</td>
                        <td>{{$locker->lockerpayment_paymentmethod->payment_method}}</td>
                    </tr>
                @endforeach
                </tbody>
                <tfoot>
                <tr>
                    <td class="text-uppercase">@lang('labels.total')</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>
@endsection

@section('other-script')

    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>

    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.print.min.js"></script>

    <script>
        $(document).ready(function () {
            init_format();

            $('#dataTable').DataTable({
                info: false,
                lengthMenu: [[10, 50, 100, -1], [10, 50, 100, '@lang('labels.all')']],
                footerCallback: function (row, data, start, end, display) {
                    var api = this.api(), data;
                    var intVal = function (i) {
                        return parseInt(rupiah_format_destroyer(i));
                    }

                    var qty = api
                        .column(5)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                    // Update footer
                    $( api.column(5).footer() ).html('Rp. ' + rupiah_formater(qty) + ',-');
                },
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<span class="fa fa-download" title="@lang('labels.export_to_excel')"></span> Excel',
                        footer: true,
                        title: '@lang('labels.locker_sales_report') {{$club_name}} {{\Carbon\Carbon::parse($from_date)->format('d-m-Y')}} @lang('labels.until') {{\Carbon\Carbon::parse($thru_date)->format('d-m-Y')}}'
                    },
                    {
                        extend:'print',
                        footer: true,
                        text: '<span class="fa fa-print" title="@lang('labels.print')"></span>Print',
                        title: '@lang('labels.locker_sales_report') {{$club_name}} {{\Carbon\Carbon::parse($from_date)->format('d-m-Y')}} @lang('labels.until') {{\Carbon\Carbon::parse($thru_date)->format('d-m-Y')}}',
                        customize: function(win){
                            $(win.document.body).find('table')
                                .addClass('compact')
                                .css('font-size','inherit');
                        }
                    }
                ]
            });

            $('.dt-buttons button').addClass('btn btn-default');
        });

        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp.' + (x));
            });
            $('.rupiah').each(function () {
                var x = rupiah_formater($(this).val().replace(/\s/g, ''));
                $(this).val((x));
            });
        }
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>
@endsection