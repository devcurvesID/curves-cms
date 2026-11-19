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
    @include('cf_lite.includes.error-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/product-inventory/'.$club->id)}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.edit_product_inventory')<br><small>{{$club->club_name}}</small></h2>
                </center>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                {!! Form::open(['method' => 'PATCH', 'action' => ['ProductController@update_bulk_product_inventory', $club->id] ,'id' => 'form-bulk']) !!}
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-inline">
                            <div class="form-group">
                                {!! Form::label('update_date', trans('labels.date').':  ', ['class' => 'control-label']) !!}
                                {!! Form::date('update_date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                </div>
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
                                    <th>@lang('labels.category')</th>
                                    <th>@lang('labels.order_quantity')</th>
                                    <th>@lang('labels.note')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td>
                                            {!! Form::checkbox('product_inv['.$product->id.']', $product->id, false, ['class' => 'product-inv', 'id' => 'product-inv-'.$product->id]) !!}
                                        </td>
                                        <td>
                                            @if(!empty($product->product_image))
                                                <img src="{{asset('images/product_images/'.$product->product_image)}}" style="width: 75px; height: 75px; object-fit: cover;">
                                            @else
                                                <img src="{{asset('images/assets/Interface Program Item-30_2.png')}}" style="width: 75px; height: 75px; object-fit: scale-down;">
                                            @endif
                                        </td>
                                        <td>{{$product->item_name}}</td>
                                        <td>{{$product->product_producttype->type}}</td>
                                        <td>{!! Form::number('qty['.$product->id.']', '0', ['class' => 'form-control', 'id' => 'qty-'.$product->id, 'disabled']) !!}</td>
                                        <td>{!! Form::textarea('notes['.$product->id.']', null, ['class' => 'form-control', 'id' => 'notes-'.$product->id, 'rows' => '3', 'disabled']) !!}</td>
                                    </tr>
                                @endforeach
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
        $(document).ready(function () {
            if ($('.product-inv').prop('checked') == true) {
                var id = $(this).attr('id').split('-').pop();
                $('#qty-' + id).prop('disabled', false);
                $('#notes-' + id).prop('disabled', false);
            }

            var table = $('#data-table').DataTable();

            $('#form-bulk').on('submit',function(e){
                table.rows().nodes().page.len(-1).draw(false);
            })
        });


        $('.x_panel').on('click', '.product-inv', function() {
            var id = $(this).attr('id').split('-').pop();
            if ($(this).prop('checked') == true) {
                $('#qty-' + id).prop('disabled', false);
                $('#notes-' + id).prop('disabled', false);
            } else {
                $('#qty-' + id).prop('disabled', true);
                $('#notes-' + id).prop('disabled', true);
            }
        });
    </script>
@endsection