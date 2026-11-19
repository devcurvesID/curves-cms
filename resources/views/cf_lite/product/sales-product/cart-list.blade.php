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

        .discount-label {
            position: absolute;
            right: 5px;
            top: -10px;
            width: 45px;
            height: 45px;
            background-color: purple;
            color: white;
            border-radius: 50px;
            padding: 10px;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/product')}}" style="left: 25px; top: 5px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.cart')</h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                @if(count($carts) > 0)
                    <ul class="list-group">
                        <li>
                            <div class="media">
                                <div class="media-left"></div>
                                <div class="media-body">
                                    <div class="col-md-6">@lang('labels.item')</div>
                                    <div class="col-md-2">@lang('labels.price')</div>
                                    <div class="col-md-2">@lang('labels.subtotal')</div>
                                    <div class="col-md-2"></div>
                                </div>
                            </div>
                        </li>
                        @foreach($carts as $cart)
                            <li class="list-group-item">
                                <div class="media">
                                    <div class="media-left">
                                        @if(!empty($cart->options->image))
                                            <img class="media-object" src="{{asset('images/product_images/'.$cart->options->image)}}" style="width: 70px; height: 70px; object-fit: cover;">
                                        @else
                                            <img class="media-object" src="{{asset('images/assets/Interface Program Item-30_2.png')}}" style="width: 70px; height: 70px; object-fit: scale-down;">
                                        @endif
                                    </div>
                                    <div class="media-body">
                                        <h4 class="media-heading">{{$cart->name}}</h4>
                                        <div class="col-md-6">
                                            {!! Form::open(['method' => 'patch', 'action' => 'ProductController@update_cart_qty']) !!}
                                            {!! Form::hidden('rowid-up', $cart->rowId) !!}
                                            <table>
                                                <tr>
                                                    <td>{!! Form::label('qty', trans('labels.quantity').': ', ['class' => 'control-label']) !!} </td>
                                                    <td>{!! Form::number('qty', $cart->qty, ['class' => 'form-control']) !!}</td>
                                                    <td>{!! Form::submit(trans('labels.edit'), ['class' => 'btn btn-default btn-sm']) !!}</td>
                                                </tr>
                                            </table>
                                            {!! Form::close() !!}
                                            @if(($cart->options->disc_percent != null) && ($cart->options->disc_value != null))
                                                <div class="discount-label">-{{$cart->options->disc_percent}}%</div>
                                            @endif
                                        </div>
                                        <div class="col-md-2 my_rupiah">{{$cart->price}}</div>
                                        <div class="col-md-2 my_rupiah">{{$cart->qty * $cart->price}}</div>
                                        <div class="col-md-2">
                                            {!! Form::open(['method' => 'DELETE', 'action' => 'ProductController@remove_from_cart']) !!}
                                            {!! Form::hidden('rowid-del', $cart->rowId) !!}
                                            {!! Form::submit(trans('labels.remove'), ['class' => 'btn btn-default btn-sm']) !!}
                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                    <div class="col-md-offset-8 col-md-4">
                        @lang('labels.total'): <span class="my_rupiah">{{\Gloudemans\Shoppingcart\Facades\Cart::subtotal()}}</span>
                    </div>
                @endif
            </div>
            <div class="col-md-12">
                <a href="{{url('cf-lite/product/cart/payment')}}" class="btn btn-success btn-lg pull-right">@lang('labels.pay')</a>
            </div>
        </div>
    </section>
@endsection

@section('other-script')
    <script>
        $(document).ready(function () {
            init_format();

            $('.empty-cart').on('click', function () {
                alert('@lang('labels.empty_cart')');
            });
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
    </script>

    <script src="{{asset('js/my_js.js')}}"></script>
@endsection