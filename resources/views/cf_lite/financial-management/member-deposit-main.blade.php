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
            <a href="{{url('cf-lite/financial-management')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.member_deposit') <br><small>({{Auth::user()->user_club->club_name}})</small></h2>
            </center>
        </div>

        @if(empty($_GET['search_option']) && empty($_GET['s']))
            <div class="col-md-12">
                <h3>@lang('labels.pending_bill_or_refund')</h3>
            </div>
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-striped" id="data-table-deposit">
                                <thead>
                                <tr>
                                    <th>@lang('labels.date')</th>
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
        @endif
    </div>
    <hr>
    @if(empty($_GET['search_option']) && empty($_GET['s']))
        <div class="row">
            <div class="col-md-12">
                <h3>@lang('labels.balance_correction')</h3>
            </div>
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-striped" id="data-table-correction">
                                <thead>
                                <tr>
                                    <th>@lang('labels.date')</th>
                                    <th>@lang('labels.name')</th>
                                    <th>@lang('labels.phone')</th>
                                    <th>@lang('labels.correction_type')</th>
                                    <th>@lang('labels.amount')</th>
                                    <th>@lang('labels.information')</th>
                                    <th>@lang('labels.payment_number')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @if(count($member_deposits) > 0)
                                    @foreach($member_deposits as $member_deposit)
                                        @php($payment_number = explode('/', $member_deposit->payment_number, 2))
                                        @if(($payment_number[0] == 'manual-debet') || ($payment_number[0] == 'manual-credit') || ($payment_number[0] == 'cash-manual-debet') || ($payment_number[0] == 'voucher-manual-debet') || ($payment_number[0] == 'paid-manual-credit'))
                                            <tr>
                                                <td>{{\Carbon\Carbon::parse($member_deposit->created_at)->format('d-m-Y')}}</td>
                                                <td><a href="{{url('cf-lite/financial-management/member/'.$member_deposit->user_id)}}">{{$member_deposit->memberdeposit_user->name}}</a></td>
                                                <td>
                                                    {{$member_deposit->memberdeposit_user->user_memberpersonal->phone}}
                                                    @if(!empty($member_deposit->memberdeposit_user->user_memberpersonal->cellphone) && ($member_deposit->memberdeposit_user->user_memberpersonal->phone != $member_deposit->memberdeposit_user->user_memberpersonal->cellphone))
                                                        <br>
                                                        {{$member_deposit->memberdeposit_user->user_memberpersonal->cellphone}}
                                                    @endif
                                                </td>
                                                @if($member_deposit->payment_type == 'Refund')
                                                    <td>@lang('labels.refund')</td>
                                                    <td class="my_rupiah">
                                                        @if(!empty($member_deposit->debet))
                                                            {{$member_deposit->debet}}
                                                        @else
                                                            {{abs($member_deposit->saldo)}}
                                                        @endif
                                                    </td>
                                                @elseif($member_deposit->payment_type == 'Payment')
                                                    <td>@lang('labels.bill')</td>
                                                    <td class="my_rupiah">
                                                        @if(!empty($member_deposit->kredit))
                                                            {{$member_deposit->kredit}}
                                                        @else
                                                            {{abs($member_deposit->saldo)}}
                                                        @endif
                                                    </td>
                                                @endif
                                                <td>
                                                    @if(!empty($member_deposit->memberdeposit_information))
                                                        {{$member_deposit->memberdeposit_information->information}}
                                                    @endif
                                                </td>
                                                <td>
                                                    <a href="{{url('cf-lite/financial-management/member-deposit/'.$member_deposit->id.'/balance-correction')}}">
                                                        @php($payment_number = explode('/', $member_deposit->payment_number, 2))
                                                        {{$payment_number[1]}}
                                                    </a>
                                                </td>
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
    @endif
    <div class="row">
        <div class="col-md-12">
            <center>
                <h2>@lang('labels.member_list')</h2>
            </center>
        </div>
        <div class="col-md-8">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                            <tr>
                                <th>@lang('labels.name')</th>
                                <th>@lang('labels.keytag')</th>
                                <th>@lang('labels.phone')</th>
                                <th>@lang('labels.membership_status')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($members) > 0)
                                @foreach($members as $member)
                                    <tr>
                                        <td><a href="{{url('cf-lite/financial-management/member/'.$member->id)}}">{{$member->name}}</a></td>
                                        <td>{{$member->user_memberpersonal->key_tag_id}}</td>
                                        <td>
                                            {{$member->user_memberpersonal->phone}}
                                            @if(!empty($member->user_memberpersonal->cellphone) && ($member->user_memberpersonal->phone != $member->user_memberpersonal->cellphone))
                                                <br>
                                                {{$member->user_memberpersonal->cellphone}}
                                            @endif
                                        </td>
                                        <td>{{$member->user_memberstatus->status}}</td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @if(!empty($members) && (count($members) > 0))
                <div class="paging hiden rom">
                    {{$members->links()}}
                </div>
            @endif
        </div>
        <div class="col-md-4" style="border: 2px solid rebeccapurple; border-radius: 10px;">
            <h4>@lang('labels.search_member'):</h4>
            <div id="filter-zone">
                {!! Form::open(['method' => 'GET', 'action' => 'FinancialManagementController@member_deposit_main']) !!}
                <div class="form-group">
                    <label class="radio-inline">
                        {!! Form::radio('search_option', 'name', true, ['id' => 'search-option-1']) !!} @lang('labels.name')
                    </label>
                    <label class="radio-inline">
                        {!! Form::radio('search_option', 'keytag', false, ['id' => 'search-option-2']) !!} @lang('labels.key_tag')
                    </label>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        {!! Form::text('s', null, ['class' => 'form-control', 'placeholder' => trans('labels.input_search')]) !!}
                        <span class="input-group-btn">
                            <button class="btn btn-default btn-filter-zone" type="submit"><span class="fa fa-search"></span></button>
                        </span>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $('#data-table').dataTable();
        $('#data-table-deposit').dataTable();
        $('#data-table-correction').dataTable();
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
