@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb pull-right" style="background-color: transparent;">
                    <li><a href="{{url('cf-lite/settings')}}" style="color: deeppink;">@lang('labels.setting')</a></li>
                    <li><a href="{{url('cf-lite/product-setting')}}" style="color: deeppink;">@lang('labels.product_setting')</a></li>
                    <li class="active" style="color: rebeccapurple;"><span>@lang('labels.digital_product')</span></li>
                </ol>
            </div>
            <div class="col-sm-12">
                <a href="{{url('cf-lite/product-setting')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')">
                    <span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span>
                </a>
                <center><h2 class="page-title">@lang('labels.product_digital_inventories')</h2></center>
            </div>
        </div>
        <div class="row" style="padding: 0 20px 20px 20px;">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_content">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="data-table">
                                <thead>
                                <tr>
                                    <th>@lang('labels.indexing')</th>
                                    <th>@lang('labels.item_name')</th>
                                    <th>@lang('labels.default_price')</th>
                                    <th>@lang('labels.qty')</th>
                                    <th>@lang('labels.status')</th>
                                    <th>@lang('labels.action')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php($indexing = 1)
                                @foreach($digitalProduct as $item)
                                    <tr>
                                        <td>{{ $indexing }}</td>
                                        <td>{{ $item->digitalproductinventory_digitalproduct->item_name }}</td>
                                        <td class="my_rupiah">{{ $item->selling_price }}</td>
                                        <td>{{ $item->qty }}</td>
                                        <td>
                                            @if($item->status == true)
                                                <label class="label label-success">Active</label>
                                            @else
                                                <label class="label label-danger">Inactive</label>
                                            @endif
                                        </td>
                                        <td style="text-align: center;">
                                            <a data-toggle="modal" data-target="#editDigProduct" class="btn btn-info edit-btn" id="digproduct-{{$item->id}}">
                                                <span class="btn-text fa fa-edit"></span>
                                            </a>
                                        </td>
                                    </tr>
                                    @php($indexing++)
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>


    <div class="modal fade" id="editDigProduct" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'POST', 'action' => 'DigitalProductController@update_digital_product_inventory', 'id' => 'edit-form']) !!}
                {!! Form::hidden('digproduct_id', null) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">@lang('labels.edit_digital_product_inventory')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('qty', trans('labels.qty').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::text('qty', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('selling_price', trans('labels.default_price').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::text('selling_price', null, ['class' => 'form-control', 'id' => 'selling-price']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('status', trans('labels.status').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <label class="radio-inline">
                                    <input type="radio"  id="active" name="status" value="Active">Active
                                </label>
                                <label class="radio-inline">
                                    <input type="radio" id="inactive" name="status" value="Inactive">Inactive
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="button" name="button" id="btn-submit" class="btn btn-success">@lang('labels.update')</button>
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
            var url = "{{url('cf-lite')}}/";
            init_format();

            $('#data-table').on('click', '.edit-btn', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {id: $(this).attr('id').split('-').pop()};

                $.ajax({
                    type: 'POST',
                    url: url + 'digital-product/get-detail-ajax',
                    data: formData,
                    success: function (data) {
                        $('input[name="digproduct_id"]').val(data.id);
                        $('input[name="qty"]').val(data.qty);
                        $('input[name="selling_price"]').val(rupiah_formater(data.selling_price));
                        if (data.status == true) {
                            $('#active').prop("checked", true);
                        } else {
                            $('#inactive').prop("checked", true);
                        }
                    },
                    error: function (data) {
                        console.log('Error : ', data);
                    }
                });
            });

            $('input[name="selling_price"]').on('keyup', function () {
                if ($(this).val() == "") {
                    var sellingPrice = 0;
                } else {
                    var sellingPrice = parseInt(rupiah_format_destroyer($('input[name="selling_price"]').val()));
                }
                $('input[name="selling_price"]').val(rupiah_formater(sellingPrice));
            });

            $('#btn-submit').on('click', function () {
                var sellingPrice = parseInt(rupiah_format_destroyer($('input[name="selling_price"]').val()));
                $('input[name="selling_price"]').val(sellingPrice);

                $('#edit-form').submit();
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