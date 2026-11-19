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

        .value-base {
            display: block;
        }

        .percent-base {
            display: none;
        }

        .btn-primary,
        .voucher-list-add a,
        #edit-voucher-btn {
            background-color: #ff1493;
            color: #ffffff;
            border-color: #ff1493;
        }

        #edit-voucher-btn{
            margin: 20px 0;
        }

        .btn-primary:hover,
        .voucher-list-add a:hover,
        #edit-voucher-btn:hover {
            color: #ff1493;
            background-color: transparent;
            border-color: #ff1493;
        }

        div.dataTables_wrapper div.dataTables_length select {
            margin: 0 10px;
        }

        table th{
            color: #ff1493;
        }

    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/voucher')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">
                        @lang('labels.voucher')
                        @if(!empty($voucher_type))
                            <br>
                            <small>(@lang('labels.type'): {{ucwords($voucher_type->voucher_type)}})</small>
                        @endif
                    </h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                {!! Form::open(['method' => 'GET', 'action' => ['VoucherController@voucher_list_filter', $voucher_type->id]]) !!}
                <div class="form-group">
                    <label class="checkbox-inline">
                        {!! Form::checkbox('in_stock', 'InStock') !!} @lang('labels.in_stock')
                    </label>
                    <label class="checkbox-inline">
                        {!! Form::checkbox('out', 'Out') !!} @lang('labels.out')
                    </label>
                    <label class="checkbox-inline">
                        {!! Form::checkbox('used', 'Used') !!} @lang('labels.used')
                    </label>
                    <label class="checkbox-inline">
                        {!! Form::checkbox('expired', 'Expired') !!} @lang('labels.expired')
                    </label>
                </div>
                <div class="form-group">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="input-group">
                                {!! Form::text('voucher_code', null, ['class' => 'form-control', 'placeholder' => trans('labels.search_voucher_code')]) !!}
                                <span class="input-group-addon"><span class="fa fa-search"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        {!! Form::submit(trans('labels.apply_filter'), ['class' => 'btn btn-primary']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <hr>
        {!! Form::open(['method' => 'PATCH', 'action' => ['VoucherController@update_voucher', $voucher_type->id]]) !!}
        <div class="row">
            <div class="col-md-10 col-md-offset-1 voucher-list-add">
                <a class="btn btn-default pull-right" href="{{url('cf-lite/voucher/add')}}"
                   title="@lang('labels.add_voucher')"><span class="fa fa-plus"></span> @lang('labels.add_voucher')</a>
            </div>
            <div class="col-md-10 col-md-offset-1">
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="data-table">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>@lang('labels.voucher_code')</th>
                                    <th>@lang('labels.valid_date')</th>
                                    <th>@lang('labels.value')</th>
                                    <th>@lang('labels.limit')</th>
                                    <th>@lang('labels.status')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($vouchers as $voucher)
                                    <tr>
                                        <td>
                                            @if($voucher->status == 'Out')
                                                {!! Form::checkbox('voucher['.$voucher->id.']', $voucher->id, false, ['disabled']) !!}
                                            @else
                                                {!! Form::checkbox('voucher['.$voucher->id.']', $voucher->id) !!}
                                            @endif
                                        </td>
                                        <td>{{$voucher->identity_number}}</td>
                                        <td>{{date('d-m-Y', strtotime($voucher->from_date))}} @lang('labels.until') {{date('d-m-Y', strtotime($voucher->thru_date))}}</td>
                                        <td>
                                            @if($voucher->voucher_base == 'value')
                                                <div class="my_rupiah">{{$voucher->value}}</div>
                                            @elseif($voucher->voucher_base == 'percent')
                                                {{$voucher->percent}} %
                                            @endif
                                        </td>
                                        <td>
                                            @if($voucher->voucher_base == 'percent')
                                                <div class="my_rupiah">{{$voucher->value}}</div>
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            @if($voucher->status == 'Used')
                                                <label class="label label-default">{{$voucher->status}}</label>
                                            @else
                                                @if(strtotime($voucher->thru_date) < strtotime(\Carbon\Carbon::now()))
                                                    <label class="label label-danger">@lang('labels.expired')</label>
                                                @else
                                                    @if($voucher->status == 'InStock')
                                                        <label class="label label-success">{{$voucher->status}}</label>
                                                    @elseif($voucher->status == 'Out')
                                                        <label class="label label-info">{{$voucher->status}}</label>
                                                    @endif
                                                @endif
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <button class="btn btn-default" type="button" id="edit-voucher-btn" data-toggle="modal"
                                        data-target="#editVoucherFormModal">@lang('labels.edit_selected')</button>
                                {!! Form::submit(trans('labels.out_selected_voucher'), ['class' => 'btn btn-info', 'name' => 'voucher_out']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="editVoucherFormModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">@lang('labels.edit_voucher')</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-inline">
                                    <div class="form-group">
                                        {!! Form::label('from_date', trans('labels.from_date').': ', ['class' => 'control-label']) !!}
                                        {!! Form::date('from_date', \Carbon\Carbon::tomorrow(), ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-inline">
                                    <div class="form-group">
                                        {!! Form::label('thru_date', trans('labels.thru_date').': ', ['class' => 'control-label']) !!}
                                        {!! Form::date('thru_date', null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-inline">
                                    <div class="form-group">
                                        {!! Form::label('voucher_base', trans('labels.voucher_base').': ', ['class' => 'control-label']) !!}
                                    </div>
                                    <div class="form-group">
                                        <div class="radio-inline">
                                            <label>
                                                {!! Form::radio('voucher_base', 'value', true, ['id' => 'voucher-base-value']) !!} @lang('labels.voucher_base')
                                                : @lang('labels.value')
                                            </label>
                                        </div>
                                        <div class="radio-inline">
                                            <label>
                                                {!! Form::radio('voucher_base', 'percent', false, ['id' => 'voucher-base-percent']) !!} @lang('labels.voucher_base')
                                                : @lang('labels.percent')
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="panel panel-default">
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-inline value-base">
                                                    <div class="form-group">
                                                        {!! Form::label('new_value', trans('labels.new_value').': ', ['class' => 'control-label']) !!}
                                                        {!! Form::text('new_value', '0', ['class' => 'form-control']) !!}
                                                    </div>
                                                </div>
                                                <div class="form-inline percent-base">
                                                    <div class="form-group">
                                                        {!! Form::label('new_percent', trans('labels.new_percent').': ', ['class' => 'control-label']) !!}
                                                        {!! Form::text('new_percent', '0', ['class' => 'form-control']) !!}
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-inline percent-base">
                                                    <div class="form-group">
                                                        {!! Form::label('new_limit', trans('labels.new_limit').': ', ['class' => 'control-label']) !!}
                                                        {!! Form::text('new_limit', '0', ['class' => 'form-control']) !!}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal">@lang('labels.close')</button>
                        <button type="submit" class="btn btn-primary">@lang('labels.save')</button>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
        <div class="row">
            <div class="col-md-12">
                <div class="paging hiden rom">
                    {{$vouchers->links()}}
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

    <script>
        $(document).ready(function () {
            $('input[name="expired"]').on('click', function () {
                if ($(this).prop('checked') == true) {
                    $('input[name="used"]').prop('checked', false);
                }
            });

            $('input[name="used"]').on('click', function () {
                if ($(this).prop('checked') == true) {
                    $('input[name="expired"]').prop('checked', false);
                }
            });

            $('#voucher-base-value').on('click', function () {
                if ($(this).prop('checked') == true) {
                    $('.value-base').show();
                    $('.percent-base').hide();
                }
            });

            $('#voucher-base-percent').on('click', function () {
                if ($(this).prop('checked') == true) {
                    $('.value-base').hide();
                    $('.percent-base').show();
                }
            });
        });
    </script>
@endsection
