@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">

    <style>
        #staff-home {
            padding: 15px;
        }

        * { box-sizing: border-box; }

        body { font-family: sans-serif; }

        /* ---- grid ---- */

        .grid {
          max-width: 1200px;
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
          height: auto;
          float: left;
          border-radius: 5px;
        }

        @media print
        {
            .grid{
                page-break-inside: avoid;
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
                {!! Form::open(['method' => 'GET', 'action' => 'FinancialManagementReportController@print_daily_sales', 'class' => 'form-inline']) !!}
                <div class="form-group">
                    {!! Form::label('d', trans('labels.choose_date').':  ', ['class' => 'control-label']) !!}
                    {!! Form::date('d', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                    {!! Form::submit(trans('labels.apply'), ['class' => 'btn btn-default']) !!}
                    <div>
                        <button type="button" id="my-btn-print" class="dt-button btn btn-default"><i class="fa fa-print"></i> <span>@lang('labels.print')</span></button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="grid">
            @foreach($member_payments->where('payment_status', '<>', 'Unpaid') as $ms)
                <div class="grid-item" style="padding-top: 20px; padding-left: 10px; padding-bottom: 10px; border: 2px #efefef solid;">
                    <div style="text-align: center; font-size: 24px;">Curves</div>
                    <div style="text-align: center;">{{$ms->memberpayment_user->user_club->club_name}}</div>
                    <br/>
                    <div>@lang('labels.from') : {{ !empty($ms->user_id) ? $ms->memberpayment_user->name : 'Guest' }}</div>
                    <div>@lang('labels.number') : {{ $ms->payment_number }}</div>
                    <div>@lang('labels.date') : {{ Carbon\Carbon::parse($ms->payment_date)->format('d-m-Y H:i') }}</div>
                    <div>--------------------------------------------------------------------------</div>
                    @php($total = 0)
                    <div>{{$ms->membership_type_name}}</div>
                    <div style="display: flex; justify-content: space-between;">
                        <div>@lang('labels.service_fee') :</div>
                        <div style="padding-right: 30px;" class="my_rupiah2">{{ $ms->sf_amount }}</div>
                        @php($total += $ms->sf_amount )
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <div>@lang('labels.monthly_fee') :</div>
                        <div style="padding-right: 30px;" class="my_rupiah2">{{ $ms->mf_amount }}</div>
                        @php($total += $ms->mf_amount )
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <div>@lang('labels.discount') : (-)</div>
                        <div style="padding-right: 30px;"><span class="my_rupiah2">{{ $ms->dc_amount }}</span></div>
                        @php($total -= $ms->dc_amount )
                    </div>
                    <div>--------------------------------------------------------------------------</div>
                    <div  style="display: flex; justify-content: space-between;">
                        <div>@lang('labels.total')</div>
                        <div style="padding-right: 30px;" class="my_rupiah2"> {{ $total }}</div>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <div>{{ !(empty($ms->memberpayment_paymentmethod)) ? $ms->memberpayment_paymentmethod->payment_method : trans('labels.cash') }}
                            @if($ms->payment_method_id != 1)
                                {{ $ms->memberpayment_bank->bank_abv }}
                            @endif
                            : 
                        </div>
                        <div style="padding-right: 30px;" class="my_rupiah2">{{ $ms->amount_paid }}</div>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <div>@lang('labels.rest_of_bill'): </div>
                        <div style="padding-right: 30px;" class="my_rupiah2">{{ $ms->rest_of_bill }}</div>
                    </div>
                </div>
            @endforeach
        </div>
        <br>
        <div class="grid">
            @foreach($promo_payments as $pms)
                <div class="grid-item" style="padding-top: 20px; padding-left: 10px; padding-bottom: 10px; border: 2px #efefef solid;">
                    <div style="text-align: center; font-size: 24px;">Curves</div>
                    <div style="text-align: center;">{{$pms->gmpayment_gmmember->gmmember_guest->buddyreferrallead_club->club_name}}</div>
                    <br/>
                    <div>@lang('labels.from') : {{ !empty($pms->guest_membership_member_id) ? $pms->gmpayment_gmmember->gmmember_guest->fullname : 'Guest' }}</div>
                    <div>@lang('labels.number') : {{ $pms->payment_number }}</div>
                    <div>@lang('labels.date') : {{ Carbon\Carbon::parse($pms->payment_date)->format('d-m-Y') }}</div>
                    <div>--------------------------------------------------------------------------</div>
                    @php($total = 0)
                    <div>{{$pms->gmpayment_gmmember->gmmember_gm->gm_name}}</div>
                    <div style="display: flex; justify-content: space-between;">
                        <div>@lang('labels.fee') :</div>
                        <div style="padding-right: 30px;" class="my_rupiah2">{{ $pms->amount_due }}</div>
                        @php($total += $pms->amount_due )
                    </div>
                    <div>--------------------------------------------------------------------------</div>
                    <div  style="display: flex; justify-content: space-between;">
                        <div>@lang('labels.total')</div>
                        <div style="padding-right: 30px;" class="my_rupiah2"> {{ $total }}</div>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <div>{{ ($pms->payment_type_id == 1) ? trans('labels.cash') : $pms->gmpayment_paymentmethod->payment_method }} {{ ($pms->payment_type_id == 1) ? '' : $pms->gmpayment_bank->bank_abv }}: </div>
                        <div style="padding-right: 30px;" class="my_rupiah2">{{ $pms->amount_paid }}</div>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <div>@lang('labels.rest_of_bill'): </div>
                        <div style="padding-right: 30px;" class="my_rupiah2">{{ $pms->rest_of_bill }}</div>
                    </div>
                </div>
            @endforeach
        </div>
        <br>
        <div class="grid">
            @foreach($product_payments as $ps)
                <div class="grid-item" style="padding-top: 20px; padding-left: 10px; padding-bottom: 10px; border: 2px #efefef solid;">
                    <div style="text-align: center; font-size: 24px;">Curves</div>
                    <div style="text-align: center;">{{$ps->productpayment_club->club_name}}</div>
                    <br/>
                    <div>@lang('labels.from') : {{ !empty($ps->member_id) ? $ps->productpayment_user->name : 'Guest' }}</div>
                    <div>@lang('labels.number') : {{ $ps->payment_number }}</div>
                    <div style="display: flex; justify-content: space-between;">
                        <div>@lang('labels.date') : {{ Carbon\Carbon::parse($ps->payment_date)->format('d-m-Y H:i') }}</div>
                        <div style="padding-right: 30px;">{{ substr($ps->productpayment_cashier->name, 0, 24) }}</div>
                    </div>
                    <div>--------------------------------------------------------------------------</div>
                    @php($total = 0)
                    @foreach($ps->productpayment_productpaymentdetail as $pd)
                        <div>{{$pd->productpaymentdetail_product->item_name}}</div>
                        <div style="display: flex; justify-content: space-between;">
                            <div>{{$pd->qty}} X <span class="my_rupiah">{{$pd->price}}</span></div>
                            <div style="padding-right: 30px;" class="my_rupiah2">{{ $pd->qty * $pd->price }}</div>
                            @php($total += $pd->qty * $pd->price )
                        </div>
                    @endforeach
                    <div>--------------------------------------------------------------------------</div>
                    <div  style="display: flex; justify-content: space-between;">
                        <div>@lang('labels.total')</div>
                        <div style="padding-right: 30px;" class="my_rupiah2"> {{ $total }}</div>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <div>{{ ($ps->payment_method_id == 1) ? trans('labels.cash') : $ps->productpayment_paymentmethod->payment_method }} {{ ($ps->payment_method_id == 1) ? '' : $ps->productpayment_bank->bank_abv }}: </div>
                        <div style="padding-right: 30px;" class="my_rupiah2">{{ $ps->pay }}</div>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <div>@lang('labels.change'): </div>
                        <div style="padding-right: 30px;" class="my_rupiah2">{{ $ps->change }}</div>
                    </div>
                </div>
            @endforeach
        </div>
        <br>
        <div class="grid">
            @foreach($locker_payments as $ls)
                <div class="grid-item" style="padding-top: 20px; padding-left: 10px; padding-bottom: 10px; border: 2px #efefef solid;">
                    <div style="text-align: center; font-size: 24px;">Curves</div>
                    <div style="text-align: center;">{{$ls->lockerpayment_locker->locker_clubinfo->club_name}}</div>
                    <br/>
                    <div>@lang('labels.from') : {{ !empty($ls->memberlocker_id) ? $ls->lockerpayment_memberlocker->memberlocker_user->name : 'Guest' }}</div>
                    <div>@lang('labels.number') : {{ $ls->payment_number }}</div>
                    <div style="display: flex; justify-content: space-between;">
                        <div>@lang('labels.date') : {{ Carbon\Carbon::parse($ls->payment_date)->format('d-m-Y') }}</div>
                        <div style="padding-right: 30px;">{{ substr($ls->lockerpayment_user->name, 0, 24) }}</div>
                    </div>
                    <div>--------------------------------------------------------------------------</div>
                    @php($total = 0)
                    <div>{{$ls->lockerpayment_memberlocker->memberlocker_lockerinfo->locker_name}} - {{$ls->lockerpayment_locker->name}}</div>
                    <div style="display: flex; justify-content: space-between;">
                        <div>{{$ls->quantity}} X <span class="my_rupiah">{{$ls->price}}</span></div>
                        <div style="padding-right: 30px;" class="my_rupiah2">{{ $ls->quantity * $ls->price }}</div>
                        @php($total += $ls->quantity * $ls->price )
                    </div>
                    <div>--------------------------------------------------------------------------</div>
                    <div  style="display: flex; justify-content: space-between;">
                        <div>@lang('labels.total')</div>
                        <div style="padding-right: 30px;" class="my_rupiah2"> {{ $total }}</div>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <div>{{ ($ls->paymentmethod_id == 1) ? trans('labels.cash') : $ls->lockerpayment_paymentmethod->payment_method }} {{ ($ls->paymentmethod_id == 1) ? '' : $ls->lockerpayment_bank->bank_abv }}: </div>
                        <div style="padding-right: 30px;" class="my_rupiah2">{{ $ls->pay }}</div>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <div>@lang('labels.change'): </div>
                        <div style="padding-right: 30px;" class="my_rupiah2">{{ $ls->pay - $total }}</div>
                    </div>
                </div>
            @endforeach
        </div>

        

    </section>
@endsection

@section('other-script')
    <script>
        $(document).ready(function () {
            init_format();

            function init_format() {
                $('.my_rupiah').each(function () {
                    var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                    $(this).text(x);
                });
                $('.my_rupiah2').each(function () {
                    var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                    $(this).text('Rp. '+(x));
                });
                $('.rupiah').each(function () {
                    var x = rupiah_formater($(this).val().replace(/\s/g, ''));
                    $(this).val((x));
                });
            }

            $('#my-btn-print').on('click', function () {
                $(this).addClass('hidden');
                $('.left-sidebar').addClass('hidden');
                $('.nav-tabs').addClass('hidden');
                $('.btn-back-to').addClass('hidden');
                $('.filter-tgl').addClass('hidden');
                $('#daily-title').addClass('hidden');
                $('#floating-button').addClass('hidden');
                $('.ba-staff-login-wrap').addClass('hidden');
                $('.box-menu').css('border', '0');
                $('.page-title').css({'margin-top': '0', 'margin:bottom': '0'});
                $('#date-title').css('font-size', '40%');
                window.print();
                $('.nav-tabs').removeClass('hidden');
                $('.left-sidebar').removeClass('hidden');
                $(this).removeClass('hidden');
                $('.btn-back-to').removeClass('hidden');
                $('.filter-tgl').removeClass('hidden');
                $('#daily-title').removeClass('hidden');
                $('#floating-button').removeClass('hidden');
                $('.ba-staff-login-wrap').removeClass('hidden');
                $('.box-menu').css('border', '');
                $('.page-title').css({'margin-top': '50px', 'margin:bottom': '50px'});
                $('#date-title').css('font-size', '65%');
            });

            $('.grid').masonry({
              itemSelector: '.grid-item'
            });
        });
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>
    <script src="{{asset('masonry/masonry.pkgd.min.js')}}"></script>
@endsection