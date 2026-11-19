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

        .edit-product-inv-btn input{
            margin: 20px 0;
            background-color: #ff1493;
            color: #ffffff;
            transition: 0.2s;
            border-color: #ff1493;
        }

        .edit-product-inv-btn input:hover{
            color: #ff1493;
            background-color: transparent;
            border-color: #ff1493;
        }

    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/invoice-list-refund')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.add_product_refund_to_inventory')<br></h2>
                </center>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                {!! Form::open(['method' => 'POST', 'action' => ['ProductController@product_refund_inventory',$id]]) !!}
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="data-table">
                                <thead>
                                <tr>
                                    <td>@lang('labels.edit')</td>
                                    <th>@lang('labels.image')</th>
                                    <th>@lang('labels.item_name')</th>
                                    <th>@lang('labels.refund_amount')</th>
                                    <th>@lang('labels.add_quantity')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(!empty($product_refund_inventory))
                                    @foreach($product_refund_inventory as $pri)
                                        <tr>
                                            <td>
                                                {!! Form::checkbox('product_inv['.$pri->id.']', $pri->id, false, ['class' => 'product-inv', 'id' => 'product-inv-'.$pri->id]) !!}
                                            </td>
                                            <td>
                                                @if(!empty($pri->productrefundinventory_product->product_image))
                                                    <img src="{{asset('images/product_images/'.$pri->productrefundinventory_product->product_image)}}" style="width: 75px; height: 75px; object-fit: cover;">
                                                @else
                                                    <img src="{{asset('images/assets/Interface Program Item-30_2.png')}}" style="width: 75px; height: 75px; object-fit: scale-down;">
                                                @endif
                                            </td>
                                            <td>{{$pri->productrefundinventory_product->item_name}}</td>
                                            <td>{{$pri->quantity}}</td>
                                            <td>
                                                {!! Form::number('qty['.$pri->id.']', '0', ['class' => 'form-control', 'id' => 'qty-'.$pri->id, 'disabled','max' => $pri->quantity , 'min' => '0']) !!}
                                                <font style="font-size: 30px;"> /{{$pri->quantity}}</font>
                                            </td>
                                        </tr>
                                    @endforeach
                                @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="edit-product-inv-btn">
                    {!! Form::submit(trans('labels.submit'), ['class' => 'btn btn-primary pull-right']) !!}
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
        $('#data-table').dataTable({
            'pageLength': 100
        });
    </script>

    <script>
        $(document).ready(function () {
            if ($('.product-inv').prop('checked') == true) {
                var id = $(this).attr('id').split('-').pop();
                $('#qty-' + id).prop('disabled', false);
            }
        });

        $('.x_panel').on('click', '.product-inv', function() {
            var id = $(this).attr('id').split('-').pop();
            if ($(this).prop('checked') == true) {
                $('#qty-' + id).prop('disabled', false);
            } else {
                $('#qty-' + id).prop('disabled', true);
            }
        });
    </script>
@endsection