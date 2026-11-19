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
    .select2 {
        width: 100% !important;
    }
</style>
@endsection

@section('content')
@include('cf_lite.includes.flash-message')
@include('cf_lite.includes.error-message')
<div class="row">
    <div class="col-sm-12">
        <a href="{{url('cf-lite/product-inventory/'. $club_id)}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span
                class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
        <center>
            <h2 class="page-title">@lang('labels.product_inventory')</h2>
        </center>
    </div>
    <div class="col-md-12">
        <div class="x_panel">
            <div class="x_content">
                <br />
                <div class="table-responsive" style="background-color: #fff;">
                    <table class="table table-hover table-bordered" id="data-table">
                        <thead>
                            <tr>
                                <th>@lang('labels.indexing')</th>
                                <th>@lang('labels.date')</th>
                                <th>@lang('labels.before')</th>
                                <th>@lang('labels.after')</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($logs as $log)
                                <tr>
                                    <td></td>
                                    <td>{{ Carbon\Carbon::parse($log->transaction_date)->format('d-m-Y') }}</td>
                                    <td>{{ $log->prev_qty }}</td>
                                    <td>{{ $log->next_qty }}</td>
                                </tr>
                            @empty
                            @endforelse
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
        $('.select2').select2();
        var t = $('#data-table').DataTable( {
                        "columnDefs": [ {
                        "searchable": false,
                        "orderable": false,
                        "targets": 0
                    } ],
                    "order": [[ 1, 'asc' ]]
                } );
        
        t.on( 'order.dt search.dt', function () {
            t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
                cell.innerHTML = i+1;
            } );
        } ).draw();
    });
</script>
@endsection