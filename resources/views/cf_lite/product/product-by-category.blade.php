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

        .product-latest-category{
            margin-bottom: 20px;
        }
        .product-latest-category h4 {
            line-height: 40px;
            font-size: 27px;
            font-weight: 600;
            color: #6752a5;
            background-color: transparent;
            border-color: transparent;
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

    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <center>
                    <h2 class="page-title">@lang('labels.product')<br><small>({{$category->type}})</small></h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3 col-md-offset-1 col-sm-4">
                {!! Form::open(['method' => 'GET', 'action' => ['ProductController@product_search_by_category', $category->id]]) !!}
                <div class="input-group">
                    {!! Form::text('product', null, ['class' => 'form-control', 'placeholder' => trans('labels.search').' '.trans('labels.'.strtolower($category->type))]) !!}
                    <span class="input-group-btn">
                        <button class="btn btn-default btn-md" type="submit"><span class="fa fa-search"></span> @lang('labels.search')</button>
                    </span>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-10 col-md-offset-1 product-latest-category">
                @if(isset($search_req))
                    <h4>@lang('labels.search_result'): {{$search_req}}</h4>
                @else
                    <h4>@lang('labels.latest_'.strtolower($category->type))</h4>
                @endif
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="data-table">
                                <thead>
                                <tr>
                                    <th>@lang('labels.image')</th>
                                    <th>@lang('labels.item_name')</th>
                                    <th>@lang('labels.default_price')</th>
                                    <th>@lang('labels.note')</th>
                                    <th>@lang('labels.added')</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td>
                                            @if(!empty($product->product_image))
                                                <img src="{{asset('images/product_images/'.$product->product_image)}}" style="width: 75px; height: 75px; object-fit: cover;">
                                            @else
                                                <img src="{{asset('images/assets/Interface Program Item-30_2.png')}}" style="width: 75px; height: 75px; object-fit: scale-down;">
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{url('cf-lite/master-product/'.$product->id.'/edit')}}">{{$product->item_name}}</a>
                                        </td>
                                        <td class="my_rupiah">{{$product->default_price}}</td>
                                        <td>
                                            @if(!empty($product->notes))
                                                {{$product->notes}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>{{date('d-m-Y H:i T', strtotime($product->updated_at))}}</td>
                                        <td></td>
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