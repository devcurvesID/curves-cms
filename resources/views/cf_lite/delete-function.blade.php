@extends('cf_lite.layouts.staff-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">

    <style>
        .select2 {
            width: 100% !important;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/dashboard')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.delete_function')</h2>
            </center>
        </div>
        <div class="col-md-12">
            <div class="row">
                {!! Form::open(['method' => 'GET']) !!}
                <div class="form-group col-md-8">
                    {!! Form::label('club', trans('labels.club'), ['class' => 'col-md-2 control-label']) !!}
                    <div class="col-md-6">
                        {!! Form::select('club', $clubs, null, ['class' => 'form-control select2', 'placeholder'=> trans('labels.choose') .' '.trans('labels.club')]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-8">
                    {!! Form::label('category', trans('labels.category'), ['class' => 'col-md-2 control-label']) !!}
                    <div class="col-md-6">
                        {!! Form::select('category', array("product" => "Product", "membership" => "Membership", "locker" => "Locker", "user" => "User"), null, ['class' => 'form-control select2', 'placeholder'=> trans('labels.choose') .' '.trans('labels.category')]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-8">
                    {!! Form::label('date', trans('labels.date'), ['class' => 'col-md-2 control-label']) !!}
                    <div class="col-md-6">
                        {!! Form::date('from_date', $fromdate , ['class' => 'form-control filter-date','placeholder'=>@trans('labels.from_date')]) !!}
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="form-group col-md-8">
                    {!! Form::label('thru_date', trans('labels.thru_date'), ['class' => 'col-md-2 control-label']) !!}
                    <div class="col-md-6">
                        {!! Form::date('thru_date', $thrudate , ['class' => 'form-control filter-date','placeholder'=>@trans('labels.from_date'), 'id'=>'filt-dari']) !!}
                    </div>
                    <button type="submit" class="btn btn-success fa fa-search" title="@lang('labels.search')" id="search">@lang('labels.search')</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive" style="background-color: #fff;">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                            <tr>
                                <th>@lang('labels.indexing')</th>
                                @if(!empty($_GET['category']))
                                    @if($_GET['category'] == 'product' || $_GET['category'] == 'membership' || $_GET['category'] == 'locker')
                                        <th>@lang('labels.payment_number')</th>
                                        <th>@lang('labels.payment_date')</th>
                                    @endif
                                @endif
                                <th>@lang('labels.category')</th>
                                <th>@lang('labels.name')</th>
                                <th>@lang('labels.club')</th>
                                <th>@lang('labels.action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($datas))
                                @foreach($datas as $data)
                                    <tr>
                                    @if(!empty($_GET['club']) && !empty($_GET['category']))
                                        <td></td>
                                        @if($_GET['category'] == 'product')
                                            <td>{{ $data->payment_number }}</td>
                                            <td>{{ \Carbon\Carbon::parse($data->payment_date)->format('Y-m-d') }}</td>
                                            <td>{{ ucfirst($_GET['category']) }}</td>
                                            <td>{{ ($data->member_id != null) ? $data->productpayment_user->name : 'Guest' }}</td>
                                            <td>{{ $data->productpayment_club->club_name }}</td>
{{--                                        @elseif($_GET['category'] == 'membership')--}}
{{--                                            <td>{{ $data->memberpayment_user->user_club->club_name }}</td>--}}
{{--                                            <td>{{ $data->memberpayment_user->name }}</td>--}}
{{--                                            <td>{{ $data->payment_number }}</td>--}}
{{--                                            <td>{{ \Carbon\Carbon::parse($data->payment_date)->format('Y-m-d') }}</td>--}}
{{--                                        @elseif($_GET['category'] == 'locker')--}}
{{--                                            <td>{{ $data->productpayment_club->club_name }}</td>--}}
{{--                                            <td>{{ $data->memberpayment_user->name }}</td>--}}
{{--                                            <td>{{ $data->payment_number }}</td>--}}
{{--                                            <td>{{ \Carbon\Carbon::parse($data->payment_date)->format('Y-m-d') }}</td>--}}
{{--                                        @elseif($_GET['category'] == 'user')--}}
{{--                                            <td>{{ $data->user_club->club_name }}</td>--}}
{{--                                            <td>{{ $data->name }}</td>--}}
                                        @endif
                                    @endif
                                        <td style="text-align: center;">
                                            <button type="button" data-toggle="modal" data-target="#modalDelete" class="btn btn-danger btn-get-data fa fa-trash" id="{{ $_GET['category'] }}-{{ $data->id }}" title="@lang('labels.delete')"></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="modalDelete" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'POST', 'action' => 'DeleteController@delete', 'id' => 'delete']) !!}
                {!! Form::hidden('id', '') !!}
                {!! Form::hidden('status', '') !!}
                <div class="modal-header" style="text-align: center;">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title"  style="text-transform: uppercase; font-size: 20px; font-weight: bold;"></h4>
                </div>
                <div class="modal-body">

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" class="btn btn-danger">@lang('labels.delete')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

@endsection

@section('other-script')
    <script src="{{asset('js/jquery-3_2_1.min.js')}}"></script>
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script src="{{asset('js/my_js.js')}}"></script>
    <script>
        $(document).ready(function () {
            var t = $('#data-table').DataTable({
                "columnDefs": [{
                    "searchable": true,
                    "orderable": true,
                    "targets": 0,
                }],
            });

            t.on('order.dt search.dt', function () {
                t.column(0, {search: 'applied', order: 'applied'}).nodes().each(function (cell, i) {
                    cell.innerHTML = i + 1;
                });
            }).draw();

            $('.select2').select2();
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#data-table').on('click', '.btn-get-data', function () {
                var id = $(this).attr('id').split('-').pop();
                var category = $(this).attr('id').split('-');
                var title = '@lang("labels.delete") - '+category[0];

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var type = 'POST';
                var url = '{{ url('cf-lite/delete-function/get-detail-ajax') }}/'+ id;

                var formData = {
                    category: category[0],
                };

                $.ajax({
                    type: type,
                    url: url,
                    data: formData,
                    success: function (data) {
                        $('input[name="id"]').val(data.header.id);
                        $('input[name="status"]').val(category[0]);
                        var discount = 0;
                        var html = '';
                        if(category[0] == 'product') {
                            html += '<div class="row" style="font-size:18px;">' +
                                        '<div class="col-md-12 form-group">' +
                                            '{{ Form::label('name', trans('labels.name'), ['class' => 'control-label col-md-4']) }}' +
                                            '<div class="col-md-4 form-group"><label class="control-label">'+ data.header.member_name +'</label></div>' +
                                        '</div>' +
                                        '<div class="col-md-12 form-group">' +
                                            '{{ Form::label('payment_number', trans('labels.payment_number'), ['class' => 'control-label col-md-4']) }}' +
                                            '<div class="col-md-4 form-group"><label class="control-label">'+ data.header.payment_number +'</label></div>' +
                                        '</div>' +
                                    '</div>' +
                                    '<div class="row">' +
                                        '<div class="table-responsive">' +
                                            '<table class="table table-hover table-bordered">' +
                                                '<thead>' +
                                                '<tr>' +
                                                    '<th>@lang("labels.product_name")</th>' +
                                                    '<th>@lang("labels.price")</th>' +
                                                    '<th>@lang("labels.qty")</th>' +
                                                    '<th>@lang("labels.subtotal")</th>' +
                                                    '<th>@lang("labels.discount")</th>' +
                                                '</tr>' +
                                                '</thead>' +
                                                '<tbody>';
                                    $.each(data.detail, function (i, item) {
                                        html += '<tr>' +
                                                    '<td>'+ item.product_name +'</td>' +
                                                    '<td>'+ rupiah_formater(item.price) +'</td>' +
                                                    '<td>'+ item.qty +'</td>' +
                                                    '<td>'+ rupiah_formater(item.price * item.qty) +'</td>' +
                                                    '<td>'+ rupiah_formater(item.discount_value) +'</td>' +
                                                '</tr>';
                                        discount += item.discount_value;
                                    });
                            html +=             '<tr>' +
                                                    '<td colspan="3">@lang('labels.subtotal')</td>' +
                                                    '<td colspan="2">'+ rupiah_formater(data.header.total) +'</td>' +
                                                '</tr>' +
                                                '<tr>' +
                                                    '<td colspan="3">@lang('labels.total_discount_item')</td>' +
                                                    '<td colspan="2">'+ rupiah_formater(discount) +'</td>' +
                                                '</tr>' +
                                                '<tr>' +
                                                    '<td colspan="3">@lang('labels.total')</td>' +
                                                    '<td colspan="2">'+ rupiah_formater(data.header.total - discount) +'</td>' +
                                                '</tr>' +

                                                '</tbody>' +
                                            '</table>' +
                                        '</div>' +
                                    '</div>';
                        }
                        $('.modal-title').html(title);
                        $('.modal-body').html(html);
                    },
                    error: function (data) {
                        alert(data.responseText);
                        console.log('Error : ', data);
                    }
                });
            });
        });
    </script>
@endsection