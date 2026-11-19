@extends('cf_acc.layouts.acc-dashboard-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">

    <style>
        .select2 {
            width: 100% !important;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-md-12">
            <a href="{{url('cf-acc/dashboard')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.purchasing_book')</h2>
            </center>
        </div>
        <div class="col-md-12">
            <div class="row">
                {!! Form::open(['method' => 'GET']) !!}
                <div class="col-xs-12">
                    <div class="col-xs-2">
                        {!! Form::label('filter', trans('labels.filter'), ['class' => 'control-label']) !!} :
                    </div>
                    <div class="col-xs-3">
                        {!! Form::date('from_date', $fromdate , ['class' => 'form-control filter-date','placeholder'=>@trans('labels.from_date'), 'id'=>'filt-dari']) !!} <br/>
                    </div>
                    <div class="col-xs-3">
                        {!! Form::date('thru_date', $thrudate , ['class' => 'form-control filter-date','placeholder'=>@trans('labels.from_date'), 'id'=>'filt-sampai']) !!} <br/>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="col-xs-2">
                        {!! Form::label('jubilant', trans('labels.jubilant').': ', ['class' => 'control-label']) !!}
                    </div>
                    <div class="col-xs-6">
                        {!! Form::select('jubilant', $jubilants, null, ['class' => 'form-control select2', 'id' => 'jubilant', 'placeholder'=> trans('labels.choose_jubilant')]) !!}
                    </div>
                </div>
                <div class="col-xs-12" style="margin-top: 10px; margin-bottom: 10px;">
                    <div class="col-xs-2">
                        {!! Form::label('clubs', trans('labels.choose_club'), ['class' => 'control-label']) !!}
                    </div>
                    <div class="col-xs-6">
                        <select name="clubs[]" class="form-control select2" id="clubs" multiple>
                            @if(!empty($_GET['jubilant']))
                                @foreach($clubs as $club)
                                    @if($club->jubilant_id == $_GET['jubilant'])
                                        @php($club_gate = false)
                                        @if(!empty($_GET['clubs']))
                                            @foreach($_GET['clubs'] as $chosen_club)
                                                @if($chosen_club == $club->id)
                                                    @php($club_gate = true)
                                                @endif
                                            @endforeach
                                        @endif

                                        @if($club_gate)
                                            <option value="{{ $club->id }}" selected>{{ $club->club_name }}</option>
                                        @else
                                            <option value="{{ $club->id }}">{{ $club->club_name }}</option>
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                </div>
                <div class="col-xs-12">
                    <div class="col-xs-2">
                        {!! Form::label('coa', trans('labels.choose_coa'), ['class' => 'control-label']) !!}
                    </div>
                    <div class="col-xs-6">
                        <select name="coas[]" class="form-control select2" id="coas" multiple>
                            @if(!empty($_GET['jubilant']))
                                @foreach($coapb as $coa)
                                    @if($coa->jubilant_id == $_GET['jubilant'])
                                        @php($coa_gate = false)
                                        @if(!empty($_GET['coas']))
                                            @foreach($_GET['coas'] as $chosen_coa)
                                                @if($chosen_coa == $coa->id)
                                                    @php($coa_gate = true)
                                                @endif
                                            @endforeach
                                        @endif

                                        @if($coa_gate)
                                            <option value="{{$coa->id}}" selected>{{$coa->coa_number}}
                                                : {{$coa->coa_name}}</option>
                                        @else
                                            <option value="{{$coa->id}}">{{$coa->coa_number}}: {{$coa->coa_name}}</option>
                                        @endif
                                    @endif
                                @endforeach
                            @endif
                        </select>
                    </div>
                    <div class="col-xs-2">
                        <button type="submit" class="btn btn-primary" id="save-btn"><i class="fa fa-search"></i> @lang('labels.search')</button>
                        </a>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
            <div class="row">
                <div class="btn btn-default pull-right" data-toggle="modal" data-target="#addpurchasingbook" role="button" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink; margin-right: 10px;" title="@lang('labels.new_purchasing_book')">
                    <span class="fa fa-plus-circle" style="margin-top: 10px; color: mediumvioletred;"></span>
                </div>
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
                                <td>@lang('labels.indexing')</td>
                                <td>@lang('labels.purchase_date')</td>
                                <td>@lang('labels.club')</td>
                                <td>@lang('labels.chart_of_account')</td>
                                <td>@lang('labels.purchasing_info')</td>
                                <td>@lang('labels.amount_due')</td>
                                <td>@lang('labels.amount_paid')</td>
                                <td>@lang('labels.payment_status')</td>
                                <td></td>
                            </tr>
                            </thead>
                            <tbody>
                            @php($indexing = 1)
                            @foreach($purchasingbooks as $pb)
                                <tr>
                                    <td>{{ $indexing }}</td>
                                    <td><a href="{{ url('cf-acc/purchasing-book/edit/'.$pb->id) }}" style="text-decoration: none;">{{Carbon\Carbon::parse($pb->purchasing_date)->format('d-m-Y')}}</a></td>
                                    <td>{{ $pb->purchasingbook_club->club_name }}</td>
                                    <td>{{ $pb->purchasingbook_chartofaccount->coa_number }}</td>
                                    <td>{{ $pb->purchasing_info }}</td>
                                    <td class="my_rupiah">{{ $pb->amount_due }}</td>
                                    <td class="my_rupiah">{{ $pb->amount_paid }}</td>
                                    <td>{{ $pb->payment_status }}</td>
                                    <td>
                                        @if($pb->payment_status != 'Paid')
                                            <a class="btn btn-success btn-pay-modal" data-toggle="modal" data-target="#paypurchasingbook" style="text-align: center;" jubid="{{ $pb->jubilant_id }}" id="btnpayid-{{$pb->id}}" maxpaid="{{ $pb->rest_of_bill }}">
                                                <span class="btn-text fa fa-money"></span>
                                            </a>
                                        @endif
                                    </td>
                                </tr>
                                @php($indexing++)
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--Modal Add--}}
    <div class="modal fade" id="addpurchasingbook" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'POST', 'action' => 'PurchasingBookController@store_purchasing_bookV2', 'id' => 'form-add-purchasing-book']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.add_purchasing_book')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('purchase_date', trans('labels.purchase_date').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-6">
                                {!! Form::date('purchase_date', Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('jubilant', trans('labels.jubilant').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-6">
                                {!! Form::select('jubilant', $jubilants, null, ['class' => 'form-control select2 select-width-modal', 'id' => 'add-modal-jubilant', 'placeholder'=> trans('labels.choose_jubilant')]) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('club', trans('labels.club').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-6">
                                <select name="selectedclub" class="form-control select2" id="add-modal-club">
                                    <option value="">@lang('labels.choose_club')</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('chart_of_account', trans('labels.chart_of_account').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-6">
                                <select name="coapb" class="form-control select2" id="add-modal-coapb">
                                    <option value="">@lang('labels.choose') @lang('labels.chart_of_account')</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('purchasing_info', trans('labels.purchasing_info').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-6">
                                {!! Form::textarea('purchasing_info', null, ['class' => 'form-control', 'id' => 'transaction-info', 'style' => 'height: 130px; resize:none;']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('amount_due', trans('labels.amount_due').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-6">
                                {!! Form::text('amount_due', 0, ['class' => 'form-control rupiah', 'onkeypress' => 'return onlyNumber(event)','id'=>'amount-due-input']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('amount_paid', trans('labels.amount_paid').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-6">
                                {!! Form::text('amount_paid', 0, ['class' => 'form-control rupiah', 'onkeypress' => 'return onlyNumber(event)','id'=>'amount-paid-input']) !!}
                            </div>
                            <div class="col-md-3">
                                <select name="coapy" class="form-control select2" id="add-modal-coapy">
                                    <option value="">@lang('labels.choose') @lang('labels.payment')</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="button" name="submit_add_btn" id="add-account-info" class="btn btn-success">@lang('labels.add')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="modal fade" id="paypurchasingbook" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'POST', 'action' => 'PurchasingBookController@pay_purchasing_bookV2', 'id' => 'form-pay-purchasing-book']) !!}
                {!! Form::hidden('pb_id','',['id'=>'purbookid']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.pay_purchasing_book')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('payment_date_pay', trans('labels.payment_date').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                {!! Form::date('payment_date_pay', Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('maxpaidamount', trans('labels.rest_of_bill').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                {!! Form::text('maxpaidamount', 0, ['class' => 'form-control rupiah', 'onkeypress' => 'return onlyNumber(event)', 'disabled' => 'disabled', 'id'=>'maxpaidamount']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('amount_paid_pay', trans('labels.amount_paid').': ', ['class' => 'control-label col-md-3']) !!}
                            <div class="col-md-3">
                                {!! Form::text('amount_paid_pay', 0, ['class' => 'form-control rupiah', 'onkeypress' => 'return onlyNumber(event)','id'=>'amount-paid-pay']) !!}
                            </div>
                            <div class="col-md-3">
                                <select name="coapypay" class="form-control select2" id="coapypay">
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="button" name="submit_pay_button" id="pay-account-info" class="btn btn-success">@lang('labels.pay')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script src="{{asset('js/my_js.js')}}"></script>
    <script>
        $(document).ready(function () {
            $.fn.select2.amd.define('select2/selectAllAdapter', [
                'select2/utils',
                'select2/dropdown',
                'select2/dropdown/attachBody'
            ], function (Utils, Dropdown, AttachBody) {

                function SelectAll() { }
                SelectAll.prototype.render = function (decorated) {
                    var self = this,
                        $rendered = decorated.call(this),
                        $selectAll = $(
                            '<button class="btn btn-xs btn-success" type="button" style="margin-left:6px;"><i class="fa fa-check-square-o"></i> @lang('labels.select_all')</button>'
                        ),
                        $unselectAll = $(
                            '<button class="btn btn-xs btn-danger" type="button" style="margin-left:6px;"><i class="fa fa-square-o"></i> @lang('labels.unselect_all')</button>'
                        ),
                        $btnContainer = $('<div style="margin-top:3px;">').append($selectAll).append($unselectAll);
                    if (!this.$element.prop("multiple")) {
                        // this isn't a multi-select -> don't add the buttons!
                        return $rendered;
                    }
                    $rendered.find('.select2-dropdown').prepend($btnContainer);
                    $selectAll.on('click', function (e) {
                        var $results = $rendered.find('.select2-results__option[aria-selected=false]');
                        $results.each(function () {
                            self.trigger('select', {
                                data: $(this).data('data')
                            });
                        });
                        self.trigger('close');
                    });
                    $unselectAll.on('click', function (e) {
                        var $results = $rendered.find('.select2-results__option[aria-selected=true]');
                        $results.each(function () {
                            self.trigger('unselect', {
                                data: $(this).data('data')
                            });
                        });
                        self.trigger('close');
                    });
                    return $rendered;
                };

                return Utils.Decorate(
                    Utils.Decorate(
                        Dropdown,
                        AttachBody
                    ),
                    SelectAll
                );

            });

            init_format();

            $('.select2').select2();
            $('#data-table').dataTable();

            $('#add-modal-jubilant').on('change', function () {
                var club = '<option value="">@lang('labels.choose_club')</option>';
                var coapb = '<option value="">@lang('labels.choose') @lang('labels.chart_of_account')</option>';
                var coapy = '<option value="">@lang('labels.choose') @lang('labels.payment')</option>';
                var choose_jubilant = $(this).val();

                @foreach($clubs as $club)
                    var jubilant = '{{ $club->jubilant_id }}';
                    if(jubilant == choose_jubilant) {
                        club += '<option value="{{ $club->id }}">{{ $club->club_name }}</option>';
                    }
                @endforeach

                @foreach($coapb as $coa)
                    var jubilant_coa = '{{ $coa->jubilant_id }}';
                    if (jubilant_coa == choose_jubilant) {
                        coapb += '<option value="{{ $coa->id }}">{{ $coa->coa_abv_name }}</option>';
                    }
                @endforeach

                @foreach($coapy as $coa)
                    var jubilant_coa = '{{ $coa->jubilant_id }}';
                    if (jubilant_coa == choose_jubilant) {
                        coapy += '<option value="{{ $coa->id }}">{{ $coa->coa_abv_name }}</option>';
                    }
                @endforeach

                $('#add-modal-club').html(club);
                $('#add-modal-coapb').html(coapb);
                $('#add-modal-coapy').html(coapy);
            });

            $('#jubilant').on('change', function () {
                var club = '<option value="">@lang('labels.choose_club')</option>';
                var choose_jubilant = $(this).val();
                $('#clubs').val("");
                @foreach($clubs as $club)
                    var jubilant = '{{ $club->jubilant_id }}';
                    if (jubilant == choose_jubilant) {
                        club += '<option value="{{ $club->id }}">{{ $club->club_name }}</option>';
                    }
                @endforeach

                var coa = '';
                $('#coas').val("");
                @foreach($coapb as $coa)
                    var jubilant_coa = '{{ $coa->jubilant_id }}';
                    if (jubilant_coa == choose_jubilant) {
                        coa += '<option value="{{ $coa->id }}">{{$coa->coa_number}}: {{$coa->coa_name}}</option>';
                    }
                @endforeach

                $('#clubs').html(club);
                $('#coas').html(coa);
            });

            $('#clubs').select2({
                placeholder: '@lang('labels.choose_club')',
                dropdownAdapter: $.fn.select2.amd.require('select2/selectAllAdapter')
            });

            $('#coas').select2({
                placeholder: '@lang('labels.choose_coa')',
                dropdownAdapter: $.fn.select2.amd.require('select2/selectAllAdapter')
            });

            $('#coapb').select2();
            $('#coapy').select2();

            $('#coapypay').select2();

            $('.rupiah').on('keyup',function(){
                if($(this).val() == ''){
                    $(this).val(0);
                }
                var newVal = parseInt(rupiah_format_destroyer($(this).val()));
                $(this).val(rupiah_formater(newVal.toString().replace(/\s/g, '')));
            });

            $('#add-account-info').on('click',function($q){
                $('#amount-due-input').val(rupiah_format_destroyer($('#amount-due-input').val()));
                $('#amount-paid-input').val(rupiah_format_destroyer($('#amount-paid-input').val()));
                if(parseInt($('#amount-due-input').val()) < parseInt($('#amount-paid-input').val())){
                    alert('@lang("messages.amount_cant_be_larger_than_rest_of_bill")');
                }else{
                    $('#form-add-purchasing-book').submit();
                }
            });

            $('#pay-account-info').on('click',function($q){
                $('#amount-paid-pay').val(rupiah_format_destroyer($('#amount-paid-pay').val()));
                if(parseInt(rupiah_format_destroyer($('#maxpaidamount').val())) < parseInt($('#amount-paid-pay').val())){
                    alert('@lang("messages.amount_cant_be_larger_than_rest_of_bill")');
                }else{
                    $('#form-pay-purchasing-book').submit();
                }
            });

            $('.btn-pay-modal').on('click',function(){
                $('#purbookid').val($(this).attr('id').split('-').pop());
                $('#maxpaidamount').val(rupiah_formater($(this).attr('maxpaid').replace(/\s/g, '')));

                var coapy = '<option value="">@lang('labels.choose') @lang('labels.payment')</option>';
                var choose_jubilant = $(this).attr('jubid');
                @foreach($coapy as $coa)
                    var jubilant_coa = '{{ $coa->jubilant_id }}';
                    if (jubilant_coa == choose_jubilant) {
                        coapy += '<option value="{{ $coa->id }}">{{ $coa->coa_abv_name }}</option>';
                    }
                @endforeach

                $('#coapypay').html(coapy);
            });

        });

        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp. '+ x);
            });
            $('.rupiah').each(function () {
                var x = rupiah_formater($(this).val().replace(/\s/g, ''));
                $(this).val((x));
            });
        }

        function onlyNumber(evt){
            var charCode = (evt.which) ? evt.which : event.keyCode;
            if(charCode == 8 || //space
                charCode == 9 || //tab
                charCode == 46 || //point
                (charCode > 47 && charCode < 58) // number
            ){
                return true;
            }

            return false;
        }
    </script>
    <script src={{asset("bootbox/bootbox.min.js")}}></script>
@endsection
