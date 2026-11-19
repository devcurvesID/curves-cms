@extends('cf_lite.layouts.staff-template')

@section('other-style')
<link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
<link href="{{asset('css/payment.css')}}" rel="stylesheet">
<style>
    /*body{*/
    /*color: white;*/
    /*}*/
    .modal-dialog,
    .modal-dialog label {
        color: #414042;
    }

    table {
        color: #414042;
    }

    .vip-box,
    .vip-box-gold {
        width: 400px;
        height: 100px;
        padding: 5px;
        padding-left: 15px;
        padding-right: 15px;
        border: 2px solid deeppink;
        border-radius: 7px;
        color: deeppink;
    }

    .vip-box-gold {
        border-color: gold;
        color: darkgoldenrod;
    }

    #print-btn {
        display: none;
        width: 100% !important;
        /* height: 100px; */
        height: 50px;
        margin-bottom: 10px;
        background-color: turquoise !important;
    }

    #reprint-customer {
        display: none;
        width: 100% !important;
        height: 50px;
    }

    #reprint-club {
        display: none;
        width: 100% !important;
        height: 50px;
    }

    #SendMailWrapper{
        width: 100%;
        background: rgba(255,255,255, 0.8);
        height: 100%;
        position: fixed;
        top: 0;
        left: 0;
        z-index: 9999;
        display: none;
    }
        #SendMailForm{
            width: 400px;
            height: 180px;
            background-color: rgba(255, 240, 147, 1);
            border: 6px solid rgba(255, 20, 147, 1);
            color: rgba(118, 43, 144, 1);
            border-radius: 10px;
            position: absolute; /*Can also be `fixed`*/
            left: 0;
            right: 0;
            top: 0;
            bottom: 0;
            margin: auto;
            /*Solves a problem in which the content is being cut when the div is smaller than its' wrapper:*/
            max-width: 100%;
            max-height: 100%;
            overflow: auto;
            padding: 0 1.5rem;
        }
        .marginRight10{ margin-right: 5px;}

    .modalBtns{
        display: grid;
        grid-template-columns: auto auto ;
    }
    .modalBtns > div {
        text-align: center;
    }
    #ezypay-checkout-modal .ezypay-intro { margin: 8px 0 0; font-size: 13px; }
    #ezypay-checkout-modal .ezypay-summary { background: #f7f5fa; border: 1px solid #e5dfeb; border-radius: 6px; padding: 18px; margin-bottom: 18px; overflow-wrap: anywhere; }
    #ezypay-checkout-modal h5 { font-size: 15px; font-weight: 600; margin: 0 0 16px; }
    #ezypay-checkout-modal dt { color: #666; font-weight: normal; font-size: 12px; margin-top: 14px; }
    #ezypay-checkout-modal dd { margin: 3px 0 0; font-weight: 600; }
    #ezypay-checkout-modal .ezypay-choice { border: 1px solid #e4e4e4; border-radius: 6px; padding: 8px 14px; margin-bottom: 16px; }
    #ezypay-checkout-modal .help-block { font-size: 12px; line-height: 1.6; margin-bottom: 8px; }
    #ezypay-checkout-modal .ezypay-amount { border-top: 1px solid #e5dfeb; padding-top: 14px; margin-top: 16px; }
    #ezypay-checkout-modal .ezypay-amount strong { display: block; font-size: 24px; margin: 4px 0; }
    #ezypay-checkout-modal .ezypay-mode-summary p { margin: 6px 0 0; font-size: 13px; }
    #membership-payment-loading {
        position: fixed;
        inset: 0;
        z-index: 1100;
        background: rgba(0, 0, 0, 0.45);
        place-items: center;
        padding: 16px;
    }
    #membership-payment-loading:not([hidden]) { display: grid; }
    #membership-payment-loading[hidden], #membership-payment-error[hidden] { display: none; }
    #membership-payment-error { white-space: pre-line; }
    .membership-payment-loading-panel {
        background: #fff;
        border-radius: 6px;
        color: #414042;
        padding: 24px;
        text-align: center;
    }
    .membership-payment-spinner {
        display: block;
        width: 28px;
        height: 28px;
        margin: 0 auto 12px;
        border: 3px solid #e5dfeb;
        border-top-color: rebeccapurple;
        border-radius: 50%;
        animation: membership-payment-spin 0.8s linear infinite;
    }
    @keyframes membership-payment-spin { to { transform: rotate(360deg); } }
    @media (prefers-reduced-motion: reduce) {
        .membership-payment-spinner { animation: none; }
    }
</style>
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12">
        <center>
            <h2 class="page-title">@lang('labels.membership_payment')</h2>
        </center>
    </div>
</div>

@include('cf_lite.includes.error-message')

{!! Form::open(['action' => 'PaymentController@store_cashier_membership', 'method' => 'POST', 'id' => 'form-payment'])
!!}
{!! Form::hidden('sf_amount', null) !!}
{!! Form::hidden('mf_amount', null) !!}
{!! Form::hidden('club_id', Auth::user()->club_id) !!}
{!! Form::hidden('login_user', Auth::user()->id) !!}
{!! Form::hidden('receipt', null, ['id' => 'receipt']) !!}
{!! Form::hidden('receiptID', null, ['id' => 'receiptID']) !!}
<div class="row">
    <div class="col-md-6">
        <div class="form-horizontal">
            <div class="form-group">
                {!! Form::label('fa_coach', trans('labels.fa_coach').': ', ['class' => 'control-label col-md-4', 'style'
                => 'color: deeppink; margin-left: 10px;']) !!}
                <div class="col-md-6">
                    @if(!empty(Session::get('staff_session')))
                    {!! Form::select('fa_coach', $staffs, Session::get('staff_session'), ['class' => 'form-control
                    select2']) !!}
                    @else
                    {!! Form::select('fa_coach', $staffs, Auth::id(), ['class' => 'form-control select2']) !!}
                    @endif
                </div>
            </div>
            <div class="form-group">
                {!! Form::label('fa_date', trans('labels.date').': ', ['class' => 'control-label col-md-4', 'style' =>
                'color: deeppink; margin-left: 10px;']) !!}
                <div class="col-md-6">
                    {!! Form::date('fa_date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                </div>
            </div>
            {{--Added 19032020--}}
            <div class="form-group">
                {!! Form::label('bonus_duration', trans('labels.bonus_duration').': ', ['class' => 'control-label
                col-md-4', 'style' => 'color: deeppink; margin-left: 10px;']) !!}
                <div class="col-md-3">
                    {!! Form::number('bonus_duration', 0, ['class' => 'form-control']) !!}
                </div>
                <div class="col-md-3">
                    {!! Form::select('bonus_type', ['day' => trans('labels.day'), 'week' => trans('labels.week'),
                    'month' => trans('labels.month')], null, ['class' => 'form-control']) !!}
                </div>
            </div>
            {{--Added 19032020--}}
        </div>
    </div>
</div>
<hr>
<div class="row">
    <div class="col-md-6">
        <table class="member-detail">
            <tr>
                <td>{!! Form::label('lead', trans('labels.lead'), ['class' => 'control-label']) !!}</td>
                <td>:</td>
                <td>
                    <select name="lead" class="form-control select2" id="lead">
                        <option value="">@lang('labels.choose') @lang('labels.lead')</option>
                        @foreach($brls as $brl)
                        @if(empty($selected_brl))
                        <option value="{{$brl->id}}" nm="{{ $brl->fullname }}" ph="{{ $brl->phone }}" data-email="{{ $brl->email }}" data-wa="{{ $brl->wa_number }}" data-ezypay-customer-id="{{ $brl->customer_id_ezypay }}"
                            so="{{ $brl->source_id }}">{{ $brl->fullname }}</option>
                        @else
                        @if($brl->id == $selected_brl->id)
                        <option value="{{$brl->id}}" nm="{{ $brl->fullname }}" ph="{{ $brl->phone }}" data-email="{{ $brl->email }}" data-wa="{{ $brl->wa_number }}" data-ezypay-customer-id="{{ $brl->customer_id_ezypay }}"
                            so="{{ $brl->source_id }}" selected>{{ $brl->fullname }}</option>
                        @else
                        <option value="{{$brl->id}}" nm="{{ $brl->fullname }}" ph="{{ $brl->phone }}" data-email="{{ $brl->email }}" data-wa="{{ $brl->wa_number }}" data-ezypay-customer-id="{{ $brl->customer_id_ezypay }}"
                            so="{{ $brl->source_id }}">{{ $brl->fullname }}</option>
                        @endif
                        @endif
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr>
                <td>{!! Form::label('premember_name', trans('labels.premember_name'), ['class' => 'control-label']) !!}
                </td>
                <td>:</td>
                @if(empty($selected_brl))
                <td>{!! Form::text('premember_name', null, ['class' => 'form-control']) !!}</td>
                @else
                <td>{!! Form::text('premember_name', $selected_brl->fullname, ['class' => 'form-control','readonly' =>
                    'readonly']) !!}</td>
                @endif
            </tr>
            <tr>
                <td>{!! Form::label('phone', trans('labels.phone'), ['class' => 'control-label']) !!}</td>
                <td>:</td>
                @if(empty($selected_brl))
                <td>{!! Form::text('phone', null, ['class' => 'form-control']) !!}</td>
                @else
                <td>{!! Form::text('phone', $selected_brl->phone, ['class' => 'form-control','readonly' => 'readonly'])
                    !!}</td>
                @endif
            </tr>
            <tr>
                <td>{!! Form::label('source', trans('labels.source'), ['class' => 'control-label']) !!}</td>
                <td>:</td>
                @if(empty($selected_brl))
                <td>{!! Form::select('source', $sources, null, ['class' => 'form-control select2', 'id' =>
                    'source','placeholder' => trans('labels.choose')]) !!}</td>
                @else
                <td>{!! Form::select('source', $sources, $selected_brl->source_id, ['class' => 'form-control select2',
                    'id' => 'source','disabled'=>'disabled','placeholder' => trans('labels.choose')]) !!}</td>
                @endif
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <table class="member-detail">
            <tr>
                <td>{!! Form::label('payment_method', trans('labels.payment_method'), ['class' => 'control-label']) !!}
                </td>
                <td>:</td>
                <td>{!! Form::select('payment_method', $payment_methods, null, ['class' => 'form-control', 'placeholder' => trans('labels.choose')]) !!}</td>
            </tr>
            <tr id="membership-type-row" style="display:none">
                <td>{!! Form::label('membership_type', trans('labels.membership_type'), ['class' => 'control-label'])
                    !!}</td>
                <td>:</td>
                <td>
                    <select name="membership_type" class="form-control select2" id="membership_type" style="width:100%">
                        <option value="">@lang('labels.choose_membership')</option>
                        @foreach($membership_types as $membership_type)
                        <option value="{{$membership_type->id}}" sf="{{$membership_type->sf_amount}}"
                            mf="{{$membership_type->mf_amount}}" dc="{{$membership_type->pre_pay_discount_value}}"
                            dur="{{$membership_type->duration}}">{{$membership_type->membership_type_name}}</option>
                        @endforeach
                    </select>
                    <p id="ezypay-plan-message" role="status" aria-live="polite" style="display:none"></p>
                </td>
            </tr>
            <tr id="bank-row">
                <td>{!! Form::label('bank', trans('labels.bank'), ['class' => 'control-label']) !!}</td>
                <td>:</td>
                <td>
                    <select name="bank" class="form-control select2" id="bank">
                        <option value="">@lang('labels.choose') @lang('labels.bank')</option>
                        @foreach($banks as $bank)
                        <option value="{{$bank->id}}">{{$bank->bank_abv}} - {{ $bank->bank_account_number }}</option>
                        @endforeach
                    </select>
                </td>
            </tr>
            <tr id="referral-detail">

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
                        <th colspan="3">@lang('labels.payment')</th>
                    </tr>
                    <tr>
                        <th>@lang('labels.type')</th>
                        <th colspan="2">@lang('labels.fee')</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>@lang('labels.service_fee')</td>
                        <td colspan="2" class="my_rupiah" id="sf-amount-txt">0</td>
                    </tr>
                    <tr>
                        <td>@lang('labels.monthly_fee')</td>
                        <td colspan="2" class="my_rupiah" id="mf-amount-txt">0</td>
                    </tr>
                    <tr>
                        <td>@lang('labels.discount')</td>
                        <td colspan="2">
                            <div class="input-group" style="max-width: 250px;">
                                <span class="input-group-addon">Rp.</span>
                                {!! Form::text('discount', null, ['class' => 'form-control rupiah']) !!}
                                <span class="input-group-addon">,-</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('labels.deposit')</td>
                        {{-- <td>
                            {!! Form::select('depositorvouch',['deposit' => trans('labels.deposit'), 'voucher' =>
                            trans('labels.voucher')]) !!}
                        </td> --}}
                        <td colspan="2">
                            <div class="input-group" style="max-width: 250px;">
                                <span class="input-group-addon">Rp.</span>
                                {!! Form::text('deposit', 0, ['class' => 'form-control rupiah','id'=>'deposit']) !!}
                                <span class="input-group-addon">,-</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>@lang('labels.total')</td>
                        <td colspan="2">
                            <div class="input-group" style="max-width: 250px;">
                                <span class="input-group-addon">Rp.</span>
                                {!! Form::text('total', null, ['class' => 'form-control rupiah', 'readonly']) !!}
                                <span class="input-group-addon">,-</span>
                            </div>
                        </td>
                    </tr>
                    <tr id="first_click">
                        <td>@lang('labels.pay')</td>
                        <td>
                            <div class="input-group" style="max-width: 250px;">
                                <span class="input-group-addon">Rp.</span>
                                {!! Form::text('pay', 0, ['class' => 'form-control rupiah', 'id' => 'pay']) !!}
                                <span class="input-group-addon">,-</span>
                            </div>
                        </td>
                        <td rowspan="2">
                            {!! Form::button(trans('labels.pay'), ['class' => 'btn btn-success btn-lg', 'id' =>
                            'pay-btn']) !!}

                            {{-- {!! Form::button('Send Receipt', ['class' => 'btn btn-success btn-lg', 'id' =>
                            'print-btn']) !!} --}}
                            {{-- {!! Form::button(trans('labels.reprint'), ['class' => 'btn btn-success btn-lg', 'id' =>
                            'print-btn']) !!} --}}
                        </td>
                    </tr>
                    <tr id="after_click">
                        <td>@lang('labels.rest_of_the_bill')</td>
                        <td class="my_rupiah" id="rest-of-bill-txt">0</td>
                    </tr>
                </tbody>
            </table>
            <div id="membership-payment-error" class="alert alert-danger" role="alert" hidden></div>
        </div>
    </div>
</div>
{!! Form::close() !!}

<div id="membership-payment-loading" role="status" hidden>
    <div class="membership-payment-loading-panel">
        <span class="membership-payment-spinner" aria-hidden="true"></span>
        <strong>Sedang memproses pembayaran</strong>
        <div>Mohon tunggu hingga hasil pembayaran muncul.</div>
    </div>
</div>

<div class="modal fade" id="ezypay-payment-link-modal" tabindex="-1" role="dialog" aria-labelledby="ezypay-payment-link-title">
    <div class="modal-dialog" role="document"><div class="modal-content">
        <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Tutup">&times;</button>
            <h4 class="modal-title" id="ezypay-payment-link-title">Tautan Pembayaran Mandiri</h4></div>
        <div class="modal-body"><p>Berikan tautan berikut kepada customer untuk menyelesaikan pembayaran. Pembayaran belum lunas sampai customer membayar.</p>
            <label for="ezypay-payment-link">Tautan checkout</label><input id="ezypay-payment-link" class="form-control" readonly>
            <p><a id="ezypay-payment-link-open" target="_blank" rel="noopener noreferrer">Buka halaman pembayaran</a></p>
        </div>
        <div class="modal-footer"><button type="button" class="btn btn-primary" data-dismiss="modal">Tutup</button></div>
    </div></div>
</div>

<div class="modal fade" id="ezypay-checkout-modal" tabindex="-1" role="dialog" aria-labelledby="ezypay-checkout-title">
    <div class="modal-dialog modal-lg" role="document"><div class="modal-content">
        <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Tutup">&times;</button>
            <h4 id="ezypay-checkout-title" class="modal-title">Pilih Metode Pembayaran EZYPAY</h4>
            <p class="text-muted ezypay-intro">Periksa calon anggota, nominal paket, dan cara pembayaran sebelum melanjutkan.</p>
        </div>
        <div class="modal-body">
            <div id="ezypay-checkout-message" role="status" aria-live="polite"></div>
            <div class="row">
                <div class="col-sm-5">
                    <div class="ezypay-summary">
                        <h5>Ringkasan keanggotaan</h5>
                        <dl><dt>Calon anggota</dt><dd id="ezypay-checkout-customer">—</dd>
                            <dt>Email</dt><dd id="ezypay-checkout-email">—</dd>
                            <dt>Tipe keanggotaan</dt><dd id="ezypay-checkout-membership">—</dd></dl>
                        <div id="ezypay-checkout-plan"></div>
                    </div>
                </div>
                <div class="col-sm-7">
                    <h5>Cara pembayaran</h5>
                    <div class="ezypay-choice">
                        <div class="checkbox"><label><input type="checkbox" id="ezypay-checkout-self-payment" aria-describedby="ezypay-self-help"> <strong>Pembayaran Mandiri</strong></label></div>
                        <p id="ezypay-self-help" class="help-block">Buat tautan checkout untuk customer. Customer menyelesaikan pembayaran melalui tautan tersebut; kartu tetap dapat dipilih di bawah.</p>
                    </div>
                    <div class="form-group" id="ezypay-checkout-method-row">
                        <label for="ezypay-checkout-method">Metode pembayaran customer</label>
                        <select id="ezypay-checkout-method" class="form-control" aria-describedby="ezypay-method-help" disabled><option value="">Pilih metode pembayaran</option></select>
                        <p id="ezypay-method-help" class="help-block">Hanya metode yang valid ditampilkan. Jika tidak dipilih, Pembayaran Mandiri digunakan otomatis.</p>
                    </div>
                    <div class="ezypay-choice" id="ezypay-checkout-subscription-row">
                        <div class="checkbox"><label><input type="checkbox" id="ezypay-checkout-subscription" aria-describedby="ezypay-subscription-help"> <strong>Subscription</strong></label></div>
                        <p id="ezypay-subscription-help" class="help-block">Aktifkan untuk membuat langganan berkala. Jika tidak dicentang, pembayaran diproses sebagai invoice sekali bayar menggunakan metode yang dipilih.</p>
                    </div>
                    <div class="alert alert-info ezypay-mode-summary" aria-live="polite">
                        <strong id="ezypay-checkout-mode-title">Menyiapkan pembayaran</strong>
                        <p id="ezypay-checkout-mode-description"></p>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
            <button id="ezypay-checkout-retry" type="button" class="btn btn-default">Muat Ulang Data</button>
            <button id="ezypay-checkout-continue" type="button" class="btn btn-primary" disabled>Lanjutkan</button>
        </div>
    </div></div>
</div>

<div class="modal fade" id="ezypay-methods-modal" tabindex="-1" role="dialog" aria-labelledby="ezypay-methods-title">
    <div class="modal-dialog modal-lg" role="document"><div class="modal-content">
        <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Tutup">&times;</button>
            <h4 class="modal-title" id="ezypay-methods-title">Metode Pembayaran EZYPAY</h4></div>
        <div class="modal-body">
            <p id="ezypay-methods-name"></p>
            <div id="ezypay-methods-message" role="status" aria-live="polite"></div>
            <div id="ezypay-methods-table" class="table-responsive" style="display:none">
                <table class="table table-bordered"><thead><tr><th>Jenis</th><th>Pemilik</th><th>Nomor kartu</th><th>Berlaku hingga</th><th>Status</th><th>Utama</th></tr></thead>
                    <tbody id="ezypay-methods-rows"></tbody></table>
            </div>
            <button type="button" id="ezypay-card-add" class="btn btn-success">Tambah Metode Pembayaran</button>
            <form id="ezypay-card-form" style="display:none" autocomplete="off">
                <hr><h4>Tambah Kartu</h4>
                <div id="ezypay-card-message" role="alert" style="display:none"></div>
                <div class="form-group"><label for="ezypay-card-holder">Nama pemilik kartu</label><input id="ezypay-card-holder" class="form-control" maxlength="255" required></div>
                <div class="form-group"><label for="ezypay-card-number">Nomor kartu</label><input id="ezypay-card-number" class="form-control" inputmode="numeric" pattern="[0-9]{12,19}" maxlength="19" required></div>
                <div class="row">
                    <div class="col-sm-6 form-group"><label for="ezypay-card-month">Bulan kedaluwarsa (MM)</label><input id="ezypay-card-month" class="form-control" inputmode="numeric" pattern="0[1-9]|1[0-2]" placeholder="MM" maxlength="2" required></div>
                    <div class="col-sm-6 form-group"><label for="ezypay-card-year">Tahun kedaluwarsa (YY)</label><input id="ezypay-card-year" class="form-control" inputmode="numeric" pattern="[0-9]{2}" placeholder="YY" maxlength="2" required></div>
                </div>
                <div class="checkbox"><label><input id="ezypay-card-consent" type="checkbox" required> Pemilik kartu menyetujui kartu ini dimasukkan ke sistem.</label></div>
                <button type="submit" id="ezypay-card-save" class="btn btn-primary">Simpan Kartu</button>
                <button type="button" id="ezypay-card-cancel" class="btn btn-default">Batal</button>
            </form>
        </div>
        <div class="modal-footer"><button type="button" id="ezypay-methods-retry" class="btn btn-default">Muat Ulang</button>
            <button type="button" class="btn btn-primary" data-dismiss="modal">Tutup</button></div>
    </div></div>
</div>

<div class="modal fade" id="ezypay-contact-modal" tabindex="-1" role="dialog" aria-labelledby="ezypay-contact-title">
    <div class="modal-dialog" role="document"><div class="modal-content">
        <form id="ezypay-contact-form">
            <div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Tutup">&times;</button>
                <h4 class="modal-title" id="ezypay-contact-title">Data Calon Anggota EZYPAY</h4></div>
            <div class="modal-body">
                <p id="ezypay-lead-name"></p>
                <div id="ezypay-contact-error" class="alert alert-danger" style="display:none" role="alert"></div>
                <div class="form-group"><label for="ezypay-phone">Telepon</label><input id="ezypay-phone" class="form-control" type="tel" maxlength="50" required></div>
                <div class="form-group"><label for="ezypay-email">Email</label><input id="ezypay-email" class="form-control" type="email" maxlength="50" required></div>
                <div class="form-group"><label for="ezypay-wa">Nomor WhatsApp</label><input id="ezypay-wa" class="form-control" type="tel" maxlength="20" required></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-default" data-dismiss="modal">Batal</button>
                <button type="submit" id="ezypay-contact-save" class="btn btn-primary">Simpan dan Lanjutkan</button></div>
        </form>
    </div></div>
</div>


<!-- MODAL -->
<div id="SendMailWrapper">
    <div id="SendMailForm">
        <h3 align="center">Please enter Member's Email</h3>
        <div style="margin-top: 20px;"">
            {!! Form::text('memberEmail', '', ['class' => 'form-control rupiah','id'=>'memberEmail', 'placeholder'=>'Email address']) !!}
            <p id="result"></p>
        </div>
        <div class="modalBtns">
            <div style="text-align: left">
            {!! Form::button('Download', ['class' => 'btn btn-info btn-md', 'id' =>'download-btn']) !!}
            </div>
            <div style="text-align: right">
            {!! Form::button('Cancel', ['class' => 'btn btn-warning btn-md marginRight10', 'id' =>'cancelSendEmail-btn']) !!}

            {!! Form::button('Send Email', ['class' => 'btn btn-success btn-md', 'id' =>'sendEmail-btn']) !!}
            </div>
        </div>
    </div>
</div>
<!-- / MODAL -->
@endsection

@section('other-script')
<script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
<script>
    $(document).ready(function () {
            $('.select2').select2();

            function toggleMembershipType() {
                var paymentSelected = !!$('#payment_method').val();
                $('#membership-type-row').toggle(paymentSelected);
                $('#membership_type').prop('disabled', !paymentSelected);
            }
            $('#payment_method').on('change', toggleMembershipType);
            toggleMembershipType();

            function paymentUsesBank() {
                var method = $('#payment_method').val();
                return !!method && method !== '1' && !usesEzypay();
            }

            function toggleBankField() {
                var usesBank = paymentUsesBank();
                $('#bank-row').toggle(usesBank);
                $('#bank').prop('disabled', !usesBank);
                if (!usesBank) $('#bank').val('').trigger('change.select2');
            }
            $('#payment_method').on('change', toggleBankField);
            toggleBankField();

            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            function showMembershipError(xhr, action, fallback, afterAlert) {
                var result = xhr.responseJSON || {};
                var diagnostic = result.diagnostic || {};
                var message = [result.exception ? fallback : (result.message || fallback), 'Proses: ' + action];
                if (xhr.status) message.push('HTTP ' + xhr.status);
                if (diagnostic.stage) message.push('Tahap: ' + diagnostic.stage);
                if (diagnostic.file) message.push('Lokasi: ' + diagnostic.file + (diagnostic.line ? ':' + diagnostic.line : ''));
                if (diagnostic.exception_type) message.push('Jenis error: ' + diagnostic.exception_type);
                if (diagnostic.sqlstate) message.push('SQLSTATE: ' + diagnostic.sqlstate);
                if (diagnostic.database_object) message.push('Objek database: ' + diagnostic.database_object);
                if (diagnostic.http_status) message.push('HTTP EZYPAY: ' + diagnostic.http_status);
                if (diagnostic.provider_code) message.push('Kode EZYPAY: ' + diagnostic.provider_code);
                if (diagnostic.status) message.push('Status percobaan: ' + diagnostic.status);

                var text = message.join('\n');
                if (!xhr.membershipAlertShown) {
                    xhr.membershipAlertShown = true;
                    // Let the browser repaint the loading overlay before opening a blocking alert.
                    window.requestAnimationFrame(function() {
                        window.requestAnimationFrame(function() {
                            alert(text);
                            if (afterAlert) afterAlert();
                        });
                    });
                }
                return text;
            }

            var ezypayPlanReady = null;
            var ezypayPlanRequests = {};
            function currentEzypayPlanKey() {
                var option = $('#membership_type option:selected');
                return $('#membership_type').val() + ':' + option.attr('sf') + ':' + option.attr('mf');
            }
            function ensureEzypayPlan() {
                ezypayPlanReady = null;
                $('#ezypay-plan-message').hide();
                if (!usesEzypay() || !$('#membership_type').val()) return;
                var option = $('#membership_type option:selected');
                var key = currentEzypayPlanKey();
                $('#ezypay-plan-message').attr('class', 'text-info').text('Menyiapkan plan EZYPAY...').show();
                if (!ezypayPlanRequests[key]) {
                    ezypayPlanRequests[key] = $.ajax({
                        url: "{{ url('cf-lite/cashier/membership/ezypay-plan') }}",
                        type: 'POST', dataType: 'json',
                        data: {membership_type_id: $('#membership_type').val(), sf_amount: option.attr('sf'), mf_amount: option.attr('mf')}
                    });
                    ezypayPlanRequests[key].fail(function() { delete ezypayPlanRequests[key]; });
                }
                ezypayPlanRequests[key].done(function(result) {
                    if (!usesEzypay() || currentEzypayPlanKey() !== key) return;
                    if (!result.plan_id_ezypay) {
                        delete ezypayPlanRequests[key];
                        $('#ezypay-plan-message').attr('class', 'text-danger').text('ID plan EZYPAY tidak tersedia. Pilih ulang tipe keanggotaan.');
                        return;
                    }
                    ezypayPlanReady = key;
                    $('#ezypay-plan-message').attr('class', 'text-success').text('Plan EZYPAY siap digunakan.');
                }).fail(function(xhr) {
                    if (!usesEzypay() || currentEzypayPlanKey() !== key) return;
                    $('#ezypay-plan-message').attr('class', 'text-danger').text(showMembershipError(xhr, 'Menyiapkan plan EZYPAY', 'Gagal menyiapkan plan EZYPAY. Pilih ulang tipe keanggotaan untuk mencoba kembali.'));
                });
            }
            $('#membership_type, #payment_method').on('change', ensureEzypayPlan);

            var ezypayReadyLead = null;
            var ezypayModalLead = null;
            function usesEzypay() {
                return $.trim($('#payment_method option:selected').text()).toUpperCase() === 'EZYPAY';
            }
            var ezypayCardSaving = false;
            var ezypayCardLead = null;
            function resetEzypayCardForm() {
                $('#ezypay-card-form')[0].reset();
                $('#ezypay-card-form').hide();
                $('#ezypay-card-message').hide().text('');
                ezypayCardLead = null;
            }
            $('#ezypay-card-add').on('click', function() {
                if (ezypayCardSaving) return;
                resetEzypayCardForm();
                ezypayCardLead = $('#lead').val();
                $('#ezypay-card-form').show();
                $('#ezypay-card-holder').focus();
            });
            $('#ezypay-card-cancel').on('click', function() {
                if (!ezypayCardSaving) resetEzypayCardForm();
            });
            $('#ezypay-methods-modal').on('hide.bs.modal', function(event) {
                if (ezypayCardSaving) event.preventDefault();
            }).on('hidden.bs.modal', resetEzypayCardForm);
            $('#ezypay-card-form').on('submit', function(event) {
                event.preventDefault();
                if (ezypayCardSaving) return;
                if (!usesEzypay() || $('#lead').val() !== ezypayCardLead) {
                    resetEzypayCardForm();
                    return;
                }
                ezypayCardSaving = true;
                $('#ezypay-card-save, #ezypay-card-cancel, #ezypay-card-add, #ezypay-methods-retry').prop('disabled', true);
                $('#ezypay-card-message').hide();
                $.ajax({
                    url: "{{ url('cf-lite/cashier/membership/ezypay-payment-methods') }}",
                    type: 'POST', dataType: 'json',
                    data: {lead_id: ezypayCardLead, payment_method_type: 'card',
                        accountHolderName: $('#ezypay-card-holder').val(), accountNumber: $('#ezypay-card-number').val(),
                        expiryMonth: $('#ezypay-card-month').val(), expiryYear: $('#ezypay-card-year').val(),
                        termAndConditionAgreed: $('#ezypay-card-consent').prop('checked')},
                    success: function() {
                        resetEzypayCardForm();
                        showEzypayPaymentMethods();
                    },
                    error: function(xhr) {
                        // Clear sensitive data after every submission, including failures.
                        $('#ezypay-card-number').val('');
                        $('#ezypay-card-consent').prop('checked', false);
                        $('#ezypay-card-message').attr('class', 'alert alert-danger').text(showMembershipError(xhr, 'Menyimpan kartu EZYPAY', 'Gagal menambahkan kartu. Periksa daftar sebelum mencoba kembali.')).show();
                    },
                    complete: function() {
                        ezypayCardSaving = false;
                        $('#ezypay-card-save, #ezypay-card-cancel, #ezypay-card-add, #ezypay-methods-retry').prop('disabled', false);
                    }
                });
            });

            var ezypayMethodsRequest = null;
            var ezypayMethodsVersion = 0;
            function showEzypayPaymentMethods() {
                var leadId = $('#lead').val();
                var version = ++ezypayMethodsVersion;
                if (ezypayMethodsRequest) ezypayMethodsRequest.abort();
                $('#ezypay-methods-name').text($('#lead option:selected').attr('nm') || '');
                $('#ezypay-methods-rows').empty();
                $('#ezypay-methods-table').hide();
                $('#ezypay-methods-message').attr('class', 'alert alert-info').text('Memuat metode pembayaran...').show();
                $('#ezypay-methods-retry').prop('disabled', true);
                $('#ezypay-methods-modal').modal('show');
                ezypayMethodsRequest = $.ajax({
                    url: "{{ url('cf-lite/cashier/membership/ezypay-payment-methods') }}",
                    type: 'GET', dataType: 'json', data: {lead_id: leadId},
                    success: function(result) {
                        if (version !== ezypayMethodsVersion || $('#lead').val() !== leadId || !usesEzypay()) return;
                        if (!result.data || result.data.length === 0) {
                            $('#ezypay-methods-message').attr('class', 'alert alert-info').text('Belum ada metode pembayaran EZYPAY yang terdaftar untuk calon anggota ini.');
                            return;
                        }
                        $('#ezypay-methods-message').hide();
                        result.data.forEach(function(method) {
                            var card = method.card || {};
                            var row = $('<tr>');
                            [method.type === 'CARD' ? (card.type || 'CARD') : (method.type || '-'),
                                card.accountHolderName || '-', card.last4 ? '**** ' + card.last4 : '-',
                                card.expiryMonth && card.expiryYear ? card.expiryMonth + '/' + card.expiryYear : '-',
                                method.valid ? 'Valid' : 'Tidak valid', method.primary ? 'Ya' : 'Tidak'
                            ].forEach(function(value) { $('<td>').text(value).appendTo(row); });
                            $('#ezypay-methods-rows').append(row);
                        });
                        $('#ezypay-methods-table').show();
                    },
                    error: function(xhr, status) {
                        if (status === 'abort' || version !== ezypayMethodsVersion) return;
                        $('#ezypay-methods-message').attr('class', 'alert alert-danger').text(showMembershipError(xhr, 'Memuat metode pembayaran EZYPAY', 'Gagal memuat metode pembayaran EZYPAY. Silakan coba kembali.')).show();
                    },
                    complete: function() {
                        if (version === ezypayMethodsVersion) $('#ezypay-methods-retry').prop('disabled', false);
                    }
                });
            }
            $('#ezypay-methods-retry').on('click', showEzypayPaymentMethods);

            function showEzypayContact(promptForManualLead) {
                ezypayReadyLead = null;
                if (!usesEzypay()) return;
                var selected = $('#lead option:selected');
                var isManual = !$('#lead').val();
                var name = isManual ? $.trim($('#premember_name').val()) : selected.attr('nm');
                var phone = isManual ? $.trim($('#phone').val()) : selected.attr('ph');
                if (isManual && (!name || !phone)) {
                    if (promptForManualLead === true) alert('Isi Nama Calon Anggota dan Nomor Telepon untuk pembayaran EZYPAY.');
                    return;
                }
                if ($.trim(selected.attr('data-ezypay-customer-id') || '') !== '') {
                    ezypayReadyLead = $('#lead').val();
                    $('#ezypay-contact-modal').modal('hide');
                    showEzypayPaymentMethods();
                    return;
                }
                ezypayModalLead = $('#lead').val();
                $('#ezypay-lead-name').text(name);
                $('#ezypay-phone').val(phone || '').prop('readonly', isManual);
                $('#ezypay-email').val(selected.attr('data-email') || '');
                $('#ezypay-wa').val(isManual ? phone : (selected.attr('data-wa') || ''))
                    .prop('required', !isManual).closest('.form-group').toggle(!isManual);
                $('#ezypay-contact-error').hide().text('');
                $('#ezypay-contact-modal').modal('show');
            }
            $('#payment_method, #lead').on('change', showEzypayContact);
            $('#ezypay-contact-form').on('submit', function(event) {
                event.preventDefault();
                var leadId = ezypayModalLead;
                var manualName = $('#ezypay-lead-name').text();
                var phone = $('#ezypay-phone').val();
                var saveButton = $('#ezypay-contact-save');
                if (saveButton.prop('disabled')) return;
                saveButton.prop('disabled', true).text('Menyimpan...');
                $('#ezypay-contact-error').hide();

                function applySavedContact(result) {
                    var savedLeadId = String(result.lead_id);
                    var option = $('#lead option').filter(function() { return this.value === savedLeadId; });
                    if (!option.length) {
                        option = $('<option>').val(savedLeadId).text(result.name).attr('nm', result.name)
                            .attr('so', result.source_id || '').appendTo('#lead');
                    }
                    option.attr('ph', result.phone).attr('data-email', result.email)
                        .attr('data-wa', result.wa_number).attr('data-ezypay-customer-id', result.customer_id_ezypay || '');
                    if ($('#lead').val() !== leadId || !usesEzypay()) return false;

                    if (!leadId) {
                        if ($.trim($('#premember_name').val()) !== manualName || $.trim($('#phone').val()) !== phone) return false;
                        $('#lead').val(savedLeadId).trigger('change.select2');
                        $('#premember_name').val(result.name).prop('readonly', true);
                        $('#source').prop('disabled', true);
                        $('#phone').prop('readonly', true);
                    }
                    $('#phone').val(result.phone);
                    ezypayModalLead = savedLeadId;
                    return true;
                }

                $.ajax({
                    url: "{{ url('cf-lite/cashier/membership/ezypay-customer') }}",
                    type: 'POST', dataType: 'json',
                    data: {lead_id: leadId, club_id: $('input[name="club_id"]').val(),
                        premember_name: leadId ? '' : manualName, fa_coach: leadId ? '' : $('#fa_coach').val(),
                        source: leadId ? '' : $('#source').val(),
                        phone: phone, email: $('#ezypay-email').val(), wa_number: $('#ezypay-wa').val()},
                    success: function(result) {
                        if (applySavedContact(result)) {
                            ezypayReadyLead = ezypayModalLead;
                            $('#ezypay-contact-modal').one('hidden.bs.modal', showEzypayPaymentMethods);
                        }
                        $('#ezypay-contact-modal').modal('hide');
                    },
                    error: function(xhr) {
                        var response = xhr.responseJSON || {};
                        if (response.lead_id && applySavedContact(response) && !leadId) {
                            $('#ezypay-phone').prop('readonly', false);
                            $('#ezypay-wa').prop('required', true).closest('.form-group').show();
                        }
                        var message = showMembershipError(xhr, 'Menyimpan customer EZYPAY', 'Gagal menyimpan customer EZYPAY. Silakan coba kembali.');
                        var validation = response.errors ? Object.keys(response.errors).map(function(key) { return response.errors[key].join(' '); }).join(' ') : '';
                        $('#ezypay-contact-error').text(validation || message).show();
                    },
                    complete: function() { saveButton.prop('disabled', false).text('Simpan dan Lanjutkan'); }
                });
            });

            $('select[name="membership_type"]').on('change', function () {
                var mfdur = parseInt($(this).find(':selected').attr('mf')) * parseInt($(this).find(':selected').attr('dur'));
                var total = parseInt($(this).find(':selected').attr('sf')) + parseInt(mfdur) - parseInt($(this).find(':selected').attr('dc'));
                var rob = parseInt(total) - parseInt(rupiah_format_destroyer($('input[name="pay"]').val()));

                $('input[name="sf_amount"]').val($(this).find(':selected').attr('sf'));
                $('input[name="mf_amount"]').val(mfdur);
                $('input[name="discount"]').val(rupiah_formater($(this).find(':selected').attr('dc')));

                $('#sf-amount-txt').text('Rp. ' + rupiah_formater($(this).find(':selected').attr('sf')) + ',-');
                $('#mf-amount-txt').text('Rp. ' + rupiah_formater(mfdur) + ',-');
                $('input[name="total"]').val(rupiah_formater(total));
                $('#rest-of-bill-txt').text('Rp. ' + rupiah_formater(rob) + ',-');
            });

            $('select[name="lead"]').on('change', function () {
                var name = $(this).find(':selected').attr('nm');
                var phone = $(this).find(':selected').attr('ph');
                var source = $(this).find(':selected').attr('so');
                var id = $(this).val();

                $('input[name="premember_name"]').val(name);
                $('input[name="phone"]').val(phone);
                $('#source').val(source).trigger('change');

                if (id != "") {
                    $('input[name="premember_name"]').prop('readonly', true);
                    $('input[name="phone"]').prop('readonly', true);
                    $('#source').attr('disabled', true);
                } else {
                    $('input[name="premember_name"]').prop('readonly', false);
                    $('input[name="phone"]').prop('readonly', false);
                    $('#source').attr('disabled', false);
                }
            });

            $('select[name="source"]').on('change', function () {
                var selected = $(this).find(':selected').text();
                var referral_field = $('#referral-detail');

                if(selected === 'BR'){
                    referral_field.html(`
                        <td>{!! Form::label('source_detail') !!}</td>
                        <td>:</td>
                        <td>
                            {!! Form::select('source_detail',[],null,['class' => 'form-control select3','required' => 'true']) !!}
                        </td>
                    `);

                    $(".select3").select2({
                        ajax: {
                            delay: 250,
                            method: "POST",
                            url: "{{ url('cf-lite/cashier/membership/get-user/ajax') }}",
                            data: function (params) {
                                var queryParameters = {
                                    q: params.term
                                }

                                return queryParameters;
                            },
                            processResults: function (data, params) {
                                return{
                                    results: $.map(data.users, function(obj){
                                        return {
                                            id: obj.id, text: obj.name
                                        };
                                    })
                                };
                            },
                        }
                    });
                }else if (['Instagram','Facebook','Papan Nama'].includes(selected)){
                    referral_field.html(`
                        <td>{!! Form::label('source_detail') !!}</td>
                        <td>:</td>
                        <td>
                            {!! Form::select('source_detail', [], null, ['class' => 'form-control select3','required' => 'true']) !!}
                        </td>
                    `);

                    $(".select3").select2({
                        ajax: {
                            delay: 250,
                            method: "POST",
                            url: "{{ url('cf-lite/cashier/membership/get-clubs/ajax') }}",
                            data: function (params) {
                                var queryParameters = {
                                    q: params.term
                                }

                                return queryParameters;
                            },
                            processResults: function (data, params) {
                                return{
                                    results: $.map(data.clubs, function(obj){
                                        return {
                                            id: obj.club_name, text: obj.club_name
                                        };
                                    })
                                };
                            },
                        }
                    });
                }else if (['Brosur Langsung','Leadbox','Guest Pass (Eksternal)','Guest Pass (Internal)'].includes(selected)){
                    referral_field.html(`
                        <td>{!! Form::label('source_detail') !!}</td>
                        <td>:</td>
                        <td>{!! Form::text('source_detail',null,['class' => 'form-control','placeholder' => trans('labels.location'),'required' => 'true','onfocus' => 'geolocate()']) !!}</td>
                    `);
                    initAutocomplete();
                }else{
                    referral_field.html(``);
                }
            });

            $('input[name="discount"]').on('keyup', function () {
                if ($(this).val() == "") {
                    var discount = 0;
                } else {
                    var discount = parseInt(rupiah_format_destroyer($(this).val()));
                }

                var total = parseInt($('input[name="sf_amount"]').val()) + parseInt($('input[name="mf_amount"]').val()) - parseInt(discount) - parseInt(rupiah_format_destroyer($('input[name="deposit"]').val()));
                var rob = parseInt(total) - parseInt(rupiah_format_destroyer($('input[name="pay"]').val()));
                $('input[name="discount"]').val(rupiah_formater(discount));
                $('input[name="total"]').val(rupiah_formater(total));
                $('#rest-of-bill-txt').text('Rp. ' + rupiah_formater(rob) + ',-');
            });

            $('input[name="deposit"]').on('keyup', function () {
                if ($(this).val() == "") {
                    var deposit = 0;
                } else {
                    var deposit = parseInt(rupiah_format_destroyer($(this).val()));
                }

                var total = parseInt($('input[name="sf_amount"]').val()) + parseInt($('input[name="mf_amount"]').val()) - parseInt(rupiah_format_destroyer($('input[name="discount"]').val())) - parseInt(deposit);
                var rob = parseInt(total)-parseInt(rupiah_format_destroyer($('input[name="pay"]').val()));
                $('input[name="deposit"]').val(rupiah_formater(deposit));
                $('input[name="total"]').val(rupiah_formater(total));
                $('#rest-of-bill-txt').text('Rp. ' + rupiah_formater(rob) + ',-');
            });

            $('input[name="pay"]').on('keyup', function () {
                if ($(this).val() == "") {
                    var pay = 0;
                } else {
                    var pay = parseInt(rupiah_format_destroyer($(this).val()));
                }

                var rob = parseInt(rupiah_format_destroyer($('input[name="total"]').val())) - parseInt(pay);
                $('input[name="pay"]').val(rupiah_formater(pay));
                $('#rest-of-bill-txt').text('Rp. ' + rupiah_formater(rob) + ',-');
            });

            /* PDF SENDING & VALIDATION */
            $('#cancelSendEmail-btn').on('click', function () {
                $('#SendMailWrapper').css('display', 'none');
                $('#memberEmail').val('');
                $('#cancelSendEmail-btn').text('Cancel');
            });

            $('#sendEmail-btn').on('click', function () {
                if($('#memberEmail').val() != ''){
                    $(this).text('Sending...').attr('disabled', true)
                    $printURL = "{{ url('cf-lite/sendpdf') }}/" + $('#receipt').val() + '/' + $('#memberEmail').val()  + '/' + $('#receiptID').val() + '/' + $('#premember_name').val() + '/membership';

                    $.ajax({
                        url: $printURL,
                        type: 'GET',
                        success: function(res) {
                           console.log('result: ', res)
                           $('#cancelSendEmail-btn').text('Close');
                           $('#sendEmail-btn').text('Send Email').removeAttr('disabled');
                        }
                    });
                }else{
                    alert('No valid email address')
                }
            });

            $('#download-btn').on('click', function () {
                if($('#receipt').val() != ''){
                    $printURL = "{{ url('cf-lite/downloadpdf') }}/" + $('#receipt').val();
                    $('#cancelSendEmail-btn').text('Close');

                    var win =  window.open($printURL, '_blank');
                    if (win) {
                        //Browser has allowed it to be opened
                        win.focus();
                    } else {
                        //Browser has blocked it
                        alert('Please allow popups for this website');
                    }
                }else{
                    console.log('no file to download')
                }
            });
            const validateEmail = (email) => {
                return email.match(
                    /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/
                );
            };

            const validate = () => {
                const $result = $('#result');
                const email = $('#memberEmail').val();
                $result.text('');

                if(validateEmail(email)){
                    //$result.text(email + ' is valid.');
                    //$result.css('color', 'green');
                } else{
                    $result.text(email + ' is invalid.');
                    $result.css('color', 'red');
                }
                return false;
            }

            $('#memberEmail').on('input', validate);
            /* PDF SENDING & VALIDATION */

            function showEzypayPaymentLink(url) {
                if (!url || !/^https:\/\//i.test(url)) return;
                $('#ezypay-payment-link').val(url);
                $('#ezypay-payment-link-open').attr('href', url);
                $('#ezypay-payment-link-modal').modal('show');
            }
            $('#ezypay-payment-link').on('click', function() { this.select(); });
            function updateEzypaySelfPayment() {
                var selfPayment = $('#ezypay-checkout-self-payment').prop('checked');
                $('#ezypay-checkout-method-row').show();
                $('#ezypay-checkout-subscription-row').toggle(!selfPayment);
                if (selfPayment) $('#ezypay-checkout-subscription').prop('checked', false);
                var ready = !!$('#ezypay-checkout-modal').data('plan-id');
                var selected = !!$('#ezypay-checkout-method').val();
                var subscription = $('#ezypay-checkout-subscription').prop('checked');
                var title, description, button;
                if (!ready) {
                    title = 'Menunggu data pembayaran';
                    description = 'Informasi paket dan metode pembayaran perlu dimuat sebelum melanjutkan.';
                    button = 'Lanjutkan';
                } else if (selfPayment || !selected) {
                    title = selfPayment ? 'Pembayaran Mandiri' : 'Pembayaran Mandiri otomatis';
                    description = 'Tautan checkout akan dibuat untuk diberikan kepada customer. Pembuatan tautan belum berarti pembayaran lunas.';
                    button = 'Buat Tautan Pembayaran';
                } else if (subscription) {
                    title = 'Pembayaran berlangganan';
                    description = 'Subscription akan dibuat dengan metode pembayaran yang dipilih. Pastikan customer menyetujui pembayaran berkala.';
                    button = 'Buat Subscription';
                } else {
                    title = 'Pembayaran sekali bayar';
                    description = 'Invoice akan dibuat menggunakan metode pembayaran yang dipilih, tanpa membuat subscription.';
                    button = 'Proses Pembayaran';
                }
                $('#ezypay-checkout-mode-title').text(title);
                $('#ezypay-checkout-mode-description').text(description);
                $('#ezypay-checkout-continue').text(button).prop('disabled', !ready);
            }
            $('#ezypay-checkout-self-payment').on('change', updateEzypaySelfPayment);
            $('#ezypay-checkout-subscription').on('change', updateEzypaySelfPayment);
            var ezypayCheckout = null;
            var ezypayCheckoutKey = null;
            var ezypayCheckoutVersion = 0;
            function ezypayInputAmount(name) {
                return Number(rupiah_format_destroyer($('input[name="' + name + '"]').val() || '0')) || 0;
            }
            function updateEzypayAmounts() {
                $('#ezypay-checkout-discount').text('IDR ' + ezypayInputAmount('discount').toLocaleString('id-ID'));
                $('#ezypay-checkout-pay-amount').text('IDR ' + ezypayInputAmount('pay').toLocaleString('id-ID'));
                var remaining = Math.max(0, Math.round((ezypayInputAmount('total') - ezypayInputAmount('pay')) * 100) / 100);
                $('#ezypay-checkout-remaining').text('IDR ' + remaining.toLocaleString('id-ID'));
                $('#ezypay-checkout-remaining-note').text(remaining > 0
                    ? 'Nominal ini belum diproses dan wajib diatur sebagai cicilan setelah proses pembayaran pertama selesai.'
                    : 'Tidak ada sisa tagihan yang perlu dijadwalkan sebagai cicilan.');
            }
            function checkoutKey() {
                return $('#lead').val() + ':' + currentEzypayPlanKey() + ':' + ezypayInputAmount('pay') + ':' + ezypayInputAmount('discount') + ':' + ezypayInputAmount('total');
            }
            $('input[name="pay"], input[name="discount"]').on('input change', function() {
                ezypayCheckout = null;
                updateEzypayAmounts();
            });
            function loadEzypayCheckout() {
                var version = ++ezypayCheckoutVersion;
                var key = checkoutKey();
                ezypayCheckout = null;
                ezypayCheckoutKey = key;
                $('#ezypay-checkout-plan').empty();
                var leadOption = $('#lead option:selected');
                $('#ezypay-checkout-customer').text(leadOption.attr('nm') || '—');
                $('#ezypay-checkout-email').text(leadOption.attr('data-email') || 'Belum diisi');
                $('#ezypay-checkout-membership').text($('#membership_type option:selected').text().trim() || '—');
                $('#ezypay-checkout-modal').removeData('plan-id');
                $('#ezypay-checkout-self-payment, #ezypay-checkout-subscription').prop('checked', false);
                updateEzypaySelfPayment();
                $('#ezypay-checkout-method').empty().append($('<option>').val('').text('Pilih metode pembayaran')).prop('disabled', true);
                $('#ezypay-checkout-continue, #ezypay-checkout-retry').prop('disabled', true);
                $('#ezypay-checkout-message').attr('class', 'alert alert-info').text('Memuat metode pembayaran dan plan...');
                $('#ezypay-checkout-modal').modal('show');
                $.ajax({
                    url: "{{ url('cf-lite/cashier/membership/ezypay-checkout-options') }}",
                    type: 'GET', dataType: 'json',
                    data: {lead_id: $('#lead').val(), membership_type_id: $('#membership_type').val()},
                    success: function(result) {
                        if (version !== ezypayCheckoutVersion || key !== checkoutKey() || !usesEzypay()) return;
                        var plan = result.plan;
                        if (!plan || !plan.id) {
                            $('#ezypay-checkout-message').attr('class', 'alert alert-danger').text('Informasi plan EZYPAY tidak tersedia.');
                            return;
                        }
                        var details = $('<dl>');
                        $('<dt>').text('Nama paket EZYPAY').appendTo(details);
                        $('<dd>').text(plan.name || '—').appendTo(details);
                        $('<dt>').text('Status paket').appendTo(details);
                        $('<dd>').text(plan.status === 'ACTIVE' ? 'Aktif' : (plan.status || '—')).appendTo(details);
                        if (plan.intervalUnit) {
                            var units = {DAY: 'hari', WEEK: 'minggu', MONTH: 'bulan', YEAR: 'tahun'};
                            $('<dt>').text('Periode pada paket').appendTo(details);
                            $('<dd>').text('Setiap ' + (plan.interval || 1) + ' ' + (units[String(plan.intervalUnit).toUpperCase()] || plan.intervalUnit)).appendTo(details);
                        }
                        $('#ezypay-checkout-plan').append(details);
                        if (plan.amount && plan.amount.value !== null && plan.amount.value !== undefined) {
                            var amount = $('<div>').addClass('ezypay-amount');
                            $('<span>').text('Nominal paket terdaftar').appendTo(amount);
                            $('<strong>').text((plan.amount.currency || 'IDR') + ' ' + Number(plan.amount.value).toLocaleString('id-ID')).appendTo(amount);
                            $('<small>').addClass('text-muted').text('Referensi paket; nominal yang diproses mengikuti input Bayar.').appendTo(amount);
                            $('#ezypay-checkout-plan').append(amount);
                        }
                        var paymentDetails = $('<dl>');
                        $('<dt>').text('Diskon').appendTo(paymentDetails);
                        $('<dd>').attr('id', 'ezypay-checkout-discount').appendTo(paymentDetails);
                        $('#ezypay-checkout-plan').append(paymentDetails);
                        var actualAmount = $('<div>').addClass('ezypay-amount');
                        $('<span>').text('Nominal yang akan diproses (Bayar)').appendTo(actualAmount);
                        $('<strong>').attr('id', 'ezypay-checkout-pay-amount').appendTo(actualAmount);
                        $('<small>').addClass('text-muted').text('Mengikuti input Bayar. Diskon ditampilkan sebagai informasi dan tidak dipotong ulang dari nominal ini.').appendTo(actualAmount);
                        $('#ezypay-checkout-plan').append(actualAmount);
                        var remainingAmount = $('<div>').addClass('ezypay-amount');
                        $('<span>').text('Sisa Tagihan (Nominal yang belum diproses)').appendTo(remainingAmount);
                        $('<strong>').attr('id', 'ezypay-checkout-remaining').appendTo(remainingAmount);
                        $('<small>').attr('id', 'ezypay-checkout-remaining-note').addClass('text-muted').appendTo(remainingAmount);
                        $('#ezypay-checkout-plan').append(remainingAmount);
                        updateEzypayAmounts();
                        $('#ezypay-checkout-modal').data('plan-id', plan.id);
                        if (!result.paymentMethods || !result.paymentMethods.length) {
                            $('#ezypay-checkout-message').attr('class', 'alert alert-info').text('Belum ada metode pembayaran EZYPAY yang valid. Pembayaran Mandiri akan digunakan.');
                            $('#ezypay-checkout-self-payment').prop('checked', true);
                            updateEzypaySelfPayment();
                            return;
                        }
                        result.paymentMethods.forEach(function(method) {
                            $('<option>').val(method.paymentMethodToken).text(method.label + (method.primary ? ' (Utama)' : '')).appendTo('#ezypay-checkout-method');
                        });
                        $('#ezypay-checkout-modal').data('plan-id', plan.id);
                        $('#ezypay-checkout-method').prop('disabled', false);
                        updateEzypaySelfPayment();
                        $('#ezypay-checkout-message').attr('class', 'alert alert-info').text('Data pembayaran berhasil dimuat. Periksa ringkasan dan pilih cara pembayaran.');
                    },
                    error: function(xhr) {
                        if (version !== ezypayCheckoutVersion) return;
                        $('#ezypay-checkout-message').attr('class', 'alert alert-danger').text(showMembershipError(xhr, 'Memuat pilihan checkout EZYPAY', 'Gagal memuat data EZYPAY.'));
                    },
                    complete: function() { if (version === ezypayCheckoutVersion) $('#ezypay-checkout-retry').prop('disabled', false); }
                });
            }
            $('#ezypay-checkout-retry').on('click', loadEzypayCheckout);
            $('#ezypay-checkout-method').on('change', function() {
                updateEzypaySelfPayment();
            });
            $('#lead, #membership_type, #payment_method').on('change', function() {
                ezypayCheckout = null;
                ++ezypayCheckoutVersion;
                $('#ezypay-checkout-modal').modal('hide');
            });
            $('#ezypay-checkout-continue').on('click', function() {
                if (!usesEzypay() || ezypayCheckoutKey !== checkoutKey() || !$('#ezypay-checkout-modal').data('plan-id')) return;
                var selfPayment = $('#ezypay-checkout-self-payment').prop('checked') || !$('#ezypay-checkout-method').val();
                ezypayCheckout = {key: checkoutKey(), paymentMethodToken: $('#ezypay-checkout-method').val(),
                    plan_id_ezypay: $('#ezypay-checkout-modal').data('plan-id'), subscription: !selfPayment && $('#ezypay-checkout-subscription').prop('checked') ? 1 : 0, self_payment: selfPayment ? 1 : 0};
                $('#ezypay-checkout-modal').modal('hide');
                $('#pay-btn').trigger('click');
            });

            $('#pay-btn').on('click', function () {
                if ($(this).prop('disabled')) return;

                if (usesEzypay() && ezypayInputAmount('pay') <= 0) {
                    alert('Isi nominal Bayar lebih dari nol.');
                    return;
                }
                if (usesEzypay() && (!$('#membership_type').val() || ezypayPlanReady !== currentEzypayPlanKey())) {
                    ensureEzypayPlan();
                    alert('Pilih tipe keanggotaan dan tunggu hingga plan EZYPAY siap sebelum melanjutkan pembayaran.');
                    return;
                }
                if (!$('#payment_method').val()) {
                    alert('Pilih metode pembayaran terlebih dahulu.');
                    return;
                }
                if (usesEzypay() && ezypayReadyLead !== $('#lead').val()) {
                    showEzypayContact(true);
                    return;
                }
                if (usesEzypay() && (!ezypayCheckout || ezypayCheckout.key !== checkoutKey())) {
                    loadEzypayCheckout();
                    return;
                }
                var vip = $('select[name="membership_type"] option:selected').val();
                var paymentMethod = Number($('#payment_method').val());
                var usesBank = paymentUsesBank();
                var bank = usesBank ? ($('#bank').val() || '') : '';
                if (usesBank && paymentMethod > 1 && paymentMethod < 6 && !bank) {
                    alert('@lang('messages.please_choose_bank')');
                } else {
                    if ($('#membership_type').val() != "" && $('input[name="fa_date"]').val() != "") {
                        if ($('#premember_name').val() != "" && $('#phone').val() != "") {
                            $('input[name="discount"]').val(rupiah_format_destroyer($('input[name="discount"]').val()));
                            $('input[name="deposit"]').val(rupiah_format_destroyer($('input[name="deposit"]').val()));
                            $('input[name="total"]').val(rupiah_format_destroyer($('input[name="total"]').val()));
                            $('input[name="pay"]').val(rupiah_format_destroyer($('input[name="pay"]').val()));
                            $('#pay').val(rupiah_format_destroyer($('#pay').val()));
                            $('#deposit').val(rupiah_format_destroyer($('#deposit').val()));

                            if ($('#pay').val() > 0 || vip == 10 || (usesEzypay() && ezypayCheckout && ezypayCheckout.self_payment)) {
                                var $paymentLoading = $('#membership-payment-loading');
                                var $paymentError = $('#membership-payment-error');
                                var $payButton = $(this);
                                var payButtonLabel = $payButton.text();
                                var $editablePaymentFields = $('input[name="premember_name"], input[name="phone"], input[name="discount"], input[name="deposit"], #pay, input[name="bonus_duration"]')
                                    .filter(':not([readonly])');
                                $paymentError.prop('hidden', true).text('');
                                $payButton.text('Memproses...').prop('disabled', true);
                                $paymentLoading.prop('hidden', false);
                                $editablePaymentFields.prop('readOnly', true);

                                var isEzypay = $.trim($('#payment_method option:selected').text()).toUpperCase() === 'EZYPAY';
                                var paymentUrl = isEzypay
                                    ? "{{ url('PrintESCPOSMembershipCashierWithEzypay?clientPrint') }}"
                                    : "{{ url('PrintESCPOSMembershipCashier?clientPrint') }}";
                                $printURL = paymentUrl + '&club_id=' + $('input[name="club_id"]').val() +
                                    '&login_user=' + $('input[name="login_user"]').val() +
                                    '&digital_product_check=' + `{{$user->user_club->digital_product? 'true':'false'}}` +
                                    '&lead=' + $('#lead').val() +
                                    '&fa_coach=' + $('#fa_coach').val() +
                                    '&fa_date=' + $('input[name="fa_date"]').val() +
                                    '&premember_name=' + encodeURIComponent($('input[name="premember_name"]').val()) +
                                    '&phone=' + encodeURIComponent($('input[name="phone"]').val()) +
                                    '&source=' + $('#source').val() +
                                    '&source_detail=' + $('#source_detail').val() +
                                    '&membership_type=' + $('#membership_type').val() +
                                    '&payment_method=' + $('#payment_method').val() +
                                    '&bank=' + bank +
                                    '&sf_amount=' + $('input[name="sf_amount"]').val() +
                                    '&mf_amount=' + $('input[name="mf_amount"]').val() +
                                    '&discount=' + $('input[name="discount"]').val() +
                                    '&deposit=' + $('input[name="deposit"]').val() +
                                    '&total=' + $('input[name="total"]').val() +
                                    '&pay=' + $('input[name="pay"]').val() +
                                    '&bonus_duration=' + $('input[name="bonus_duration"]').val() +
                                    '&bonus_type=' + $('select[name="bonus_type"]').val() +
                                    '&for=club' +
                                    '&useDefaultPrinter=undefined&printerName=XP-80' +
                                    '&id=1';
                                //console.log($printURL)
                                $.ajax({
                                    url: $printURL,
                                    type: isEzypay ? 'POST' : 'GET',
                                    data: isEzypay ? {paymentMethodToken: ezypayCheckout.paymentMethodToken, plan_id_ezypay: ezypayCheckout.plan_id_ezypay, subscription: ezypayCheckout.subscription, self_payment: ezypayCheckout.self_payment} : {},
                                    dataType: 'json',
                                    success: function(res) {
                                        var result = res;
                                        if (result.installment_url) { window.location.assign(result.installment_url); return; }
                                        $paymentLoading.prop('hidden', true);
                                        if (result.checkoutUrl) showEzypayPaymentLink(result.checkoutUrl);
                                        $('#receipt').val(result.pdf);
                                        $('#receiptID').val(result.receiptID);

                                        @if($user->user_club->digital_product)
                                            console.log('D1');
                                            // Remove last element
                                            var get_last_element = $('#first_click').children()[2];
                                            $( get_last_element ).remove();
                                            $('#first_click').append(`<td>{!! Form::button('Send Receipt', ['class' => 'btn btn-success btn-md', 'id' => 'print-btn']) !!} {!! Form::button(trans('labels.reprint_customer'), ['class' => 'btn btn-warning btn-md reprint-customer', 'id' => 'reprint-customer']) !!}</td>`);
                                            $('#after_click').append(`<td>{!! Form::button(trans('labels.reprint_club'), ['class' => 'btn btn-success btn-md reprint-club', 'id' => 'reprint-club', 'style' => 'background-color: rebeccapurple']) !!}</td>`);

                                            //$('#reprint-club').show();
                                            //$('#reprint-customer').show();
                                            //$('#print-btn').show();
                                        @else
                                            console.log('D2');
                                            //$('#print-btn').show();
                                            var get_last_element = $('#first_click').children()[2];
                                            $( get_last_element ).remove();
                                            $('#first_click').append(`<td>{!! Form::button('Send Receipt', ['class' => 'btn btn-success btn-md', 'id' => 'print-btn']) !!} {!! Form::button(trans('labels.reprint_customer'), ['class' => 'btn btn-warning btn-md reprint-customer', 'id' => 'reprint-customer']) !!}</td>`);

                                            $('#after_click').append(`<td>{!! Form::button(trans('labels.reprint_club'), ['class' => 'btn btn-success btn-md reprint-club', 'id' => 'reprint-club', 'style' => 'background-color: rebeccapurple']) !!}</td>`);

                                            //$('#reprint-club').show();
                                            //$('#reprint-customer').show();
                                            //$('#print-btn').show();

                                        @endif

                                        $('#reprint-club').show();
                                        $('#reprint-customer').show();
                                        $('#print-btn').show();
                                        $('#pay-btn').text('Pay').removeAttr('disabled')
                                    },
                                    error: function(xhr) {
                                        var result = xhr.responseJSON || {};
                                        $paymentLoading.prop('hidden', true);
                                        if (!result.installment_url && result.checkoutUrl) showEzypayPaymentLink(result.checkoutUrl);
                                        if (result.retry_allowed === true) {
                                            ezypayCheckout = null;
                                            $editablePaymentFields.prop('readOnly', false);
                                            $payButton.text(payButtonLabel).prop('disabled', false);
                                        } else {
                                            $payButton.text('Periksa status pembayaran').prop('disabled', true);
                                        }
                                        var message = showMembershipError(xhr, 'Mengirim pembayaran membership',
                                            'Respons pembayaran gagal diterima. Pembayaran mungkin sudah tersimpan. Periksa riwayat pembayaran sebelum mencoba kembali untuk menghindari transaksi ganda.',
                                            function() {
                                                if (result.installment_url) window.location.assign(result.installment_url);
                                            });
                                        $paymentError.text(message).prop('hidden', false);
                                    }
                                });
                                /* javascript:jsWebClientPrint.print(
                                    'club_id=' + $('input[name="club_id"]').val() +
                                    '&login_user=' + $('input[name="login_user"]').val() +
                                    '&digital_product_check=' + `{{$user->user_club->digital_product? 'true':'false'}}` +
                                    '&lead=' + $('#lead').val() +
                                    '&fa_coach=' + $('#fa_coach').val() +
                                    '&fa_date=' + $('input[name="fa_date"]').val() +
                                    '&premember_name=' + $('input[name="premember_name"]').val() +
                                    '&phone=' + $('input[name="phone"]').val() +
                                    '&source=' + $('#source').val() +
                                    '&source_detail=' + $('#source_detail').val() +
                                    '&membership_type=' + $('#membership_type').val() +
                                    '&payment_method=' + $('#payment_method').val() +
                                    '&bank=' + $('#bank').val() +
                                    '&sf_amount=' + $('input[name="sf_amount"]').val() +
                                    '&mf_amount=' + $('input[name="mf_amount"]').val() +
                                    '&discount=' + $('input[name="discount"]').val() +
                                    '&deposit=' + $('input[name="deposit"]').val() +
                                    '&total=' + $('input[name="total"]').val() +
                                    '&pay=' + $('input[name="pay"]').val() +
                                    '&bonus_duration=' + $('input[name="bonus_duration"]').val() +
                                    '&bonus_type=' + $('select[name="bonus_type"]').val() +
                                    '&for=club' +
                                    '&useDefaultPrinter=undefined&printerName=XP-80' +
                                    '&id=1'
                                ); */
                            } else {
                                alert('@lang('messages.please_check_amount_pay')');
                            }
                        } else {
                            alert('@lang("messages.please_check_name_and_phone")');
                        }
                    } else {
                        alert('@lang("messages.please_choose_date_and_membership_type")');
                    }
                }
                // $('#form-payment').submit();
            });


            /* $('#print-btn').on('click', function () {
                @if($user->user_club->digital_product)
                // Remove last element
                var get_last_element = $('#first_click').children()[2];
                $( get_last_element ).remove();
                $('#first_click').append(
                    `<td>
                        {!! Form::button(trans('labels.reprint_customer'), ['class' => 'btn btn-success btn-md', 'id' => 'reprint-customer']) !!}
                    </td>`
                );
                $('#after_click').append(
                    `<td>
                        {!! Form::button(trans('labels.reprint_club'), ['class' => 'btn btn-success btn-md', 'id' => 'reprint-club']) !!}
                    </td>`
                );

                $('#reprint-club').show();
                $('#reprint-customer').show();
                @endif

                //SEND PDF TO CUSTOMER
                console.log('pdf: ',$('#receipt').val());
                $('#SendMailWrapper').css('display', 'block')


            }); */

            /*
            //FOR buttons action testing
            var get_last_element = $('#first_click').children()[2];
            $( get_last_element ).remove();
            $('#first_click').append(`<td>{!! Form::button('Send Receipt', ['class' => 'btn btn-success btn-md', 'id' => 'print-btn']) !!} {!! Form::button(trans('labels.reprint_customer'), ['class' => 'btn btn-warning btn-md reprint-customer', 'id' => 'reprint-customer']) !!}</td>`);

            $('#after_click').append(`<td>{!! Form::button(trans('labels.reprint_club'), ['class' => 'btn btn-success btn-md reprint-club', 'id' => 'reprint-club', 'style' => 'background-color: rebeccapurple']) !!}</td>`);

            $('#reprint-club').show();
            $('#reprint-customer').show();
            $('#print-btn').show(); */


            //@if($user->user_club->digital_product)

            //@endif

            $('#first_click').on('click', '#print-btn', function () {
                $('#SendMailWrapper').css('display', 'block')
            });

            $('#first_click').on('click', '#reprint-customer', function () {
                javascript:jsWebClientPrint.print(
                        'club_id=' + $('input[name="club_id"]').val() +
                        '&login_user=' + $('input[name="login_user"]').val() +
                        '&digital_product_check=' + `{{$user->user_club->digital_product? 'true':'false'}}` +
                        '&lead=' + $('#lead').val() +
                        '&fa_coach=' + $('#fa_coach').val() +
                        '&fa_date=' + $('input[name="fa_date"]').val() +
                        '&premember_name=' + $('input[name="premember_name"]').val() +
                        '&phone=' + $('input[name="phone"]').val() +
                        '&source=' + $('#source').val() +
                        '&source_detail=' + $('#source_detail').val() +
                        '&membership_type=' + $('#membership_type').val() +
                        '&payment_method=' + $('#payment_method').val() +
                        '&bank=' + $('#bank').val() +
                        '&sf_amount=' + $('input[name="sf_amount"]').val() +
                        '&mf_amount=' + $('input[name="mf_amount"]').val() +
                        '&discount=' + $('input[name="discount"]').val() +
                        '&deposit=' + $('input[name="deposit"]').val() +
                        '&total=' + $('input[name="total"]').val() +
                        '&pay=' + $('input[name="pay"]').val() +
                        '&bonus_duration=' + $('input[name="bonus_duration"]').val() +
                        '&bonus_type=' + $('select[name="bonus_type"]').val() +
                        '&for=customer' +
                        '&useDefaultPrinter=undefined&printerName=XP-80' +
                        '&id=0' +
                        '&axn=print'
                    );
            });

            $('#after_click').on('click', '#reprint-club', function () {
                javascript:jsWebClientPrint.print(
                        'club_id=' + $('input[name="club_id"]').val() +
                        '&login_user=' + $('input[name="login_user"]').val() +
                        '&digital_product_check=' + `{{$user->user_club->digital_product? 'true':'false'}}` +
                        '&lead=' + $('#lead').val() +
                        '&fa_coach=' + $('#fa_coach').val() +
                        '&fa_date=' + $('input[name="fa_date"]').val() +
                        '&premember_name=' + $('input[name="premember_name"]').val() +
                        '&phone=' + $('input[name="phone"]').val() +
                        '&source=' + $('#source').val() +
                        '&source_detail=' + $('#source_detail').val() +
                        '&membership_type=' + $('#membership_type').val() +
                        '&payment_method=' + $('#payment_method').val() +
                        '&bank=' + $('#bank').val() +
                        '&sf_amount=' + $('input[name="sf_amount"]').val() +
                        '&mf_amount=' + $('input[name="mf_amount"]').val() +
                        '&discount=' + $('input[name="discount"]').val() +
                        '&deposit=' + $('input[name="deposit"]').val() +
                        '&total=' + $('input[name="total"]').val() +
                        '&pay=' + $('input[name="pay"]').val() +
                        '&bonus_duration=' + $('input[name="bonus_duration"]').val() +
                        '&bonus_type=' + $('select[name="bonus_type"]').val() +
                        '&for=club' +
                        '&useDefaultPrinter=undefined&printerName=XP-80' +
                        '&id=0' +
                        '&axn=print'
                    );
            });

            init_format();
        });

        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp.' + (x) + ',-');
            });
            $('.rupiah').each(function () {
                var x = rupiah_formater($(this).val().replace(/\s/g, ''));
                $(this).val((x));
            });
        }
</script>
<script>
    var placeSearch, autocomplete;

        var componentForm = {
        street_number: 'short_name',
        route: 'long_name',
        locality: 'long_name',
        administrative_area_level_1: 'short_name',
        country: 'long_name',
        postal_code: 'short_name'
        };

        function initAutocomplete() {
            // Create the autocomplete object, restricting the search to geographical
            // location types.
            autocomplete = new google.maps.places.Autocomplete(
            /** @type {!HTMLInputElement} */(document.getElementById('source_detail')),
            {types: ['geocode']});

            // When the user selects an address from the dropdown, populate the address
            // fields in the form.
            autocomplete.addListener('place_changed', fillInAddress);
        }

        function fillInAddress() {
            // Get the place details from the autocomplete object.
            var place = autocomplete.getPlace();

            for (var component in componentForm) {
                document.getElementById(component).value = '';
                document.getElementById(component).disabled = false;
            }

            // Get each component of the address from the place details
            // and fill the corresponding field on the form.
            for (var i = 0; i < place.address_components.length; i++) {
                var addressType=place.address_components[i].types[0];
                if (componentForm[addressType]) {
                    var val=place.address_components[i][componentForm[addressType]];
                    document.getElementById(addressType).value=val;
                }
            }
        }

        function geolocate() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(function (position) {
                    var geolocation={
                        lat: position.coords.
                        latitude, lng: position.coords.longitude
                    };
                    var circle=new google.maps.Circle({
                        center: geolocation,
                        radius: position.coords.accuracy
                    });
                    autocomplete.setBounds(circle.getBounds());
                });
            }
        }
</script>
<script
    src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAx42dzcUZAXbWOYbCUV8-sEyN9_ljWzcs&libraries=places&callback=initAutocomplete"
    async defer></script>

<script type="text/javascript">
    var wcppGetPrintersTimeout_ms = 10000; //10 sec
        var wcppGetPrintersTimeoutStep_ms = 500; //0.5 sec

        function wcpGetPrintersOnSuccess() {
            // Display client installed printers
            if (arguments[0].length > 0) {
                var p = arguments[0].split("|");
                var options = '';
                for (var i = 0; i < p.length; i++) {
                    options += '<option>' + p[i] + '</option>';
                }
                $('#installedPrinters').css('visibility', 'visible');
                $('#installedPrinterName').html(options);
                $('#installedPrinterName').focus();
                $('#loadPrinters').hide();
            } else {
                alert("No printers are installed in your system.");
            }
        }

        function wcpGetPrintersOnFailure() {
            // Do something if printers cannot be got from the client
            alert("No printers are installed in your system.");
        }
</script>
{!!

// Register the WebClientPrint script code
// The $wcpScript was generated by PrintESCPOSController@index

$wcpScript;

!!}

<script src="{{asset('js/my_js.js')}}"></script>
@endsection
