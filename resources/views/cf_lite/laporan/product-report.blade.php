@extends('cf_lite.layouts.staff-dashboard-template')

@section('title', 'Laporan Produk : ' . $club->club_name)

@section('other-style')

    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">
    
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

        .dataTables_length{
            margin-left: -460px;
        }


        table th{
            color: #ff1493;
        }

        table, th{
            text-align: center;
        }
    </style>
@endsection

@section('content')
    <div class="col-xs-12">
        <left>
            <a href="{{url('cf-lite/laporan')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
        </left>
        <center>
            <h2 class="page-title">@lang('labels.list') @lang('labels.product') : {{$club->club_name}}</h2>
        </center>
    </div>
    <div class="col-xs-12">
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-striped table-hover" style="background: white">
                <thead>
                <tr>
                    <th>@lang('labels.indexing')</th>
                    <th>@lang('labels.product_name')</th>
                    <th>@lang('labels.current_stock')</th>
                    <th>@lang('labels.broken_stock')</th>
                    <th>@lang('labels.total')</th>
                </tr>
                </thead>
                <tbody>
                @php($count = 1)
                @php($current_total = 0)
                @php($broken_item_total = 0)
                @php($total_of_total = 0)
                @foreach($product_list as $product)
                    <tr>
                        <td>{{$count++}}</td>
                        <td>{{$product->item_name}}</td>
                        <td>{{$product->qty}}</td>
                        <td>{{$product->broken_qty}}</td>
                        <td>{{$product->total}}</td>
                    </tr>
                    @php($current_total += $product->qty)
                    @php($broken_item_total += $product->broken_qty)
                    @php($total_of_total += $product->total)
                @endforeach
                </tbody>
                <tr>
                    <td colspan="2">@lang('labels.total')</td>
                    <td>{{$current_total}}</td>
                    <td>{{$broken_item_total}}</td>
                    <td>{{$total_of_total}}</td>
                </tr>
            </table>
        </div>
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

        $('#dataTable').DataTable({
            info: false,
            paging:false,
            dom: 'Bfrtip',
            buttons: [
                'print',
            ]
        });

        $('.dt-buttons button').prepend('<i class="fa fa-print"></i> ');
        $('.dt-buttons button').addClass('btn btn-default');
    </script>
@endsection