@extends('cf_lite.layouts.staff-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <style>

        .page-title {
            margin-top: 0px;
        }

        .btn-filter-zone{
            padding: 9px 12px;
        }

        select[name="data-table_length"]{
            margin: 0px 10px;
        }

        .btn-member-status {
            position: absolute;
            top: 0;
            right: 0;
            border-radius: 50px;
            width: 50px;
            height: 50px;
            border: 3px solid rebeccapurple;
        }

        .btn-member-status:hover {
            background-color: rebeccapurple;
            border: 3px solid rebeccapurple;
        }

        .btn-member-status:hover > span {
            color: white !important;
        }

    </style>

@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/dashboard')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.refund_list') <br><small>({{\Illuminate\Support\Facades\Auth::user()->user_club->club_name}})</small></h2>
            </center>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h3>@lang('labels.pending_bill_or_refund')</h3>
        </div>
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                            <tr>
                                <th>@lang('labels.date')</th>
                                <th>@lang('labels.club')</th>
                                <th>@lang('labels.name')</th>
                                <th>@lang('labels.phone')</th>
                                <th>@lang('labels.type')</th>
                                <th>@lang('labels.amount')</th>
                                <th>@lang('labels.payment_number')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($member_deposits) > 0)
                                @foreach($member_deposits as $member_deposit)
                                    @php($payment_number = explode('/', $member_deposit->payment_number, 2))
                                    @if(($payment_number[0] != 'manual-debet') && ($payment_number[0] != 'manual-credit') && ($payment_number[0] != 'cash-manual-debet') && ($payment_number[0] != 'voucher-manual-debet') && ($payment_number[0] != 'paid-manual-credit'))
                                        <tr>
                                            <td>{{\Carbon\Carbon::parse($member_deposit->created_at)->format('d-m-Y')}}</td>
                                            <td>{{$member_deposit->memberdeposit_user->user_club->club_name}}</td>
                                            <td>{{$member_deposit->memberdeposit_user->name}}</td>
                                            <td>
                                                {{$member_deposit->memberdeposit_user->user_memberpersonal->phone}}
                                                @if(!empty($member_deposit->memberdeposit_user->user_memberpersonal->cellphone) && ($member_deposit->memberdeposit_user->user_memberpersonal->phone != $member_deposit->memberdeposit_user->user_memberpersonal->cellphone))
                                                    <br>
                                                    {{$member_deposit->memberdeposit_user->user_memberpersonal->cellphone}}
                                                @endif
                                            </td>
                                            @if($member_deposit->payment_type == 'Refund')
                                                <td>@lang('labels.refund')</td>
                                                <td class="my_rupiah">{{$member_deposit->debet}}</td>
                                            @elseif($member_deposit->payment_type == 'Payment')
                                                <td>@lang('labels.bill')</td>
                                                <td class="my_rupiah">{{$member_deposit->kredit}}</td>
                                            @endif
                                            <td><a href="{{url('cf-lite/financial-management/member-deposit/'.$member_deposit->id)}}">{{$member_deposit->payment_number}}</a></td>
                                        </tr>
                                    @endif
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @if(!empty($member_deposits) && (count($member_deposits) > 0))
                <div class="paging hiden rom">
                    {{$member_deposits->links()}}
                </div>
            @endif
        </div>
    </div>
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
@endsection
