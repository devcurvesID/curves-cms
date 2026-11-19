@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">

    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <style>
        .btn-primary,
        .voucher-list-add a,
        #edit-voucher-btn {
            background-color: #ff1493;
            color: #ffffff;
            border-color: #ff1493;
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

        tfoot tr td {
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
    <section id="staff-home">
        @include('cf_lite.includes.error-message')
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/product-request/product-request-main')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">
                        @lang('labels.consignment_order_bill')
                    </h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                {!! Form::open(['method' => 'GET']) !!}
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-2">
                            {!! Form::date('from_date', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-2">
                            {!! Form::date('thru_date', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-3">
                            {!! Form::select('jubilant', $jubilants, null, ['class' => 'form-control select2', 'placeholder' => trans('labels.choose_jubilant')]) !!}
                        </div>
                        <div class="col-md-1">
                            {!! Form::submit(trans('labels.apply_filter'), ['class' => 'btn btn-primary']) !!}
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <a href="{{ url('cf-lite/consignment-order/bill/generate') }}" class="btn btn-primary pull-right"><span class="fa fa-plus"></span> @lang('labels.create_bill')</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="data-table">
                                <thead>
                                <tr>
                                    <th>@lang('labels.indexing')</th>
                                    <th>@lang('labels.date')</th>
                                    <th>@lang('labels.due_date')</th>
                                    <th>@lang('labels.invoice_number')</th>
                                    <th>@lang('labels.subtotal')</th>
                                    <th>@lang('labels.status')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php($indexing = 1)
                                @foreach($co_bills as $co_bill)
                                    <tr>
                                        <td>{{$indexing}}</td>
                                        <td>{{ \Carbon\Carbon::parse($co_bill->co_bill_date)->format('Y-m-d') }}</td>
                                        <td>{{ \Carbon\Carbon::parse($co_bill->co_bill_due_date)->format('Y-m-d') }}</td>
                                        <td><a href="{{ url('cf-lite/consignment-order/bill/detail/'.$co_bill->id) }}">{{ $co_bill->co_bill_number }}</a></td>
                                        <td class="my_rupiah">{{ $co_bill->total }}</td>
                                        <td>{{ $co_bill->status }}</td>
                                    </tr>
                                    @php($indexing++)
                                @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-uppercase">@lang('labels.total')</td>
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

    <script type="text/javascript">
        function numberOnlyInput(event){
            var charCode = (event.which) ? event.which : event.keyCode;
            if(charCode > 31 && (charCode < 48 || charCode > 57)){
                return false;
            }
            return true;
        }
    </script>

    <script>
        $(document).ready(function () {
            init_format();

            $('#data-table').dataTable({
                'iDisplayLength': -1,
                'lengthMenu': [[10, 50, 100, -1], [10, 50, 100, '@lang('labels.all')']],
                'footerCallback': function (row, data, start, end, display) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
//                    var intVal = function ( i ) {
//                        return typeof i === 'string' ?
//                            i.replace(/[\Rp.,]/g, '')*1 :
//                            typeof i === 'number' ?
//                                i : 0;
//                    };
                    var intVal = function (i) {
                        return parseInt(rupiah_format_destroyer(i));
                    }

                    var outdatedDebt = api
                        .column(4)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                    // Update footer
                    $( api.column(4).footer() ).html('Rp. ' + rupiah_formater(outdatedDebt) + ',-');
                },
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<span class="fa fa-download" title="@lang('labels.export_excel')"></span> Excel',
                        footer: true,
                        title: '@lang('labels.consignment_order_bill')'
                    }
                ]
            });
            co_bills.bindResetListeners();
        });

        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp.' + (x));
            });
        }
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>

    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('input[name="search_option"]').on('click', function () {
                $('input[name="s"]').val('');
            })

            $('select[name="jubilant"]').select2();
        });
    </script>
@endsection