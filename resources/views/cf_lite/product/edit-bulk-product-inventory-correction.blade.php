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
    <section id="staff-home">
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb pull-right" style="background-color: transparent;">
                    <li><a href="{{url('cf-lite/settings')}}" style="color: deeppink;">@lang('labels.setting')</a></li>
                    <li><a href="{{url('cf-lite/product-setting')}}" style="color: deeppink;">@lang('labels.product_setting')</a></li>
                    <li><a href="{{url('cf-lite/product-inventory-correction/club')}}" style="color: deeppink;">@lang('labels.choose_club')</a></li>
                    <li class="active" style="color: rebeccapurple;"><span>@lang('labels.product_inventory_correction')</span></li>
                </ol>
            </div>
            <div class="col-sm-12">
                <a href="{{url('cf-lite/product-setting')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.product_inventory_correction')<br><small>{{$club->club_name}}</small></h2>
                </center>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                {!! Form::open(['method' => 'PATCH', 'action' => ['ProductController@update_bulk_product_inventory_correction', $club->id], 'id' => 'form-bulk']) !!}
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
                                    <th>@lang('labels.current_quantity')</th>
                                    <th>@lang('labels.change_qty_to')</th>
                                    <th>@lang('labels.correction_date')</th>
                                    <th>@lang('labels.broken')</th>
                                    <th>@lang('labels.correction_reason')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($products as $product)
                                    <tr>
                                        <td>
                                            {!! Form::checkbox('product_inv['.$product->id.']', $product->id, false, ['class' => 'product-inv', 'id' => 'product-inv-'.$product->id]) !!}
                                        </td>
                                        <td>
                                            @if(!empty($product->productinv_product->product_image))
                                                <img src="{{asset('images/product_images/'.$product->productinv_product->product_image)}}" style="width: 75px; height: 75px; object-fit: cover;">
                                            @else
                                                <img src="{{asset('images/assets/Interface Program Item-30_2.png')}}" style="width: 75px; height: 75px; object-fit: scale-down;">
                                            @endif
                                        </td>
                                        <td>{{$product->productinv_product->item_name}}</td>
                                        <td>{{$product->productinv_product->product_producttype->type}}</td>
                                        <td>{{$product->qty}}</td>
                                        <td>
                                            {!! Form::number('qty['.$product->id.']', $product->qty, ['class' => 'form-control', 'id' => 'qty-'.$product->id, 'disabled','max'=> $product->qty , 'min'=>'0']) !!}
                                        </td>
                                        <td>
                                            {!! Form::date('cordate['.$product->id.']', Carbon\Carbon::now(), ['class' => 'form-control', 'id' => 'cordate-'.$product->id, 'disabled']) !!}
                                        </td>
                                        <td>
                                            {!! Form::checkbox('product_brk['.$product->id.']', $product->id, false, ['class' => 'product-brk', 'id' => 'product-brk-'.$product->id]) !!} @lang('labels.broken')
                                        </td>
                                        <td>
                                            {!! Form::textarea('notes['.$product->id.']', 'Salah input.', ['class' => 'form-control', 'id' => 'notes-'.$product->id, 'rows' => '3', 'disabled']) !!}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div><br/>
                {!! Form::select('staff',$staffs,Auth::user()->id,['class' => 'form-control pull-right','style'=>'width: 200px;']) !!}<br/><br/>
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
        $('#data-table').dataTable();
    </script>

    <script>
        $(document).ready(function () {
            if ($('.product-inv').prop('checked') == true) {
                var id = $(this).attr('id').split('-').pop();
                $('#qty-' + id).prop('disabled', false);
                $('#cordate-' + id).prop('disabled', false);
                $('#notes-' + id).prop('disabled', false);
                $('#product-brk-' + id).prop('disabled', false);
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
                $('#cordate-' + id).prop('disabled', false);
                $('#notes-' + id).prop('disabled', false);
                $('#product-brk-' + id).prop('disabled', false);
            } else {
                $('#qty-' + id).prop('disabled', true);
                $('#cordate-' + id).prop('disabled', true);
                $('#notes-' + id).prop('disabled', true);
                $('#product-brk-' + id).prop('disabled', true);
            }
        });
    </script>
@endsection