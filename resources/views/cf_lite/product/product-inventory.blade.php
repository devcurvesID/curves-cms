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
        .home-btn-title{
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

        .product-inv-add a{
            background-color: #ff1493;
            color: #ffffff;
            transition: 0.2s;
            border-color: #ff1493;
        }

        .product-inv-add a:hover{
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

        section#staff-home{
            padding-bottom: 30px;
        }

    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb pull-right" style="background-color: transparent;">
                    <li><a href="{{url('cf-lite/settings')}}" style="color: deeppink;">@lang('labels.setting')</a></li>
                    <li><a href="{{url('cf-lite/product-setting')}}" style="color: deeppink;">@lang('labels.product_setting')</a></li>
                    <li><a href="{{url('cf-lite/product-inventory/club')}}" style="color: deeppink;">@lang('labels.choose_club')</a></li>
                    <li class="active" style="color: rebeccapurple;"><span>@lang('labels.product_inventory')</span></li>
                </ol>
            </div>
            <div class="col-sm-12">
                <a href="{{url('cf-lite/product-setting')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.product_inventory')<br><small>{{$club->club_name}}</small></h2>
                </center>
            </div>
        </div>
        <div class="row">
            @if(count($need_stock_products) > 0)
                <div class="col-md-12">
                    <h4 style="margin-left: 15px;">@lang('labels.need_restock')</h4>
                </div>
                <div class="col-md-12">
                    @foreach($need_stock_products as $nsp)
                        <div class="col-md-3">
                            <a href="{{url('cf-lite/product-inventory/edit/'.$nsp->id)}}">
                                <div class="thumbnail">
                                    @if(!empty($nsp->productinv_product->product_image))
                                        <img src="{{asset('images/product_images/'.$nsp->productinv_product->product_image)}}" class="img-rounded" style="width: 100%; height: 200px; object-fit: cover;">
                                    @else
                                        <img src="{{asset('images/assets/Interface Program Item-30_2.png')}}" class="img-rounded" style="width: 100%; height: 200px; object-fit: scale-down;">
                                    @endif
                                    <h4>{{$nsp->productinv_product->item_name}}</h4>
                                    <h5>@lang('labels.stock'): {{$nsp->qty}}</h5>
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
        <hr>
        {{--<div class="row">
            <div class="col-md-3 col-sm-4">
                {!! Form::open(['method' => 'GET']) !!}
                <div class="input-group">
                    {!! Form::text('product', null, ['class' => 'form-control', 'placeholder' => trans('labels.search_product')]) !!}
                    <span class="input-group-btn">
                        <button class="btn btn-default btn-md" type="submit"><span class="fa fa-search"></span> @lang('labels.search')</button>
                    </span>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <hr>--}}
        <div class="row">
            <div class="col-md-10 col-md-offset-1 product-inv-add">
                <a class="btn btn-default pull-right" href="{{url('cf-lite/product-inventory/'.$club->id.'/edit')}}" title="@lang('labels.add_bulk_product')">@lang('labels.add_inventory_stock')</a>
            </div>
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
                                    <th>@lang('labels.category')</th>
                                    <th>@lang('labels.price')</th>
                                    <th>@lang('labels.quantity')</th>
                                    <th>@lang('labels.discount')</th>
                                    <th>@lang('labels.note')</th>
                                    <th>@lang('labels.status')</th>
                                    <th>@lang('labels.latest_update_date')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($inventories as $inv)
                                    <tr>
                                        <td>
                                            @if(!empty($inv->productinv_product->product_image))
                                                <img src="{{asset('images/product_images/'.$inv->productinv_product->product_image)}}" style="width: 75px; height: 75px; object-fit: cover;">
                                            @else
                                                <img src="{{asset('images/assets/Interface Program Item-30_2.png')}}" style="width: 75px; height: 75px;object-fit: scale-down;">
                                            @endif
                                        </td>
                                        <td><a href="{{url('cf-lite/product-inventory/edit/'.$inv->id)}}">{{$inv->productinv_product->item_name}}</a></td>
                                        <td>{{$inv->productinv_product->product_producttype->type}}</td>
                                        <td class="my_rupiah">{{$inv->selling_price}}</td>
                                        <td>{{$inv->qty}}</td>
                                        <td>
                                            @if(!empty($inv->productinv_productdiscount))
                                                @if((strtotime($inv->productinv_productdiscount->from_date) <= strtotime(\Carbon\Carbon::now())) && (strtotime($inv->productinv_productdiscount->thru_date) >= strtotime(\Carbon\Carbon::now())))
                                                    <a target="_blank" href="{{url('cf-lite/product-inventory/edit/'.$inv->id)}}">
                                                        <label role="button" class="label label-info">@lang('labels.discount') {{$inv->productinv_productdiscount->discount_percent}}%</label>
                                                    </a>
                                                @endif
                                            @endif
                                        </td>
                                        <td>
                                            @if(!empty($inv->notes))
                                                {{$inv->notes}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($inv->productinv_product->status == 'Active')
                                                <label class="label label-success">{{$inv->productinv_product->status}}</label>
                                            @else
                                                <label class="label label-danger">{{$inv->productinv_product->status}}</label>
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{ url('cf-lite/product-inventory/'. $club->id .'/stock/'. $inv->product_id .'/history') }}">
                                                @if(!empty($inv->latest_update_date))
                                                    {{$inv->latest_update_date}}
                                                @else
                                                    -
                                                @endif
                                            </a>
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

        {{--<div class="row">--}}
            {{--<div class="col-md-12">--}}
                {{--<div class="pull-right paging">--}}
                    {{--{{$inventories->links()}}--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    </section>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>

    <script>
        $(document).ready(function () {
            init_format();
            
            $('#data-table').dataTable();
            inventories.bindResetListeners();
        });


        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp.' + (x));
            });
        }
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>
@endsection