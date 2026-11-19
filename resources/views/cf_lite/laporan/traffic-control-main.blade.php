@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <style>
        #menuutama {
            padding-left: 30px;
            padding-right: 30px;
        }

        .date-day {
            font-size: 18px;
            margin-top: 30px;
            margin-bottom: 30px;
        }

        .page-title {
            font-size: 44px;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/laporan')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">
                        @lang('labels.traffic_control_report')
                    </h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10">
                {!! Form::open(['method' => 'GET', 'action' => ['ReportController@traffic_control_main'], 'class' => 'form-inline']) !!}
                <div class="form-group">
                    {!! Form::date('date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                    {!! Form::submit(trans('labels.apply_filter'), ['class' => 'btn btn-primary']) !!}
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="row date-day">
            @php($days = ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu'])
            @if(isset($_GET['date']) && !empty($_GET['date']))
                <div class="col-md-3">@lang('labels.day'): {{$days[(\Carbon\Carbon::parse($_GET['date'])->format('N')) - 1]}}</div>
                <div class="col-md-3">@lang('labels.date'): {{\Carbon\Carbon::parse($_GET['date'])->format('d-m-Y')}}</div>
            @else
                <div class="col-md-3">@lang('labels.day'): {{$days[(\Carbon\Carbon::today()->format('N')) - 1]}}</div>
                <div class="col-md-3">@lang('labels.date'): {{\Carbon\Carbon::today()->format('d-m-Y')}}</div>
            @endif
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_content">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="data-table">
                                <thead>
                                <tr>
                                    <th>@lang('labels.name')</th>
                                    <th>@lang('labels.membership_number')</th>
                                    <th>@lang('labels.source')</th>
                                    <th>@lang('labels.staff')</th>
                                    <th>@lang('labels.debet')</th>
                                    <th>@lang('labels.credit')</th>
                                    <th>@lang('labels.transfer')</th>
                                    <th>@lang('labels.cash')</th>
                                    <th>@lang('labels.money_owed')</th>
                                    <th>@lang('labels.autodebet')</th>
                                    <th>@lang('labels.prepay')</th>
                                    <th>@lang('labels.month_to_month')</th>
                                    <th>@lang('labels.detail')-NS-@lang('labels.reason')</th>
                                </tr>
                                </thead>
                                <tbody id="tbody-mode-1">
                                <?php $mode = 0;

                                    if (isset($_GET['mode']) && !empty($_GET['mode'])):
                                        if ($_GET['mode'] == 'Sale'):
                                            $mode = 1;
                                        endif;
                                    else:
                                        $mode = 1;
                                    endif;

                                    $total_bc = 0; $total_cc = 0; $total_transfer = 0; $total_cash = 0; $total_money_owed = 0; $total_ad = 0; $total_pp = 0; $total_mm = 0;
                                    $total_bc_2 = 0; $total_cc_2 = 0; $total_transfer_2 = 0; $total_cash_2 = 0; $total_money_owed_2 = 0; $total_ad_2 = 0; $total_pp_2 = 0; $total_mm_2 = 0;

                                if( $mode == 1):
                                    foreach($payments as $payment):
                                        if(empty($payment->past_day)):
                                ?>
                                            <tr>
                                                <td>{{$payment->memberpayment_user->name}}</td>
                                                <td>{{$payment->memberpayment_user->user_memberpersonal->key_tag_id}}</td>
                                                <td>{{$payment->memberpayment_user->user_memberpersonal->memberpersonal_buddyreferrallead->buddyreferrallead_membersource->source ? $payment->memberpayment_user->user_memberpersonal->memberpersonal_buddyreferrallead->buddyreferrallead_membersource->source : '' }}</td>
                                                <td>{{$payment->memberpayment_user->user_memberpersonal->memberpersonal_sales->name}}</td>
                                                <?php
                                                    $owed = 0;
                                                    $money_owed = 0;
                                                    if(!empty($payment->owed)):
                                                        $owed = $payment->owed;
                                                    endif;

                                                    $cash = 0; $bc = 0; $cc = 0; $transfer = 0;
                                                    if ($payment->payment_method_id == 1):
                                                        $cash = $payment->amount_paid - $owed;
                                                        $money_owed = $payment->amount_due + $payment->money_owed - $cash;
                                                    elseif ($payment->payment_method_id == 2):
                                                        $bc = $payment->amount_paid - $owed;
                                                        $money_owed = $payment->amount_due + $payment->money_owed - $bc;
                                                    elseif ($payment->payment_method_id == 3):
                                                        $cc = $payment->amount_paid - $owed;
                                                        $money_owed = $payment->amount_due + $payment->money_owed - $cc;
                                                    elseif ($payment->payment_method_id == 4):
                                                        $transfer = $payment->amount_paid - $owed;
                                                        $money_owed = $payment->amount_due + $payment->money_owed - $transfer;
                                                    endif;
                                                ?>

                                                <td class="my_rupiah">{{$bc}} @php($total_bc = $total_bc + $bc)</td>
                                                <td class="my_rupiah">{{$cc}} @php($total_cc = $total_cc + $cc)</td>
                                                <td class="my_rupiah">{{$transfer}} @php($total_transfer = $total_transfer + $transfer)</td>
                                                <td class="my_rupiah">{{$cash}} @php($total_bc = $total_bc + $cash)</td>
                                                <td class="my_rupiah">{{$money_owed}} @php($total_money_owed = $total_money_owed + $money_owed)</td>
                                                <td>
                                                    @if($payment->memberpayment_user->user_memberstatus->memberstatus_membershiptype->payment_type == 'AD')
                                                        <span class="text-center glyphicon glyphicon-ok"></span>
                                                        @php($total_ad++)
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($payment->memberpayment_user->user_memberstatus->memberstatus_membershiptype->payment_type == 'Prepaid')
                                                        <span class="text-center glyphicon glyphicon-ok"></span>
                                                        @php($total_pp++)
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($payment->memberpayment_user->user_memberstatus->memberstatus_membershiptype->id == 1)
                                                        <span class="text-center glyphicon glyphicon-ok"></span>
                                                        @php($total_mm++)
                                                    @endif
                                                </td>
                                                <td></td>
                                            </tr>
                                        <?php endif;
                                    endforeach;
                                endif;
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 50px;">
            <div class="col-md-12">
                <h3>@lang('labels.guest_from_the_day_before')</h3>
                <div class="x_panel">
                    <div class="x_content">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="data-table-2">
                                <thead>
                                <tr>
                                    <th>@lang('labels.name')</th>
                                    <th>@lang('labels.membership_number')</th>
                                    <th>@lang('labels.source')</th>
                                    <th>@lang('labels.staff')</th>
                                    <th>@lang('labels.debet')</th>
                                    <th>@lang('labels.credit')</th>
                                    <th>@lang('labels.transfer')</th>
                                    <th>@lang('labels.cash')</th>
                                    <th>@lang('labels.money_owed')</th>
                                    <th>@lang('labels.autodebet')</th>
                                    <th>@lang('labels.prepay')</th>
                                    <th>@lang('labels.month_to_month')</th>
                                    <th>@lang('labels.detail')-NS-@lang('labels.reason')</th>
                                </tr>
                                </thead>
                                <tbody id="tbody-mode-2">
                                <?php
                                    foreach($payments as $payment):
                                    if(!empty($payment->past_day)):
                                ?>
                                        <tr>
                                            <td>{{$payment->memberpayment_user->name}}</td>
                                            <td>{{$payment->memberpayment_user->user_memberpersonal->key_tag_id}}</td>
                                            <td>{{$payment->memberpayment_user->user_memberpersonal->memberpersonal_buddyreferrallead->buddyreferrallead_membersource->source}}</td>
                                            <td>{{$payment->memberpayment_user->user_memberpersonal->memberpersonal_sales->name}}</td>
                                            <?php
                                                $owed = 0;
                                                $money_owed = 0;
                                                if (!empty($payment->owed)):
                                                    $owed = $payment->owed;
                                                endif;

                                                $cash = 0; $bc = 0; $cc = 0; $transfer = 0;
                                                if ($payment->payment_method_id == 1):
                                                    $cash = $payment->amount_paid - $owed;
                                                    $money_owed = $payment->amount_due + $payment->money_owed - $cash;
                                                elseif ($payment->payment_method_id == 2):
                                                    $bc = $payment->amount_paid - $owed;
                                                    $money_owed = $payment->amount_due + $payment->money_owed - $bc;
                                                elseif ($payment->payment_method_id == 3):
                                                    $cc = $payment->amount_paid - $owed;
                                                    $money_owed = $payment->amount_due + $payment->money_owed - $cc;
                                                elseif ($payment->payment_method_id == 4):
                                                    $transfer = $payment->amount_paid - $owed;
                                                    $money_owed = $payment->amount_due + $payment->money_owed - $transfer;
                                                endif;
                                            ?>

                                            <td class="my_rupiah">{{$bc}}@php($total_bc_2 = $total_bc_2 + $bc)</td>
                                            <td class="my_rupiah">{{$cc}}@php($total_cc_2 = $total_cc_2 + $cc)</td>
                                            <td class="my_rupiah">{{$transfer}}@php($total_transfer_2 = $total_transfer_2 + $transfer)</td>
                                            <td class="my_rupiah">{{$cash}}@php($total_bc_2 = $total_bc_2 + $cash)</td>
                                            <td class="my_rupiah">{{$money_owed}}@php($total_money_owed_2 = $total_money_owed_2 + $money_owed)</td>
                                            <td>
                                                @if($payment->memberpayment_user->user_memberstatus->memberstatus_membershiptype->payment_type == 'AD'):
                                                    <span class="text-center glyphicon glyphicon-ok"></span>
                                                    @php($total_ad_2++)
                                                @endif
                                            </td>
                                            <td>
                                                @if($payment->memberpayment_user->user_memberstatus->memberstatus_membershiptype->payment_type == 'Prepaid'):
                                                    <span class="text-center glyphicon glyphicon-ok"></span>
                                                    @php($total_pp_2++)
                                                @endif
                                            </td>
                                            <td>
                                                @if($payment->memberpayment_user->user_memberstatus->memberstatus_membershiptype->id == 1):
                                                    <span class="text-center glyphicon glyphicon-ok"></span>
                                                    @php($total_mm_2++)
                                                @endif
                                            </td>
                                            <td></td>
                                        </tr>
                                    <?php endif;
                                endforeach;
                                ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 50px;">
            <div class="col-md-12">
                <h3>@lang('labels.source') - @lang('labels.todays_fa')</h3>
                <div class="x_panel">
                    <div class="x_content">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>BRs</th>
                                        <th colspan="6">@lang('labels.branding')</th>
                                        <th colspan="10">@lang('labels.agp')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        @foreach($sources as $source):
                                            <td>{{ucwords($source->source)}}</td>
                                        @endforeach
                                    </tr>
                                    <tr>
                                        @foreach($sources as $source):
                                            <td>{{count($fa_sources->where('source_id', $source->id))}}</td>
                                        @endforeach
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 50px;">
            <div class="col-md-12">
                <h3>@lang('labels.sales_come_from_all_guest')</h3>
                <div class="x_panel">
                    <div class="x_content">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered">
                                <thead>
                                    <tr>
                                        <th>@lang('labels.membership_type')</th>
                                        <th>@lang('labels.total')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($membership_types as $membership_type):
                                    <tr>
                                        <td>{{$membership_type->membership_type_name}}</td>
                                        <td>{{count($sales->where('membership_type_id', $membership_type->id))}}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
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
        $('#data-table-2').dataTable();
    </script>

    <script>
        $(document).ready(function () {
            init_format();

            var tbody_mode_1 = '<tr>' +
                '<td colspan="4" style="text-align: right;">@lang('labels.total')</td>' +
                '<td class="my_rupiah">Rp.' + rupiah_formater('{{$total_bc}}') + '</td>' +
                '<td class="my_rupiah">Rp.' + rupiah_formater('{{$total_cc}}') + '</td>' +
                '<td class="my_rupiah">Rp.' + rupiah_formater('{{$total_transfer}}') + '</td>' +
                '<td class="my_rupiah">Rp.' + rupiah_formater('{{$total_cash}}') + '</td>' +
                '<td class="my_rupiah">Rp.' + rupiah_formater('{{$total_money_owed}}') + '</td>' +
                '<td>{{$total_ad}}</td>' +
                '<td>{{$total_pp}}</td>' +
                '<td>{{$total_mm}}</td>' +
                '<td></td>' +
                '</tr>';
            $('#tbody-mode-1').append(tbody_mode_1);

            $('#data-table').on('click', 'th', function () {
                $('#tbody-mode-1').append(tbody_mode_1);
            });

            var tbody_mode_2 = '<tr>' +
                '<td colspan="4" style="text-align: right;">@lang('labels.total')</td>' +
                '<td class="my_rupiah">Rp.' + rupiah_formater('{{$total_bc_2}}') + '</td>' +
                '<td class="my_rupiah">Rp.' + rupiah_formater('{{$total_cc_2}}') + '</td>' +
                '<td class="my_rupiah">Rp.' + rupiah_formater('{{$total_transfer_2}}') + '</td>' +
                '<td class="my_rupiah">Rp.' + rupiah_formater('{{$total_cash_2}}') + '</td>' +
                '<td class="my_rupiah">Rp.' + rupiah_formater('{{$total_money_owed_2}}') + '</td>' +
                '<td>{{$total_ad_2}}</td>' +
                '<td>{{$total_pp_2}}</td>' +
                '<td>{{$total_mm_2}}</td>' +
                '<td></td>' +
                '</tr>';
            $('#tbody-mode-2').append(tbody_mode_2);

            $('#data-table-2').on('click', 'th', function () {
                $('#tbody-mode-2').append(tbody_mode_2);
            });
        });

        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp.' + (x));
            });
        }
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>
@endsection
