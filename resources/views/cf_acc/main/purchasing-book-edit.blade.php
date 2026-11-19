@extends('cf_acc.layouts.acc-dashboard-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">

    <style>
        .detail td{
            padding: 10px;
        }

        table{
            color: #414042;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-md-12">
            <a href="{{url('cf-acc/purchasing-book')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.edit_purchasing_book')</h2>
            </center>
        </div>
        <div class="row">
            <div class="col-md-12">
                {!! Form::open(['method' => 'POST', 'action' => ['PurchasingBookController@update_purchasing_book',$purchase_book->id], 'id' => 'store-form']) !!}
                <table class="detail">
                    <tr>
                        <td>{!! Form::label('purchase_date', trans('labels.purchase_date'), ['class' => 'control-label']) !!}</td>
                        <td>:</td>
                        <td>
                            {!! Form::date('purchase_date', \Carbon\Carbon::parse($purchase_book->purchasing_date), ['class' => 'form-control']) !!}
                        </td>
                    </tr>
                    <tr>
                        <td>{!! Form::label('invoice_number', trans('labels.invoice_number'), ['class' => 'control-label']) !!}</td>
                        <td>:</td>
                        <td>{!! Form::label('invoice_number', $purchase_book->invoice_number, ['class' => 'control-label', 'style' => 'font-weight: bold;']) !!}</td>
                    </tr>
                    <tr>
                        <td>
                            {!! Form::label('purchasing_info', trans('labels.purchasing_info'), ['class' => 'control-label']) !!}
                            <p class="help-block" style="color: red">*@lang('labels.required')</p>
                        </td>
                        <td>:</td>
                        <td>
                            <textarea name="purchasing_info" class="form-control" id="transaction-info" style="width: 300px; height: 130px; resize:none;">{{ $purchase_book->purchasing_info }}</textarea>
                        </td>
                    </tr>
                    <tr>
                        <td>{!! Form::label('amount_due', trans('labels.amount_due'), ['class' => 'control-label']) !!}</td>
                        <td>:</td>
                        <td>
                            <div class="input-group">
                                <span class="input-group-addon">Rp.</span>
                                {!! Form::text('amount_due', $purchase_book->amount_due, ['class' => 'form-control rupiah', 'id' => 'amount-due', 'onkeypress' => 'return onlyNumber(event)']) !!}
                                <span class="input-group-addon">,-</span>
                            </div>
                        </td>
                        <td>
                            <button type="btn" name="submit-btn-form" class="btn btn-primary" id="submit-btn-form">@lang('labels.save')</button>
                        </td>
                    </tr>
                </table>
                {!! Form::close() !!}
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive" style="background-color: #fff;">
                            <table class="table table-hover table-bordered" id="data-table">
                                <thead>
                                <tr>
                                    <th>@lang('labels.indexing')</th>
                                    <th>@lang('labels.payment_date')</th>
                                    <th>@lang('labels.amount_paid')</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @php($indexing = 1)
                                @if(!empty($purchase_book->purchasingbook_purchasingbookpayment))
                                    @foreach($purchase_book->purchasingbook_purchasingbookpayment as $detail)
                                        <tr>
                                            <td>{{ $indexing }}</td>
                                            <td>{{ \Carbon\Carbon::parse($detail->payment_date)->format('d F Y') }}</td>
                                            <td class="my_rupiah">{{ $detail->amount_paid }}</td>
                                            <td style="text-align: center;">
                                                <a pydate="{{ $detail->payment_date }}" id="btnpayid-{{ $detail->id }}" pynominal="{{ $detail->amount_paid }}" class="btn btn-info btn-modal" data-toggle="modal" data-target="#editmodal">
                                                    <span class="btn-text fa fa-edit"></span>
                                                </a>
                                            </td>
                                        </tr>
                                        @php($indexing++)
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!--MODAL-->
    <div class="modal fade" id="editmodal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'PATCH', 'action' => 'PurchasingBookController@update_purchasing_book_payment', 'id' => 'store-modal-form']) !!}
                {!! Form::hidden('py_id', '', ['id' => 'py-id']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.edit_purchasing_book_payment')</h4>
                </div>
                <div class="modal-body">
                    <table class="detail">
                        <tr>
                            <td>{!! Form::label('payment_date', trans('labels.payment_date'), ['class' => 'control-label']) !!}</td>
                            <td>:</td>
                            <td>
                                {!! Form::date('payment_date', '', ['class' => 'form-control', 'id' => 'payment-date']) !!}
                            </td>
                        </tr>
                        <tr>
                            <td>{!! Form::label('amount_paid', trans('labels.amount_paid'), ['class' => 'control-label']) !!}</td>
                            <td>:</td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-addon">Rp.</span>
                                    {!! Form::text('amount_paid', '', ['class' => 'form-control rupiah', 'id' => 'amount-paid', 'onkeypress' => 'return onlyNumber(event)']) !!}
                                    <span class="input-group-addon">,-</span>
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="button" name="submit_btn" id="modal-submit-btn" class="btn btn-success">@lang('labels.update')</button>
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
    <script src="{{asset('js/my_js.js')}}"></script>
    <script>
        $(document).ready(function () {
            init_format();
            $('#data-table').dataTable();

            $('.rupiah').on('keyup',function(){
                if($(this).val() == ''){
                    $(this).val(0);
                }
                var newVal = parseInt(rupiah_format_destroyer($(this).val()));
                $(this).val(rupiah_formater(newVal.toString().replace(/\s/g, '')));
            });

            $('.btn-modal').on('click',function(){
                $('#py-id').val($(this).attr('id').split('-').pop());
                $('#payment-date').val($(this).attr('pydate'));
                $('#amount-paid').val(rupiah_formater($(this).attr('pynominal').replace(/\s/g, '')));
            });

            $('#submit-btn-form').on('click', function () {
                $('#amount-due').val(rupiah_format_destroyer($('#amount-due').val()));
                $('#store-form').submit();
            });

            $('#modal-submit-btn').on('click', function () {
                $('#amount-paid').val(rupiah_format_destroyer($('#amount-paid').val()));
                $('#store-modal-form').submit();
            });
        });

        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp. '+ x);
            });
            $('.rupiah').each(function () {
                var x = rupiah_formater($(this).val().replace(/\s/g, ''));
                $(this).val((x));
            });
        }

        function onlyNumber(evt){
            var charCode = (evt.which) ? evt.which : event.keyCode;
            if(charCode == 8 || //space
                charCode == 9 || //tab
                charCode == 46 || //point
                (charCode > 47 && charCode < 58) // number
            ){
                return true;
            }

            return false;
        }
    </script>
    <script src={{asset("bootbox/bootbox.min.js")}}></script>
@endsection
