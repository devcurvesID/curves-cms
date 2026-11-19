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
        #logo-print, #ttd-print, #ttd-print1 {
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

        #ttd-print1 h4 {
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
            <a href="{{url('cf-lite/consignment-order/detail/'.$co->id)}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.delivery_order')</h2>
            </center>
            <a id="my-btn-print" doid="{{ $co->id }}" class="btn btn-primary pull-right"
               style="top: 5px; right: 25px; font-size: 45px; border-radius: 50%; padding: 20px;"
               title="@lang('labels.print_delivery_order')"><i class="fa fa-print"></i>
            </a>
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
                    <td id="member-detail-2">@lang('labels.send_to')</td>
                    <td>:</td>
                    <td>{{ $co->co_club->club_name }}</td>
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
                    <td id="member-detail-3">@lang('labels.order_number')</td>
                    <td>:</td>
                    <td>{{ $co->order_number }}</td>
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
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 col-sm-6 col-xs-6" id="ttd-print" style="text-align: center;">
            <h4>@lang('labels.staff_signature')</h4>
            <h4 style="margin-top: 50px;">(....................)</h4>
        </div>
        <div class="col-md-6 col-sm-6 col-xs-6" id="ttd-print1" style="text-align: center;">
            <h4>@lang('labels.regards')</h4>
            <h4 style="margin-top: 50px;">(....................)</h4>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script>
        $(document).ready(function () {

            $('#my-btn-print').on('click', function () {
                var url = "{{url('cf-lite')}}/"
                //Ajax update delivery order
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    consignment_id: $(this).attr('doid')
                };

                var type = 'POST';
                var my_url = url + 'delivery-order-ajax';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    success: function (data) {
                        //Print Function
                        $('.left-sidebar').addClass('hidden');
                        $('.nav-tabs').addClass('hidden');
                        $('#floating-button').addClass('hidden');
                        $('#manajemenkeanggotaan').addClass('hidden');
                        $('#my-btn-print').addClass('hidden');
                        $('.btn-back-to').addClass('hidden');
                        // $('.page-title').addClass('hidden');
                        $('.page-title').css({'font-size' : '30', 'margin-top' : '0', 'margin-bottom' : '30'});
                        $('.member-detail').css('font-size', '12');
                        $('.member-detail td').css('padding', '3');
                        $('.box-menu').css('border-image', 'none');
                        $('#member-detail-1').text('@lang('labels.from')');
                        $('#member-detail-2').text('@lang('labels.send_to')');
                        $('#member-detail-3').text('@lang('labels.order_number')');
                        $('#logo-print').show();
                        $('#ttd-print').show();
                        $('#ttd-print1').show();
                        $('.table-bordered').css('margin-top', '25');
                        $('td').css({'padding-top' : '3px', 'padding-bottom' : '3px', 'font-size' : '11px'});
                        $('th').css({'padding-top' : '3px', 'padding-bottom' : '3px', 'font-size' : '12px'});
                        window.print();
                        $('#member-detail-1').text('@lang('labels.from')');
                        $('#member-detail-2').text('@lang('labels.send_to')');
                        $('#member-detail-3').text('@lang('labels.order_number')');
                        $('.member-detail td').css('padding', '10');
                        $('.member-detail').css('font-size', '');
                        // $('.page-title').removeClass('hidden');
                        $('.page-title').css({'font-size' : '50', 'margin-top' : '50', 'margin-bottom' : '50'});
                        $('#my-btn-print').removeClass('hidden');
                        $('#manajemenkeanggotaan').removeClass('hidden');
                        $('#floating-button').removeClass('hidden');
                        $('.nav-tabs').removeClass('hidden');
                        $('.left-sidebar').removeClass('hidden');
                        $('.btn-back-to').removeClass('hidden');
                        $('#logo-print').hide();
                        $('#ttd-print').hide();
                        $('#ttd-print1').hide();
                        $('.table-bordered').css('margin-top', '0');
                        $('td').css({'padding-top' : '8px', 'padding-bottom' : '8px', 'font-size' : '16px'});
                        $('th').css({'padding-top' : '8px', 'padding-bottom' : '8px', 'font-size' : '16px'});
                    },
                    error: function (data) {
                        console.log('Error : ', data);
                    }
                });
            });
        });
    </script>
@endsection