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

        #edit-voucher-btn{
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

        table th{
            color: #ff1493;
        }

    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/invoice-list')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">
                        @lang('labels.product_invoice_detail')
                    </h2>
                </center>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-10 col-md-offset-1 voucher-list-add">
                <a class="btn btn-default pull-right" href="{{url('cf-lite/refund-product-list',$id)}}" title="@lang('labels.product_refund')">@lang('labels.product_refund')</a>
            </div>
            <div class="col-md-10 col-md-offset-1">
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" >
                                <thead>
                                <tr>
                                    <th>@lang('labels.number')</th>
                                    <th>@lang('labels.product_name')</th>
                                    <th>@lang('labels.price')</th>
                                    <th>@lang('labels.quantity')</th>
                                    <th>@lang('labels.discount')</th>
                                    <th>@lang('labels.subtotal')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php($indexing = 1)
                                @php($total = 0)
                                @foreach($invoice_details as $invoice_detail)
                                    <tr>
                                        <td>{{$indexing}}</td>
                                        <td>{{$invoice_detail->productpaymentdetail_product->item_name}}</td>
                                        <td class="my_rupiah">{{$invoice_detail->price}}</td>
                                        <td>{{$invoice_detail->qty}}</td>
                                        <td class="my_rupiah">{{$invoice_detail->discount_value}}</td>
                                        <td class="my_rupiah">{{($invoice_detail->price * $invoice_detail->qty) - $invoice_detail->discount_value}}</td>
                                        @php($total += ($invoice_detail->price * $invoice_detail->qty) - $invoice_detail->discount_value)
                                    </tr>
                                @php($indexing++)
                                @endforeach
                                </tbody>
                            </table>
                            <div>
                                <label style="font-size: 20px">Total</label>
                                <label class="pull-right my_rupiah" style="font-size: 20px;margin-right: 70px;">{{$total}}</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="x_panel">
                    @lang('labels.refund_history') :
                    <table class="table table-hover table-bordered" >
                    <thead>
                        <tr>
                            <th>@lang('labels.refund_number')</th>
                            <th>@lang('labels.refund_date')</th>
                            <th>@lang('labels.refund_items')</th>
                            <th>@lang('labels.refund_amount')</th>
                            <th>@lang('labels.refund_refund_type')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($product_refunds as $prs)
                        <tr>
                            <td>{{$prs->refund_number}}</td>
                            <td>{{$prs->refund_date}}</td>
                            <td>
                                @foreach($prs->productrefund_productrefunddetail as $detail)
                                    {{$detail->refund_quantity}}x {{$detail->productrefunddetail_productpaymentdetail->productpaymentdetail_product->item_name}}, 
                                @endforeach
                            </td>
                            <td class="my_rupiah">{{$prs->refund_amount}}</td>
                            <td>{{$prs->refund_type}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                    </table>
                </div>
                <div class="x_panel">
                    @lang('labels.voucher_code') :
                    <table class="table table-hover table-bordered" >
                    <thead>
                        <tr>
                            <th>@lang('labels.voucher_code')</th>
                            <th>@lang('labels.amount')</th>
                            <th>@lang('labels.status')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($product_refunds as $prs)
                        @if(!empty($prs->productrefund_voucher))
                        <tr>
                            <td>{{$prs->productrefund_voucher->identity_number}}</td>
                            <td class="my_rupiah">{{$prs->productrefund_voucher->value}}</td>
                            <td>{{$prs->productrefund_voucher->status}}</td>
                        </tr>
                        @endif
                    @endforeach
                    </tbody>
                    </table>
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
        });

        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp.' + (x));
            });
        }
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>
@endsection