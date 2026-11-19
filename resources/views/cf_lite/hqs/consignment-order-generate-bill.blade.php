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
                <a href="{{url('cf-lite/consignment-order/bill')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">
                        @lang('labels.generate_consignment_order_bill')
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
                {{--Generate Bill--}}
                @if(!empty($_GET['jubilant']))
                    <a href="javascript:;" class="btn btn-primary pull-right" data-toggle="modal" data-target="#createModal"><span class="fa fa-plus"></span> @lang('labels.generate_bill')</a>
                @else
                    <a href="javascript:;" class="btn btn-primary pull-right" id="show-alert"><span class="fa fa-plus"></span> @lang('labels.generate_bill')</a>
                @endif
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
                                    <th>@lang('labels.invoice_number')</th>
                                    <th>{{ !empty($_GET['jubilant']) ? trans('labels.club') : trans('labels.jubilant') }}</th>
                                    <th>@lang('labels.subtotal')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php($indexing = 1)
                                @foreach($co_bills as $co_bill)
                                    <tr>
                                        <td>{{$indexing}}</td>
                                        <td>{{ \Carbon\Carbon::parse($co_bill->payment_date)->format('Y-m-d') }}</td>
                                        <td>{{ $co_bill->payment_number }}</td>
                                        <td>{{ !empty($_GET['jubilant']) ? $co_bill->productpayment_club->club_name : $co_bill->productpayment_club->club_jubilant->jubilant_name }}</td>
                                        <td class="my_rupiah">{{ $co_bill->total }}</td>
                                    </tr>
                                    @php($indexing++)
                                @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-uppercase">@lang('labels.total')</td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{--Modal--}}
        @if(!empty($_GET['jubilant']))
            <div class="modal fade" id="createModal" role="dialog">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        {!! Form::open(['method' => 'POST', 'action' => 'ConsignmentOrderController@generate_bill', 'id' => 'generate-bill-form']) !!}
                        {!! Form::hidden('f_date', !empty($_GET['from_date']) ? $_GET['from_date'] : null) !!}
                        {!! Form::hidden('t_date', !empty($_GET['thru_date']) ? $_GET['thru_date'] : null) !!}
                        {!! Form::hidden('jbl', !empty($_GET['jubilant']) ? $_GET['jubilant'] : null) !!}
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">@lang('labels.generate_bill')</h4>
                        </div>
                        <div class="modal-body">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    {!! Form::label('co_bill_date', trans('labels.date').': ', ['class' => 'control-label col-md-4']) !!}
                                    <div class="col-md-8">
                                        {!! Form::date('co_bill_date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('co_bill_due_date', trans('labels.due_date').': ', ['class' => 'control-label col-md-4']) !!}
                                    <div class="col-md-8">
                                        {!! Form::date('co_bill_due_date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('total', trans('labels.total').': ', ['class' => 'control-label col-md-4']) !!}
                                    <div class="col-md-8">
                                        <div class="input-group">
                                            <span class="input-group-addon">Rp.</span>
                                            {!! Form::text('total', null, ['class' => 'form-control my_rupiah', 'readonly']) !!}
                                            <span class="input-group-addon">,-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default" data-dismiss="modal">@lang('labels.close')</button>
                            <button type="button" id="generate-btn" class="btn btn-primary">@lang('labels.generate')</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        @endif
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
                    $('input[name="total"]').val(rupiah_formater(outdatedDebt));
                },
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
            $('select[name="jubilant"]').select2();

            $('#generate-btn').on('click', function () {
                $('input[name="total"]').val(rupiah_format_destroyer($('input[name="total"]').val()));
                $('#generate-bill-form').submit();
            });

            $('#show-alert').on('click', function () {
                alert('@lang('messages.choose_jubilant_first_then_set_filter')!!');
            });
        });
    </script>
@endsection