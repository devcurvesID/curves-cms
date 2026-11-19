@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/payment.css')}}" rel="stylesheet">
    <style>
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
        #logo-print, #ttd-print, #print-title, #ktr-print {
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

        .account-detail {
            font-size: 12px;
        }

        @media print {
            #ktr-print {
                display: block;
            }

            #ktr-print p {
                font-size: 11px;
            }

            .edit-installment-btn {
                display: none;
            }
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-xs-4" id="logo-print">
            <img src="{{asset('images/Curves_Logo_Purple_Amaze.png')}}">
        </div>
        <div class="col-xs-6 col-xs-offset-2" id="print-title">
            <h2 class="text-uppercase">@lang('labels.sales_invoice')</h2>
        </div>
        <center>
            <h2 class="page-title">@lang('labels.payment_detail')</h2>
        </center>
        <div class="col-sm-12">
            <a id="my-btn-print" class="btn btn-primary pull-right"
               style="top: 5px; right: 25px; font-size: 45px; border-radius: 50%; padding: 20px;"><i class="fa fa-print"></i>
            </a>
            @if(Auth::user()->role_id == 1 || Auth::user()->role_id == 2 || Auth::user()->role_id == 3 || Auth::user()->role_id == 8)
                <a class="btn btn-info pull-right"
                   style="top: 5px; right: 20px; font-size: 45px; border-radius: 50%; padding: 20px; margin-right: 15px;"
                   title="@lang('labels.edit')"
                   data-toggle="modal" data-target="#edit"><i class="fa fa-edit"></i>
                </a>
            @endif
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-xs-6">
            <table class="member-detail">
                <tr>
                    <td>@lang('labels.due_date')</td>
                    <td>:</td>
                    <td>
                        {{\Carbon\Carbon::parse($bill->payment_date)->format('d-m-Y')}}
                    </td>
                </tr>
                <tr>
                    <td>@lang('labels.member_name')</td>
                    <td>:</td>
                    <td>{{$bill->memberpayment_user->name}}</td>
                </tr>
                <tr>
                    <td>@lang('labels.membership_type')</td>
                    <td>:</td>
                    <td>
                        {{$bill->memberpayment_user->user_memberstatus->memberstatus_membershiptype->membership_type_name}}
                    </td>
                </tr>
                <tr>
                    <td>@lang('labels.fwo_date')</td>
                    <td>:</td>
                    <td>
                        @if(!empty($fwo))
                            @if($fwo)
                                {{\Carbon\Carbon::parse($fwo->workout_date)->format('d-m-Y')}}
                            @else
                                @lang('labels.not_workout_yet')
                            @endif
                        @else
                            @lang('labels.not_workout_yet')
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-md-6 col-xs-6">
            <table class="account-detail hidden">
                <tr>
                    <td>*Mohon untuk pembayaran keanggotaan Curves dapat ditransfer ke</td>
                </tr>
                <tr>
                    <td>Bank BCA KCP Maranatha : 282–753-5318</td>
                </tr>
                <tr>
                    <td>Bank BNI KCP Perguruan Tinggi Bandung : 5353181817</td>
                </tr>
                <tr>
                    <td>Bank Mandiri KCP Bandung Jamika : 130-00-5353187-0</td>
                </tr>
                <tr>
                    <td>Atas Nama: PT Unity Brands Indonesia</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            @php($number = 1)
            @php($month_id = ['', 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'])
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th class="text-center">@lang('labels.number')</th>
                            <th class="text-center">@lang('labels.rincian')</th>
                            <th class="text-center">@lang('labels.price_per_unit')</th>
                            <th class="text-center">@lang('labels.total')</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center">{{$number}}</td>
                            <td>
                                @lang('labels.curves_membership_payment')
                                <br>
                                {{$month_id[\Carbon\Carbon::parse($bill->payment_date)->format('n')].' '.\Carbon\Carbon::parse($bill->payment_date)->format('Y')}}
                            </td>
                            <td class="my_rupiah">{{ !empty($bill->memberpayment_digitalproductpayment) ? ($bill->amount_due - $bill->memberpayment_digitalproductpayment->price) : $bill->amount_due }}</td>
                            <td class="my_rupiah">{{ !empty($bill->memberpayment_digitalproductpayment) ? ($bill->amount_due - $bill->memberpayment_digitalproductpayment->price) : $bill->amount_due }}</td>
                            @php($number++)
                        </tr>
                        @if(!empty($bill->memberpayment_digitalproductpayment))
                            <tr>
                                <td class="text-center">{{ $number }}</td>
                                <td>
                                   {{ $bill->memberpayment_digitalproductpayment->digitalproductpayment_digitalproductinventory->digitalproductinventory_digitalproduct->item_name }}
                                </td>
                                <td class="my_rupiah">{{ $bill->memberpayment_digitalproductpayment->price }}</td>
                                <td class="my_rupiah">{{ $bill->memberpayment_digitalproductpayment->price }}</td>
                            </tr>
                        @endif
                        <tr>
                            <td colspan="3" class="text-right text-uppercase">@lang('labels.total')</td>
                            <td class="my_rupiah">{{$bill->amount_due}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-4">
            <h4>@lang('labels.payment'):</h4>
            <div class="form-group">
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('payment[]', 'cash', null) !!} @lang('labels.cash')
                    </label>
                </div>
                <div class="checkbox">
                    <label>
                        {!! Form::checkbox('payment[]', 'transfer', null) !!} @lang('labels.transfer')
                    </label>
                </div>
            </div>
        </div>
        <div class="col-xs-4 col-xs-offset-4" id="ttd-print">
            <h4>
                @php($city = explode(' ', $club->city))
                {{ucwords($city[0]).', ____________'}}
                <br>
                {{ucwords($club->club_name)}}
            </h4>
            <h4 style="margin-top: 50px;">@lang('labels.staff_name')</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-8" id="ktr-print">
            <p>Setelah membayar, dapat menunjukkan bukti transfer kepada staff atau mengirimkan melalui WA pada no. WA 0811-2277-838<br>Terima Kasih</p>
        </div>
    </div>

    <!--Modal-->
    <div class="modal fade" role="dialog" id="edit">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'PATCH', 'action' => ['PaymentController@update_bill_detail', $bill->id], 'id' => 'form-edit']) !!}
                <div class="modal-header">
                    <h4 class="modal-title">@lang('labels.installment_payment') - @lang('labels.amount_installment')</h4>
                </div>
                <div class="modal-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th colspan="3">@lang('labels.installment_payment')</th>
                            </tr>
                            <tr>
                                <td colspan="2">@lang('labels.set_due_date')</td>
                                <td>{!! Form::date('payment_date_temp', \Carbon\Carbon::parse($bill->payment_date), ['class' => 'form-control']) !!}</td>
                            </tr>
                            <tr>
                                <td colspan="2">@lang('labels.bill')</td>
                                <td>
                                    {!! Form::text('bill', $bill->amount_due, ['class' => 'form-control rupiah', 'id' => 'bill', 'onkeypress' => 'return onlyNumber(event)']) !!}
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2">@lang('labels.digital_product')</td>
                                <td>
                                    {!! Form::text('digital_product', !empty($bill->memberpayment_digitalproductpayment) ? $bill->memberpayment_digitalproductpayment->price : 0, ['class' => 'form-control rupiah', 'id' => 'digProduct', 'onkeypress' => 'return onlyNumber(event)']) !!}
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('labels.cancel')</button>
                    <button type="button" class="btn btn-primary" id="edit-installment">@lang('labels.edit')</button>
                </div>
                {!! Form::close() !!}
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
                $('.account-detail').removeClass('hidden');
                $('.member-detail td').css('padding', '3');
                $('.box-menu').css('border-image', 'none');
                $('#logo-print').show();
                $('#ttd-print').show();
                $('#print-title').show();
                $('.table-bordered').css('margin-top', '25');
                $('td').css({'padding-top' : '3px', 'padding-bottom' : '3px', 'font-size' : '11px'});
                $('th').css({'padding-top' : '3px', 'padding-bottom' : '3px', 'font-size' : '12px'});
                window.print();
                $('.member-detail td').css('padding', '10');
                $('.member-detail').css('font-size', '');
                $('.account-detail').addClass('hidden');
                $('.page-title').removeClass('hidden');
                $('#my-btn-print').removeClass('hidden');
                $('#manajemenkeanggotaan').removeClass('hidden');
                $('#floating-button').removeClass('hidden');
                $('.nav-tabs').removeClass('hidden');
                $('.left-sidebar').removeClass('hidden');
                $('#logo-print').hide();
                $('#ttd-print').hide();
                $('#print-title').hide();
                $('.table-bordered').css('margin-top', '0');
                $('td').css({'padding-top' : '8px', 'padding-bottom' : '8px', 'font-size' : '16px'});
                $('th').css({'padding-top' : '8px', 'padding-bottom' : '8px', 'font-size' : '16px'});
            });
        });

        $('#bill').on('keyup', function () {
            if ($(this).val() == "") {
                var bill = 0;
            } else {
                var bill = parseInt(rupiah_format_destroyer($(this).val()));
            }

            // Cash formatter
            if ($("#bill").val() != ""){
                $("#bill").val(rupiah_formater(bill));
            } else {
                $("#bill").val(0);
            }
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

        function onlyNumber(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode;
            if (charCode == 8 || //space
                charCode == 9 || //tab
                charCode == 46 || //point
                (charCode > 47 && charCode < 58) // number
            ) {
                return true;
            }
            return false;
        }

        $('#edit-installment').on('click', function () {
            var temp = rupiah_format_destroyer($('#bill').val());
            var digProductTemp = rupiah_format_destroyer($("#digProduct").val());
            $('#bill').val(temp);
            $("#digProduct").val(digProductTemp);
            $('#form-edit').submit();
        });
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>
@endsection
