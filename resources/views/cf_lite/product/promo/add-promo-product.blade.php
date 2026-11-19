@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">

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
    @include('cf_lite.includes.error-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <ol class="breadcrumb pull-right" style="background-color: transparent;">
                    <li><a href="{{url('cf-lite/settings')}}" style="color: deeppink;">@lang('labels.setting')</a></li>
                    <li><a href="{{url('cf-lite/product-setting')}}"
                           style="color: deeppink;">@lang('labels.product_setting')</a></li>
                    <li><a href="{{ url('cf-lite/master-product') }}"
                           style="color: deeppink;">@lang('labels.product')</a></li>
                    <li class="active" style="color: rebeccapurple;"><span>@lang('labels.add_promo')</span></li>
                </ol>
            </div>
            <div class="col-sm-12">
                <a href="{{url('cf-lite/master-product')}}" style="left: 25px; top: 20px;"
                   class="btn btn-default btn-back-to" title="@lang('labels.back')"><span
                            class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.add_promo_product')</h2>
                </center>
            </div>
        </div>
        {!! Form::open(['method' => 'POST', 'action' => 'ProductController@store_promo_product']) !!}
        <div class="row">
            <div class="col-md-10">
                <div class="form-horizontal">
                    <div class="form-group">
                        {!! Form::label('promo_name', trans('labels.promo_name').' : ', ['class' => 'control-label col-md-3']) !!}
                        <div class="col-md-4">
                            {!! Form::text('promo_name', null, ['class' => 'form-control']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('product_type', trans('labels.category').' : ', ['class' => 'control-label col-md-3']) !!}
                        <div class="col-md-4">
                            {!! Form::select('product_type', $categories, null, ['class' => 'form-control select2', 'id' => 'product-types-select']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-10 col-md-offset-1 maspro-add">
                <button type="button" class="btn btn-default pull-right" id="add-item-btn" title="@lang('labels.add_item')"
                        style="margin-bottom: 10px">
                    <span class="fa fa-plus"></span>
                </button>
            </div>
            <div class="col-md-10 col-md-offset-1">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered" id="data-table">
                        <thead>
                        <tr>
                            <th>@lang('labels.image')</th>
                            <th>@lang('labels.item_name')</th>
                            <th>@lang('labels.qty')</th>
                            <th>@lang('labels.delete')</th>
                        </tr>
                        </thead>
                        <tbody id="item-body">
                        @php($index = 1)
                        {!! Form::hidden('index', $index) !!}
                        <tr id="row-{{ $index }}">
                            <td style="text-align: center;">
                                <img id="img-{{ $index }}"
                                     src="{{asset('images/assets/Interface Program Item-30_2.png')}}"
                                     style="width: 75px; height: 75px; object-fit: scale-down;">
                            </td>
                            <td>
                                <select style="width: 100%;" name="item[]" class="form-control select2"
                                        data-placeholder="@lang('labels.select_item')" id="select-item-{{ $index }}">
                                    @foreach($products as $product)
                                        <option value="{{ $product->id }}"
                                                image="{{ $product->product_image }}">{{ $product->item_name }}</option>
                                    @endforeach
                                </select>
                            </td>
                            <td>{!! Form::number('qty[]', null, ['class' => 'form-control', 'min' => '0']) !!}</td>
                            <td style="text-align: center;">
                                <button type="button" class="btn btn-danger btn-remove" id="remove-{{ $index }}">
                                    <span class="fa fa-trash"></span>
                                </button>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1 maspro-add" style="margin-bottom: 15px">
                {!! Form::submit(trans('labels.submit'), ['class' => 'btn btn-success pull-right']) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </section>
@endsection

@section('other-script')
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('.select2').select2();

            $('#add-item-btn').on('click', function () {
                var index = parseInt($('input[name="index"]').val()) + 1;
                var html = '<tr id="row-' + index + '">';
                html += '<td style="text-align: center;"><img id="img-' + index + '" src="{{asset('images/assets/Interface Program Item-30_2.png')}}" style="width: 75px; height: 75px; object-fit: scale-down;"></td>';
                html += '<td>' +
                    '<select style="width: 100%;" name="item[]" class="form-control select2" data-placeholder="@lang('labels.select_item')" id="select-item-' + index + '">';
                @foreach($products as $product)
                    html += '<option value="{{ $product->id }}" image="{{ $product->product_image }}">{{ $product->item_name }}</option>';
                @endforeach
                    html += '</select></td>';
                html += '<td>{!! Form::number('qty[]', null, ['class' => 'form-control', 'min' => '0']) !!}</td>';
                html += '<td style="text-align: center;">' +
                    '<button type="button" class="btn btn-danger btn-remove" id="remove-' + index + '">' +
                    '<span class="fa fa-trash"></span>' +
                    '</button>' +
                    '</td>';
                html += '</tr>';

                $('input[name="index"]').val(index);
                $('#item-body').append(html);
                $('#select-item-' + index).select2();
            });

            $('#item-body').on('change', '.select2', function () {
                var id = $(this).attr('id').split('-').pop();

                if ($('option:selected', this).attr('image') != '') {
                    var img = '{{ asset('images/product_images') }}/' + $('option:selected', this).attr('image');
                    $('#img-' + id).attr('src', img);
                } else {
                    var img = '{{ asset('images/assets/Interface Program Item-30_2.png') }}';
                    $('#img-' + id).attr('src', img);
                }
            });

            $('#item-body').on('click', '.btn-remove', function () {
                var id = $(this).attr('id').split('-').pop();
                console.log(id);
                $('#row-' + id).remove();
            });
        });
    </script>
@endsection