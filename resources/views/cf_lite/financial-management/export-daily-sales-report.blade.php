@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css" rel="stylesheet">

    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">

    <style>
        #staff-home {
            padding: 15px;
        }

        * { box-sizing: border-box; }

        body { font-family: sans-serif; }

        /* ---- grid ---- */

        .grid {
            max-width: 800px;
        }

        /* clearfix */
        .grid:after {
            content: '';
            display: block;
            clear: both;
        }

        /* ---- grid-item ---- */

        .grid-item {
            width: 400px;
            height: 397px;
            float: left;
            border-radius: 5px;
            overflow-y: hidden;
        }

        @media print
        {
            .grid{
                /*page-break-inside: avoid;*/
            }
        }

    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/financial-management/daily-sales-report')}}" style="left: 25px; top: 5px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">
                        <span id="daily-title">@lang('labels.daily_sales_report')</span>
                        <br>
                        @php
                            $date = \Carbon\Carbon::today();
                            if (!empty($_GET['d'])):
                                $date = \Carbon\Carbon::parse($_GET['d']);
                            endif;
                            $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
                            $n= \Carbon\Carbon::parse($date)->format('w');
                        @endphp
                        <small id="date-title">({{$days[$n].', '.\Carbon\Carbon::parse($date)->format('d-m-Y')}})</small>
                    </h2>
                </center>
            </div>
        </div>

        <div class="row filter-tgl">
            <div class="col-md-12">
                <!-- Filter -->
                {!! Form::open(['method' => 'GET', 'action' => 'FinancialManagementReportController@export_daily_sales', 'class' => 'form-inline']) !!}
                <div class="form-group">
                    {!! Form::label('d', trans('labels.choose_date').':  ', ['class' => 'control-label']) !!}
                    {!! Form::date('d', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                    {!! Form::submit(trans('labels.apply'), ['class' => 'btn btn-default']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive" style="background-color: #fff;">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                            <tr>
                                <th>@lang('labels.date')</th>
                                <th>@lang('labels.name')</th>
                                <th>@lang('labels.variety')</th>
                                <th>@lang('labels.information')</th>
                                <th>@lang('labels.total')</th>
                                <th>@lang('labels.payment_method')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($member_payments->where('payment_status', '<>', 'Unpaid') as $ms)
                                <tr>
                                    <td>{{ Carbon\Carbon::parse($ms->payment_date)->format('d-M-Y') }}</td>
                                    <td>{{ !empty($ms->user_id) ? $ms->memberpayment_user->name : 'Guest' }}</td>
                                    <td>
                                        @if($ms->renewal == true)
                                            @lang('labels.renewal')
                                        @elseif($ms->conversion == true)
                                            @lang('labels.conversion')
                                        @else
                                            @lang('labels.membership')
                                        @endif
                                    </td>
                                    <td>{{ $ms->membership_type_name }}</td>
                                    @php($total = 0)
                                    @php($total += $ms->sf_amount + $ms->mf_amount - $ms->dc_amount)
                                    <td class="my_rupiah">{{ $total }}</td>
                                    <td>
                                        {{-- {{ !(empty($ms->memberpayment_paymentmethod)) ? $ms->memberpayment_paymentmethod->payment_method : trans('labels.cash') }} --}}
                                        {{ ($ms->payment_method_id == 1) ? trans('labels.cash') : (($ms->payment_method_id == 6 || $ms->payment_method_id == 7) ? $ms->memberpayment_paymentmethod->payment_method : $ms->memberpayment_bank->bank_name) }}
                                        {{-- @if($ms->payment_method_id != 1)
                                            {{ $ms->memberpayment_bank->bank_name }}
                                        @endif --}}
                                    </td>
                                </tr>
                            @endforeach
                            @foreach($promo_payments as $pms)
                                <tr>
                                    <td>{{ Carbon\Carbon::parse($pms->payment_date)->format('d-M-Y') }}</td>
                                    <td>{{ !empty($pms->guest_membership_member_id) ? $pms->gmpayment_gmmember->gmmember_guest->fullname : 'Guest' }}</td>
                                    <td>@lang('labels.promo_membership')</td>
                                    <td>{{ $pms->gmpayment_gmmember->gmmember_gm->gm_name }}</td>
                                    @php($total = 0)
                                    @php($total += $pms->amount_due )
                                    <td class="my_rupiah">{{ $total }}</td>
                                    <td>
                                        {{ ($pms->payment_type_id == 1) ? trans('labels.cash') : $pms->gmpayment_paymentmethod->payment_method }} {{ ($pms->payment_type_id == 1 || $pms->payment_type_id == 6 || $pms->payment_type_id == 7) ? '' : $pms->gmpayment_bank->bank_name }}
                                    </td>
                                </tr>
                            @endforeach
                            @foreach($product_payments as $ps)
                                <tr>
                                    <td>{{ Carbon\Carbon::parse($ps->payment_date)->format('d-M-Y') }}</td>
                                    <td>{{ !empty($ps->member_id) ? $ps->productpayment_user->name : 'Guest' }}</td>
                                    <td>@lang('labels.product')</td>
                                    <td>
                                        @php($total = 0)
                                        @foreach($ps->productpayment_productpaymentdetail as $pd)
                                            @if(count($ps->productpayment_productpaymentdetail) > 1)
                                                {{$pd->productpaymentdetail_product->item_name}},
                                            @else
                                                {{$pd->productpaymentdetail_product->item_name}}
                                            @endif
                                            @php($total += $pd->qty * $pd->price )
                                        @endforeach
                                    </td>
                                    <td class="my_rupiah">{{ $total }}</td>
                                    <td>
                                        {{ ($ps->payment_method_id == 1) ? trans('labels.cash') : $ps->productpayment_paymentmethod->payment_method }} {{ ($ps->payment_method_id == 1 || $ps->payment_method_id == 6 || $ps->payment_method_id == 7) ? '' : $ps->productpayment_bank->bank_name }}
                                    </td>
                                </tr>
                            @endforeach
                            @foreach($locker_payments as $ls)
                                <tr>
                                    <td>{{ Carbon\Carbon::parse($ls->payment_date)->format('d-M-Y') }}</td>
                                    <td>{{ !empty($ls->memberlocker_id) ? $ls->lockerpayment_memberlocker->memberlocker_user->name : 'Guest' }}</td>
                                    <td>@lang('labels.locker')</td>
                                    <td>{{$ls->lockerpayment_memberlocker->memberlocker_lockerinfo->locker_name}} - {{$ls->lockerpayment_locker->name}}</td>
                                    @php($total += $ls->quantity * $ls->price)
                                    <td class="my_rupiah">{{ $total }}</td>
                                    <td>
                                        {{ ($ls->paymentmethod_id == 1) ? trans('labels.cash') : $ls->lockerpayment_paymentmethod->payment_method }} {{ ($ls->paymentmethod_id == 1 || $ls->paymentmethod_id == 6 || $ls->paymentmethod_id == 7) ? '' : $ls->lockerpayment_bank->bank_name }}
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#data-table').DataTable({
                dom: 'Bfrtip',
                buttons: [{
                    extend: 'excel',
                    title: '@lang('labels.daily_sales_report') - ({{\Carbon\Carbon::parse($date)->format('d-m-Y')}})',
                }]

            });

            init_format();

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
