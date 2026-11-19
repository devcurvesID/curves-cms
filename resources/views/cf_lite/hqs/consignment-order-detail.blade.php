@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/payment.css')}}" rel="stylesheet">
    <style>
        body{
            color: black;
        }
        .column-no-border, .column-no-border td {
            border: none !important;
        }
        .modal-dialog, .modal-dialog label{
            color: #414042;
        }
        table{
            color: #414042;
        }
    </style>

    <style>
        #logo-print, #ttd-print {
            display: none;
        }

        #logo-print img {
            width: 100px;
            height: auto;
            margin-bottom: 30px;
        }

        #ttd-print h4 {
            margin-top: 20px;
            font-size: 12px;
            color: #414042;
        }

        .select2{
            width: 100% !important;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-4" id="logo-print">
            <img src="{{asset('images/Curves_Logo_Purple_Amaze.png')}}">
        </div>
        <div class="col-sm-12">
            <a href="{{url('cf-lite/consignment-order')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.consignment_order_detail')</h2>
            </center>
            <a id="my-btn-print" class="btn btn-primary pull-right"
               style="top: 5px; right: 25px; font-size: 45px; border-radius: 50%; padding: 20px;"
               title="@lang('labels.print_invoice')"><i class="fa fa-print"></i>
            </a>
            <a id="delivery-order-btn" class="btn btn-primary pull-right" href="{{ url('cf-lite/consignment-order/detail/'.$co->id.'/do') }}"
               style="top: 5px; right: 25px; font-size: 45px; border-radius: 50%; padding: 20px; margin-right: 10px;"
               title="@lang('labels.delivery_order')"><i class="fa fa-road"></i>
            </a>
            @if($co->delivery_order != true)
                <a id="edit-order-btn" class="btn btn-primary pull-right" data-toggle="modal" data-target="#edit" style="top: 5px; right: 25px; font-size: 45px; border-radius: 50%; padding: 20px; margin-right: 10px;"
                   title="@lang('labels.edit')"><i class="fa fa-edit"></i>
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-xs-6">
            <table class="member-detail">
                <tr>
                    <td id="member-detail-1">@lang('labels.from')</td>
                    <td>:</td>
                    <td>{{ $co->co_hq->club_name }}</td>
                </tr>
                <tr>
                    <td id="member-detail-2">@lang('labels.to')</td>
                    <td>:</td>
                    <td>{{ $co->co_club->club_name }}</td>
                </tr>
                <tr>
                    <td id="member-detail-3">@lang('labels.order_number')</td>
                    <td>:</td>
                    <td>{{ $co->order_number }}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6 col-xs-6">
            <table class="member-detail">
                <tr>
                    <td>@lang('labels.date')</td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::parse($co->order_date)->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <td>@lang('labels.delivery_order')</td>
                    <td>:</td>
                    <td>{{ ($co->delivery_order) ? trans('labels.printed') : trans('labels.not_printed') }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>@lang('labels.indexing')</th>
                            <th>@lang('labels.product')</th>
                            <th>@lang('labels.qty')</th>
                            <th>@lang('labels.price')</th>
                            <th>@lang('labels.subtotal')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @php($indexing = 0)
                    @foreach($co->co_codetail as $codetail)
                        @php($indexing++)
                        <tr>
                            <td>{{ $indexing }}</td>
                            <td>{{ $codetail->codetail_product->item_name }}</td>
                            <td>{{ $codetail->qty }}</td>
                            <td class="my_rupiah">{{ $codetail->price_per_pcs }}</td>
                            <td class="my_rupiah">{{ $codetail->qty * $codetail->price_per_pcs }}</td>
                        </tr>
                    @endforeach
                        <tr>
                            <td colspan="4" class="text-right">@lang('labels.subtotal')</td>
                            <td class="my_rupiah">{{ $co->subtotal }}</td>
                        </tr>
                        <tr>
                            <td colspan="4" class="text-right">@lang('labels.discount')</td>
                            <td class="my_rupiah">{{ $co->discount }}</td>
                        </tr>
                        <tr>
                            <th colspan="4" class="text-right">@lang('labels.total')</th>
                            <th class="my_rupiah">{{ $co->total }}</th>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-4" id="ttd-print">
            <h4>@lang('labels.staff_signature'):</h4>
            <h4 style="margin-top: 50px;">@lang('labels.staff_name')</h4>
        </div>
    </div>

    <!--Modal-->
    <div class="modal fade" id="edit" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'PATCH', 'action' => ['ConsignmentOrderController@update_consignment_order_detail',$co->id]]) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">@lang('labels.edit_consignment_order')</h4>
                </div>
                <div class="modal-body">
                    <div class="row form-inline" style="margin-left: 5px; margin-bottom: 20px;">
                        {!! Form::label('order_date', trans('labels.date'), ['class' => 'control-label', 'style' => 'margin-right: 20px;']) !!}
                        {!! Form::date('order_date', \Carbon\Carbon::parse($co->order_date), ['class' => 'form-control']) !!}
                    </div>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>@lang('labels.product')</th>
                                    <th>@lang('labels.qty')</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($co->co_codetail as $codetail)
                                <tr>
                                    {!! Form::hidden('id['.$codetail->id.']', $codetail->id) !!}
                                    <td>{{ $codetail->codetail_product->item_name }}</td>
                                    <td>
                                        {{ Form::text('qty['.$codetail->id.']', $codetail->qty, ['class' => 'form-control', 'style' => 'width: 100px;']) }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" name="submit" class="btn btn-success">@lang('labels.update')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            init_format();

            $('#my-btn-print').on('click', function () {
                $('.left-sidebar').addClass('hidden');
                $('.nav-tabs').addClass('hidden');
                $('#floating-button').addClass('hidden');
                $('#manajemenkeanggotaan').addClass('hidden');
                $('#my-btn-print').addClass('hidden');
                $('.page-title').addClass('hidden');
                $('.btn-back-to').addClass('hidden');
                $('#edit-order-btn').addClass('hidden');
                $('#delivery-order-btn').addClass('hidden');
                $('.member-detail').css('font-size', '12');
                $('.member-detail td').css('padding', '3');
                $('.box-menu').css('border-image', 'none');
                $('#member-detail-1').text('@lang('labels.name')');
                $('#member-detail-2').text('@lang('labels.indexing')');
                $('#member-detail-3').text('@lang('labels.type')');
                $('#logo-print').show();
                $('#ttd-print').show();
                $('.table-bordered').css('margin-top', '25');
                $('td').css({'padding-top' : '3px', 'padding-bottom' : '3px', 'font-size' : '11px'});
                $('th').css({'padding-top' : '3px', 'padding-bottom' : '3px', 'font-size' : '12px'});
                window.print();
                $('#member-detail-1').text('@lang('labels.premember_name')');
                $('#member-detail-2').text('@lang('labels.payment_number')');
                $('#member-detail-3').text('@lang('labels.membership_type')');
                $('.member-detail td').css('padding', '10');
                $('.member-detail').css('font-size', '');
                $('.page-title').removeClass('hidden');
                $('#my-btn-print').removeClass('hidden');
                $('#manajemenkeanggotaan').removeClass('hidden');
                $('#floating-button').removeClass('hidden');
                $('.nav-tabs').removeClass('hidden');
                $('.left-sidebar').removeClass('hidden');
                $('.btn-back-to').removeClass('hidden');
                $('#edit-order-btn').removeClass('hidden');
                $('#delivery-order-btn').removeClass('hidden');
                $('#logo-print').hide();
                $('#ttd-print').hide();
                $('.table-bordered').css('margin-top', '0');
                $('td').css({'padding-top' : '8px', 'padding-bottom' : '8px', 'font-size' : '16px'});
                $('th').css({'padding-top' : '8px', 'padding-bottom' : '8px', 'font-size' : '16px'});
            });
        });

        function init_format() {
            $('.rupiah').each(function () {
                if ($(this).val() == "") {
                    $(this).val('0');
                } else {
                    var x = rupiah_formater($(this).val().replace(/\s/g, ''));
                    $(this).val((x));
                }
            });

            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp.' + (x));
            });
        }
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>
@endsection