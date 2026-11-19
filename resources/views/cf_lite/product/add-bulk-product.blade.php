@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <style>
        .borderless-input {
            width: 100%;
            border: none;
        }

        .circle-medium{
            width: 200px;
            height: 200px;
            border-radius: 50%;
            padding-top: 50px;
        }

        .select2 {
            width: 30% !important;
            float: right;
        }

        .page-title {
            margin-top: 0px;
        }

        .select2-container--default .select2-selection--single{
            background-color: #ffc0cb;
        }

        .select2-container--default .select2-selection--single:hover .select2-selection__rendered {
            color: #ff1493;
            font-weight: 600;
        }

        .select2-container--default .select2-selection--single:hover {
            border: 1px solid #ff1493;
        }

        .btn-primary {
            background-color: #ff1493;
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
    @include('cf_lite.includes.error-message')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/master-product')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.add') @lang('labels.product')</h2>
            </center>
        </div>
        <div class="col-md-12">
            {!! Form::open(['method' => 'POST', 'action' => 'ProductController@store_bulk_product']) !!}
            <button type="button" class="btn btn-primary pull-right" id="btn-add-more-row" title="@lang('labels.add') @lang('labels.new_row')" style="margin-bottom: 20px;"><span class="glyphicon glyphicon-plus"></span></button>
            <table class="table table-bordered" style="background-color: #fff;">
                <thead>
                <tr>
                    <td colspan="6">
                        {!! Form::select('product_type', $categories, null, ['class' => 'form-control pull-right', 'id' => 'product-types', 'placeholder' => trans('labels.choose_category')]) !!}
                    </td>
                </tr>
                <tr>
                    <th>@lang('labels.indexing')</th>
                    <th>@lang('labels.item_name')</th>
                    <th>@lang('labels.default_price') (@lang('labels.currency'))</th>
                    <th>@lang('labels.is_taxable')</th>
                    <th>@lang('labels.accessibility')</th>
                    <th>@lang('labels.status')</th>
                </tr>
                </thead>
                <tbody id="bulk-product-table-body">
                @for($i = 1; $i <= 10; $i++)
                    <tr>
                        <td>{{$i}}</td>
                        <td>{!! Form::text('item_name['.$i.']', null, ['class' => 'borderless-input form-control']) !!}</td>
                        <td>{!! Form::text('default_price['.$i.']', null, ['class' => 'borderless-input form-control']) !!}</td>
                        <td>
                            <label class="radio-inline">
                                {!! Form::radio('is_taxable['.$i.']', 'Yes', true) !!} @lang('labels.yes')
                            </label>
                            <label class="radio-inline">
                                {!! Form::radio('is_taxable['.$i.']', 'No', false) !!} @lang('labels.no')
                            </label>
                        </td>
                        <td>
                            <label class="radio-inline">
                                {!! Form::radio('accessibility['.$i.']', 'Retail', true) !!} @lang('labels.retail')
                            </label>
                            <label class="radio-inline">
                                {!! Form::radio('accessibility['.$i.']', 'Member', false) !!} @lang('labels.member')
                            </label>
                            <label class="radio-inline">
                                {!! Form::radio('accessibility['.$i.']', 'Reward', false) !!} @lang('labels.reward')
                            </label>
                        </td>
                        <td>
                            <label class="radio-inline">
                                {!! Form::radio('status['.$i.']', 'Active', false) !!} @lang('labels.active')
                            </label>
                            <label class="radio-inline">
                                {!! Form::radio('status['.$i.']', 'Inactive', true) !!} @lang('labels.inactive')
                            </label>
                        </td>
                    </tr>
                @endfor
                </tbody>
            </table>
            {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary pull-right']) !!}
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script type="text/javascript">
        $('#product-types').select2();
    </script>
    <script>
        var counter = parseInt(10);
        $('#btn-add-more-row').on('click', function() {
            counter = counter + 1;
            var html = '<tr>' +
                '<td>' + counter + '</td>' +
                '<td><input type="text" name="item_name[' + counter + ']" class="borderless-input form-control"></td>' +
                '<td><input type="text" name="default_price[' + counter + ']" class="borderless-input form-control"></td>' +
                '<td>' +
                '<label class="radio-inline"><input name="is_taxable[' + counter + ']" type="radio" value="Yes" checked> @lang('labels.yes')</label>' +
                '<label class="radio-inline"><input name="is_taxable[' + counter + ']" type="radio" value="No"> @lang('labels.no')</label>' +
                '</td>' +
                '<td>' +
                '<label class="radio-inline"><input name="accessibility[' + counter + ']" type="radio" value="Retail" checked> @lang('labels.retail')</label>' +
                '<label class="radio-inline"><input name="accessibility[' + counter + ']" type="radio" value="Member"> @lang('labels.member')</label>' +
                '<label class="radio-inline"><input name="accessibility[' + counter + ']" type="radio" value="Reward"> @lang('labels.reward')</label>' +
                '</td>' +
                '<td>' +
                '<label class="radio-inline"><input name="status[' + counter + ']" type="radio" value="Active"> @lang('labels.active')</label>' +
                '<label class="radio-inline"><input name="status[' + counter + ']" type="radio" value="Inactive" checked> @lang('labels.inactive')</label>' +
                '</td>' +
                '</tr>';
            $('#bulk-product-table-body').append(html);
        });
    </script>
@endsection