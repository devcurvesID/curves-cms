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
        #staff-home {
            padding: 15px;
        }

        .btn-default {
            border-color: deeppink;
            color: deeppink;
            font-size: 16px;
        }

        .btn-default:hover {
            border-color: deeppink;
            color: white;
            background-color: deeppink;
        }

        .dt-button {
            border-color: deeppink;
            color: deeppink;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    @php($the_club = '')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/financial-management')}}" style="left: 25px; top: 5px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">
                        @lang('labels.product_sales_report')
                    </h2>
                </center>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <!-- Filter -->
                {!! Form::open(['method' => 'GET', 'action' => 'ReportController@product_sales_report', 'class' => 'form-horizontal']) !!}
                <div class="form-group">
                    {!! Form::label('club', trans('labels.club').': ', ['class' => 'col-md-2 control-label']) !!}
                    <div class="col-md-3">
                        <select name="club" class="form-control">
                            @php($selected_all = '')
                            @if($club_id == null)
                                @php($selected_all = 'selected')
                            @endif
                            <option value="0" {{$selected_all}}>@lang('labels.all_club')</option>

                            @foreach($clubs as $key => $value)
                                @php($selected = '')
                                @if($club_id == $key)
                                    @php($selected = 'selected')
                                @endif
                                <option value="{{$key}}" {{$selected}}>{{$value}}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    {!! Form::label('fd', trans('labels.from_date').': ', ['class' => 'col-md-2 control-label']) !!}
                    <div class="col-md-3">
                        {!! Form::date('fd', \Carbon\Carbon::parse($from_date), ['class' => 'form-control']) !!}
                    </div>
                    {!! Form::label('td', trans('labels.until'), ['class' => 'col-md-1 control-label']) !!}
                    <div class="col-md-3">
                        {!! Form::date('td', \Carbon\Carbon::parse($thru_date), ['class' => 'form-control']) !!}
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-md-2">
                        {!! Form::submit(trans('labels.apply'), ['class' => 'btn btn-default pull-right']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <hr>

        <div class="row">
            <div class="col-md-12">
                @if($club != null)
                    @php($the_club = $club->club_name)
                @endif
                <h4>@lang('labels.product_sales') {{$the_club}} {{\Carbon\Carbon::parse($from_date)->format('d-m-Y')}} @lang('labels.until') {{\Carbon\Carbon::parse($thru_date)->format('d-m-Y')}}</h4>
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="data-table">
                                <thead>
                                <tr>
                                    <th>@lang('labels.date')</th>
                                    <th>@lang('labels.payment_number')</th>
                                    @if($club_id == null)
                                        <th>@lang('labels.club')</th>
                                    @endif
                                    <th>@lang('labels.product')</th>
                                    <th>@lang('labels.quantity')</th>
                                    <th>@lang('labels.discount')</th>
                                    <th>@lang('labels.total')</th>
                                    <th>@lang('labels.payment')</th>
                                    <th>@lang('labels.buyer')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($product_sales as $product)
                                    <tr>
                                        <td>{{\Carbon\Carbon::parse($product->productpaymentdetail_productpayment->payment_date)->format('Y/m/d')}}</td>

                                        <td>{{$product->productpaymentdetail_productpayment->payment_number}}</td>

                                        @if($club_id == null)
                                            <td>{{$product->productpaymentdetail_productpayment->productpayment_club->club_name}}</td>
                                        @endif
                                        <td>{{$product->productpaymentdetail_product->item_name}}</td>
                                        <td>{{$product->qty}}</td>
                                        <td class="my_rupiah">{{$product->discount_value}}</td>
                                        <td class="my_rupiah">{{($product->qty * $product->price) - $product->discount_value}}</td>
                                        <td>{{$product->productpaymentdetail_productpayment->productpayment_paymentmethod->payment_method}}</td>
                                        <td>
                                            @if(!empty($product->productpaymentdetail_productpayment->productpayment_user))
                                                {{$product->productpaymentdetail_productpayment->productpayment_user->name}}
                                            @else
                                                @lang('labels.guest')
                                            @endif
                                        </td>
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
                </div>
            </div>
        </div>
    </section>
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

            $('#data-table').dataTable({
                'iDisplayLength': 100,
                'lengthMenu': [[10, 50, 100, -1], [10, 50, 100, '@lang('labels.all')']],
                'footerCallback': function (row, data, start, end, display) {
                    var api = this.api(), data;
                    var intVal = function (i) {
                        return parseInt(rupiah_format_destroyer(i));
                    }

                    @if($club_id == null)
                        var x = 3;
                        var y = 4;
                        var z = 5;
                    @else
                        var x = 2;
                        var y = 3;
                        var z = 4;
                    @endif

                    var qty = api
                        .column(x)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                    var discount = api
                        .column(y)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                    var total = api
                        .column(z)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                    // Update footer
                    $( api.column(x).footer() ).html(qty);
                    $( api.column(y).footer() ).html('Rp. ' + rupiah_formater(discount) + ',-');
                    $( api.column(z).footer() ).html('Rp. ' + rupiah_formater(total) + ',-');
                },
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<span class="fa fa-download" title="@lang('labels.export_excel')"></span> Excel',
                        footer: true,
                        title: '@lang('labels.product_sales') {{$the_club}} {{\Carbon\Carbon::parse($from_date)->format('d-m-Y')}} @lang('labels.until') {{\Carbon\Carbon::parse($thru_date)->format('d-m-Y')}}'
                    },
                    {
                        extend: 'print',
                        footer: true,
                        text: '<span class="fa fa-print" title="@lang('labels.print')"></span> Print',
                        title: '@lang('labels.product_sales') {{$the_club}} {{\Carbon\Carbon::parse($from_date)->format('d-m-Y')}} @lang('labels.until') {{\Carbon\Carbon::parse($thru_date)->format('d-m-Y')}}',
                        customize: function ( win ) {
                            $(win.document.body).find( 'table' )
                                .addClass( 'compact' )
                                .css( 'font-size', 'inherit' );

                            $(win.document.body).find( 'h1' )
                                .css( 'font-size', '16px' );
                        }
                    }
                ]
            });

            $('.dt-button').addClass('btn');
            $('.dt-button').addClass('btn-default');

            function init_format() {
                $('.my_rupiah').each(function () {
                    var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                    $(this).text('Rp.' + (x) + ',-');
                });
                $('.rupiah').each(function () {
                    var x = rupiah_formater($(this).val().replace(/\s/g, ''));
                    $(this).val((x));
                });
            }
        });
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>
@endsection
