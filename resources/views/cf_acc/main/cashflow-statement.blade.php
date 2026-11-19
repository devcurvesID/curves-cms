@extends('cf_acc.layouts.acc-dashboard-template')

@section('other-style')
    {{--DATA TABLE--}}
    {{--<link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">--}}
    {{--<link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">--}}
    {{--<link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">--}}
    {{--<link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">--}}
    {{--<link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">--}}

    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">

    <style>
        .select2 {
            width: 100% !important;
        }

        .my_rupiah {
            text-align: right;
        }

        .td-border-bottom {
            border-bottom: 2px solid black;
        }
    </style>
@endsection

@section('content')
    <div class="col-xs-12">
        <center>
            <h2 class="page-title">
                @lang('labels.cashflow_statement')
                <br><small>({{Carbon\Carbon::parse($fromdate)->format('d-m-Y')}} @lang('labels.until') {{Carbon\Carbon::parse($thrudate)->format('d-m-Y')}})</small>
            </h2>
        </center>
    </div>
    <div class="col-xs-12">
        <div class="row">
            {!! Form::open(['method' => 'GET']) !!}
            <div class="col-xs-12">
                <div class="col-xs-2">
                    {!! Form::label('filter', trans('labels.filter'), ['class' => 'col-md-4 control-label']) !!} :
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
                    {!! Form::label('clubs', trans('labels.choose_club'), ['class' => 'col-md-4 control-label']) !!}
                </div>
                <div class="col-xs-6">
                    {!! Form::select('clubs[]', $clubs, null, ['class' => 'form-control select2', 'multiple', 'id' => 'clubs']) !!}
                </div>
                <div class="col-xs-2">
                    <button type="submit" class="btn btn-primary" id="save-btn"><i class="fa fa-search"></i> @lang('labels.search')</button>
                    </a>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        <hr/>

        @if(!empty($accjournals))
            @php($total_receipt = 0)
            @php($total_expense = 0)
            @php($total_interest = 0)
            @php($beginning_cash_eqiv = 0)
            @php($capital_stock = 0)

            @foreach($accjournals as $accjournal)
                @php($total_receipt += $accjournal->total)
            @endforeach

            @foreach($interest as $intrs)
                @php($total_interest += $intrs->total)
            @endforeach

            @foreach($cash_eqiv as $ce)
                @php($beginning_cash_eqiv += $ce->debit)
            @endforeach

            @foreach($capital_stocks as $cs)
                @php($capital_stock += $cs->total)
            @endforeach
            <center>
                <h4 class="text-uppercase">
                    <strong>
                    PT Unity Brands Indonesia
                    <br>Laporan Arus Kas (Cashflow Statement)
                    <br>Periode {{\Carbon\Carbon::parse($fromdate)->format('d F Y')}} s/d {{\Carbon\Carbon::parse($thrudate)->format('d F Y')}}
                    </strong>
                </h4>
            </center>
            <br>

            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <td colspan="7">
                            <h5 class="text-uppercase">Arus kas dari aktivitas operasi</h5>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="4">Penerimaan Kas dari Member</td>
                        <td class="my_rupiah">{{$total_receipt}}</td>
                        <td></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="6">Pembayaran Kas ke Vendor & Karyawan</td>
                    </tr>

                    @php($count_expense = 0)
                    @foreach($expenses as $expense)
                        @php($count_expense++)
                        <tr>
                            <td colspan="2"></td>
                            <td>{{$expense->coa}}</td>
                            <td class="my_rupiah {{ ($count_expense == count($expense)) ? 'td-border-bottom' : '' }}">
                                {{$expense->total}}
                                @php($total_receipt -= $expense->total)
                                @php($total_expense += $expense->total)
                            </td>
                            <td class="{{ ($count_expense == count($expense)) ? 'td-border-bottom' : '' }}">
                                @if($count_expense == count($expense))
                                    +
                                @endif
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    @endforeach

                    <tr>
                        <td colspan="5"></td>
                        <td class="my_rupiah td-border-bottom">{{$total_expense}}</td>
                        <td class="td-border-bottom">-</td>
                    </tr>

                    <tr>
                        <td></td>
                        <td colspan="2">Kas yang dihasilkan operasi</td>
                        <td colspan="2"></td>
                        <td class="my_rupiah">{{$total_receipt}}</td>
                        <td></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td colspan="2">Pembayaran bunga</td>
                        <td colspan="2"></td>
                        <td class="my_rupiah">
                            {{$total_interest}}
                            @php($total_receipt -= $total_interest)
                        </td>
                        <td></td>
                    </tr>

                    @if(count($taxes) > 0)
                        @php($count_taxes = 0)
                        @foreach($taxes as $tax)
                            @php($count_taxes++)
                            <tr>
                                <td></td>
                                <td colspan="2">{{$tax->coa}}</td>
                                <td colspan="2"></td>
                                <td class="my_rupiah {{ ($count_expense == count($expense)) ? 'td-border-bottom' : '' }}">
                                    {{$tax->total}}
                                    @php($total_receipt -= $tax->total)
                                </td>
                                <td class="{{ ($count_expense == count($expense)) ? 'td-border-bottom' : '' }}">
                                    @if($count_taxes == count($taxes))
                                        -
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td></td>
                            <td colspan="2">Pembayaran pajak penghasilan</td>
                            <td colspan="2"></td>
                            <td class="my_rupiah td-border-bottom">0</td>
                            <td class="td-border-bottom">-</td>
                        </tr>
                    @endif

                    <tr>
                        <td></td>
                        <td colspan="2">Arus Kas Bersih dari Aktivitas Operasi</td>
                        <td colspan="2"></td>
                        <td class="my_rupiah">{{$total_receipt}}</td>
                        <td></td>
                    </tr>

                    <tr>
                        <td colspan="7">
                            <h5 class="text-uppercase">Arus kas dari aktivitas investasi</h5>
                        </td>
                    </tr>

                    @if(!empty(count($invests)))
                        @php($count_invest = 0)
                        @foreach($invests as $invest)
                            @php($count_invest++)
                            <tr>
                                <td></td>
                                <td colspan="2">{{$invest->coa}}</td>
                                <td colspan="2"></td>
                                <td class="my_rupiah {{ ($count_expense == count($expense)) ? 'td-border-bottom' : '' }}">
                                    {{$invest->total}}
                                    @php($total_receipt += $invest->total)
                                </td>
                                <td class="{{ ($count_expense == count($expense)) ? 'td-border-bottom' : '' }}">
                                    @if($count_invest == count($invest))
                                        +
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <tr>
                            <td></td>
                            <td colspan="2">Pembelian Peralatan Kantor</td>
                            <td colspan="2"></td>
                            <td class="my_rupiah td-border-bottom">0</td>
                            <td class="td-border-bottom">+</td>
                        </tr>
                    @endif

                    <tr>
                        <td colspan="5"></td>
                        <td class="my_rupiah">{{$total_receipt}}</td>
                        <td></td>
                    </tr>

                    <tr>
                        <td colspan="7">
                            <h5 class="text-uppercase">Arus kas dari aktivitas pendanaan</h5>
                        </td>
                    </tr>

                    <tr>
                        <td></td>
                        <td colspan="2">Investasi Awal</td>
                        <td class="my_rupiah">
                            {{$capital_stock}}
                            @php($total_receipt += $capital_stock)
                        </td>
                        <td></td>
                        <td colspan="2"></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="4">Arus kas bersih yang digunakan untuk aktivitas pendanaan</td>
                        <td class="my_rupiah td-border-bottom">{{$capital_stock}}</td>
                        <td class="td-border-bottom">+</td>
                    </tr>

                    <tr>
                        <td></td>
                        <td colspan="2">Kenaikan Bersih kas dan setara kas</td>
                        <td colspan="2"></td>
                        <td class="my_rupiah">{{$total_receipt}}</td>
                        <td></td>
                    </tr>

                    <tr>
                        <td></td>
                        <td colspan="2">Kas dan setara kas pada awal periode</td>
                        <td colspan="2"></td>
                        <td class="my_rupiah td-border-bottom">
                            {{$beginning_cash_eqiv}}
                            @php($total_receipt -= $beginning_cash_eqiv)
                        </td>
                        <td class="td-border-bottom">-</td>
                    </tr>
                    <tr>
                        <td></td>
                        <td colspan="2">Kas dan setara kas pada akhir periode</td>
                        <td colspan="2"></td>
                        <td class="my_rupiah">{{$total_receipt}} </td>
                        <td></td>
                    </tr>
                </table>
            </div>
        @endif
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/my_js.js')}}"></script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            init_format();

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

            $('#clubs').select2({
                dropdownAdapter: $.fn.select2.amd.require('select2/selectAllAdapter')
            });
        });

        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp. ' + x + ',-');
            });
            $('.rupiah').each(function () {
                var x = rupiah_formater($(this).val().replace(/\s/g, ''));
                $(this).val((x));
            });
        }
    </script>
@endsection