@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
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

        .row{
            padding-top: 10px;
            padding-left: 20px;
            padding-right: 20px;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/dashboard')}}" style="left: 25px; top: 5px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.product_list')</h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-9">
                <div id="filter-zone">
                    {!! Form::open(['method' => 'GET', 'action' => 'ProductController@sales_product_list_filtered']) !!}
                    <div class="form-group">
                        <label class="radio-inline">
                            {!! Form::radio('access', 'Retail', false) !!} @lang('labels.retail')
                        </label>
                        <label class="radio-inline">
                            {!! Form::radio('access', 'Member', true) !!} @lang('labels.member')
                        </label>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            {!! Form::text('s', null, ['class' => 'form-control', 'placeholder' => trans('labels.input_search')]) !!}
                            <span class="input-group-btn">
                            <button class="btn btn-default btn-filter-zone" type="submit"><span class="fa fa-search"></span></button>
                        </span>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
            <div class="col-md-3">
                @if(\Gloudemans\Shoppingcart\Facades\Cart::count() > 0)
                    <a href="{{url('cf-lite/product/cart')}}" class="btn btn-lg btn-success pull-right"><span class="fa fa-shopping-cart"></span> @lang('labels.payment') <span class="badge">{{\Gloudemans\Shoppingcart\Facades\Cart::count()}}</span></a>
                @else
                    <a href="javascript:;" class="btn btn-lg btn-success pull-right empty-cart"><span class="fa fa-shopping-cart"></span> @lang('labels.payment')</a>
                @endif
            </div>
        </div>
        <div class="row">
            @if(count($products) > 0)
                @foreach($products as $product)
                    <div class="col-md-3">
                        {!! Form::open(['method' => 'POST', 'action' => ['ProductController@add_to_cart', $product->id]]) !!}
                        <div class="thumbnail">
                            @if(!empty($product->productinv_product->product_image))
                                <img src="{{asset('images/product_images/'.$product->productinv_product->product_image)}}" class="img-rounded" style="width: 100%; height: 200px; object-fit: cover;">
                            @else
                                <img src="{{asset('images/assets/Interface Program Item-30_2.png')}}" class="img-rounded" style="width: 100%; height: 200px; object-fit: scale-down;">
                            @endif
                            <h4>{{$product->productinv_product->item_name}}</h4>
                            <label class="label label-info" style="position: absolute; top: 10px;">{{$product->productinv_product->accessibility}}</label>
                                @if(!empty($product->productinv_productdiscount))
                                    @if((strtotime($product->productinv_productdiscount->from_date) <= strtotime(\Carbon\Carbon::now())) && (strtotime($product->productinv_productdiscount->thru_date) >= strtotime(\Carbon\Carbon::now())))
                                        <label role="button" class="label label-success" style="position: absolute; top: 20px; right: 10px;">@lang('labels.discount') {{$product->productinv_productdiscount->discount_percent}}% ({{$product->productinv_productdiscount->discount_category}})</label>
                                    @endif
                                @endif
                            <div class="form-inline">
                                <div class="form-group">
                                    {!! Form::label('qty', trans('labels.quantity').': ', ['class' => 'control-label']) !!}
                                    {!! Form::number('qty', '1', ['class' => 'form-control', 'max' => $product->qty]) !!}
                                    {!! Form::label('stock', trans('labels.stock').': '.$product->qty, ['class' => 'control-label']) !!}
                                </div>
                            </div>
                            <button type="submit" class="btn btn-primary center-block"><span class="fa fa-cart-plus"></span> @lang('labels.add_to_cart')</button>
                        </div>
                        {!! Form::close() !!}
                    </div>
                @endforeach
            @endif
        </div>
    </section>
@endsection

@section('other-script')
    <script>
        $('.empty-cart').on('click', function () {
            alert('@lang('labels.empty_cart')');
        });
    </script>
@endsection