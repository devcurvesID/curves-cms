@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
{{--DATA TABLE--}}
<link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
<link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
<link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
<link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
<link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

<link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
<style>
    .home-btn-title {
        font-family: 'Alegreya Sans', sans-serif;
    }

    .value-base {
        display: block;
    }

    .percent-base {
        display: none;
    }

    .btn-primary,
    .voucher-list-add a,
    #edit-voucher-btn {
        background-color: #ff1493;
        color: #ffffff;
        border-color: #ff1493;
    }

    #edit-voucher-btn {
        margin: 20px 0;
    }

    .btn-primary:hover,
    .voucher-list-add a:hover,
    #edit-voucher-btn:hover {
        color: #ff1493;
        background-color: transparent;
        border-color: #ff1493;
    }

    div.dataTables_wrapper div.dataTables_length select {
        margin: 0 10px;
    }

    table th {
        color: #ff1493;
    }
</style>
@endsection

@section('content')
@include('cf_lite.includes.flash-message')
@include('cf_lite.includes.error-message')
<section id="staff-home">
    {!! Form::hidden('locker_payment_id', $id) !!}
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/locker-invoice-list')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to"
                title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left"
                    style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">
                    @lang('labels.locker_invoice_detail')
                </h2>
            </center>
            <a class="btn btn-success pull-right" id="print"
                style="margin-top: -110px; font-size: 45px; border-radius: 50%; padding: 20px; margin-right: 100px;"
                title="@lang('labels.print_receipt')"><i class="fa fa-print"></i>
            </a>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <label style="font-size: 14px">@lang('labels.payment_number') :</label>
            <label style="font-size: 14px;margin-right: 70px;">{{
                $invoice->payment_number }}</label>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <label style="font-size: 14px">@lang('labels.member') :</label>
            <label style="font-size: 14px;margin-right: 70px;">{{ $invoice->lockerpayment_memberlocker->memberlocker_user->name }}</label>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <label style="font-size: 14px">@lang('labels.staff') :</label>
            <label style="font-size: 14px;margin-right: 70px;">{{
                $invoice->lockerpayment_user->name }}</label>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <label style="font-size: 14px">@lang('labels.locker') :</label>
            <label style="font-size: 14px;margin-right: 70px;">{{
                $invoice->lockerpayment_memberlocker->memberlocker_lockerinfo->locker_name }}</label>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <label style="font-size: 14px">@lang('labels.duration') :</label>
            <label style="font-size: 14px;margin-right: 70px;">{{
                Carbon\Carbon::parse($invoice->lockerpayment_memberlocker->from_date)->format('d/m/Y') }} - {{
                Carbon\Carbon::parse($invoice->lockerpayment_memberlocker->thru_date)->format('d/m/Y') }}</label>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            <div class="x_panel">
                <div class="x_content">
                    <br />
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>@lang('labels.number')</th>
                                    <th>@lang('labels.product_name')</th>
                                    <th>@lang('labels.price')</th>
                                    <th>@lang('labels.quantity')</th>
                                    <th>@lang('labels.subtotal')</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>1</td>
                                    <td>{{$invoice->lockerpayment_locker->name}}</td>
                                    <td class="my_rupiah">{{$invoice->lockerpayment_locker->price}}</td>
                                    <td>{{$invoice->qty}}</td>
                                    <td class="my_rupiah">{{ $invoice->price }}</td>
                                </tr>
                            </tbody>
                        </table>
                        <div>
                            <label style="font-size: 20px">@lang('labels.total')</label>
                            <label class="pull-right my_rupiah" style="font-size: 20px;margin-right: 70px;">{{ $invoice->price }}</label>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>

    <script>
        $('#data-table').dataTable();
    </script>

    <script>
        $(document).ready(function () {
            init_format();


            $('#print').on('click', function () {
                javascript:jsWebClientPrint.print(
                    'id=' + $('input[name="locker_payment_id"]').val() +
                    '&useDefaultPrinter=undefined&printerName=XP-80'
                );
            });
        });

            function init_format() {
                $('.my_rupiah').each(function () {
                    var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                    $(this).text('Rp.' + (x));
                });
            }
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>

    {!!

    // Register the WebClientPrint script code
    // The $wcpScript was generated by PrintESCPOSController@index

    $wcpScript;

    !!}
@endsection