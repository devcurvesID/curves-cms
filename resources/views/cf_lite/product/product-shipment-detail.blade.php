@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">

    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <style>
        .home-btn-title{
            font-family: 'Alegreya Sans', sans-serif;
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
                <a href="{{url('cf-lite/product-request/requests')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.product_request_detail')</h2>
                </center>
            </div>
        </div>
        <hr>
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
                                    <th>@lang('labels.qty')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td>
                                            @if(!empty($product->productrequestdetail_product->product_image))
                                                <img src="{{asset('images/product_images/'.$product->productrequestdetail_product->product_image)}}" style="width: 75px; height: 75px; object-fit: cover;">
                                            @else
                                                <img src="{{asset('images/assets/Interface Program Item-30_2.png')}}" style="width: 75px; height: 75px;object-fit: scale-down;">
                                            @endif
                                        </td>
                                        <td>{{$product->productrequestdetail_product->item_name}}</td>
                                        <td>{{$product->amount}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <div class="col-md-1 pull-right" style="padding-top: 50px;">
                @if($requeststatus == 'notyet')
                    {!! Form::open(['method' => 'POST' , 'style'=>'display:inline-block' , 'action' => ['ProductController@accept_shipment' , $productrequestid]])!!}
                    {!! Form::select('staff',$staffs,Auth::user()->id,['class' => 'form-control']) !!}
                    {!! Form::submit(trans('labels.submit'),['class' => 'btn btn-success pull-right' , 'onclick' => 'return confirm("Anda yakin akan menerima permintaan ini?")', 'style' => 'margin-top:20px;']) !!} 
                    {!! Form::close() !!}
                @elseif($requeststatus == 'thisopennext')
                    <a href="">CONTINUE</a>
                @elseif($requeststatus == 'thisclose')
                    THIS CLUB ACCEPT IT
                @else
                    NONE
                @endif
            </div>
            </div>
        </div>

    </section>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script type="text/javascript">
        $('select[name="staff"]').select2();
    </script>

    <script>
        $('#data-table').dataTable();
    </script>

    <script src="{{asset('js/my_js.js')}}"></script>
@endsection