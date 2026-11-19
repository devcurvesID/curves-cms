@extends('cf_lite.layouts.staff-template')

@section('other-style')
@endsection

@section('content')
    @include('cf_lite.includes.error-message')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/financial-management/member-deposit')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.member_deposit') <br><small>({{Auth::user()->user_club->club_name}})</small></h2>
            </center>
        </div>
    </div>
    <div class="row">
        <div class="col-md-3">
            @if(!empty($member->user_memberpersonal->photo))
                <img class="img-circle member-card-img" src="{{asset('images/member_photos/'.$member->user_memberpersonal->photo)}}" style="width: 150px; height: 150px; object-fit: cover;">
            @else
                <img class="img-circle member-card-img" src="{{asset('images/img.png')}}">
            @endif
        </div>
        <div class="col-md-6">
            <table>
                <tr>
                    <td><span class="fa fa-female" title="@lang('labels.name')"></span></td>
                    <td>:</td>
                    <td>{{$member->name}}</td>
                </tr>
                <tr>
                    <td><span class="fa fa-calendar" title="@lang('labels.joined')"></span></td>
                    <td>:</td>
                    <td>{{date('d F Y', strtotime($member->user_memberpersonal->joined))}}</td>
                </tr>
                <tr>
                    <td><span class="fa fa-group" title="@lang('labels.status')"></span></td>
                    <td>:</td>
                    <td>
                        @if($member->user_memberstatus->status == 'Active')
                            @lang('labels.active')
                        @elseif($member->user_memberstatus->status == 'Inactive')
                            @lang('labels.nonactive')
                        @elseif($member->user_memberstatus->status == 'Stopped')
                            @lang('labels.resign')
                        @endif
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-md-3">
            <a href="javascript:;" class="btn btn-warning pull-right" data-toggle="modal" data-target="#addCorrectionModal" title="@lang('labels.add_balance_correction')"><span class="fa fa-plus"></span> <span class="fa fa-edit"></span> @lang('labels.correction')</a>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>@lang('labels.date')</th>
                            <th>@lang('labels.payment_number')</th>
                            <th>@lang('labels.debet')</th>
                            <th>@lang('labels.credit')</th>
                            <th>@lang('labels.balance')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @if(count($member_deposits) > 0)
                            @php($total_debet = 0)
                            @php($total_kredit = 0)
                            @php($total_saldo = 0)
                            @foreach($member_deposits as $member_deposit)
                                <tr>
                                    <td>{{\Carbon\Carbon::parse($member_deposit->created_at)->format('d-m-Y')}}</td>
                                    <td>
                                        @if(!empty($member_deposit->payment_number))
                                            @php($md_status = explode('/', $member_deposit->payment_number, 2))
                                            @if($md_status[0] == 'paid')
                                                @lang('labels.payment'): {{$md_status[1]}}
                                            @elseif($md_status[0] == 'cash')
                                                @lang('labels.refund') @lang('labels.cash'): {{$md_status[1]}}
                                            @elseif($md_status[0] == 'voucher')
                                                @lang('labels.refund') @lang('labels.voucher'): {{$md_status[1]}}
                                            @elseif(($md_status[0] == 'manual-credit') || ($md_status[0] == 'manual-debet'))
                                                <a href="{{url('cf-lite/financial-management/member-deposit/'.$member_deposit->id.'/balance-correction')}}">
                                                    @lang('labels.balance_correction'): {{$md_status[1]}}
                                                </a>
                                            @else
                                                @if($member_deposit->status == 'Pending')
                                                    @if($md_status[0] == 'cash-manual-debet')
                                                        <a href="{{url('cf-lite/financial-management/member-deposit/'.$member_deposit->id.'/balance-correction')}}">
                                                            @lang('labels.cash_withdrawing'): {{$md_status[1]}}
                                                        </a>
                                                    @elseif($md_status[0] == 'voucher-manual-debet')
                                                        <a href="{{url('cf-lite/financial-management/member-deposit/'.$member_deposit->id.'/balance-correction')}}">
                                                            @lang('labels.voucher_withdrawing'): {{$md_status[1]}}
                                                        </a>
                                                    @elseif($md_status[0] == 'paid-manual-credit')
                                                        <a href="{{url('cf-lite/financial-management/member-deposit/'.$member_deposit->id.'/balance-correction')}}">
                                                            @lang('labels.payment'): {{$md_status[1]}}
                                                        </a>
                                                    @else
                                                        <a href="{{url('cf-lite/financial-management/member-deposit/'.$member_deposit->id)}}">
                                                            {{$member_deposit->payment_number}}
                                                        </a>
                                                    @endif
                                                @else
                                                    @if($md_status[0] == 'cash-manual-debet')
                                                        @lang('labels.cash_withdrawing'): {{$md_status[1]}}
                                                    @elseif($md_status[0] == 'voucher-manual-debet')
                                                        @lang('labels.voucher_withdrawing'): {{$md_status[1]}}
                                                    @elseif($md_status[0] == 'paid-manual-credit')
                                                        @lang('labels.payment'): {{$md_status[1]}}
                                                    @else
                                                        {{$member_deposit->payment_number}}
                                                    @endif
                                                @endif
                                            @endif
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td class="my_rupiah">
                                        {{$member_deposit->debet}}
                                        @php($total_debet = $total_debet + $member_deposit->debet)
                                    </td>
                                    <td class="my_rupiah">
                                        {{$member_deposit->kredit}}
                                        @php($total_kredit = $total_kredit + $member_deposit->kredit)
                                    </td>
                                    <td class="my_rupiah">
                                        {{$member_deposit->saldo}}
                                        @php($total_saldo = $member_deposit->saldo)
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="2" style="text-align: right; font-weight: 600;">@lang('labels.total'):</td>
                                <td class="my_rupiah" style="font-weight: 600;">{{$total_debet}}</td>
                                <td class="my_rupiah" style="font-weight: 600;">{{$total_kredit}}</td>
                                <td class="my_rupiah" style="font-weight: 600;">{{$total_saldo}}</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addCorrectionModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content" style="padding: 20px;">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.balance_correction')</h4>
                </div>
                {!! Form::open(['method' => 'POST', 'action' => ['FinancialManagementController@store_member_deposit_correction', $member->id], 'id' => 'correction-form']) !!}
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('payment_type', trans('labels.type').': ', ['class' => 'control-label col-md-2', 'style' => 'text-align: right;']) !!}
                            <div class="col-md-6">
                                <label class="radio-inline">
                                    {!! Form::radio('payment_type', 'payment', true) !!} @lang('labels.bill')
                                </label>
                                <label class="radio-inline">
                                    {!! Form::radio('payment_type', 'refund', false) !!} @lang('labels.refund')
                                </label>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('amount', trans('labels.amount').': ', ['class' => 'control-label col-md-2', 'style' => 'text-align: right;']) !!}
                            <div class="col-md-3">
                                {!! Form::text('amount', '0', ['class' => 'form-control rupiah']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('information', trans('labels.information').': ', ['class' => 'control-label col-md-2', 'style' => 'text-align: right;']) !!}
                            <div class="col-md-4">
                                {!! Form::textarea('information', null, ['class' => 'form-control', 'rows' => '4']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">@lang('labels.close')</button>
                    <button type="button" id="correction-btn-submit" class="btn btn-primary">@lang('labels.save')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script>
        $(document).ready(function () {
            $('.rupiah').each(function () {
                $(this).wrap('<div class="input-group"></div>')
                $(this).before('<span class="input-group-addon">Rp.</span>');
                $(this).after('<span class="input-group-addon">,-</span>');
            });

            init_format();
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

    <script>
        $(document).ready(function () {
            $('#amount').on('keyup', function () {
                if ($(this).val() == "") {
                    var nominal = 0;
                } else {
                    var nominal = parseInt(rupiah_format_destroyer($(this).val()));
                }

                $(this).val(rupiah_formater(nominal));
            });

            $('#correction-btn-submit').on('click', function () {
                var nominal = parseInt(rupiah_format_destroyer($('#amount').val()));
                $('input[name="amount"]').val(nominal);
                $('#correction-form').submit();
            });
        });
    </script>
@endsection