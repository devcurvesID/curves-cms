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
                <a href="{{url('cf-lite/product-request/product-request-main')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.product_request')<br><small>{{$club->club_name}}</small></h2>
                </center>
            </div>
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
                <a class="btn btn-default pull-right" href="{{url('cf-lite/product-request-item-list')}}" title="@lang('labels.new_request')"><span class="fa fa-plus"></span> @lang('labels.new_product_request')</a>
            </div>
            <div class="col-md-10 col-md-offset-1">
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="data-table">
                                <thead>
                                <tr>
                                    <th>@lang('labels.product_request_number')</th>
                                    <th>@lang('labels.product_request_status')</th>
                                    <th>@lang('labels.request_date')</th>
                                    <th>@lang('labels.target_club')</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($requests as $req)
                                    <tr>
                                        <td><a href="{{url('cf-lite/product-request-detail/'.$req->id)}}">{{$req->request_number}}</a></td>
                                        <td>
                                        @if($req->request_status == 'Pending')
                                            <label class="label label-warning">
                                                {{$req->request_status}}
                                            </label>
                                        @elseif($req->request_status == 'Accepted')
                                            <label class="label label-success">
                                                {{$req->request_status}}
                                            </label>
                                            &nbsp;
                                            @if(!empty($req->productrequest_productshipment))
                                                @if($req->productrequest_productshipment->shipment_status == 'Pending')
                                                    <label class="label label-warning">
                                                        {{$req->productrequest_productshipment->shipment_status}}
                                                    </label>
                                                @elseif($req->productrequest_productshipment->shipment_status == 'Accepted')
                                                    <label class="label label-success">
                                                        {{$req->productrequest_productshipment->shipment_status}}
                                                    </label>
                                                @elseif($req->productrequest_productshipment->shipment_status == 'Canceled')
                                                    <label class="label label-danger">
                                                        {{$req->productrequest_productshipment->shipment_status}}
                                                    </label>
                                                @elseif($req->productrequest_productshipment->shipment_status == 'Sending')
                                                    <label class="label label-primary">
                                                        {{$req->productrequest_productshipment->shipment_status}}
                                                    </label>
                                                @elseif($req->productrequest_productshipment->shipment_status == 'Failed')
                                                    <label class="label label-danger">
                                                        {{$req->productrequest_productshipment->shipment_status}}
                                                    </label>
                                                @endif
                                        @endif
                                        @elseif($req->request_status == 'Canceled')
                                            <label class="label label-danger">
                                                {{$req->request_status}}
                                            </label>
                                        @endif
                                        </td>
                                        <td>
                                            {{$req->request_date}}
                                        </td>
                                        <td>
                                            {{$req->productrequest_targetclub->club_name}}
                                        </td>
                                        <td>
                                            @if($req->request_status == 'Pending')
                                                {!! Form::open(['method' => 'PATCH' , 'style'=>'display:inline-block' , 'action' => ['ProductController@product_request_cancel' , $req->id, ]])!!}{!! Form::submit(trans('labels.cancel'),['class' => 'btn btn-danger' , 'onclick' => 'return confirm("Anda yakin akan membatalkan permintaan ini?")']) !!} {!! Form::close() !!}
                                            @endif
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

        <div class="row">
            <div class="col-md-12">
                <div class="pull-right paging">
                    {{$requests->links()}}
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