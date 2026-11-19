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
        .select2 {
             width: 100% !important;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/document')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">
                        @lang('labels.product_invoice')
                    </h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                {!! Form::open(['method' => 'GET', 'action' => ['InvoiceController@invoice_list_filter']]) !!}
                <div class="form-group">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="input-group">
                                {!! Form::text('payment_number', null, ['class' => 'form-control', 'placeholder' => trans('labels.search_product_invoice')]) !!}
                                <span class="input-group-addon"><span class="fa fa-search"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        {!! Form::submit(trans('labels.apply_filter'), ['class' => 'btn btn-primary']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="data-table">
                                <thead>
                                <tr>
                                    <th>@lang('labels.number')</th>
                                    <th>@lang('labels.payment_number')</th>
                                    <th>@lang('labels.payment_date')</th>
                                    <th>@lang('labels.staff')</th>
                                    <th>@lang('labels.total')</th>
                                    <th>@lang('labels.action')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php($indexing = 1)
                                @foreach($product_payments as $payment)
                                    <tr>
                                        <td>{{$indexing}}</td>
                                        <td><a href="{{url('cf-lite/invoice-detail-list',$payment->id)}}">{{$payment->payment_number}}</a></td>
                                        <td>{{Carbon\Carbon::parse($payment->payment_date)->format('d-m-Y')}}</td>
                                        <td>{{$payment->productpayment_cashier->name}}</td>
                                        <td class="my_rupiah">{{ $payment->total - $payment->invoice_discount }}</td>
                                        <td style="text-align: center;">
                                            <a class="btn btn-info edit-btn" id="edit-{{$payment->id}}" data-toggle="modal" data-target="#edit">
                                                <span class="btn-text fa fa-edit"></span>
                                            </a>
                                        </td>
                                    </tr>
                                @php($indexing++)
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="edit" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'PATCH', 'action' => 'InvoiceController@update_invoice', 'id' => 'form-edit']) !!}
                {!! Form::hidden('payment_id', null) !!}
                {!! Form::hidden('total', null) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">@lang('labels.edit_invoice_product')</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12 form-group">
                            {!! Form::label('buyer', trans('labels.buyer').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-4">
                                {{-- {!! Form::label('buyer', trans('labels.buyer'), ['class' => 'control-label', 'id' => 'buyer']) !!} --}}
                                {!! Form::select('buyer',$users, null, ['class' => 'form-control select2','id' => 'buyer','placeholder' => trans('labels.choose')]) !!}
                            </div>
                        </div>
                        <div class="col-md-12 form-group">
                            {!! Form::label('invoice_number', trans('labels.invoice_number').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-4">
                                {!! Form::label('invoice_number', trans('labels.invoice_number'), ['class' => 'control-label', 'id' => 'invoice-number']) !!}
                            </div>
                        </div>
                        <div class="col-md-12 form-group">
                            {!! Form::label('date', trans('labels.date').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-4">
                                {!! Form::date('edit_invoice_date',\Carbon\Carbon::today(), ['class' => 'form-control','id' => 'edit-invoice-date']) !!}
                            </div>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <thead>
                                <tr>
                                    <th>@lang('labels.product_name')</th>
                                    <th>@lang('labels.price')</th>
                                    <th>@lang('labels.qty')</th>
                                    <th>@lang('labels.subtotal')</th>
                                    <th>@lang('labels.discount')</th>
                                </tr>
                            </thead>
                            <tbody id="list-detail">

                            </tbody>
                            <tfoot id="foot">

                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="button" id="btn-submit" class="btn btn-success">@lang('labels.update')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>

    <script>
        $(document).ready(function () {
            $('#data-table').dataTable();
            // $('.select2').select2();
            init_format();
        });

        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp.' + (x));
            });
        }
    </script>
    <script>
        var url = "{{url('cf-lite')}}/";

        $('#data-table').on('click', '.edit-btn', function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var formData = {
                id: $(this).attr('id').split('-').pop()
            };

            var type = 'POST';
            var my_url = url + 'invoice-list/detail-ajax';

            $.ajax({
                type: type,
                url: my_url,
                data: formData,
                success: function (data) {
                    $('input[name="payment_id"]').val(data.product_payment.id);
                    // $('#buyer').html((data.product_payment.productpayment_user != null) ? data.product_payment.productpayment_user.name : 'Guest');
                    $('#buyer').val((data.product_payment.member_id != null) ? data.product_payment.member_id : '').trigger('change');
                    $('#invoice-number').html(data.product_payment.payment_number);

                    var tzoffset = (new Date()).getTimezoneOffset() * 60000;
                    var localISOTime = (new Date(new Date(data.product_payment.payment_date) - tzoffset)).toISOString().slice(0, -1);

                    $('#edit-invoice-date').val(localISOTime.substring(0,10));

                    var tbody = '';
                    $.each(data.product_payment.productpayment_productpaymentdetail, function (index, value) {
                        tbody +=
                            `<tr>
                                <td>
                                    <select class="form-control product select2" id="product-${value.id}">`; //value.id ini adalah id product payment detail
                        $.each(data.product_inventory, function (index, inventory) {
                            if(inventory.selling_price <= value.price) {
                                if(inventory.product_id == value.product_id) {
                                    tbody += `<option value="${inventory.product_id}" selected>${inventory.productinv_product.item_name}</option>`;
                                } else {
                                    tbody += `<option value="${inventory.product_id}">${inventory.productinv_product.item_name}</option>`;
                                }
                            }
                        });
                        tbody +=
                                    `</select>
                                </td>
                                <td>Rp. ${rupiah_formater(value.price)}</td>
                                <td>${value.qty}</td>
                                <td>Rp. ${rupiah_formater(value.qty * value.price)}</td>
                                <td>Rp. ${rupiah_formater(value.discount_value)}</td>
                            </tr>`;
                    });

                    tbody +=
                        `   <tr>
                                <td colspan="3">@lang('labels.subtotal')</td>
                                <td colspan="2" id="total">Rp. ${rupiah_formater(data.product_payment.total)}</td>
                            </tr>
                            <tr>
                                <td colspan="3">@lang('labels.total_discount_item')</td>
                                <td colspan="2">Rp. ${rupiah_formater(data.product_payment.invoice_discount)}</td>
                            </tr>
                            <tr>
                                <td colspan="3">@lang('labels.total')</td>
                                <td colspan="2">Rp. ${rupiah_formater(data.product_payment.total - data.product_payment.invoice_discount)}</td>
                            </tr>
                            <tr>
                                <td colspan="3">@lang('labels.payment_method')</td>
                                <td colspan="2">
                                    <select id="payment-method" name="payment_method" class="form-control select2">
                                    @foreach($payment_methods as $item)
                                        <option value="{{ $item->id }}">{{ $item->payment_method }}</option>
                                    @endforeach
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">@lang('labels.bank')</td>
                                <td colspan="2">
                                    {!! Form::select('bank',$banks,null,['class' => 'form-control select2','id' => 'bank']) !!}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">@lang('labels.pay')</td>
                                <td colspan="2"><input name="pay" id="pay" type="text" class="form-control" onkeyup="rupiahku()" value="${rupiah_formater(data.product_payment.pay)}"></td>
                            </tr>
                            `;

                    $('#list-detail').html(tbody);
                    $('.select2').select2({
                        dropdownParent: $("#edit")
                    });

                    $('select#payment-method').val(data.product_payment.payment_method_id).trigger('change');
                    $('select#bank').val(data.product_payment.bank_id).trigger('change');
                },
                error: function (data) { console.log('Error : ', data); }
            });
        });

        $('#edit').on('scroll', function (event) {
            $(this).find(".select2").each(function () {
                $(this).select2({ dropdownParent: $(this).parent() });
            });
        });

        $('#edit').on('change', '.product', function () {
            var invoice_id = $(this).attr('id').split('-').pop();
            var newproductid = $('#product-'+invoice_id).val();
            console.log(newproductid);

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            var formData = {
                invoice_id: invoice_id,
                newproduct_id: newproductid
            };

            var type = 'POST';
            var my_url = url + 'invoice-list/store-detail-ajax';

            $.ajax({
                type: type,
                url: my_url,
                data: formData,
                success: function (data) {},
                error: function (data) { console.log('Error : ', data); }
            });
        });

        $('#btn-submit').on('click', function () {
            if ($('#payment-method').val() != 1 && $('#bank').val() == "") {
                alert('@lang('messages.please_choose_bank')');
            } else if($('#method').val() == 1 && $('#bank').val() != ""){
                alert('@lang('messages.please_check_payment_method')');
            } else {
                if (parseInt(rupiah_format_destroyer($('#pay').val())) - parseInt(rupiah_format_destroyer($('#total').html()).trim()) >= 0) {
                    $('#pay').val(rupiah_format_destroyer($('#pay').val()));
                    $('#form-edit').submit();
                } else {
                    if (confirm("Jumlah yang dibayar kurang dari total, apakah anda yakin ingin melanjutkan?") == true) {
                        $('#pay').val(rupiah_format_destroyer($('#pay').val()));
                        $('#form-edit').submit();
                    } else {
                        alert('@lang("messages.check_payment")');
                    }
                }
            }
        });

        function rupiahku() {
            $('#pay').val(rupiah_formater($('#pay').val()))
        }
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>
@endsection
