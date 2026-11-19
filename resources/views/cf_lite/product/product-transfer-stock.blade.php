@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/css/select2.min.css" rel="stylesheet" />

    <style>
        .select2 {
            width: 100% !important;
        }
    </style>
    <style>
        .home-btn-title {
            font-family: 'Alegreya Sans', sans-serif;
        }

        .product-category-box {
            height: 100px;
            width: auto;
            padding: 10px;
            position: relative;
            float: left;
            margin-right: 10px;
            margin-bottom: 10px;
            background-color: #1fa67a;
            color: #fff;
            border-radius: 10px;
        }

        .product-category-box:hover {
            background-color: rgba(31, 166, 122, 0.7);
        }

        div.dataTables_wrapper div.dataTables_length select {
            margin: 0 10px;
        }

        .product-inv-add a {
            background-color: #ff1493;
            color: #ffffff;
            transition: 0.2s;
            border-color: #ff1493;
        }

        .product-inv-add a:hover {
            color: #ff1493;
            background-color: transparent;
            border-color: #ff1493;
        }

        #data-table th {
            color: #ff1493;
        }

        .pagination > .active > a,
        .pagination > .active > a:focus,
        .pagination > .active > a:hover, .pagination > .active > span,
        .pagination > .active > span:focus,
        .pagination > .active > span:hover {
            background-color: #ff1493;
            border-color: #ff1493;
        }

        section#staff-home {
            padding-bottom: 30px;
        }

    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/product-request/product-request-main')}}" style="left: 25px; top: 20px;"
                   class="btn btn-default btn-back-to" title="@lang('labels.back')"><span
                            class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.product_inventory')<br>
                        <small>{{$club->club_name}}</small>
                    </h2>
                </center>
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
                                    <th>@lang('labels.image')</th>
                                    <th>@lang('labels.item_name')</th>
                                    <th>@lang('labels.stock')</th>
                                    <th>@lang('labels.transfer_quantity') (@lang('labels.per_club'))</th>
                                    <th>@lang('labels.transfer_to')</th>
                                    <th>@lang('labels.action')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($inventories as $inv)
                                    <tr>
                                        <td>
                                            @if(!empty($inv->productinv_product->product_image))
                                                <img src="{{asset('images/product_images/'.$inv->productinv_product->product_image)}}"
                                                     style="width: 75px; height: 75px; object-fit: cover;">
                                            @else
                                                <img src="{{asset('images/assets/Interface Program Item-30_2.png')}}"
                                                     style="width: 75px; height: 75px;object-fit: scale-down;">
                                            @endif
                                        </td>
                                        <td>{{$inv->productinv_product->item_name}}</td>
                                        <td>
                                            <span id="stock-text-{{$inv->id}}">{{$inv->qty}}</span>
                                            {!! Form::hidden('stock_'.$inv->id, $inv->qty, ['id' => 'stock-'.$inv->id]) !!}
                                        </td>
                                        <td>
                                            <div class="input-group">
                                                {!! Form::number('transfer_qty_'.$inv->id, 0, ['class' => 'form-control', 'id' => 'transfer-qty-'.$inv->id, 'max' => $inv->qty]) !!}
                                                <span class="input-group-addon">@lang('labels.pcs')</span>
                                            </div>
                                        </td>
                                        <td>
                                            <select name="club[]" id="club-{{$inv->id}}"
                                                    class="form-control club"
                                                    data-placeholder="@lang('labels.select_club_to_transfer')" multiple>
                                                @foreach($clubs as $club)
                                                    <option value="{{ $club->id }}">{{ $club->club_name }}</option>
                                                @endforeach
                                            </select>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-default submit-transfer-btn"
                                                    id="btn-{{$inv->id}}" title="@lang('labels.submit')">
                                                <span class="fa fa-check"></span>
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
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
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-beta.1/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function () {
            $.fn.select2.amd.define('select2/selectAllAdapter', [
                'select2/utils',
                'select2/dropdown',
                'select2/dropdown/attachBody'
            ], function (Utils, Dropdown, AttachBody) {

                function SelectAll() {
                }

                SelectAll.prototype.render = function (decorated) {
                    var self = this,
                        $rendered = decorated.call(this),
                        $selectAll = $(
                            '<button class="btn btn-xs btn-success" type="button" style="margin-left:6px;"><i class="fa fa-check-square-o"></i> @lang('labels.select_all')</button>'
                        ),
                        $unselectAll = $(
                            '<button class="btn btn-xs btn-danger" type="button" style="margin-left:6px;"><i class="fa fa-square-o"></i> @lang('labels.unselect_all')</button>'
                        ),
                        $btnContainer = $('<div style="margin-top:3px;">').append($selectAll).append($unselectAll);
                    if (!this.$element.prop("multiple")) {
                        // this isn't a multi-select -> don't add the buttons!
                        return $rendered;
                    }
                    $rendered.find('.select2-dropdown').prepend($btnContainer);
                    $selectAll.on('click', function (e) {
                        self.$element.find('option').prop('selected', 'selected').select2()

                        var $results = $rendered.find('.select2-results__option[aria-selected=false]');
                        /* var $results = self.$element.find('option'); */
                        /* $results.each(function () {
                            self.trigger('select', {
                                data: $(this).data('data')
                            });
                        }); */
                        self.trigger('close');
                        $rendered.find('.select2-dropdown').prepend($btnContainer);
                    });
                    $unselectAll.on('click', function (e) {
                        self.$element.find('option').prop('selected', false).select2()
                        /* var $results = $rendered.find('.select2-results__option[aria-selected=true]');
                        $results.each(function () {
                            self.trigger('unselect', {
                                data: $(this).data('data')
                            });
                        }); */
                        self.trigger('close');
                        $rendered.find('.select2-dropdown').prepend($btnContainer);
                    });
                    return $rendered;
                };

                return Utils.Decorate(
                    Utils.Decorate(
                        Dropdown,
                        AttachBody
                    ),
                    SelectAll
                );

            });

            $('.club').select2({
                dropdownAdapter: $.fn.select2.amd.require('select2/selectAllAdapter')
            });


            $('#data-table').dataTable({
                "columns": [
                    { "width":"15%" },
                    { "width":"20%" },
                    { "width":"10%" },
                    { "width":"20%" },
                    { "width":"35%" },
                    { "width":"5%" },
                ]
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#data-table').on('click', '.submit-transfer-btn', function () {
                var id = $(this).attr('id').split('-').pop();
                var stock = parseInt($('#stock-' + id).val());
                var total_qty = parseInt($('#transfer-qty-' + id).val());
                var total_club = $('#club-' + id).val();

                if ((total_club.length * total_qty) <= stock) {
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });

                    var formData = {
                        inv_id: id,
                        qty_each: total_qty,
                        clubs: $('#club-' + id).val()
                    };

                    var type = 'POST';
                    var my_url = '{{url('cf-lite')}}/product-request/transfer';

                    $.ajax({
                        type: type,
                        url: my_url,
                        data: formData,
                        dataType: 'json',
                        success: function (data) {
                            console.log(data);
                            if (data != 'failed') {
                                $('#stock-text-' + id).text(data.qty);
                                $('#stock-' + id).val(data.qty);
                            }
                        },
                        error: function (data) {
                            console.log('Error: ', data);
                        }
                    });
                } else {
                    alert('@lang('messages.not_enough_stock')');
                }
            });
        });
    </script>
    {{--<script src="{{asset('js/my_js.js')}}"></script>--}}
@endsection
