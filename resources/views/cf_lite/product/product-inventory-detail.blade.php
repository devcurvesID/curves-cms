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
            cursor: pointer;
        }

        .product-category-box:hover {
            background-color: rgba(31, 166, 122, 0.7);
        }
    </style>
@endsection

@section('content')
    <section id="product-detail">
        @include('cf_lite.includes.flash-message')
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb pull-right" style="background-color: transparent;">
                    <li><a href="{{url('cf-lite/settings')}}" style="color: deeppink;">@lang('labels.setting')</a></li>
                    <li><a href="{{url('cf-lite/product-setting')}}" style="color: deeppink;">@lang('labels.product_setting')</a></li>
                    <li><a href="{{url('cf-lite/product-inventory/club')}}" style="color: deeppink;">@lang('labels.choose_club')</a></li>
                    <li><a href="{{url('cf-lite/product-inventory/'.$product->club_id)}}" style="color: deeppink;">@lang('labels.product_inventory')</a></li>
                    <li class="active" style="color: rebeccapurple;"><span>@lang('labels.product_inventory_detail')</span></li>
                </ol>
            </div>
            <div class="col-sm-12">
                <a href="{{url('cf-lite/product-inventory/'.Auth::user()->club_id)}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.product_inventory_detail')</h2>
                </center>
            </div>
        </div>

        {!! Form::model($product, ['method' => 'PATCH', 'action' => ['ProductController@update_product_inventory_detail', $product->id]]) !!}
        <div class="row">
            <div class="col-md-3">
                @if(!empty($product->productinv_product->product_image))
                    <img role="button" id="image_preview" class="img-rounded" src="{{asset('images/product_images/'.$product->productinv_product->product_image)}}" style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <img role="button" id="image_preview" class="img-rounded" src="{{asset('images/assets/Interface Program Item-30_2.png')}}" style="width: 150px; height: 150px; object-fit: scale-down;">
                @endif
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('item_name', trans('labels.product_name').': ', ['class' => 'control-label col-md-6']) !!}
                            <div class="col-md-6">
                                {!! Form::label('item_name', $product->productinv_product->item_name, ['class' => 'control-label']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('category', trans('labels.category').': ', ['class' => 'control-label col-md-6']) !!}
                            <div class="col-md-6">
                                {!! Form::label('category', $product->productinv_product->product_producttype->type, ['class' => 'control-label']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('accessibility', trans('labels.accessibility').': ', ['class' => 'control-label col-md-6']) !!}
                            <div class="col-md-6">
                                {!! Form::label('accessibility', $product->productinv_product->accessibility, ['class' => 'control-label']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('selling_price', trans('labels.selling_price').': ', ['class' => 'control-label col-md-6']) !!}
                            <div class="col-md-6">
                                {!! Form::text('selling_price', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('notes', trans('labels.description').': ', ['class' => 'control-label col-md-6']) !!}
                            <div class="col-md-4">
                                {!! Form::textarea('notes', null, ['class' => 'form-group', 'rows' => '4']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('update_date', trans('labels.update_date').': ', ['class' => 'control-label col-md-6']) !!}
                            <div class="col-md-6">
                                {!! Form::date('update_date', Carbon\Carbon::now(), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-3" style="text-align: right;">
                <h1>@lang('labels.stock'): </h1>
                <h1>{{$product->qty}}</h1>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8">
                {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary btn-lg pull-right']) !!}
            </div>
        </div>
        {!! Form::close() !!}
        <hr>
        <div class="row">
            <div class="col-md-12">
                <h4>
                    @lang('labels.set_discount')
                    @if(!empty($product->productinv_productdiscount))
                        @if(strtotime($product->productinv_productdiscount->thru_date) < strtotime(\Carbon\Carbon::now()))
                            <span> (@lang('labels.expired')</span>
                        @else
                            @if(strtotime($product->productinv_productdiscount->from_date) > strtotime(\Carbon\Carbon::now()))
                                <span> (@lang('labels.starting') ({{date('d-m-Y', strtotime($product->productinv_productdiscount->from_date))}} @lang('labels.until') {{date('d-m-Y', strtotime($product->productinv_productdiscount->thru_date))}})</span>
                            @else
                                <span> (@lang('labels.ongoing') ({{date('d-m-Y', strtotime($product->productinv_productdiscount->from_date))}} @lang('labels.until') {{date('d-m-Y', strtotime($product->productinv_productdiscount->thru_date))}})</span>
                            @endif
                        @endif
                    @endif
                </h4>
                {!! Form::model(!empty($product->productinv_productdiscount) ? $product->productinv_productdiscount : null, ['method' => 'PATCH', 'action' => ['ProductController@update_product_discount', $product->id]]) !!}
                <table>
                    <tr>
                        <td>@lang('labels.type')</td>
                        <td>:</td>
                        <td>
                            @if(!empty($product->productinv_productdiscount))
                                {{$product->productinv_productdiscount->discount_type}}
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('labels.category')</td>
                        <td>:</td>
                        <td>
                            <label class="radio-inline">
                                {!! Form::radio('discount_category', 'Retail', true) !!} @lang('labels.retail')
                            </label>
                            <label class="radio-inline">
                                {!! Form::radio('discount_category', 'Member', false) !!} @lang('labels.member')
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('labels.discount') (%)</td>
                        <td>:</td>
                        <td>{!! Form::text('discount_percent', null, ['class' => 'form-control']) !!}</td>
                    </tr>
                    <tr>
                        <td>@lang('labels.discount') (@lang('labels.currency'))</td>
                        <td>:</td>
                        <td>{!! Form::text('discount_value', null, ['class' => 'form-control']) !!}</td>
                    </tr>
                    <tr>
                        <td>{!! Form::label('from_date', trans('labels.from_date'), ['class' => 'control-label']) !!}</td>
                        <td>:</td>
                        <td>{!! Form::date('from_date', null, ['class' => 'form-control']) !!}</td>
                    </tr>
                    <tr>
                        <td>{!! Form::label('thru_date', trans('labels.thru_date'), ['class' => 'control-label']) !!}</td>
                        <td>:</td>
                        <td>{!! Form::date('thru_date', null, ['class' => 'form-control']) !!}</td>
                    </tr>
                    <tr>
                        <td colspan="3">
                            {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary pull-right']) !!}
                        </td>
                    </tr>
                </table>
                {!! Form::close() !!}
            </div>
        </div>
    </section>
@endsection

@section('other-script')
    <script>
        $(document).ready(function () {
            $('input[name="discount_percent"]').on('keyup', function () {
                var percent = parseFloat($('input[name="discount_percent"]').val());
                var price = parseInt($('input[name="selling_price"]').val());
                var discount = percent * price / 100;
                $('input[name="discount_value"]').val(discount);
            });

            $('input[name="discount_value"]').on('keyup', function () {
                var discount = parseInt($('input[name="discount_value"]').val());
                var price = parseInt($('input[name="selling_price"]').val());
                var percent = discount * 100 / price;
                $('input[name="discount_percent"]').val(percent.toFixed(2));
            });
        });
    </script>
@endsection