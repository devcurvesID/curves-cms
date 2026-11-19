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
        <hr>
        <div class="row">
            <div class="col-md-10 col-md-offset-1 maspro-sub-title">
                <h4>@lang('labels.digital_product')</h4>
            </div>
            <div class="col-md-10 col-md-offset-1 maspro-add">
                <button class="btn btn-default pull-right" data-toggle="modal" data-target="#addOneProductModal"
                        title="@lang('labels.add_new_product')" style="margin-left: 10px;"><span
                            class="fa fa-plus"></span> <span class="fa fa-shopping-bag"></span></button>
            </div>
            <div class="col-md-10 col-md-offset-1">
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="data-table">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>@lang('labels.item_name')</th>
                                    <th>@lang('labels.default_price')</th>
                                    <th>@lang('labels.notes')</th>
                                    <th>@lang('labels.action')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($digital_products as $product)
                                    <tr>
                                        <td></td>
                                        <td>{{$product->item_name}}</td>
                                        <td class="my_rupiah">{{$product->default_price}}</td>
                                        <td>{{$product->notes}}</td>
                                        <td>
                                            <button type="button" id="editproduct_{{$product->id}}" class="btn btn-primary btn-edit">@lang('labels.edit')</button>
                                            <button type="button" id="deleteproduct_{{$product->id}}" class="btn btn-primary btn-delete">@lang('labels.delete')</button>
                                        </td>
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

    <!-- Modal Add -->
    <div class="modal fade" id="addOneProductModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.add_product')</h4>
                </div>
                {!! Form::open(['method' => 'POST', 'action' => 'DigitalProductController@store_digital_product','id'=>'form_add']) !!}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    {!! Form::label('item_name', trans('labels.product_name').': ', ['class' => 'control-label col-md-3']) !!}
                                    <div class="col-md-4">
                                        {!! Form::text('item_name', null, ['class' => 'form-control','required']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('default_price', trans('labels.default_price').': ', ['class' => 'control-label col-md-3']) !!}
                                    <div class="col-md-4">
                                        {!! Form::text('default_price', null, ['class' => 'input_rupiah form-control','required','id'=>'add_input_rupiah']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('notes', trans('labels.notes').': ', ['class' => 'control-label col-md-3']) !!}
                                    <div class="col-md-4">
                                        {!! Form::textarea('notes', null, ['class' => 'form-control','required','rows' => 4]) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" class="btn btn-primary btn-save-add">@lang('labels.save')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <!-- Modal Edit -->
    <div class="modal fade" id="editOneProductModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.edit_product')</h4>
                </div>
                {!! Form::open(['method' => 'PATCH', 'action' => 'DigitalProductController@update_digital_product','id'=>'form_edit']) !!}
                {!! Form::text('id',null,['hidden','hidden','id'=>'edit_id']) !!}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    {!! Form::label('item_name', trans('labels.product_name').': ', ['class' => 'control-label col-md-3']) !!}
                                    <div class="col-md-4">
                                        {!! Form::text('item_name', null, ['class' => 'form-control','id'=>'edit_item_name']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('default_price', trans('labels.default_price').': ', ['class' => 'control-label col-md-3']) !!}
                                    <div class="col-md-4">
                                        {!! Form::text('default_price', null, ['class' => 'input_rupiah form-control','id'=>'edit_default_price']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('notes', trans('labels.notes').': ', ['class' => 'control-label col-md-3']) !!}
                                    <div class="col-md-4">
                                        {!! Form::textarea('notes', null, ['class' => 'form-control','rows' => 4,'id'=>'edit_notes']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" class="btn btn-primary btn-save-edit">@lang('labels.save')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    {!! Form::open(['method' => 'DELETE', 'action' => 'DigitalProductController@delete_digital_product','id'=>'delete_form','hidden'=>'hidden']) !!}
    {!! Form::text('id',null,['hidden','hidden','id'=>'delete_id']) !!}
    {!! Form::close() !!}
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script src={{asset("bootbox/bootbox.min.js")}}></script>
    <script>
        $(document).ready(function () {
            var t = $('#data-table').DataTable( {
                "columnDefs": [ {
                    "searchable": false,
                    "orderable": false,
                    "targets": 0
                } ],
                "order": [[ 1, 'asc' ]]
            } );
            t.on('order.dt search.dt', function () {
                t.column(0, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
            }).draw();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            init_format();
            $('.input_rupiah').on('input',function () {
                var rupiah = rupiah_formater($(this).val().replace(/\s/g, ''));
                $(this).val( `Rp ${rupiah}` );
            });

            $('.btn-save-add').on('click',function () {
                var rupiah = rupiah_format_destroyer( $('#add_input_rupiah').val() );
                $('#add_input_rupiah').val(rupiah );
            });
            $('.btn-edit').on('click',function () {
                $("#editOneProductModal").modal('toggle');
                var formData = {
                    id: $(this).attr('id').split('_').pop(),
                };

                var type = 'GET';
                var my_url = '{{ url('cf-lite/digital-product-detail') }}';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        if (data){
                            var rupiah = rupiah_formater( data.default_price);
                            $('#edit_id').val(data.id);
                            $('#edit_item_name').val(data.item_name);
                            $('#edit_default_price').val(`Rp ${rupiah}`);
                            $('#edit_notes').val(data.notes);
                        }
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });

                $('.btn-save-edit').on('click',function () {
                    var rupiah = rupiah_format_destroyer( $('#edit_default_price').val() );
                    $('#edit_default_price').val(rupiah );
                });
            });
            $('.btn-delete').on('click',function () {
                var parent = this;
                bootbox.confirm({
                    message: "@lang('messages.are_you_sure')",
                    buttons: {
                        confirm: {
                            label: '@lang('labels.yes')',
                            className: 'btn-danger'
                        },
                        cancel: {
                            label: '@lang('labels.no')',
                        }
                    },
                    callback: function (result) {
                        if (result === false) {
                        } else {
                            $('#delete_id').val( $(parent).attr('id').split('_').pop() );
                            $('#delete_form').submit();
                        }
                    }
                });
            });
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