@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/payment.css')}}" rel="stylesheet">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {display:none;}

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: deeppink;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')

    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/dashboard')}}" class="btn btn-default btn-back-to"
               title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left"
                                                  style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.product_payment')</h2>
            </center>
        </div>
    </div>

    <div class="row">
        {!! Form::open(['method'=>'POST', 'action' => 'ProductController@store_product_cashier', 'id' => 'form-product-cashier']) !!}
        <input type="hidden" name="club_id" id="club-id" value="{{Auth::user()->club_id}}">
        <div class="col-lg-9 col-md-9 col-sm-12 col-xs-12 panel panel-default card-view">
            <div class="panel-body">
                <div class="row col-md-12" style="margin-bottom: 10px;">
                    <div class="col-md-3">
                        {!! Form::label('cashier', trans('labels.cashier').' : ', ['class' => 'control-label', 'style' => 'font-size: 16; color: deeppink;']) !!}
                    </div>
                    <div class="col-md-6">
                        <select id="staff" name="staff" class="form-control select2"
                                placeholder="@lang('labels.choose_staff')">
                            <option value="" disabled selected hidden>@lang('labels.choose_staff')</option>
                            @foreach($members as $member)
                                @if($member->role_id != 7)
                                    <option value="{{ $member->id }}">{{ $member->name }}</option>
                                @endif
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="row col-md-12" style="margin-bottom: 10px;">
                    <div class="col-md-3">
                        {!! Form::label('order_date', trans('labels.order_date').' : ', ['class' => 'control-label', 'style' => 'font-size: 16; color: deeppink;']) !!}
                    </div>
                    <div class="col-md-6">
                        {!! Form::date('order_date',Carbon\Carbon::today(),['class'=>'form-control', 'id'=>'order-date']) !!}
                    </div>
                </div>
                <div class="row col-md-12">
                    <div class="col-md-3">
                        {!! Form::label('product_name', trans('labels.product_name').' : ', ['class' => 'control-label', 'style' => 'font-size: 16; color: deeppink;']) !!}
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <div class="input-group">
                                <div class="input-group-addon"><span class="fa fa-barcode"></span></div>
                                <input type="text" name="product_name" class="form-control" id="product-input">
                                <input type="hidden" name="product-id" id="product-id">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="table-wrap">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered display pb-30" id="list-purchase">
                                <thead>
                                <tr>
                                    <th>@lang('labels.product_name')</th>
                                    <th>Accurate Item</th>
                                    <th>@lang('labels.unit_price')</th>
                                    <th>@lang('labels.qty')</th>
                                    <th>@lang('labels.price')</th>
                                    <th>@lang('labels.discount_per_item')</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody id="list-purchase-body">

                                </tbody>
                            </table>

                            <?php //print_r($accurateItems) ?>
                        </div>
                    </div>
                </div> {{--end of table--}}
            </div>
        </div>

        <div class="col-lg-3 col-md-3 col-sm-12 col-xs-12 panel panel-default card-view">
            <div class="panel-body">
                <div class="row">
                    <input type="hidden" name="item_list" id="item-list">
                    <div style="font-size: 32px; font-weight: bold; text-align: center; margin-bottom: 20px;">
                        <p>@lang('labels.payment')</p>
                    </div>
                    <div class="col-md-12" style="margin-bottom: 10px;">
                        {!! Form::label('buyer', trans('labels.buyer').' : ', ['class' => 'control-label mb-10 text-left']) !!}
                        <select id="buyer" name="buyer" class="form-control select2">
                            <option value="">@lang('labels.buyer')</option>
                            @foreach($members as $member)
                                <option value="{{ $member->id }}">{{ $member->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12" style="margin-bottom: 10px;">
                        {!! Form::label('payment_type', trans('labels.payment_method').' :', ['class' => 'control-label mb-10 text-left']) !!}
                        {!! Form::select('payment_type', $payment_methods, null, ['class' => 'form-control', 'id' => 'payment-type']) !!}
                    </div>
                    <div class="col-md-12" style="margin-bottom: 10px;">
                        {!! Form::label('bank', trans('labels.bank').' :', ['class' => 'control-label mb-10 text-left']) !!}
                        <select id="bank" name="bank" class="form-control select2">
                            <option value="">@lang('labels.choose') @lang('labels.bank')</option>
                            @foreach($banks as $bank)
                                <option value="{{ $bank->id }}">{{ $bank->bank_abv }}
                                    - {{ $bank->bank_account_number }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-12" style="margin-bottom: 10px;">
                        <div style="display: flex; align-items: center;">
                            {!! Form::label('prize', trans('labels.prize').' :', ['class' => 'control-label mb-10', 'style' => 'margin-right: 10px;']) !!}
                            <div class="switch">
                                <label>
                                    <input class="cek" type="checkbox" id="status" value="0">
                                    <span class="slider round"></span>
                                </label>
                            </div>
                        </div>

                    </div>
                    <div class="col-md-12" style="margin-bottom: 10px;">
                        {!! Form::label('subtotal', trans('labels.subtotal').' :', ['class' => 'control-label mb-10 text-left']) !!}
                        <div class="col-md-offset-0 col-md-12" id="subtotal"
                             style="font-size: 32px; color: black; font-weight: bold; text-align: right; margin-top: 5px;">
                            0
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-bottom: 10px;">
                        {!! Form::label('total_discount', trans('labels.total_discount_item').' :', ['class' => 'control-label mb-10 text-left']) !!}
{{--                        {!! Form::text('invoice_discount', 0, ['class' => 'form-control']) !!}--}}
                        <div class="col-md-offset-0 col-md-12" id="total-discount"
                             style="font-size: 32px; color: black; font-weight: bold; text-align: right; margin-top: 5px;">
                            0
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-bottom: 10px;">
                        {!! Form::label('total', trans('labels.total').' :', ['class' => 'control-label mb-10 text-left']) !!}
                        <div class="col-md-offset-0 col-md-12" id="total"
                             style="font-size: 32px; color: black; font-weight: bold; text-align: right; margin-top: 5px;">
                            0
                        </div>
{{--                        {!! Form::hidden('subtotal', 0, ['id' => 'subtotal']) !!}--}}
                        {!! Form::hidden('billing_total', 0, ['id' => 'billing_total']) !!}
                    </div>
                    <div class="col-md-12" style="margin-bottom: 10px;">
                        {!! Form::label('pay', trans('labels.pay').' :', ['class' => 'control-label mb-5 text-left']) !!}
                        <input type="text" name="cash" id="pay" onkeypress="return onlyNumber(event)"
                               class="form-control rupiah">
                    </div>
                    <div class="col-md-12" style="margin-bottom: 10px;">
                        <div class="col-md-offset-0 col-md-12" id="change"
                             style="font-size: 32px; color: black; font-weight: bold; text-align: right; margin-top: 5px;">
                            0
                        </div>
                    </div>
                    <div class="col-md-12" style="margin-bottom: 10px;">
                        <button type="button" style="font-size:18px" class="btn btn-success form-control"
                                id="submit_form">@lang('labels.pay')</button>
                        <button type="button" style="font-size:18px; display: none;" class="btn btn-info form-control"
                                id="reprint">@lang('labels.reprint')</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    {!! Form::close() !!}

@endsection

@section('other-script')
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script src={{asset("bootbox/bootbox.min.js")}}></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2();
            $('select[name="staff"]').select2();
            $('select[name="buyer"]').select2();
        });
    </script>
    <script>
        var listpurchase = new Array();
        var table = document.getElementById("list-purchase");
        var count = 1;

        $(document).ready(function () {
            $(function () {
                var products = [
                        @foreach($products as $product){
                        id: "{{ $product->id }}",
                        label: "{{ $product->productinv_product->item_name }}",
                        price: "{{ $product->selling_price }}",
                        qty: "{{ $product->qty }}",
                        discount: "{{ !empty($product->productinv_productdiscount)
                            ? ($product->productinv_productdiscount->thru_date >= \Carbon\Carbon::today())
                                ? $product->productinv_productdiscount->discount_value
                                : 0
                            : 0 }}",
                    },
                    @endforeach
                ];

                //AutoComplete
                $("#product-input").autocomplete({
                    minLength: 2,
                    source: products,
                    focus: function (event, ui) {
                        $("#product-input").val(ui.item.label);
                        return false;
                    },
                    select: function (event, ui) {
                        var found = false;
                        //check if the item already exist
                        for (var i = 0; i < listpurchase.length; i++) {
                            if (listpurchase[i].name == ui.item.label) {
                                found = true;
                                break;
                            }
                        }

                        //add to array
                        if (!found) {
                            if (ui.item.qty > 0) {
                                var newObj = new Object({
                                    id: count,
                                    prid: ui.item.id,
                                    name: ui.item.label,
                                    price: ui.item.price,
                                    qty: 1,
                                    subtotal: ui.item.price,
                                    discount: ui.item.discount,
                                });
                                listpurchase.push(newObj);

                                //view to user
                                var html = '<tr id="'+count+'">';
                                html += '<td>';
                                html += ui.item.label
                                html += '</td>';


                                //ACCURATE
                                html += '<td>';
                                html += '<select id="accurate_code-'+count+'" class="form-control accurate_code select2" data-prod="'+ ui.item.label +'">';
                                    html += '<option value="">Choose Accurate Item</option>';

                                    @foreach($accurateItems as $key => $val){
                                        html += '<option value="{{$key}}">{{$val}}</option>';
                                    }
                                @endforeach
                                html += '</select>';
                                html += '</td>';

                                html += '<td>';
                                html += rupiah_formater(ui.item.price)
                                html += '</td>';

                                html += '<td>';
                                html += '<input type="number" class="form-control item-qty" min="1" max="'+ui.item.qty+'" id="qty-count-'+count+'" value="1" onKeyPress="return onlyNumber(event)" onfocus="return $(this).select()"></input>';
                                html += '</td>';
                                html += '<td>';
                                html += rupiah_formater(ui.item.price)
                                html += '</td>';
                                html += '<td>';
                                html += '<input type="text" class="form-control item-discount" id="discount-'+count+'" value="'+ rupiah_formater(ui.item.discount) +'" onkeypress="return onlyNumber(event)"></input>';
                                html += '</td>';
                                html += '<td style="text-align: center;">';
                                html += '<a class="btn btn-xs btn-danger btn-delete-submit" id="delete-list-'+count+'" onclick="delete_from_list('+count+')">X</a>';
                                html += '</td>';
                                html += '</tr>';

                                $('#list-purchase-body').append(html);
                                $('.select2').select2();
                                count++;
                            } else {
                                bootbox.dialog({
                                    message: '<div class="text-center">' +
                                        '<p style="font-size: 18px;">@lang('messages.stock_empty_please_update')</p>' +
                                        '<a  style="font-size: 18px; color: red;" href="{{ url('cf-lite/product-inventory/'.Auth::user()->club_id.'/edit') }}">Update Here</a>' +
                                        '</div>',

                                });
                            }
                        } else {
                            for (var i = 0, row; row = table.rows[i]; i++) {
                                for (var j = 0, col; col = row.cells[j]; j++) {
                                    if (row.cells[j].innerHTML == ui.item.label) {
                                        var id = listpurchase.map(function (o) {
                                            return o.name;
                                        }).indexOf(ui.item.label);
                                        listpurchase[id].qty = parseInt(listpurchase[id].qty) + 1;
                                        listpurchase[id].subtotal = parseInt(listpurchase[id].subtotal) + parseInt(ui.item.price);
                                        $("#qty-count-"+listpurchase[id].id).val(listpurchase[id].qty);
                                        row.cells[j+3].innerHTML = rupiah_formater(listpurchase[id].subtotal);
                                    }
                                }
                            }
                        }

                        is_reward();
                        // Total Count
                        $("#total").html(count_total());
                        // Change Count
                        count_change();

                        // empty and focus to product name field
                        $("#product-input").val("");
                        $("#product-input").focus();

                        return false;
                    }
                })
                    .autocomplete("instance")._renderItem = function (ul, item) {
                    return $("<li>")
                        .append("<div>" + item.label + "</div>")
                        .appendTo(ul);
                };
            });

            init_format();

            // enter on product name field
            $("#product-input").on('keyup', function (event) {
                if (event.which == 13 && $("#product-input").val() != "") {
                    var objectResult = find_item($("#product-input").val());
                    if (!objectResult.value) {
                        alert('item not found');
                    } else {
                        var found = false;
                        // check if the item already exist
                        for (var i = 0; i < listpurchase.length; i++) {
                            if (listpurchase[i].code == objectResult.value) {
                                found = true;
                                break;
                            }
                        }

                        //add to array
                        if (!found) {
                            var newObj = new Object({
                                id: count,
                                prid: ui.item.id,
                                name: ui.item.label,
                                price: ui.item.price,
                                qty: 1,
                                subtotal: ui.item.price,
                                discount: ui.item.discount,
                            });
                            listpurchase.push(newObj);

                            //view to user
                            var html = '<tr id="'+count+'">';
                            html += '<td>';
                            html += ui.item.value
                            html += '</td>';
                            html += '<td>';
                            html += ui.item.label
                            html += '</td>';

                             //ACCURATE
                             html += '<td>';
                                html += '<select id="accurate_code-'+count+'" class="form-control accurate_code select2" data-prod="'+ ui.item.label +'">';
                                    html += '<option value="">Choose Accurate Item</option>';

                                    @foreach($accurateItems as $key => $val){
                                        html += '<option value="{{$key}}">{{$val}}</option>';
                                    }
                                @endforeach
                                html += '</select>';
                                html += '</td>';

                            html += '<td>';
                            html += rupiah_formater(ui.item.price)
                            html += '</td>';
                            html += '<td>';
                            html += '<input type="text" class="form-control item-qty" min="1" max="'+ui.item.qty+'" id="qty-count-'+count+'" value="1" onKeyPress="return onlyNumber(event)" onfocus="return $(this).select()"></input>';
                            html += '</td>';
                            html += '<td>';
                            html += rupiah_formater(ui.item.price)
                            html += '</td>';
                            html += '<td>';
                            html += '<input type="text" class="form-control item-discount" id="discount-'+count+'" value="'+ rupiah_formater(ui.item.discount) +'" onkeypress="return onlyNumber(event)"></input>';
                            html += '</td>';
                            html += '<td>';
                            html += '<a class="btn btn-xs btn-danger btn-delete-submit" id="delete-list-'+count+'" onclick="delete_from_list(' + count + ')">X</a>';
                            html += '</td>';
                            html += '</tr>';

                            $('#list-purchase-body').append(html);
                            $('.select2').select2();

                            count++;
                        } else {
                            var isfound = false;
                            for (var i = 0, row; row = table.rows[i]; i++) {
                                for (var j = 0, col; col = row.cells[j]; j++) {
                                    if (row.cells[j].innerHTML == objectResult.label) {
                                        isfound = true;
                                        var id = listpurchase.map(function (o) {
                                            return o.name;
                                        }).indexOf(objectResult.name);
                                        listpurchase[id].qty = parseInt(listpurchase[id].qty) + 1;
                                        listpurchase[id].subtotal = parseInt(listpurchase[id].subtotal) + parseInt(objectResult.price);
                                        $("##qty-count-" + listpurchase[id].id).val(listpurchase[id].qty);
                                        row.cells[j + 3].innerHTML = rupiah_formater(listpurchase[id].subtotal);
                                    }
                                }
                            }
                            if (isfound == false) {
                                var newObj = new Object({
                                    id: count,
                                    prid: objectResult.id,
                                    name: objectResult.label,
                                    price: objectResult.price,
                                    qty: 1,
                                    subtotal: objectResult.price,
                                    discount: objectResult.discount,
                                });
                                listpurchase.push(newObj);

                                //view to user
                                var html = '<tr id="' + count + '">';
                                html += '<td>';
                                html += ui.item.label
                                html += '</td>';

                                //ACCURATE
                                html += '<td>';
                                html += '<select id="accurate_code-'+count+'" class="form-control accurate_code select2" data-prod="'+ ui.item.label +'">';
                                    html += '<option value="">Choose Accurate Item</option>';

                                    @foreach($accurateItems as $key => $val){
                                        html += '<option value="{{$key}}">{{$val}}</option>';
                                    }
                                @endforeach
                                html += '</select>';
                                html += '</td>';

                                html += '<td>';
                                html += rupiah_formater(ui.item.price)
                                html += '</td>';
                                html += '<td>';
                                html += '<input type="text" class="form-control item-qty" min="1" max="'+ui.item.qty+'" id="qty-count-'+count+'" value="1" onKeyPress="return onlyNumber(event)" onfocus="return $(this).select()"></input>';
                                html += '</td>';
                                html += '<td>';
                                html += rupiah_formater(ui.item.price)
                                html += '</td>';
                                html += '<td>';
                                html += '<input type="text" class="form-control item-discount" id="discount-'+count+'" value="'+ rupiah_formater(ui.item.discount) +'" onkeypress="return onlyNumber(event)"></input>';
                                html += '</td>';
                                html += '<td>';
                                html += '<a class="btn btn-xs btn-danger btn-delete-submit" id="delete-list-'+count+'" onclick="delete_from_list(' + count + ')">X</a>';
                                html += '</td>';
                                html += '</tr>';

                                $('#list-purchase-body').append(html);
                                $('.select2').select2();

                                count++;
                            }
                        }

                        is_reward();
                        // Total Count
                        $("#total").html(count_total());
                        // Change Count
                        count_change();

                    }
                    // empty and focus to barcode
                    $("#product-input").val("");
                    $("#product-input").focus();
                } else {
                    if (event.which >= 97 && event.which <= 122) {
                        var newKey = event.which - 32;

                        event.keyCode = newKey;
                        event.charCode = newKey;
                    }

                    $("#product-input").val(($("#product-input").val()).toUpperCase());
                }
            }); // End of product name field

            // Select all when on focus
            $("input:text").focus(function() { $(this).select(); } );

            // Count QTY
            $(document).on('keyup','.item-qty',function(event){
                var unreal_id = $(this).attr('id').split('-').pop();
                var max_qty = $(this).attr('max');
                var id = listpurchase.map(function(o) { return o.id; }).indexOf(parseInt(unreal_id));
                if (parseInt($("#qty-count-"+unreal_id).val()) > max_qty) {
                    alert('@lang('messages.stock_not_enough')');
                    $("#qty-count-"+unreal_id).val(1);
                } else {
                    if(event.which == 13){
                        // empty and focus to product
                        $("#product-input").val("");
                        $("#product-input").focus();
                    }else{
                        if($("#qty-count-"+unreal_id).val() != ""){
                            listpurchase[id].qty = $("#qty-count-"+unreal_id).val();
                        }else{
                            $("#qty-count-"+unreal_id).val(1);
                            listpurchase[id].qty = 1;
                        }
                        listpurchase[id].subtotal = parseInt(listpurchase[id].qty) * parseInt(listpurchase[id].price);
                        for (var i = 0, row; row = table.rows[i]; i++) {
                            for (var j = 0, col; col = row.cells[j]; j++) {
                                if(row.cells[j].innerHTML == listpurchase[id].name){
                                    row.cells[j+3].innerHTML = rupiah_formater(listpurchase[id].subtotal);
                                }
                            }
                        }
                        is_reward();
                        // Total Count
                        $("#total").html(count_total());
                        // Change Count
                        count_change();
                    }
                }
            }); // End of Count QTY

            $(document).on('change','.item-qty',function(event){
                var unreal_id = $(this).attr('id').split('-').pop();
                var id = listpurchase.map(function(o) { return o.id; }).indexOf(parseInt(unreal_id));
                if ($("#qty-count-"+unreal_id).val() != "") {
                    listpurchase[id].qty = $("#qty-count-"+unreal_id).val();
                } else {
                    $("#qty-count-"+unreal_id).val(1);
                    listpurchase[id].qty = 1;
                }
                listpurchase[id].subtotal = parseInt(listpurchase[id].qty) * parseInt(listpurchase[id].price);
                for (var i = 0, row; row = table.rows[i]; i++) {
                    for (var j = 0, col; col = row.cells[j]; j++) {
                        if(row.cells[j].innerHTML == listpurchase[id].name){
                            row.cells[j+3].innerHTML = rupiah_formater(listpurchase[id].subtotal);
                        }
                    }
                }

                is_reward()
                // Total Count
                $("#total").html(count_total());
                // Change Count
                count_change();
            }); // End of Count QTY

            // Discount Item
            $(document).on('keyup', '.item-discount', function (event) {
                var unreal_id = $(this).attr('id').split('-').pop();
                var id = listpurchase.map(function(o) { return o.id; }).indexOf(parseInt(unreal_id));
                var discount = 0;

                if (rupiah_format_destroyer($("#discount-"+unreal_id).val()) != "") {
                    discount = parseInt(rupiah_format_destroyer($("#discount-"+unreal_id).val()));
                    listpurchase[id].discount = discount;
                    $('#discount-'+unreal_id).val(rupiah_formater(discount));
                } else {
                    listpurchase[id].discount = 0;
                    $('#discount-'+unreal_id).val(discount);
                }

                // Total Count
                $("#total").html(count_total());
                // Change Count
                count_change();
            });

            // Cash KeyUp
            $("#pay").on('keyup', function (event) {
                if ($(this).val() == "") {
                    var pay = 0;
                } else {
                    var pay = parseInt(rupiah_format_destroyer($(this).val()));
                }

                // Cash formatter
                if ($("#pay").val() != ""){
                    $("#pay").val(rupiah_formater(pay));
                } else {
                    $("#pay").val(0);
                }
                // Change Count
                count_change();
            });

            $('input[name="invoice_discount"]').on('keyup', function () {
                if ($(this).val() == "") {
                    var invoice_discount = 0;
                } else {
                    var invoice_discount = parseInt(rupiah_format_destroyer($('input[name="invoice_discount"]').val()));
                }
                $('input[name="invoice_discount"]').val(rupiah_formater(invoice_discount));
                var new_total = parseInt($('input[name="subtotal"]').val()) - invoice_discount;
                $('input[name="billing_total"]').val(new_total);
                $('#total').text(rupiah_formater(new_total));
            });

            $('#status').on('change', function () {
                is_reward();
            });
        }); // End of Document Ready

        //Search Item
        function find_item(stringin){
            var objectResult = new Object();
            @foreach($products as $product)
                var itemloop = "{{$product->productinv_product->item_name}}";
                if(itemloop == stringin){
                    objectResult = {
                        id: "{{ $product->id }}",
                        label: "{{ $product->productinv_product->item_name}}",
                        price: "{{ $product->selling_price }}",
                    };
                }
            @endforeach
            return objectResult;
        } //end of search item

        //delete item from list
        function delete_from_list(id) {
            if (confirm('Are you sure to delete this row?') == true) {
                for (var i = 0; i < listpurchase.length; i++) {
                    if (listpurchase[i].id == id) {
                        listpurchase.splice(i, 1);
                    }
                }
                $('#' + id).remove();
            }

            is_reward();
            $("#total").html(count_total());
            // Change Count
            count_change();
        } // End of delete item from list

        // Count Total
        function count_total() {
            var subtotal = 0;
            var total = 0;
            var discount = 0;
            for (var i = 0; i < listpurchase.length; i++) {
                if (listpurchase[i].discount != 0) {
                    total += (listpurchase[i].qty * listpurchase[i].price) - (listpurchase[i].qty * rupiah_format_destroyer(listpurchase[i].discount));
                    discount += (listpurchase[i].qty * rupiah_format_destroyer(listpurchase[i].discount));
                    // $('#discount-'+listpurchase[i].id).val(rupiah_formater(discount));
                } else {
                    total += (listpurchase[i].qty * listpurchase[i].price);
                }
                subtotal +=  (listpurchase[i].qty * listpurchase[i].price);
            }
            if (total < 0) {
                total = 0;
            }

            $('#total-discount').html(rupiah_formater(discount));
            $("#subtotal").html(rupiah_formater(subtotal));
            total = rupiah_formater(total);

            return total;
        } // End of count total

        // Count Change
        function count_change() {
            if ((parseInt(rupiah_format_destroyer($("#pay").val())) - parseInt(rupiah_format_destroyer($("#total").html()))) < 0) {
                $("#change").html(0);
            } else {
                $("#change").html(rupiah_formater(parseInt(rupiah_format_destroyer($("#pay").val())) - parseInt(rupiah_format_destroyer($("#total").html()))));
            }
        } // End of count change

        function is_reward() {
            if ($('#status').is(':checked')) {
                $('#status').val(1);
                $('#total-discount').html($('#subtotal').html());
                $('#pay').attr('readonly', 'readonly');
                $('#pay').val(0);
                $("#total").html(0);

                for (var i = 0; i < listpurchase.length; i++) {
                    var id = listpurchase[i].id;
                    $("#qty-count-"+id).attr('readonly', 'readonly');
                    $('#discount-'+id).attr('readonly', 'readonly');
                    $('#discount-'+id).val(rupiah_formater(listpurchase[i].subtotal));
                }

                count_change();
            } else {
                $('#status').val(0);
                $('#total-discount').html();
                $('#pay').removeAttr('readonly');
                $("#total").html(count_total());

                for (var i = 0; i < listpurchase.length; i++) {
                    var id = listpurchase[i].id;
                    $("#qty-count-"+id).removeAttr('readonly', 'readonly');
                    $('#discount-'+id).removeAttr('readonly', 'readonly');
                    $('#discount-'+id).val(rupiah_formater(listpurchase[i].discount));
                }
            }
        }

        // Only Number
        function onlyNumber(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode;
            if (charCode == 8 || //space
                charCode == 9 || //tab
                charCode == 46 || //point
                (charCode > 47 && charCode < 58) // number
            ) {
                return true;
            }
            return false;
        } // End of only number

        // Init Format
        function init_format() {
            $('.rupiah').each(function () {
                if ($(this).val() == "") {
                    $(this).val('0');
                } else {
                    var x = rupiah_formater($(this).val().replace(/\s/g, ''));
                    $(this).val((x));
                }
            });

            $('.my-rupiah').each(function () {
                if ($(this).text() == "") {
                    $(this).text('0');
                } else {
                    var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                    $(this).text((x));
                }
            });
        } // End of init format
    </script>
    <script>
        // Submit Form
        $(document).ready(function () {
            $(document).on('click', '#submit_form', function () {
                // $('#submit_form').on('click', function () {
                var accurateError = false;
                var productError = [];
                var index = 0;
                $(".accurate_code").each(function() {
                    if($(this).val() == ''){
                        accurateError = true;
                        productError.push($(this).attr('data-prod'));
                    }else{
                        listpurchase[index]['accurate'] = $(this).val();
                    }
                    index++;
                });

                if(accurateError){
                    alert('You must select equivalent Accurate product for '+ productError.join(', '));
                }else{
                    //console.log(listpurchase)
                    if(rupiah_format_destroyer($('#pay').val()) == 0){
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
                                    if ($('#payment-type').val() != 1 && $('#bank').val() == "") {
                                        alert('@lang('messages.please_choose_bank')');
                                    } else if($('#payment-type').val() == 1 && $('#bank').val() != ""){
                                        alert('@lang('messages.please_check_payment_method')');
                                    } else {
                                        if ($('#staff').val() != null && $('#payment-type').val() != ""){ //check select
                                            if (listpurchase.length != 0){ //check list
                                                if (rupiah_format_destroyer($('#pay').val()) - rupiah_format_destroyer($('#total').val()) >= 0){
                                                    if ($('#billing_total').val() == '0') {
                                                        $('#billing_total').val(rupiah_format_destroyer($('#subtotal').val()));
                                                    }
                                                    $('#pay').val(rupiah_format_destroyer($('#pay').val()))
                                                    $('#change').html(rupiah_format_destroyer($('#change').html()));

                                                    $('#item-list').val(JSON.stringify(listpurchase));
                                                    $('#submit_form').remove();
                                                    $('#reprint').show();
                                                    $('#pay').attr('readonly', 'readonly');

                                                    alert('clubid='+ $('#club-id').val() +
                                                        '&staff='+ $('#staff').val() +
                                                        '&payment_date='+ $('#order-date').val() +
                                                        '&buyer='+ $('#buyer').val() +
                                                        '&paymenttype='+ $('#payment-type').val() +
                                                        '&bank='+ $('#bank').val() +
                                                        '&subtotal='+ rupiah_format_destroyer($('#subtotal').text()) +
                                                        '&pay='+ $('#pay').val() +
                                                        '&change='+ $('#change').html() +
                                                        '&invoice_discount='+ rupiah_format_destroyer($('#total-discount').html()) +
                                                        '&useDefaultPrinter=undefined&printerName=XP-80' +
                                                        '&idprint=1' +
                                                        '&itemlist='+ $('#item-list').val().replace("&amp;","%3F") +
                                                        '&status='+ $('#status').val())
                                                    /* javascript:jsWebClientPrint.print(
                                                        'clubid='+ $('#club-id').val() +
                                                        '&staff='+ $('#staff').val() +
                                                        '&payment_date='+ $('#order-date').val() +
                                                        '&buyer='+ $('#buyer').val() +
                                                        '&paymenttype='+ $('#payment-type').val() +
                                                        '&bank='+ $('#bank').val() +
                                                        '&subtotal='+ rupiah_format_destroyer($('#subtotal').text()) +
                                                        '&pay='+ $('#pay').val() +
                                                        '&change='+ $('#change').html() +
                                                        '&invoice_discount='+ rupiah_format_destroyer($('#total-discount').html()) +
                                                        '&useDefaultPrinter=undefined&printerName=XP-80' +
                                                        '&idprint=1' +
                                                        '&itemlist='+ $('#item-list').val().replace("&amp;","%3F") +
                                                        '&status='+ $('#status').val()
                                                    ); */
                                                } else {
                                                    alert('@lang("messages.check_payment")');
                                                }
                                            } else {
                                                alert('@lang("messages.cart_still_empty")');
                                            }
                                        } else {
                                            alert('@lang("messages.choose_staff_buyer_and_payment_method")');
                                        }
                                    }
                                }
                            }
                        });
                    } else {
                        if ($('#payment-type').val() != 1 && $('#bank').val() == "") {
                            alert('@lang('messages.please_choose_bank')');
                        } else if($('#payment-type').val() == 1 && $('#bank').val() != ""){
                            alert('@lang('messages.please_check_payment_method')');
                        } else {
                            if ($('#staff').val() != null && $('#payment-type').val() != ""){ //check select
                                if (listpurchase.length != 0){ //check list
                                    if (rupiah_format_destroyer($('#pay').val()) - rupiah_format_destroyer($('#total').val()) >= 0){
                                        if ($('#billing_total').val() == '0') {
                                            $('#billing_total').val(rupiah_format_destroyer($('#subtotal').val()));
                                        }
                                        $('#pay').val(rupiah_format_destroyer($('#pay').val()));
                                        $('#change').html(rupiah_format_destroyer($('#change').html()));
                                        $('#invoice_discount').val(rupiah_format_destroyer($('#invoice_discount').val()));
                                        $('#item-list').val(JSON.stringify(listpurchase));
                                        $('#submit_form').remove();
                                        $('#reprint').show();
                                        $('#pay').attr('readonly', 'readonly');

                                        alert('clubid='+ $('#club-id').val() +
                                            '&staff='+ $('#staff').val() +
                                            '&payment_date='+ $('#order-date').val() +
                                            '&buyer='+ $('#buyer').val() +
                                            '&paymenttype='+ $('#payment-type').val() +
                                            '&bank='+ $('#bank').val() +
                                            '&subtotal='+ rupiah_format_destroyer($('#subtotal').text()) +
                                            '&pay='+ $('#pay').val() +
                                            '&change='+ $('#change').html() +
                                            '&invoice_discount='+ rupiah_format_destroyer($('#total-discount').html()) +
                                            '&useDefaultPrinter=undefined&printerName=XP-80' +
                                            '&idprint=1' +
                                            '&itemlist='+ $('#item-list').val().replace("&amp;","%3F") +
                                            '&status='+ $('#status').val())

                                        /* javascript:jsWebClientPrint.print(
                                            'clubid='+ $('#club-id').val() +
                                            '&staff='+ $('#staff').val() +
                                            '&payment_date='+ $('#order-date').val() +
                                            '&buyer='+ $('#buyer').val() +
                                            '&paymenttype='+ $('#payment-type').val() +
                                            '&bank='+ $('#bank').val() +
                                            '&subtotal='+ rupiah_format_destroyer($('#subtotal').text()) +
                                            '&pay='+ $('#pay').val() +
                                            '&change='+ $('#change').html() +
                                            '&invoice_discount='+ rupiah_format_destroyer($('#total-discount').html()) +
                                            '&useDefaultPrinter=undefined&printerName=XP-80' +
                                            '&idprint=1' +
                                            '&itemlist='+ $('#item-list').val().replace("&amp;","%3F") +
                                            '&status='+ $('#status').val()
                                        ); */
                                    } else {
                                        alert('@lang("messages.check_payment")');
                                    }
                                } else {
                                    alert('@lang("messages.cart_still_empty")');
                                }
                            } else {
                                alert('@lang("messages.choose_staff_buyer_and_payment_method")');
                            }
                        }
                    }
                }
            }); // End of submit form

            $('#reprint').on('click', function () {
                javascript:jsWebClientPrint.print(
                    'clubid='+ $('#club-id').val() +
                    '&staff='+ $('#staff').val() +
                    '&payment_date='+ $('#order-date').val() +
                    '&buyer='+ $('#buyer').val() +
                    '&paymenttype='+ $('#payment-type').val() +
                    '&bank='+ $('#bank').val() +
                    '&subtotal='+ rupiah_format_destroyer($('#subtotal').text()) +
                    '&pay='+ $('#pay').val() +
                    '&change='+ $('#change').html() +
                    '&invoice_discount='+ rupiah_format_destroyer($('#total-discount').html()) +
                    '&useDefaultPrinter=undefined&printerName=XP-80' +
                    '&idprint=0' +
                    '&itemlist='+ $('#item-list').val().replace("&amp;","%3F") +
                    '&status='+ $('#status').val()
                );
            });
        })
    </script>
    <script type="text/javascript">
        var wcppGetPrintersTimeout_ms = 10000; //10 sec
        var wcppGetPrintersTimeoutStep_ms = 500; //0.5 sec

        function wcpGetPrintersOnSuccess() {
            // Display client installed printers
            if (arguments[0].length > 0) {
                var p = arguments[0].split("|");
                var options = '';
                for (var i = 0; i < p.length; i++) {
                    options += '<option>' + p[i] + '</option>';
                }
                $('#installedPrinters').css('visibility', 'visible');
                $('#installedPrinterName').html(options);
                $('#installedPrinterName').focus();
                $('#loadPrinters').hide();
            } else {
                alert("No printers are installed in your system.");
            }
        }

        function wcpGetPrintersOnFailure() {
            // Do something if printers cannot be got from the client
            alert("No printers are installed in your system.");
        }

        {{--window.location.href = '{{action('PrintESCPOSController@index')}}';--}}
    </script>
    {!!

    // Register the WebClientPrint script code
    // The $wcpScript was generated by PrintESCPOSController@index

    $wcpScript;

    !!}

    <script src="{{asset('js/my_js.js')}}"></script>
@endsection
