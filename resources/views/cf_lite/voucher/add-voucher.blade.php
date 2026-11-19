@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <style>
        .borderless-input {
            width: 100%;
            border: none;
        }

        .circle-medium {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            padding-top: 50px;
        }

        .select2 {
            width: 100% !important;
            float: right;
        }

        .page-title {
            margin-top: 0px;
        }

        .select2-container--default .select2-selection--single {
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
            color: #ffffff;
            border-color: #ff1493;
        }

        .btn-primary:hover {
            color: #ff1493;
            background-color: transparent;
            border-color: #ff1493;
        }

        .panel {
            padding: 15px;
        }

        .panel-body {
            padding: 0px;
        }

        table th:first-child{
            width: 30px;
        }

        table th {
            color: #ff1493;
        }

        #table-voucher{
            width: 70%;
        }

    </style>
@endsection

@section('content')
    @include('cf_lite.includes.error-message')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/voucher')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.add') @lang('labels.voucher')</h2>
            </center>
        </div>
        <div class="col-md-10 col-md-offset-1">
            {!! Form::open(['method' => 'POST', 'action' => 'VoucherController@store_voucher']) !!}
            <div class="form-horizontal">
                <div class="form-group">
                    <div class="col-md-2">
                        {!! Form::select('type_id', $voucher_types, null, ['class' => 'form-control pull-right', 'placeholder' => trans('labels.choose_voucher_type')]) !!}
                    </div>
                    {!! Form::label('from_date', trans('labels.date').': ', ['class' => 'control-label col-md-1 col-md-offset-2']) !!}
                    <div class="col-md-2">
                        {!! Form::date('from_date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                    </div>
                    {!! Form::label('thru_date', trans('labels.until'), ['class' => 'control-label col-md-1']) !!}
                    <div class="col-md-2">
                        {!! Form::date('thru_date', null, ['class' => 'form-control']) !!}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="row">
                    <div class="panel panel-default">
                        <div class="panel-header">
                            <div class="radio">
                                <label>
                                    {!! Form::radio('voucher_base', 'value', true, ['id' => 'voucher-base-value']) !!} @lang('labels.voucher_base')
                                    : @lang('labels.value')
                                </label>
                            </div>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                {!! Form::label('value', trans('labels.value').' ('.trans('labels.currency').')', ['class' => 'control-label']) !!}
                                {!! Form::text('value', '0', ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-header">
                        <div class="radio">
                            <label>
                                {!! Form::radio('voucher_base', 'percent', false, ['id' => 'voucher-base-percent']) !!} @lang('labels.voucher_base')
                                : @lang('labels.percent')
                            </label>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="form-group">
                            {!! Form::label('percent', trans('labels.percent').' (%)', ['class' => 'control-label']) !!}
                            {!! Form::text('percent', '0', ['class' => 'form-control', 'disabled']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('limit', trans('labels.limit').' ('.trans('labels.currency').')', ['class' => 'control-label']) !!}
                            {!! Form::text('limit', '0', ['class' => 'form-control', 'disabled']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-9 col-md-offset-1">
                <button type="button" class="btn btn-primary pull-right" id="btn-add-more-row"
                        title="@lang('labels.add') @lang('labels.new_row')" style="margin-bottom: 20px;"><span
                            class="glyphicon glyphicon-plus"></span></button>
            </div>
            <table id="table-voucher" class="table table-bordered" style="background-color: #fff;">
                <thead>
                <tr>
                    <th>@lang('labels.indexing')</th>
                    <th>@lang('labels.voucher_code')</th>
                </tr>
                </thead>
                <tbody id="bulk-voucher-table-body">
                @for($i = 1; $i <= 10; $i++)
                    <tr>
                        <td>{{$i}}</td>
                        <td>{!! Form::text('voucher_code['.$i.']', null, ['class' => 'borderless-input form-control']) !!}</td>
                    </tr>
                @endfor
                </tbody>
            </table>
            <div class="col-md-9 col-md-offset-1">
            {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary pull-right']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script type="text/javascript">
        $('select[name="type_id"]').select2();
    </script>
    <script>
        var counter = parseInt(10);
        $('#btn-add-more-row').on('click', function () {
            counter = counter + 1;
            var html = '<tr>' +
                '<td>' + counter + '</td>' +
                '<td><input type="text" name="voucher_code[' + counter + ']" class="borderless-input form-control"></td>' +
                '</tr>';
            $('#bulk-voucher-table-body').append(html);
        });
    </script>
    <script>
        $(document).ready(function () {
            $('#voucher-base-value').on('click', function () {
                $('input[name="percent"]').prop('disabled', true);
                $('input[name="limit"]').prop('disabled', true);
                $('input[name="value"]').prop('disabled', false);
            });

            $('#voucher-base-percent').on('click', function () {
                $('input[name="value"]').prop('disabled', true);
                $('input[name="percent"]').prop('disabled', false);
                $('input[name="limit"]').prop('disabled', false);
            });
        })
    </script>
@endsection