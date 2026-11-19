@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/payment.css')}}" rel="stylesheet">
    <style>
        body{
            color: white;
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
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <center>
                <h2 class="page-title">@lang('labels.membership_cancellation_refund_detail')</h2>
            </center>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <table class="member-detail">
                <tr>
                    <td>@lang('labels.name')</td>
                    <td>:</td>
                    <td>{{$refund->continuemembershiprefund_continuemembership->continuemembership_user->name}}</td>
                </tr>
                <tr>
                    <td>@lang('labels.membership_type')</td>
                    <td>:</td>
                    <td>{{$refund->continuemembershiprefund_continuemembership->continuemembership_membershiptype->membership_type_name}}</td>
                </tr>
                <tr>
                    <td>@lang('labels.start_date')</td>
                    <td>:</td>
                    <td>{{date('d F Y', strtotime($refund->continuemembershiprefund_continuemembership->from_date))}}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="member-detail">
                <tr>
                    <td>@lang('labels.date')</td>
                    <td>:</td>
                    <td>{{date('d-m-Y', strtotime(\Carbon\Carbon::parse($refund->refund_process_date)))}}</td>
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
                            <th>@lang('labels.payment_date')</th>
                            <th>@lang('labels.type')</th>
                            <th>@lang('labels.amount')</th>
                        </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td style="text-align: right;">{{date('d-m-Y', strtotime(\Carbon\Carbon::parse($refund->continuemembershiprefund_memberpayment->payment_date)))}}</td>
                        <td>
                            @if($refund->continuemembershiprefund_memberpayment->payment_category == 'First')
                                @lang('labels.first_payment')
                            @elseif($refund->continuemembershiprefund_memberpayment->payment_category == 'Monthly')
                                @lang('labels.monthly_payment')
                            @endif
                        </td>
                        <td class="my_rupiah">{{$refund->continuemembershiprefund_memberpayment->amount_paid}}</td>
                    </tr>
                    <tr>
                        <td colspan="2" style="text-align: right;">@lang('labels.refund')</td>
                        <td class="my_rupiah">{{$refund->continuemembershiprefund_memberpayment->amount_paid}}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            @if($refund->refund_status == 'Paid')
                <h3 style="padding: 10px; border: 2px solid red;">@lang('labels.processed')</h3>
            @endif
        </div>
    </div>
@endsection

@section('other-script')
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
@endsection