@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/payment.css')}}" rel="stylesheet">
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <style>
        body{
            color: black;
        }
        .modal-dialog, .modal-dialog label{
            color: #414042;
        }
        table{
            color: #414042;
        }

        .btn-primary {
            background-color: #ff1493;
            color: #ffffff;
            border-color: #ff1493;
        }

        .btn-primary:hover {
            color: #ff1493;
            background-color: transparent;
            border-color: #ff1493;
        }

        .select2 {
            width: auto !important;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/consignment-order/bill')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.product_credit_detail')</h2>
            </center>
        </div>
    </div>

    @include('cf_lite.includes.error-message')
    @include('cf_lite.includes.flash-message')

    <div class="row">
        <div class="col-md-12">
            <h2>
                @lang('labels.payment_detail')
                @if($cob->status != 'Paid')
                    <a href="javascript:;" data-toggle="modal" data-target="#addPaymentModal" class="btn btn-success pull-right">
                        <span class="fa fa-money"></span> @lang('labels.pay_now')
                    </a>
                @endif
            </h2>
            @if(count($cob->cobill_cobillpayment) > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                        <tr>
                            <th>@lang('labels.indexing')</th>
                            <th>@lang('labels.date')</th>
                            <th>@lang('labels.number')</th>
                            <th>@lang('labels.information')</th>
                            <th>@lang('labels.total')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @php($index = 1)
                        @foreach($cob->cobill_cobillpayment as $cob_payment)
                            <tr>
                                <td>{{$index}}</td>
                                <td>{{ \Carbon\Carbon::parse($cob_payment->payment_date)->format('d-m-Y') }}</td>
                                <td>
                                    <a href="javascript:;" class="credit-payment" id="cp-{{ $cob_payment->id }}" data-toggle="modal" data-target="#editPaymentModal">{{ $cob_payment->payment_number }}</a>
                                </td>
                                <td>{{ $cob_payment->cobillpayment_paymentmethod->payment_method }} {{ ($cob_payment->bank_id != 1) ? $cob_payment->cobillpayment_bank->bank_name : '' }}</td>
                                <td class="my_rupiah">{{ $cob_payment->total }}</td>
                            </tr>
                            @php($index++)
                        @endforeach
                        </tbody>
                        <tfoot>
                        <tr>
                            <th colspan="4" class="text-right">Total</th>
                            <th class="my_rupiah">{{ $cob->amount_paid }}</th>
                        </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <table class="member-detail">
                <tr>
                    <td>@lang('labels.date')</td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::parse($cob->co_bill_date)->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <td>@lang('labels.invoice_number')</td>
                    <td>:</td>
                    <td>{{ $cob->co_bill_number }}</td>
                </tr>
                <tr>
                    <td>@lang('labels.jubilant')</td>
                    <td>:</td>
                    <td>{{ $cob->cobill_jubilant->jubilant_name }}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="member-detail">
                <tr>
                    <td style="font-size: 20px;">@lang('labels.rest_of_bill')</td>
                    <td>:</td>
                    <td class="my_rupiah text-right" style="font-size: 32px;">{{ $cob->rest_of_bill }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>@lang('labels.indexing')</th>
                            <th>@lang('labels.date')</th>
                            <th>@lang('labels.product_payment')</th>
                            <th>@lang('labels.club')</th>
                            <th>@lang('labels.subtotal')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($index = 1)
                        @foreach($cob->cobill_cobilldetail as $cod)
                            <tr>
                                <td>{{$index}}</td>
                                <td>{{ \Carbon\Carbon::parse($cod->cobilldetail_productpayment->payment_date)->format('d-m-Y') }}</td>
                                <td>{{ $cod->cobilldetail_productpayment->payment_number }}</td>
                                <td>{{ $cod->cobilldetail_productpayment->productpayment_club->club_name }}</td>
                                <td class="my_rupiah">{{ $cod->cobilldetail_productpayment->total }}</td>
                            </tr>
                            @php($index++)
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-right">@lang('labels.total')</th>
                            <th class="my_rupiah">{{ $cob->total }}</th>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-right">@lang('labels.paid')</th>
                            <th class="my_rupiah">{{ $cob->amount_paid }}</th>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-right">@lang('labels.rest_of_bill')</th>
                            <th class="my_rupiah">{{ $cob->rest_of_bill }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addPaymentModal" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'POST', 'action' => ['ConsignmentOrderController@store_bill_payment', $cob->id], 'id' => 'credit-payment-form']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.add_credit_payment')</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-9">
                            <table class="member-detail">
                                <tr>
                                    <td style="color: deeppink; margin-left: 10px; font-weight: bold;">@lang('labels.staff')</td>
                                    <td>:</td>
                                    <td>
                                        {!! Form::select('py_staff',$staffs, $cob->staff_id,['class'=>'form-control', 'id'=>'py-staff']) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: deeppink; margin-left: 10px; font-weight: bold;">@lang('labels.payment_date')</td>
                                    <td>:</td>
                                    <td>{!! Form::date('payment_date',Carbon\Carbon::now(),['class'=>'form-control']) !!}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="member-detail">
                                <tr>
                                    <td>@lang('labels.payment_method')</td>
                                    <td>:</td>
                                    <td>
                                        {!! Form::select('payment_method', $payment_methods, null, ['class' => 'form-control']) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang('labels.bank')</td>
                                    <td>:</td>
                                    <td>
                                        {!! Form::select('bank', $banks, null, ['class' => 'form-control select2', 'placeholder' => trans('labels.choose_bank')]) !!}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>@lang('labels.indexing')</th>
                                        <th>@lang('labels.invoice_number')</th>
                                        <th>@lang('labels.information')</th>
                                        <th>@lang('labels.subtotal')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($index = 1)
                                    <tr>
                                        <td>{{$index}}</td>
                                        <td>{{ $cob->co_bill_number }}</td>
                                        <td>
                                            {!! Form::textArea('py_information', null, ['class' => 'form-control', 'rows' => '3']) !!}
                                        </td>
                                        <td>
                                            {!! Form::text('py_subtotal', null, ['class' => 'form-control rupiah','style'=>'max-width: 250px;']) !!}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="button" id="payment-process" class="btn btn-success">@lang('labels.save')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editPaymentModal" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'POST', 'action' => 'ConsignmentOrderController@update_bill_payment', 'id' => 'update-credit-payment-form']) !!}
                {!! Form::hidden('cp_id', null) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.update_credit_payment') <span id="cp-number"></span></h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-9">
                            <table class="member-detail">
                                <tr>
                                    <td style="color: deeppink; margin-left: 10px; font-weight: bold;">@lang('labels.staff')</td>
                                    <td>:</td>
                                    <td>
                                        {!! Form::select('edit_py_staff',$staffs, null,['class'=>'form-control', 'id'=>'edit-py-staff']) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td style="color: deeppink; margin-left: 10px; font-weight: bold;">@lang('labels.payment_date')</td>
                                    <td>:</td>
                                    <td>{!! Form::date('edit_payment_date',null,['class'=>'form-control']) !!}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <div class="col-md-12">
                            <table class="member-detail">
                                <tr>
                                    <td>@lang('labels.payment_method')</td>
                                    <td>:</td>
                                    <td>
                                        {!! Form::select('edit_payment_method', $payment_methods, null, ['class' => 'form-control']) !!}
                                    </td>
                                </tr>
                                <tr>
                                    <td>@lang('labels.bank')</td>
                                    <td>:</td>
                                    <td>
                                        {!! Form::select('edit_bank', $banks, null, ['class' => 'form-control select2', 'placeholder' => trans('labels.choose_bank')]) !!}
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                    <tr>
                                        <th>@lang('labels.indexing')</th>
                                        <th>@lang('labels.invoice_number')</th>
                                        <th>@lang('labels.information')</th>
                                        <th>@lang('labels.subtotal')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($index = 1)
                                    <tr>
                                        <td>{{$index}}</td>
                                        <td>{{ $cob->co_bill_number }}</td>
                                        <td>
                                            {!! Form::textArea('edit_py_information', null, ['class' => 'form-control', 'rows' => '3']) !!}
                                        </td>
                                        <td>
                                            {!! Form::text('edit_py_subtotal', null, ['class' => 'form-control rupiah','style'=>'max-width: 250px;']) !!}
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="button" id="update-payment-process" class="btn btn-success">@lang('labels.save')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script>
        $(document).ready(function () {
            // $('.select2').select2();

            $('.rupiah').each(function () {
                $(this).wrap('<div class="input-group" style="max-width: 250px;"></div>')
                $(this).before('<span class="input-group-addon">Rp.</span>');
                $(this).after('<span class="input-group-addon">,-</span>');
            });

            init_format();

//            $('input[name="subtotal"]').on('keyup', function () {
//                if ($(this).val() == "") {
//                    var total_pay = 0;
//                } else {
//                    var total_pay = parseInt(rupiah_format_destroyer($('input[name="subtotal"]').val()));
//                }
//                $('input[name="subtotal"]').val(rupiah_formater(total_pay));
//            });

            $('input[name="py_subtotal"]').on('keyup', function () {
                if ($(this).val() == "") {
                    var total_pay = 0;
                } else {
                    var total_pay = parseInt(rupiah_format_destroyer($('input[name="py_subtotal"]').val()));
                }
                $('input[name="py_subtotal"]').val(rupiah_formater(total_pay));
            });

            $('input[name="edit_py_subtotal"]').on('keyup', function () {
                if ($(this).val() == "") {
                    var total_pay = 0;
                } else {
                    var total_pay = parseInt(rupiah_format_destroyer($('input[name="edit_py_subtotal"]').val()));
                }
                $('input[name="edit_py_subtotal"]').val(rupiah_formater(total_pay));
            });
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

    <script src={{asset("bootbox/bootbox.min.js")}}></script>
    <script>
        $(document).ready(function () {
            $(document).on('click', '#payment-process', function () {
                if (rupiah_format_destroyer($('input[name="py_subtotal"]').val()) == 0) {
                    bootbox.confirm({
                        message: "@lang('messages.are_you_sure')",
                        buttons: {
                            confirm: {
                                label: '@lang('labels.yes')',
                                className: 'btn-success'
                            },
                            cancel: {
                                label: '@lang('labels.no')',
                                className: 'btn-danger'
                            }
                        },
                        callback: function (result) {
                            if (result == false) {
                            } else {

                            }
                        }
                    });
                } else {
                    var payment_gate = true;
                    if ($('select[name="payment_method"]').val() != '1') {
                        if ($('select[name="bank"]').val() == '') {
                            payment_gate = false;
                        }
                    }

                    if (payment_gate == true) {
                        $('input[name="py_subtotal"]').val(rupiah_format_destroyer($('input[name="py_subtotal"]').val()));
                        $('#credit-payment-form').submit();
                    } else {
                        alert('@lang('messages.bank_not_chosen')');
                    }
                }
            });

            $('.credit-payment').on('click', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    cp_id: $(this).attr('id').split('-').pop()
                };

                var type = 'GET';
                var my_url = "{{url('cf-lite')}}/consignment-order/bill/product-payment-ajax";

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    success: function (data) {
                        console.log(data);
                        $('input[name="cp_id"]').val(data.id);
                        $('select[name="edit_py_staff"]').val(data.staff_id);
                        $('input[name="edit_payment_date"]').val(data.payment_date);
                        $('select[name="edit_payment_method"]').val(data.payment_type_id);

                        if (data.bank_id != '1') {
                            $('select[name="edit_bank"]').val(data.bank_id);
                        }

                        $('textarea[name="edit_py_information"]').val(data.information);
                        $('input[name="edit_py_subtotal"]').val(rupiah_formater(data.total));
                        $('#cp-number').text(data.payment_number);
                    },
                    error: function (data) {
                        console.log('Error : ', data);
                    }
                });
            });

            $(document).on('click', '#update-payment-process', function () {
                if (rupiah_format_destroyer($('input[name="edit_py_subtotal"]').val()) == 0) {
                    bootbox.confirm({
                        message: "@lang('messages.are_you_sure')",
                        buttons: {
                            confirm: {
                                label: '@lang('labels.yes')',
                                className: 'btn-success'
                            },
                            cancel: {
                                label: '@lang('labels.no')',
                                className: 'btn-danger'
                            }
                        },
                        callback: function (result) {
                            if (result == false) {
                            } else {

                            }
                        }
                    });
                } else {
                    var payment_gate = true;
                    if ($('select[name="edit_payment_method"]').val() != '1') {
                        if ($('select[name="edit_bank"]').val() == '') {
                            payment_gate = false;
                        }
                    }

                    if (payment_gate == true) {
                        $('input[name="edit_py_subtotal"]').val(rupiah_format_destroyer($('input[name="edit_py_subtotal"]').val()));
                        $('#update-credit-payment-form').submit();
                    } else {
                        alert('@lang('messages.bank_not_chosen')');
                    }
                }
            });
        });
    </script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script type="text/javascript">
        $('select[name="jubilant"]').select2();
        $('select[name="staff"]').select2();
        $('select[name="bank"]').select2();
    </script>

@endsection