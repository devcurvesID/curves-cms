@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/payment.css')}}" rel="stylesheet">
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('bootstrap-toggle/css/bootstrap-toggle.min.css')}}" rel="stylesheet">
    <style>
        .modal-dialog, .modal-dialog label{
            color: #414042;
        }

        table{
            color: #414042;
        }

        .select2 {
            width: 100% !important;
            float: right;
        }

        #challenge-section {
            display: none;
        }
    </style>

    <style>
        .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20px; }
        .toggle.ios .toggle-handle { border-radius: 20px; }

        .checkbox label .toggle {
            margin-left: 0;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/product/cart')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.payment_detail')</h2>
            </center>
        </div>
    </div>

    @include('cf_lite.includes.error-message')

    {!! Form::open(['method' => 'POST', 'action' => 'ProductController@store_product_payment', 'id' => 'form-product-payment']) !!}
    <div class="row">
        <div class="col-md-6">
            <table class="member-detail">
                <tr>
                    <td>@lang('labels.payment_number')</td>
                    <td>:</td>
                    <td>
                        @php($month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'))
                        {{sprintf('%08d', $temp_auto_number->current_number)}}/PY/CIND/{{sprintf('%03d', $club_id)}}/{{$month_roman[\Carbon\Carbon::now()->format('n')]}}/{{\Carbon\Carbon::now()->format('Y')}}
                        {!! Form::hidden('payment_number', '/PY/CIND/'.sprintf('%03d', $club_id).'/'.$month_roman[\Carbon\Carbon::now()->format('n')].'/'.\Carbon\Carbon::now()->format('Y')) !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang('labels.buyer')</td>
                    <td>:</td>
                    <td>
                        {!! Form::select('member', $members, null, ['class' => 'form-control', 'placeholder' => trans('labels.not_member')]) !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang('labels.type')</td>
                    <td>:</td>
                    <td>
                        <div class="checkbox">
                            <label>
                                {!! Form::checkbox('reward', 'reward', false, ['data-toggle' => 'toggle', 'data-size' => 'small', 'data-on' => trans('labels.reward'), 'data-off' => trans('labels.standard'), 'data-onstyle' => 'success', 'data-offstyle' => 'info', 'data-style' => 'ios', 'id' => 'reward-product']) !!}
                            </label>
                        </div>
                    </td>
                </tr>
                <tr id="challenge-section">
                    <td>@lang('labels.challenge')</td>
                    <td>:</td>
                    <td>
                        {!! Form::select('challenge', $challenges, null, ['class' => 'form-control', 'placeholder' => '== '.trans('labels.pick_challenge').' ==', 'disabled']) !!}
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="member-detail">
                <tr>
                    <td>@lang('labels.payment_date')</td>
                    <td>:</td>
                    <td>
                        {!! Form::date('payment_date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                        {{--{{date('d-m-Y', strtotime(\Carbon\Carbon::now()))}}--}}
                    </td>
                </tr>
                <tr>
                    <td>{!! Form::label('payment_method', trans('labels.payment_method'), ['class' => 'control-label']) !!}</td>
                    <td>:</td>
                    <td>{!! Form::select('payment_method', $payment_methods, null, ['class' => 'form-control', 'placeholder' => '-- '.trans('labels.choose').' '.trans('labels.payment_method').' --']) !!}</td>
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
                            <th>@lang('labels.item_name')</th>
                            <th>@lang('labels.price')</th>
                            <th>@lang('labels.quantity')</th>
                            <th>@lang('labels.subtotal')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(\Gloudemans\Shoppingcart\Facades\Cart::count() > 0)
                            @php($index = 0)
                            @php($total = 0)
                            @foreach(\Gloudemans\Shoppingcart\Facades\Cart::content() as $cart)
                                @php($index++)
                                @if(($cart->options->disc_percent != null) && ($cart->options->disc_value != null))
                                    @php($rowspan = 2)
                                @else
                                    @php($rowspan = 1)
                                @endif
                                <tr>
                                    <td rowspan="{{$rowspan}}">{{$index}}</td>
                                    <td>{{$cart->name}}</td>
                                    <td class="my_rupiah">{{$cart->price}}</td>
                                    <td>{{$cart->qty}}</td>
                                    <td class="my_rupiah">{{$cart->price * $cart->qty}}</td>
                                </tr>
                                @php($total = $total + ($cart->price * $cart->qty))
                                @if($rowspan == '2')
                                    <tr>
                                        <td colspan="3" style="text-align: right;">@lang('labels.discount') ({{number_format($cart->options->disc_percent, 2)}}%):</td>
                                        <td>({{$cart->options->disc_value * $cart->qty}})</td>
                                    </tr>
                                    @php($total = $total - ($cart->options->disc_value * $cart->qty))
                                @endif
                            @endforeach
                            <tr>
                                <td colspan="4" style="text-align: right;">@lang('labels.subtotal'): </td>
                                <td class="my_rupiah">
                                    {{$total}}
                                </td>
                                {!! Form::hidden('subtotal', $total) !!}
                            </tr>
                            {{--<tr>--}}
                                {{--<td colspan="3" style="text-align: right;">@lang('labels.tax'):</td>--}}
                                {{--<td>--}}
                                    {{--{!! Form::select('tax', $taxes, null, ['class' => 'form-control', 'placeholder' => '-- '.trans('labels.choose').' '.trans('labels.tax_type').' --']) !!}--}}
                                {{--</td>--}}
                                {{--<td>--}}
                                    {{--{!! Form::text('tax_value', '0', ['class' => 'form-control']) !!}--}}
                                {{--</td>--}}
                            {{--</tr>--}}
                            <tr>
                                <td colspan="3" style="text-align: right;">@lang('labels.voucher_code'):</td>
                                <td>
                                    <div class="input-group">
                                        {!! Form::text('voucher_code', null, ['class' => 'form-control', 'placeholder' => trans('labels.insert_voucher_code')]) !!}
                                        <span class="input-group-btn">
                                            <button class="btn btn-default" id="check-voucher-btn" type="button">@lang('labels.verify')</button>
                                        </span>
                                    </div>
                                </td>
                                <td>
                                    {!! Form::text('voucher_value', '0', ['class' => 'form-control rupiah', 'readonly']) !!}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" style="text-align: right;">@lang('labels.invoice_discount'): </td>
                                <td>
                                    {!! Form::text('invoice_discount', 0, ['class' => 'form-control rupiah']) !!}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" style="text-align: right;">@lang('labels.total'): </td>
                                <td>
                                    <div id="billing-total" class="my_rupiah">{{$total}}</div>
                                    {!! Form::hidden('billing-total', $total) !!}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4" style="text-align: right;">@lang('labels.pay'): </td>
                                <td>
                                    {!! Form::text('pay', null, ['class' => 'form-control rupiah']) !!}
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-success btn-lg pull-right" id="payment-process">@lang('labels.pay')</button>
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@section('other-script')
    <script src="{{asset('bootstrap-toggle/js/bootstrap-toggle.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('.rupiah').each(function () {
                $(this).wrap('<div class="input-group"></div>')
                $(this).before('<span class="input-group-addon">Rp.</span>');
                $(this).after('<span class="input-group-addon">,-</span>');
            });

            init_format();
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

        $('input[name="pay"]').on('keyup', function () {
            if ($(this).val() == "") {
                var total_pay = 0;
            } else {
                var total_pay = parseInt(rupiah_format_destroyer($('input[name="pay"]').val()));
            }
            $('input[name="pay"]').val(rupiah_formater(total_pay));
        });

        $('input[name="invoice_discount"]').on('keyup', function () {
            if ($(this).val() == "") {
                var invoice_discount = 0;
            } else {
                var invoice_discount = parseInt(rupiah_format_destroyer($('input[name="invoice_discount"]').val()));
            }
            $('input[name="invoice_discount"]').val(rupiah_formater(invoice_discount));
            var new_total = parseInt($('input[name="subtotal"]').val()) - invoice_discount;
            $('input[name="billing-total"]').val(new_total);
            $('#billing-total').text(rupiah_formater(new_total));
        });
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>

    <script>
        $(document).ready(function () {
            var url = "{{url('cf-lite')}}/";

            {{--$('select[name="tax"]').on('change', function () {--}}
                {{--if ($('select[name="tax"]').val() == '') {--}}
                    {{--return false;--}}
                {{--}--}}

                {{--$.ajaxSetup({--}}
                    {{--headers: {--}}
                        {{--'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')--}}
                    {{--}--}}
                {{--});--}}

                {{--var formData = {--}}
                    {{--tax_type_id: $('select[name="tax"]').val(),--}}
                    {{--club_id: '{{\Illuminate\Support\Facades\Auth::user()->club_id}}'--}}
                {{--};--}}

                {{--var type = 'POST';--}}
                {{--var my_url = url + 'get-tax-config-detail';--}}

                {{--$.ajax({--}}
                    {{--type: type,--}}
                    {{--url: my_url,--}}
                    {{--data: formData,--}}
                    {{--dataType: 'json',--}}
                    {{--success: function (data) {--}}
                        {{--var subtotal = parseInt($('input[name="subtotal"]').val());--}}
                        {{--var voucher = 0;--}}
                        {{--if ($('input[name="voucher_value"]').val() != '') {--}}
                            {{--voucher = parseInt($('input[name="voucher_value"]').val());--}}
                        {{--}--}}

                        {{--var tax = subtotal * parseInt(data.tax_config['rate']) * 0.01;--}}
                        {{--var total = subtotal + tax - voucher;--}}

                        {{--$('input[name="tax_value"]').val(tax);--}}
                        {{--$('input[name="billing-total"]').val(total);--}}
                        {{--$('#billing-total').text(total);--}}
                    {{--},--}}
                    {{--error: function (data) {--}}
                        {{--console.log('Error: ', data);--}}
                    {{--}--}}
                {{--});--}}
            {{--});--}}

            $('#check-voucher-btn').on('click', function () {
                if ($('input[name="voucher_code"]').val() == '') {
                    return false;
                }

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    voucher_code: $('input[name="voucher_code"]').val()
                };

                var type = 'POST';
                var my_url = url + 'verify-voucher-code-product';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        if (data.voucher_data == 'expired') {
                            alert('@lang('messages.sorry_the_voucher_expired')');
                        } else {
                            if (data.voucher_data['type_id'] == '1') {
//                                var subtotal = parseInt($('input[name="subtotal"]').val()) + parseInt($('input[name="tax_value"]').val());
                                var subtotal = parseInt($('input[name="subtotal"]').val());
                                var old_voucher = parseInt(rupiah_format_destroyer($('input[name="voucher_value"]').val()));
                                if (data.voucher_data['status'] == 'Out') {
                                    if (data.voucher_data['voucher_base'] == 'value') {
                                        var voucher = parseInt(data.voucher_data['value']);
                                    } else if (data.voucher_data['voucher_base'] == 'percent') {
                                        var voucher = (subtotal - old_voucher) * parseInt(data.voucher_data['percent']) * 0.01;
                                        if (voucher < parseInt(data.voucher_data['value'])) {
                                            voucher = parseInt(data.voucher_data['value']);
                                        }
                                    }
                                    var total = subtotal + old_voucher - voucher;
                                    $('input[name="voucher_value"]').val(rupiah_formater(voucher));
                                } else if (data.voucher_data['status'] == 'Used') {
                                    var total = subtotal;
                                    alert('@lang('messages.sorry_the_voucher_has_been_used')');
                                    $('input[name="voucher_value"]').val('0');
                                } else if (data.voucher_data['status'] == 'InStock') {
                                    var total = subtotal;
                                    alert('@lang('messages.sorry_the_voucher_has_not_been_released')');
                                    $('input[name="voucher_value"]').val('0');
                                }
                                $('input[name="billing-total"]').val(total);
                                $('#billing-total').text(rupiah_formater(total));
                            }
                        }
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });

            $('#payment-process').on('click', function () {
                var gate = 0;

                if ($('#reward-product').prop('checked') == true) {
                    gate = 1;
                } else {
                    if (parseInt(rupiah_format_destroyer($('input[name="pay"]').val())) >= parseInt(rupiah_format_destroyer($('input[name="billing-total"]').val()))) {
                        gate = 1;
                    }
                }

                if (gate == 0) {
                    alert('@lang('messages.not_enough_money')');
                } else {
                    var pay = parseInt(rupiah_format_destroyer($('input[name="pay"]').val()));
                    var billing = parseInt(rupiah_format_destroyer($('input[name="billing-total"]').val()));
                    var invoice_discount = parseInt(rupiah_format_destroyer($('input[name="invoice_discount"]').val()));
                    $('input[name="pay"]').val(pay);
                    $('input[name="billing-total"]').val(billing);
                    $('input[name="invoice_discount"]').val(invoice_discount);
                    $('#form-product-payment').submit();
                }
            });
        });
    </script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('select[name="member"]').select2();
            $('select[name="challenge"]').select2();

            if ($('#reward-product').prop('checked') == true) {
                $('select[name="challenge"]').prop('disabled', false);
                $('#challenge-section').show();
            }

            $('.toggle-group').on('click', function () {
                if ($('#reward-product').prop('checked') == false) {
                    $('select[name="challenge"]').prop('disabled', false);
                    $('#challenge-section').show();
                    $('#billing-total').text('Rp.0');
                    $('input[name="pay"]').val(rupiah_formater('0'));
                } else {
                    $('select[name="challenge"]').prop('disabled', true);
                    $('#challenge-section').hide();
                    $('#billing-total').text('Rp.' + rupiah_formater($('input[name="billing-total"]').val()));
                }
            });
        });
    </script>
@endsection