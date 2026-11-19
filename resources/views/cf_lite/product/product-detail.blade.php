@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <style>
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

        .page-title {
            margin-top: 20px;
        }

        table#box-1 {
            margin-top: 25px;
        }

        table td:nth-child(2) {
            font-size: 18px;
            font-weight: 600;
        }

        table#box-1 td,
        table#box-2 td,
        table#box-2-1 td,
        table#box-3 td,
        table#box-3-1 td {
            padding: 10px;
        }

        table#box-2 td:first-child {
            width: 115px;
        }

        table#box-3 td:first-child {
            width: 180px;
        }

        table td:first-child {
            color: #ff1493;
        }

        .section-product-category {
            margin-top: 25px;
        }

        .section-product-category h4,
        .section-product-set-global-discount h3 {
            line-height: 40px;
            font-size: 27px;
            font-weight: 600;
            color: #6752a5;
            background-color: transparent;
            border-color: transparent;
        }

        .btn-primary {
            background-color: #ff1493;
            color: #ffffff;
            transition: 0.2s;
            border-color: #ff1493;
        }

        .btn-primary:hover {
            color: #ff1493;
            background-color: transparent;
            border-color: #ff1493;
        }


    </style>
@endsection

@section('content')
    <section id="product-detail">
        @include('cf_lite.includes.flash-message')
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb pull-right" style="background-color: transparent;">
                    <li><a href="{{url('cf-lite/settings')}}" style="color: deeppink;">@lang('labels.setting')</a></li>
                    <li><a href="{{url('cf-lite/product-setting')}}" style="color: deeppink;">@lang('labels.product_setting')</a></li>
                    <li><a href="{{url('cf-lite/master-product')}}" style="color: deeppink;">@lang('labels.product')</a></li>
                    <li class="active" style="color: rebeccapurple;"><span>@lang('labels.product_detail')</span></li>
                </ol>
            </div>
            <div class="col-sm-12">
                <a href="{{url('cf-lite/master-product')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.product_detail')</h2>
                </center>
            </div>
        </div>

        {!! Form::model($product, ['method' => 'PATCH', 'action' => ['ProductController@update_product', $product->id], 'files' => 'true']) !!}
        {!! Form::hidden('attribute', $product->id) !!}
        <div class="row">
            <div class="col-md-2 col-md-offset-1">
                @if(!empty($product->product_image))
                    <img role="button" id="image_preview" class="img-rounded"
                         src="{{asset('images/product_images/'.$product->product_image)}}"
                         style="width: 150px; height: 150px; object-fit: cover;">
                @else
                    <img role="button" id="image_preview" class="img-rounded" src="{{asset('images/assets/Interface Program Item-30_2.png')}}"
                         style="width: 150px; height: 150px; object-fit: scale-down;">
                @endif
                <div class="form-group hidden">
                    {!! Form::file('product_image', ['class' => 'file_multi_video']) !!}
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <table id="box-1">
                        <tbody>
                        <tr>
                            <td>{!! Form::label('item_name', trans('labels.product_name'), ['class' => 'control-label']) !!}</td>
                            <td>:</td>
                            <td>{!! Form::text('item_name', null, ['class' => 'form-control']) !!}</td>
                        </tr>
                        <tr>
                            <td>{!! Form::label('default_price', trans('labels.default_price'), ['class' => 'control-label']) !!}</td>
                            <td>:</td>
                            <td>{!! Form::text('default_price', null, ['class' => 'form-control']) !!}</td>
                        </tr>
                        <tr>
                            <td>{!! Form::label('consignment_price', trans('labels.consignment_price'), ['class' => 'control-label']) !!}</td>
                            <td>:</td>
                            <td>{!! Form::text('consignment_price', null, ['class' => 'form-control']) !!}</td>

                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1 section-product-category">
                <h4>@lang('labels.category'): </h4>
                @if(!empty($categories))
                    @foreach($categories as $category)
                        <div class="product-category-box">
                            @if(!empty($category->icon))
                                <img src="{{asset('images/product-images/'.$category->icon)}}"
                                     style="width: 14px; height: 14px;">
                            @else
                                <div class="fa fa-shopping-bag"></div>
                            @endif
                            <span style="margin-left: 7px;">
                                <label class="radio-inline">
                                    @if($category->id == $product->product_type_id)
                                        {!! Form::radio('product_type', $category->id, true) !!} {{$category->type}}
                                    @else
                                        {!! Form::radio('product_type', $category->id, false) !!} {{$category->type}}
                                    @endif
                                </label>
                            </span>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
        <hr>
        @if($product->is_promo)
            <div class="col-md-10 col-md-offset-1">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="data-table">
                        <thead>
                        <tr>
                            <th>@lang('labels.image')</th>
                            <th>@lang('labels.item_name')</th>
                            <th>@lang('labels.qty')</th>
                            <th>@lang('labels.edit')</th>
                        </tr>
                        </thead>
                        <tbody id="item-body">
                        @foreach($product->product_promoproduct as $detail)
                            <tr>
                                <td style="text-align: center;">
                                    @if(!empty($detail->productpromo_product->product_image))
                                        <img id="img-{{ $detail->id }}" role="button" class="img-rounded" src="{{asset('images/product_images/'.$detail->productpromo_product->product_image)}}" style="width: 75px; height: 75px; object-fit: scale-down;">
                                    @else
                                        <img role="button" id="img-{{ $detail->id }}" class="img-rounded" src="{{asset('images/assets/Interface Program Item-30_2.png')}}" style="width: 75px; height: 75px; object-fit: scale-down;">
                                    @endif
                                </td>
                                <td>
                                    <select style="width: 100%;" name="item" class="form-control select2" id="item-{{ $detail->id }}" data-placeholder="@lang('labels.select_item')">
                                        @foreach($allproduct as $prod)
                                            @if($detail->product_id == $prod->id)
                                                <option value="{{ $prod->id }}" image="{{ $prod->product_image }}" selected>{{ $prod->item_name }}</option>
                                            @else
                                                <option value="{{ $prod->id }}" image="{{ $prod->product_image }}">{{ $prod->item_name }}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </td>
                                <td>
                                    {!! Form::number('qty', $detail->qty, ['class' => 'form-control', 'min' => '0', 'id' => 'qty-'.$detail->id]) !!}
                                </td>
                                <td style="text-align: center;">
                                    <button type="button" class="btn btn-info btn-edit" id="edit-{{ $detail->id }}">
                                        <span class="fa fa-save"></span>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <table id="box-2">
                    <tbody>
                    <tr>
                        <td>{!! Form::label('is_taxable', trans('labels.taxable'), ['class' => 'control-label']) !!}</td>
                        <td>:</td>
                        <td>
                            <label class="radio-inline">
                                {!! Form::radio('is_taxable', 'Yes') !!} @lang('labels.yes')
                            </label>
                        </td>
                        <td>
                            <label class="radio-inline">
                                {!! Form::radio('is_taxable', 'No') !!} @lang('labels.no')
                            </label>
                        </td>
                    </tr>

                    <tr>
                        <td>{!! Form::label('accessibility', trans('labels.accessibility'), ['class' => 'control-label ']) !!}</td>
                        <td>:</td>
                        <td>
                            <label class="radio-inline">
                                {!! Form::radio('accessibility', 'Retail') !!} @lang('labels.retail')
                            </label>
                        </td>
                        <td>
                            <label class="radio-inline">
                                {!! Form::radio('accessibility', 'Member') !!} @lang('labels.member')
                            </label>
                        </td>
                        <td>
                            <label class="radio-inline">
                                {!! Form::radio('accessibility', 'Reward') !!} @lang('labels.reward')
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <td>{!! Form::label('status', trans('labels.status'), ['class' => 'control-label ']) !!}</td>
                        <td>:</td>
                        <td>
                            <label class="radio-inline">
                                {!! Form::radio('status', 'Active') !!} @lang('labels.active')
                            </label>
                        </td>
                        <td>
                            <label class="radio-inline">
                                {!! Form::radio('status', 'Inactive') !!} @lang('labels.inactive')
                            </label>
                        </td>
                    </tr>
                    </tbody>
                </table>
                <table id="box-2-1">
                    <tbody>
                    <tr class="form-group">
                        <td valign="top">{!! Form::label('notes', trans('labels.description'), ['class' => 'control-label']) !!}</td>
                        <td valign="top">:</td>
                        <td>{!! Form::textarea('notes', null, ['class' => 'form-group form-control', 'rows' => '4']) !!}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-8 col-md-offset-3">
                {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary btn-lg pull-right']) !!}
            </div>
        </div>
        {!! Form::close() !!}
        <hr>
        <div class="row">
            <div class="col-md-10 col-md-offset-1 section-product-set-global-discount">
                <h3>@lang('labels.set_global_discount')</h3>
                @if(!empty($last_discount))
                    @if((strtotime($last_discount->from_date) <= strtotime(\Carbon\Carbon::now())) && (strtotime($last_discount->thru_date) >= strtotime(\Carbon\Carbon::now())))
                        <h3><span class="label label-success">@lang('labels.ongoing')
                                ({{date('d-m-Y', strtotime($last_discount->from_date))}} @lang('labels.until') {{date('d-m-Y', strtotime($last_discount->thru_date))}}
                                )</span></h3>
                    @elseif((strtotime($last_discount->from_date) >= strtotime(\Carbon\Carbon::now())) && (strtotime($last_discount->thru_date) >= strtotime(\Carbon\Carbon::now())))
                        <h3><span class="label label-info">@lang('labels.starting')
                                ({{date('d-m-Y', strtotime($last_discount->from_date))}} @lang('labels.until') {{date('d-m-Y', strtotime($last_discount->thru_date))}}
                                )</span></h3>
                    @else
                        <h3><span class="label label-default">@lang('labels.expired')
                                ({{date('d-m-Y', strtotime($last_discount->thru_date))}})</span></h3>
                    @endif
                @endif
                <div class="col-md-12">
                    <div class="row">
                        {!! Form::model($last_discount, ['method' => 'PATCH', 'action' => ['ProductController@update_product_discount_global', $product->id]]) !!}
                        <table id="box-3">
                            <tr>
                                <td>{!! Form::label('discount_category', trans('labels.accessibility'), ['class' => 'control-label']) !!}</td>
                                <td>:</td>
                                @php
                                    if ($product->accessibility == 'Member'):
                                        $retail_disability = 'disabled';
                                        $member_disability = '';
                                        $retail = false;
                                        $member = true;
                                    elseif ($product->accessibility == 'Retail'):
                                        $retail_disability = '';
                                        $member_disability = '';
                                        $retail = true;
                                        $member = false;
                                    else:
                                        $retail_disability = 'disabled';
                                        $member_disability = 'disabled';
                                        $retail = false;
                                        $member = false;
                                    endif;
                                @endphp
                                <td>
                                    <label class="radio-inline">
                                        {!! Form::radio('discount_category', 'Retail', $retail, [$retail_disability]) !!} @lang('labels.retail')
                                    </label>
                                </td>
                                <td>
                                    <label class="radio-inline">
                                        {!! Form::radio('discount_category', 'Member', $member, [$member_disability]) !!} @lang('labels.member')
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td>{!! Form::label('discount_base', trans('labels.discount_base'), ['class' => 'control-label']) !!}</td>
                                <td>:</td>
                                <td>
                                    <label class="radio-inline">
                                        {!! Form::radio('discount_base', 'percent', true, ['id' => 'discount-base-percent']) !!} @lang('labels.discount_percent')
                                    </label>
                                </td>
                                <td>
                                    <label class="radio-inline">
                                        {!! Form::radio('discount_base', 'value', false, ['id' => 'discount-base-value']) !!} @lang('labels.discount_value')
                                    </label>
                                </td>
                            </tr>
                        </table>
                        <table id="box-3-1">
                            <tr>
                                <td>{!! Form::label('discount_percent', trans('labels.discount_percent').' (%)', ['class' => 'control-label']) !!}</td>
                                <td>:</td>
                                <td>{!! Form::text('discount_percent', null, ['class' => 'form-control', 'disabled']) !!}</td>
                            </tr>
                            <tr>
                                <td>{!! Form::label('discount_value', trans('labels.discount_value').'('.trans('labels.currency').')', ['class' => 'control-label']) !!}</td>
                                <td>:</td>
                                <td>{!! Form::text('discount_value', null, ['class' => 'form-control', 'disabled']) !!}</td>
                            </tr>
                            <tr>
                                <td>{!! Form::label('from_date', trans('labels.from_date'), ['class' => 'control-label']) !!}</td>
                                <td>:</td>
                                <td>{!! Form::date('from_date', null, ['class' => 'form-control']) !!}</td>
                            </tr>
                            <tr>
                                <td>{!! Form::label('thru_date', trans('labels.thru_date'), ['class' => 'control-label']) !!}</td>
                                <td>:</td>
                                <td>
                                    {!! Form::date('thru_date', null, ['class' => 'form-control']) !!}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary btn-md pull-right']) !!}
                                </td>
                            </tr>
                        </table>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('other-script')
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script>
        $('#image_preview').on('click', function () {
            $('.file_multi_video').click();
        });

        $(document).on('change', '.file_multi_video', function (evt) {
            var $source = $('#image_preview');
            $source[0].src = URL.createObjectURL(this.files[0]);
            $source.load();
        });
    </script>

    <script>
        $(document).ready(function () {
            $('.select2').select2();

            if ($('#discount-base-percent').prop('checked') == true) {
                $('input[name="discount_percent"]').attr('disabled', false);
                $('input[name="discount_value"]').attr('disabled', true);
            }

            if ($('#discount-base-value').prop('checked') == true) {
                $('input[name="discount_percent"]').attr('disabled', true);
                $('input[name="discount_value"]').attr('disabled', false);
            }

            $('#discount-base-percent').on('click', function () {
                if ($('#discount-base-percent').prop('checked') == true) {
                    $('input[name="discount_percent"]').attr('disabled', false);
                    $('input[name="discount_value"]').attr('disabled', true);
                }
            });

            $('#discount-base-value').on('click', function () {
                if ($('#discount-base-value').prop('checked') == true) {
                    $('input[name="discount_percent"]').attr('disabled', true);
                    $('input[name="discount_value"]').attr('disabled', false);
                }
            });

            $('#data-table').on('change', '.select2', function () {
                var id = $(this).attr('id').split('-').pop();
                if ($('option:selected', this).attr('image') != '') {
                    var item = $('option:selected', this).attr('image');
                    var img = '{{ asset('images/product_images') }}/' + item;
                    $('#img-' + id).attr('src', img);
                } else {
                    var img = '{{ asset('images/assets/Interface Program Item-30_2.png') }}';
                    $('#img-' + id).attr('src', img);
                }
            });

            $('#data-table').on('click', '.btn-edit', function () {
                var url = "{{url('cf-lite')}}/";
                var prodpromo_id = $(this).attr('id').split('-').pop()
                var new_item = $('#item-'+prodpromo_id).val();
                var qty = $('#qty-'+prodpromo_id).val();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    promoid: prodpromo_id,
                    newitem: new_item,
                    qty: qty
                };

                var type = 'POST';
                var my_url = url + 'update-promo-product';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    success: function (data) {
                        console.log(data);
                    },
                    error: function (data) {
                        console.log('Error : ', data);
                    }
                });
            });
        });
    </script>
@endsection