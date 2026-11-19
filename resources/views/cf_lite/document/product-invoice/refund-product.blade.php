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
                <center>
                    <h2 class="page-title">
                        @lang('labels.refund_detail')
                    </h2>
                </center>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                {!! Form::open(['method' => 'PATCH', 'action' => ['ProductController@refund_product', $id]]) !!}
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <h4>
                            {!! Form::label('refund_number', trans('labels.refund_number').': ', ['class' => 'control-label']) !!}
                            @php($month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'))
                            {{sprintf('%08d', $temp_auto_number->current_number)}}/RF/CIND/{{sprintf('%03d', $club_id)}}/{{$month_roman[\Carbon\Carbon::now()->format('n')]}}/{{\Carbon\Carbon::now()->format('Y')}}
                        </h4>
                            {!! Form::hidden('payment_number', '/RF/CIND/'.sprintf('%03d', $club_id).'/'.$month_roman[\Carbon\Carbon::now()->format('n')].'/'.\Carbon\Carbon::now()->format('Y')) !!}
                        <hr>
                        <div class="col-md-6">
                        {!! Form::label('refund_date', trans('labels.refund_date').': ', ['class' => 'control-label']) !!}{!! Form::date('refund_date', Carbon\Carbon::now() , ['class' => 'form-control', 'style' => 'margin-bottom : 20px;']) !!}

                       {!! Form::label('staff_name', trans('labels.staff_name').': ', ['class' => 'control-label']) !!}{!! Form::select('staff',$staffs, null, ['class' => 'form-control', 'style' => 'margin-bottom : 20px;']) !!}
                        {!! Form::label('refund_type', trans('labels.refund_type').': ', ['class' => 'control-label']) !!}{!! Form::select('refund_type',[
                                    'cash' => 'Tunai',
                                    'voucher' => 'Voucher'
                                ], 'voucher', ['class' => 'form-control ref-type', 'style' => 'margin-bottom : 20px;']) !!}
                        {!! Form::label('voucher_code', trans('labels.voucher_code').': ', ['class' => 'control-label create-voucher']) !!}{!! Form::text('voucher_code', '', ['class' => 'form-control create-voucher', 'style' => 'margin-bottom : 20px;']) !!}
                        {!! Form::label('from_date', trans('labels.from_date').': ', ['class' => 'control-label create-voucher']) !!}{!! Form::date('from_date', Carbon\Carbon::today(), ['class' => 'form-control create-voucher', 'style' => 'margin-bottom : 20px;']) !!}
                        {!! Form::label('thru_date', trans('labels.thru_date').': ', ['class' => 'control-label create-voucher']) !!}{!! Form::date('thru_date', Carbon\Carbon::today(), ['class' => 'form-control create-voucher', 'style' => 'margin-bottom : 20px;']) !!}
                        {!! Form::label('refund_reason', trans('labels.refund_reason').': ', ['class' => 'control-label']) !!}{!! Form::textarea('refund_reason', '', ['class' => 'form-control', 'style' => 'margin-bottom : 20px;','rows'=>'3']) !!}
                        </div>
                        <hr>
                        <div class="table-responsive col-md-12">
                            <table class="table table-hover table-bordered" >
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>@lang('labels.product_name')</th>
                                    <th>@lang('labels.price')</th>
                                    <th>@lang('labels.quantity') ( @lang('labels.refund_amount_done') )</th>
                                    <th>@lang('labels.refund_amount')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php($indexing = 1)
                                @php($total = 0)
                                @foreach($invoice_details as $invoice_detail)
                                    <tr>
                                        <td>{!! Form::checkbox('product_ref['.$invoice_detail->id.']', $invoice_detail->id, false, ['class' => 'product-ref', 'id' => 'product-ref-'.$invoice_detail->id]) !!}</td>
                                        <td>{{$invoice_detail->productpaymentdetail_product->item_name}}</td>
                                        <td class="my_rupiah">{{$invoice_detail->price - $invoice_detail->discount_value}}</td>
                                        {!! Form::hidden('product_price['.$invoice_detail->id.']', $invoice_detail->price - $invoice_detail->discount_value ) !!}
                                        @php($counter = 0)
                                        @foreach($invoice_detail->productpaymentdetail_productrefunddetail as $refunddetailqty)
                                            @php($counter += $refunddetailqty->refund_quantity)
                                        @endforeach
                                        <td>{{$invoice_detail->qty}}({{$counter}})</td>
                                        <td>{!! Form::number('refund_qty['.$invoice_detail->id.']', 0, ['class' => 'form-control', 'id' => 'refund-qty-'.$invoice_detail->id, 'disabled'=> 'disabled','max'=> $invoice_detail->qty-$counter]) !!}</td>
                                    </tr>
                                @php($indexing++)
                                @endforeach
                                </tbody>
                            </table>
                            <div>
                                {!! Form::submit(trans('labels.submit'), ['class' => 'btn btn-primary pull-right']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
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

            if ($('.product-ref').prop('checked') == true) {
                var id = $(this).attr('id').split('-').pop();
                $('#refund-qty-' + id).prop('disabled', false);
            }
        });

        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp.' + (x));
            });
        }

        $('.x_panel').on('click', '.product-ref', function() {
            var id = $(this).attr('id').split('-').pop();
            if ($(this).prop('checked') == true) {
                $('#refund-qty-' + id).prop('disabled', false);
            } else {
                $('#refund-qty-' + id).prop('disabled', true);
            }
        });

        $('.ref-type').on('change',function(){
            if($('.ref-type').prop('value') == 'cash'){
                $('.create-voucher').hide();
            }else{
                $('.create-voucher').show();
            }
        });
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>
@endsection