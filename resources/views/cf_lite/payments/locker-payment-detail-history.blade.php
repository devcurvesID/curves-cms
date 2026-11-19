@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/payment.css')}}" rel="stylesheet">
    <style>
        body{
            color: white;
        }
        .modal-dialog, .modal-dialog label{
            color: #414042;
        }
        table{
            color: #414042;
        }
    </style>

    <style>
        #logo-print {
            display: none;
        }

        #logo-print img {
            width: 100px;
            height: auto;
            margin-bottom: 30px;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-4" id="logo-print">
            <img src="{{asset('images/Curves_Logo_Purple_Amaze.png')}}">
        </div>
        <div class="col-sm-12">
            <center>
                <h2 class="page-title">@lang('labels.payment_detail')</h2>
            </center>
            <a id="my-btn-print" class="btn btn-primary"
               style="position: absolute; top: 5px; right: 25px; font-size: 45px; border-radius: 50%; padding: 20px;"><i class="fa fa-print"></i>
            </a>
        </div>
    </div>

    @include('cf_lite.includes.error-message')

    <div class="row">
        <div class="col-md-6">
            <table class="member-detail">
                <tr>
                    <td>@lang('labels.payment_number')</td>
                    <td>:</td>
                    <td>{{$locker_payment->payment_number}}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="member-detail">
                <tr>
                    <td>@lang('labels.payment_date')</td>
                    <td>:</td>
                    <td>{{date('d-m-Y', strtotime($locker_payment->payment_date))}}</td>
                </tr>
                <tr>
                    <td>@lang('labels.payment_method')</td>
                    <td>:</td>
                    <td>{{$locker_payment->payment_method}}</td>
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
                            <th>@lang('labels.name')</th>
                            <th>@lang('labels.price')</th>
                            <th>@lang('labels.quantity')</th>
                            <th>@lang('labels.subtotal')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($index = 1)
                        @php($total = 0)
                        <tr>
                            <td>{{$index}}</td>
                            <td>{{$locker_payment->lockerpayment_locker->name}}</td>
                            <td class="my_rupiah">{{$locker_payment->lockerpayment_locker->price}}</td>
                            <td>{{$locker_payment->quantity}}</td>
                            <td class="my_rupiah">{{$locker_payment->lockerpayment_locker->price * $locker_payment->quantity}}</td>
                        </tr>
                        @php($total = $total + ($locker_payment->lockerpayment_locker->price * $locker_payment->quantity))
                        <tr>
                            <td colspan="4" style="text-align: right;">@lang('labels.subtotal'): </td>
                            <td class="my_rupiah">
                                {{$total}}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: right;">@lang('labels.total'): </td>
                            <td>
                                <div id="billing-total" class="my_rupiah">{{$total}}</div>
                                {!! Form::hidden('billing-total', $total) !!}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: right;">@lang('labels.pay'): </td>
                            <td class="my_rupiah">
                                {{$locker_payment->pay}}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('other-script')
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
                $('.member-detail').css('font-size', '12');
                $('.member-detail td').css('padding', '3');
                $('.box-menu').css('border-image', 'none');
                $('#member-detail-1').text('@lang('labels.name')');
                $('#member-detail-2').text('@lang('labels.indexing')');
                $('#member-detail-3').text('@lang('labels.type')');
                $('#logo-print').show();
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
                $('#logo-print').hide();
                $('.table-bordered').css('margin-top', '0');
                $('td').css({'padding-top' : '8px', 'padding-bottom' : '8px', 'font-size' : '16px'});
                $('th').css({'padding-top' : '8px', 'padding-bottom' : '8px', 'font-size' : '16px'});
            });
        });


        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp.' + (x));
            });
            $('.rupiah').each(function () {
                var x = rupiah_formater($(this).val().replace(/\s/g, ''));
                $(this).val((x));
            });
        }
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>
@endsection