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
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/locker/choose-packet',$lockerinfo_id)}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.payment_detail')</h2>
            </center>
        </div>
    </div>

    @include('cf_lite.includes.error-message')

    {!! Form::open(['method' => 'POST', 'action' => 'LockerController@store_locker_payment', 'id' => 'form-product-payment']) !!}
    {!! Form::hidden('lockerinfo_id', $lockerinfo_id) !!}
    <div class="row">
        <div class="col-md-6">
            <table class="member-detail">
                <tr>
                    <td>@lang('labels.staff')</td>
                    <td>:</td>
                    <td>
                        {!! Form::select('staff',$staff,Auth::user()->id,['class'=>'form-control']) !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang('labels.payment_number')</td>
                    <td>:</td>
                    <td>
                        @php($month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'));
                        {{sprintf('%08d', $temp_auto_number->current_number)}}/PY/CIND/{{sprintf('%03d', $club_id)}}/{{$month_roman[\Carbon\Carbon::now()->format('n')]}}/{{\Carbon\Carbon::now()->format('Y')}}
                        {!! Form::hidden('payment_number', '/PY/CIND/'.sprintf('%03d', $club_id).'/'.$month_roman[\Carbon\Carbon::now()->format('n')].'/'.\Carbon\Carbon::now()->format('Y')) !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang('labels.type')</td>
                    <td>:</td>
                    <td>
                        <select name="neworrenew" class="form-control">
                            <option value="0">@lang('labels.new_locker')</option>
                            @if(count($memberslocker) > 0)
                                @foreach($memberslocker as $ml)
                                    @if($ml->locker_info_id == $lockerinfo_id)
                                        <option value="{{$ml->id}}" selected>(Loker {{$ml->memberlocker_lockerinfo->locker_name}}) s/d {{Carbon\Carbon::parse($ml->thru_date)->format('d-m-Y')}}</option>
                                    @else
                                        <option value="{{$ml->id}}">(Loker {{$ml->memberlocker_lockerinfo->locker_name}}) s/d {{Carbon\Carbon::parse($ml->thru_date)->format('d-m-Y')}}</option>
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">
            <table class="member-detail">
                <tr>
                    <td>@lang('labels.date')</td>
                    <td>:</td>
                    <td>{!! Form::date('payment_date',Carbon\Carbon::now(),['class'=>'form-control']) !!}</td>
                </tr>
                <tr>
                    <td>{!! Form::label('payment_method', trans('labels.payment_method'), ['class' => 'control-label']) !!}</td>
                    <td>:</td>
                    <td>{!! Form::select('payment_method', $payment_methods, null, ['class' => 'form-control', 'placeholder' => '-- '.trans('labels.choose').' '.trans('labels.payment_method').' --']) !!}</td>
                </tr>
                <tr id='start_date_row'>
                    <td>@lang('labels.from_date')</td>
                    <td>:</td>
                    <td>{!! Form::date('start_date',Carbon\Carbon::now(),['class' => 'form-control']) !!}</td>
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
                            <td>{{$locker->name}}</td>
                            <td class="my_rupiah">{{$locker->price}}</td>
                            <td>{{$qty}}</td>
                            <td class="my_rupiah">{{$locker->price * $qty}}</td>
                        </tr>
                        @php($total = $total + ($locker->price * $qty))
                        <tr>
                            <td colspan="4" style="text-align: right;">@lang('labels.subtotal'): </td>
                            <td class="my_rupiah">
                                {{$total}}
                            </td>
                            {!! Form::hidden('subtotal', $total) !!}
                            {!! Form::hidden('qty', $qty) !!}
                            {!! Form::hidden('locker', $locker->id) !!}
                            {!! Form::hidden('user_id', $member->id) !!}
                        </tr>
                        {{--<tr>--}}
                            {{--<td colspan="3" style="text-align: right;">@lang('labels.tax'):</td>--}}
                            {{--<td>--}}
                                {{--{!! Form::select('tax', $taxes, null, ['class' => 'form-control', 'placeholder' => '-- '.trans('labels.choose').' '.trans('labels.tax_type').' --']) !!}--}}
                            {{--</td>--}}
                            {{--<td>--}}
                                {{--{!! Form::text('tax_value', '0', ['class' => 'form-control']) !!}--}}
                            {{--</td>--}}
                        {{--</tr>--}}
                        <tr>
                            <td colspan="4" style="text-align: right;">@lang('labels.total'): </td>
                            <td>
                                <div id="billing-total" class="my_rupiah">{{$total}}</div>
                                {!! Form::hidden('billing-total', $total) !!}
                            </td>
                        </tr>
                        <tr>
                            <td colspan="4" style="text-align: right;">@lang('labels.pay'): </td>
                            <td>
                                {!! Form::text('pay', null, ['class' => 'form-control rupiah']) !!}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-success btn-lg pull-right" id="payment-process">@lang('labels.pay')</button>
        </div>
    </div>
    {!! Form::close() !!}
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

            if($('select[name="neworrenew"]').val()=='0'){
                $('#start_date_row').show();
            }else{
                $('#start_date_row').hide();
            }

            $('select[name="neworrenew"]').on('change', function () {
                if($('select[name="neworrenew"]').val()=='0'){
                    $('#start_date_row').show();
                }else{
                    $('#start_date_row').hide();
                }
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

        $('input[name="pay"]').on('keyup', function () {
            if ($(this).val() == "") {
                var total_pay = 0;
            } else {
                var total_pay = parseInt(rupiah_format_destroyer($('input[name="pay"]').val()));
            }
            $('input[name="pay"]').val(rupiah_formater(total_pay));
        });
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>

    <script>
        $(document).ready(function () {
            var url = "{{url('cf-lite')}}/";

            $('#payment-process').on('click', function () {
                if (parseInt(rupiah_format_destroyer($('input[name="pay"]').val())) < parseInt(rupiah_format_destroyer($('input[name="billing-total"]').val()))) {
                    alert('@lang('messages.not_enough_money')');
                } else {
                    var pay = parseInt(rupiah_format_destroyer($('input[name="pay"]').val()));
                    var billing = parseInt(rupiah_format_destroyer($('input[name="billing-total"]').val()));
                    $('input[name="pay"]').val(pay);
                    $('input[name="billing-total"]').val(billing);
                    $('#form-product-payment').submit();
                }
            });
        });
    </script>
@endsection