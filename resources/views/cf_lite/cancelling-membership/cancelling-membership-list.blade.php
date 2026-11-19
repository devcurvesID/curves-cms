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
            <a href="{{url('cf-lite/cancelling-membership')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.membership_cancellation_list') <br><small>({{\Illuminate\Support\Facades\Auth::user()->user_club->club_name}})</small></h2>
            </center>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <h3>@lang('labels.pending_cancellation_process')</h3>
        </div>
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="data-table-3">
                            <thead>
                            <tr>
                                <th>@lang('labels.photo')</th>
                                <th>@lang('labels.fullname')</th>
                                <th>@lang('labels.phone')</th>
                                <th>@lang('labels.filling_date')</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($pending_members))
                                @foreach($pending_members as $pending_member)
                                    <tr>
                                        <td>
                                            @if(!empty($pending_member->refundsubmission_user->user_memberpersonal->photo))
                                                <img class="img-circle" src="{{asset('images/member_photos/'.$pending_member->refundsubmission_user->user_memberpersonal->photo)}}" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                {{--<a href="#">--}}
                                                <img class="img-circle" src="{{asset('images/img.png')}}" title="@lang('labels.photo')" style="width: 40px; height: 40px; object-fit: cover;">
                                                {{--</a>--}}
                                            @endif
                                        </td>
                                        <td>{{$pending_member->refundsubmission_user->name}} </td>
                                        <td>{{$pending_member->refundsubmission_user->user_memberpersonal->phone}}</td>
                                        <td>{{\Carbon\Carbon::parse($pending_member->refund_submission_date)->format('d-m-Y')}}</td>
                                        <td>
                                            <a href="{{url('cf-lite/refund/'.$pending_member->id.'/cancellation-detail')}}" class="btn btn-info">@lang('labels.information') <span class="fa fa-chevron-right"></span> </a>
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

    <hr>
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
        </div>
    </div>

    <hr>
    <div class="row">
        <div class="col-md-12">
            <h3>@lang('labels.stopped_member_list')</h3>
        </div>
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="data-table-2">
                            <thead>
                            <tr>
                                <th>@lang('labels.photo')</th>
                                <th>@lang('labels.fullname')</th>
                                <th>@lang('labels.phone')</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(!empty($cancelled_members))
                                @foreach($cancelled_members as $cancelled_member)
                                    <tr>
                                        <td>
                                            @if(!empty($cancelled_member->user_memberpersonal->photo))
                                                <img class="img-circle" src="{{asset('images/member_photos/'.$cancelled_member->user_memberpersonal->photo)}}" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                {{--<a href="#">--}}
                                                <img class="img-circle" src="{{asset('images/img.png')}}" title="@lang('labels.photo')" style="width: 40px; height: 40px; object-fit: cover;">
                                                {{--</a>--}}
                                            @endif
                                        </td>
                                        <td>{{$cancelled_member->name}} </td>
                                        <td>{{$cancelled_member->user_memberpersonal->phone}}</td>
                                        <td>
                                            <a href="{{url('cf-lite/continue-membership/'.$cancelled_member->id.'/reactivate')}}" class="btn btn-warning">@lang('labels.reactivate') <span class="fa fa-chevron-right"></span> </a>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @if(!empty($cancelled_members) && (count($cancelled_members) > 0))
                <div class="paging hiden rom">
                    {{$cancelled_members->links()}}
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
        $('#data-table-2').dataTable();
        $('#data-table-3').dataTable();
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
