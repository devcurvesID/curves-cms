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

        .page-title {
            margin-top: 20px;
        }

        .home-btn-title {
            font-family: 'Alegreya Sans', sans-serif;
        }

        .product-category-box {
            width: auto;
            padding: 7px;
            position: relative;
            float: left;
            margin-right: 10px;
            margin-bottom: 10px;
            background-color: #1fa67a;
            color: #fff;
            border-radius: 5px;
        }

        .product-category-box:hover {
            background-color: rgba(31, 166, 122, 0.7);
        }

        .maspro-sub-title h4 {
            line-height: 40px;
            font-size: 27px;
            font-weight: 600;
            color: #6752a5;
            background-color: transparent;
            border-color: transparent;
        }

        div.dataTables_wrapper div.dataTables_length select {
            margin: 0 10px;
        }

        .maspro-add button,
        .maspro-add a {
            background-color: #ff1493;
            color: #ffffff;
            transition: 0.2s;
            border-color: #ff1493;
        }

        .maspro-add button:hover,
        .maspro-add a:hover {
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

        .maspro-back a {
            color: #ff1493;
            text-decoration: none;
        }

        .maspro-back a:hover {
            color: #b32275;
            text-decoration: none;
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
                    <li><a href="{{url('cf-lite/product-setting')}}"
                           style="color: deeppink;">@lang('labels.product_setting')</a></li>
                    <li class="active" style="color: rebeccapurple;"><span>@lang('labels.product')</span></li>
                </ol>
            </div>
            <div class="col-sm-12">
                <a href="{{url('cf-lite/product-setting')}}" style="left: 25px; top: 20px;"
                   class="btn btn-default btn-back-to" title="@lang('labels.back')"><span
                            class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.product')</h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-md-offset-1  col-sm-4">
                {!! Form::open(['method' => 'GET', 'action' => 'ProductController@product_search']) !!}
                <div class="input-group">
                    {!! Form::text('product', null, ['class' => 'form-control', 'placeholder' => trans('labels.search_product')]) !!}
                    <span class="input-group-btn">
                        <button class="btn btn-default btn-md" type="submit"><span
                                    class="fa fa-search"></span> @lang('labels.search')</button>
                    </span>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1 ">
                @if(count($product_types) > 0)
                    @foreach($product_types as $product_type)
                        <a href="{{url('cf-lite/master-product/category/'.$product_type->id)}}">
                            <div class="product-category-box">
                                @if(!empty($product_type->icon))
                                    <img src="{{asset('images/product-images/'.$product_type->icon)}}"
                                         style="width: 14px; height: 14px;">
                                @else
                                    <span class="fa fa-shopping-bag"></span>
                                @endif
                                <span style="margin-left: 5px;">{{$product_type->type}}</span>
                            </div>
                        </a>
                    @endforeach
                @endif
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-10 col-md-offset-1 maspro-sub-title">
                @if(isset($search_req))
                    <h4>@lang('labels.search_result'): {{$search_req}}</h4>
                @else
                    <h4>@lang('labels.latest_product')</h4>
                @endif
            </div>
            <div class="col-md-10 col-md-offset-1 maspro-add">
                <a class="btn btn-default pull-right" href="{{url('cf-lite/master-product/add-promo')}}"
                   title="@lang('labels.add_promo')" style="margin-left: 10px;"><span
                            class="fa fa-plus"></span> @lang('labels.add_promo')</a>
                <button class="btn btn-default pull-right" data-toggle="modal" data-target="#addOneProductModal"
                        title="@lang('labels.add_new_product')" style="margin-left: 10px;"><span
                            class="fa fa-plus"></span> <span class="fa fa-shopping-bag"></span></button>
                <a class="btn btn-default pull-right" href="{{url('cf-lite/master-product/add-products')}}"
                   title="@lang('labels.add_bulk_product')"><span
                            class="fa fa-plus"></span> @lang('labels.add_bulk_product')</a>
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
                                    <th>@lang('labels.default_price')</th>
                                    <th>@lang('labels.description')</th>
                                    <th>@lang('labels.status')</th>
                                    <th>@lang('labels.added')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($latest_products as $product)
                                    <tr>
                                        <td>
                                            @if(!empty($product->product_image))
                                                <img src="{{asset('images/product_images/'.$product->product_image)}}"
                                                     style="width: 75px; height: 75px; object-fit: cover;">
                                            @else
                                                <img src="{{asset('images/assets/Interface Program Item-30_2.png')}}"
                                                     style="width: 75px; height: 75px; object-fit: scale-down;">
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{url('cf-lite/master-product/'.$product->id.'/edit')}}">{{$product->item_name}}</a>
                                        </td>
                                        <td>{{$product->product_producttype->type}}</td>
                                        <td class="my_rupiah">{{$product->default_price}}</td>
                                        <td>
                                            @if(!empty($product->notes))
                                                {{$product->notes}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($product->status == 'Active')
                                                <label class="label label-success">{{$product->status}}</label>
                                            @else
                                                <label class="label label-danger">{{$product->status}}</label>
                                            @endif
                                        </td>
                                        <td>{{\Carbon\Carbon::parse($product->updated_at)->setTimezone(session('timezone'))->format('d-m-Y, H:i  T')}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-12 maspro-back" style="padding: 15px;">
                <a href="#" style="margin-left: 15px; font-size: 17px;"><span class="fa fa-chevron-circle-left"
                                                                              style="margin-right: 10px;"></span>@lang('labels.see_all_products')
                </a>
            </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="addOneProductModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.add_product')</h4>
                </div>
                {!! Form::open(['method' => 'POST', 'action' => 'ProductController@store_new_product']) !!}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    {!! Form::label('item_name', trans('labels.product_name').': ', ['class' => 'control-label col-md-3']) !!}
                                    <div class="col-md-4">
                                        {!! Form::text('item_name', null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('product_type', trans('labels.category').': ', ['class' => 'control-label col-md-3']) !!}
                                    <div class="col-md-4">
                                        {!! Form::select('product_type', $product_type_list, null, ['class' => 'form-control', 'id' => 'product-types-select']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('labels.save')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
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