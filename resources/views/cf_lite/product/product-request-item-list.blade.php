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

        /*.blink{*/
            /*animation:blink 800ms infinite alternate;*/
        /*}*/

        /*@keyframes blink {*/
            /*from { background-color: #ffffff; }*/
            /*to { background-color: #e5e5e5; }*/
        /*}*/
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/product-request')}}" style="left: 25px; top: 5px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.product_list')</h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                {!! Form::open(['method' => 'GET', 'class' => 'form-inline']) !!}
                <div class="form-group">
                    {!! Form::text('p', null, ['class' => 'form-control', 'placeholder' => trans('labels.insert').' '.trans('labels.product_name')]) !!}
                </div>
                <div class="form-group">
                    <button type="submit" class="btn btn-info" style="font-size: 21px;" title="@lang('labels.search')"><span class="fa fa-search"></span></button>
                </div>
                {!! Form::close() !!}
            </div>
            <div class="col-md-6">
                @if(\Gloudemans\Shoppingcart\Facades\Cart::count() > 0)
                    <a href="{{url('cf-lite/product-request/cart')}}" class="btn btn-lg btn-success pull-right"><span class="glyphicon glyphicon-list-alt"></span> &nbsp;@lang('labels.request_list') <span class="badge">{{\Gloudemans\Shoppingcart\Facades\Cart::count()}}</span></a>
                @else
                    <a href="javascript:;" class="btn btn-lg btn-success empty-cart pull-right"><span class="glyphicon glyphicon-list-alt"></span>  &nbsp;@lang('labels.request_list')</a>
                @endif
            </div>
        </div>
        <div class="row">
            @if(count($products) > 0)
                @foreach($products as $product)
                    <div class="col-md-3">
                        {!! Form::open(['method' => 'POST', 'action' => ['ProductController@add_to_request_list', $product->pr_id]]) !!}
                        <div class="thumbnail">
                            @if(!empty($product->product_image))
                                <img src="{{asset('images/product_images/'.$product->product_image)}}" class="img-rounded" style="width: 100%; height: 200px; object-fit: cover;">
                            @else
                                <img src="{{asset('images/assets/Interface Program Item-30_2.png')}}" class="img-rounded" style="width: 100%; height: 200px; object-fit: scale-down;">
                            @endif
                            <h4>{{$product->item_name}}</h4>
                            <div class="form-inline">
                                <div class="form-group">
                                    {!! Form::label('qty', trans('labels.quantity').': ', ['class' => 'control-label']) !!}
                                    {!! Form::number('qty', '1', ['class' => 'form-control', 'max' => '10000']) !!}
                                    @if(!empty($product->qty))
                                        {!! Form::label('stock', trans('labels.stock').': '.$product->qty, ['class' => 'control-label']) !!}
                                    @else
                                        {!! Form::label('stock', trans('labels.stock').': 0', ['class' => 'control-label']) !!}
                                    @endif
                                </div>
                            </div>
                            <br/>
                            <button type="submit" class="btn btn-primary center-block" style="margin-bottom: 10px;"><span class="glyphicon glyphicon-list-alt"></span> @lang('labels.add_to_request_list')</button>
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
            alert('@lang('labels.empty_request')');
        });
    </script>
@endsection