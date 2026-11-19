@extends('cf_acc.layouts.acc-dashboard-template')

@section('other-style')
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">

    <style>
        .select2 {
            width: 100% !important;
        }

        .my_rupiah, .my_rupiah_acc {
            text-align: right;
        }

        .table>tbody>tr>td, .table>tbody>tr>th, .table>tfoot>tr>td, .table>tfoot>tr>th, .table>thead>tr>td, .table>thead>tr>th {
            border-top: none;
        }

        .td-border-bottom {
            border-bottom: 2px solid black;
        }

        .td-border-full {
            border-top: 2px solid black!important;
            border-bottom: 2px solid black;
        }
    </style>
@endsection

@section('content')
    <div class="col-xs-12">
        <center>
            <h2 class="page-title">
                @lang('labels.income_statement')
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

        @if(!empty($bank_coas))
            @php($total_sales = 0)
            @php($total_sales_expenses = 0)
            @php($total_operationals = 0)
            @php($total_external = 0)
            @php($total_balance = 0)

            <center>
                <h4 class="text-uppercase">
                    <strong>
                        PT Unity Brands Indonesia
                        <br>Laporan Laba Rugi
                        <br>Periode {{\Carbon\Carbon::parse($fromdate)->format('d F Y')}} s/d {{\Carbon\Carbon::parse($thrudate)->format('d F Y')}}
                    </strong>
                </h4>
            </center>
            <br>

            <div class="table-responsive">
                <table class="table">
                    <tr>
                        <th colspan="8" class="td-border-full" style="text-align: right">{{\Carbon\Carbon::parse($fromdate)->format('d F Y')}} - {{\Carbon\Carbon::parse($thrudate)->format('d F Y')}}</th>
                    </tr>

                    {{--PENJUALAN--}}
                    <tr>
                        <th colspan="2" class="text-uppercase">penjualan</th>
                        <th colspan="6" class="text-uppercase">sales</th>
                    </tr>
                    @if(!empty($sales))
                        @php($count_sales = 0)
                        @foreach($sales as $sale)
                            @php($count_sales++)
                            <tr>
                                <td></td>
                                <td class="text-uppercase">{{ $sale->coa }}</td>
                                <td></td>
                                <td>{{ $sale->coa }}</td>
                                <td colspan="3"></td>
                                <td class="{{ (($sale->coa_num == '4-3000') || ($sale->total < 0)) ? 'my_rupiah_acc' : 'my_rupiah' }} {{ ($count_sales == count($sales)) ? 'td-border-bottom' : '' }}">
                                    {{ abs($sale->total) }}
                                    @php(($sale->coa_num == '4-3000') ? $total_balance -= $sale->total : $total_balance += $sale->total)
                                    @php(($sale->coa_num == '4-3000') ? $total_sales -= $sale->total : $total_sales += $sale->total)
                                </td>
                            </tr>
                        @endforeach
                        {{--<tr>--}}
                            {{--<th class="text-uppercase" colspan="2">penjualan bersih</th>--}}
                            {{--<th class="text-uppercase" colspan="5">net sales</th>--}}
                            {{--<th class="td-border-bottom {{ ($total_sales < 0) ? 'my_rupiah_acc' : 'my_rupiah' }}">{{ abs($total_sales) }}</th>--}}
                        {{--</tr>--}}
                    @endif

                    @if(!empty($retur_sales))
                        @php($count_rsales = 0)
                        @foreach($retur_sales as $sale)
                            @php($count_rsales++)
                            <tr>
                                <td></td>
                                <td class="text-uppercase">{{ $sale->coa }}</td>
                                <td></td>
                                <td>{{ $sale->coa }}</td>
                                <td colspan="3"></td>
                                <td class="{{ (($sale->coa_num == '4-3000') || ($sale->total < 0)) ? 'my_rupiah_acc' : 'my_rupiah' }} {{ ($count_rsales == count($retur_sales)) ? 'td-border-bottom' : '' }}">
                                    {{ abs($sale->total) }}
                                    @php(($sale->coa_num == '4-3000') ? $total_balance -= $sale->total : $total_balance += $sale->total)
                                    @php(($sale->coa_num == '4-3000') ? $total_sales -= $sale->total : $total_sales += $sale->total)
                                </td>
                            </tr>
                        @endforeach
                    @endif

                    <tr>
                        <th class="text-uppercase" colspan="2">penjualan bersih</th>
                        <th class="text-uppercase" colspan="5">net sales</th>
                        <th class="td-border-bottom {{ ($total_sales < 0) ? 'my_rupiah_acc' : 'my_rupiah' }}">{{ abs($total_sales) }}</th>
                    </tr>

                    <tr>
                        <td colspan="8"></td>
                    </tr>

                    {{--HARGA POKOK PENJUALAN--}}
                    <tr>
                        <th colspan="2" class="text-uppercase">harga pokok penjualan</th>
                        <th colspan="5" class="text-uppercase">details of cogs</th>
                        <th class="my_rupiah">0</th>
                    </tr>

                    <tr>
                        <td colspan="8"></td>
                    </tr>

                    <tr>
                        <th colspan="7" class="text-uppercase td-border-full">laba kotor</th>
                        <th class="td-border-full {{ ($total_balance < 0) ? 'my_rupiah_acc' : 'my_rupiah' }}">{{ abs($total_balance) }}</th>
                    </tr>

                    <tr>
                        <td colspan="8"></td>
                    </tr>

                    {{--BEBAN PENJUALAN--}}
                    <tr>
                        <th colspan="2" class="text-uppercase">beban penjualan</th>
                        <th colspan="6" class="text-uppercase">sales expenses</th>
                    </tr>
                    @if(!empty($sales_expenses))
                        @php($count_sales_expenses = 0)
                        @foreach($sales_expenses as $sales_expense)
                            @php($count_sales_expenses++)
                            <tr>
                                <td></td>
                                <td class="text-uppercase">{{ $sales_expense->coa }}</td>
                                <td></td>
                                <td>{{ $sales_expense->coa }}</td>
                                <td class="my_rupiah {{ ($count_sales_expenses == count($sales_expenses)) ? 'td-border-bottom' : '' }}">
                                    {{ $sales_expense->total }}
                                    @php($total_sales_expenses += $sales_expense)
                                </td>
                                <td colspan="3"></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <th class="text-uppercase" colspan="2">total beban penjualan</th>
                            <th class="text-uppercase" colspan="2">total sales expenses</th>
                            <th class="my_rupiah">{{ $total_sales_expenses }}</th>
                            <td colspan="2"></td>
                        </tr>
                    @endif

                    <tr>
                        <td colspan="8"></td>
                    </tr>

                    {{--BEBAN ADMINISTRASI DAN UMUM--}}
                    <tr>
                        <th colspan="2" class="text-uppercase">beban administrasi dan umum</th>
                        <th colspan="6" class="text-uppercase">operational expenses</th>
                    </tr>
                    @if(!empty($operationals))
                        @php($count_operationals = 0)
                        @foreach($operationals as $operational)
                            @php($count_operationals++)
                            <tr>
                                <td></td>
                                <td class="text-uppercase">{{ $operational->coa }}</td>
                                <td></td>
                                <td>{{ $operational->coa }}</td>
                                <td colspan="2"></td>
                                <td class="my_rupiah {{ ($count_operationals == count($operationals)) ? 'td-border-bottom' : '' }}">
                                    {{ $operational->total }}
                                    @php($total_balance -= $operational->total)
                                    @php($total_operationals += $operational->total)
                                </td>
                                <td></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="8"></td>
                        </tr>
                        <tr>
                            <td></td>
                            <th colspan="6" class="text-uppercase">total biaya administrasi dan umum</th>
                            <th class="my_rupiah">{{ $total_operationals }}</th>
                        </tr>
                    @endif

                    <tr>
                        <td colspan="8"></td>
                    </tr>

                    <tr>
                        <th colspan="7" class="text-uppercase td-border-full">laba/rugi</th>
                        <th class="td-border-full {{ ($total_balance < 0) ? 'my_rupiah_acc' : 'my_rupiah' }}">{{ abs($total_balance) }}</th>
                    </tr>

                    <tr>
                        <td colspan="8"></td>
                    </tr>

                    {{--PENDAPATAN (BEBAN) LAIN-LAIN--}}
                    <tr>
                        <th colspan="2" class="text-uppercase">pendapatan (beban) lain-lain</th>
                        <th colspan="6" class="text-uppercase">income and (costs) from external source</th>
                    </tr>
                    @if(!empty($external_sources))
                        @php($count_external = 0)
                        @foreach($external_sources as $external_source)
                            @php($count_external++)
                            <tr>
                                <td></td>
                                <td class="text-uppercase">{{ $external_source->coa }}</td>
                                <td colspan="5"></td>
                                <td class="my_rupiah {{ ($count_external == count($external_sources)) ? 'td-border-bottom' : '' }}">
                                    {{ $external_source->total }}
                                    @php($total_balance += $external_source->total)
                                    @php($total_external += $external_source->total)
                                </td>
                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <th colspan="2" class="text-uppercase">total pendapatan (beban) lain-lain</th>
                            <th colspan="4" class="text-uppercase">total income and (costs) from external source</th>
                            <th class="my_rupiah">{{ $total_external }}</th>
                        </tr>

                        <tr>
                            <td colspan="8"></td>
                        </tr>

                        <tr>
                            <th colspan="7" class="text-uppercase td-border-full">laba / (rugi) bersih sebelum pajak</th>
                            <th class="td-border-full {{ ($total_balance < 0) ? 'my_rupiah_acc' : 'my_rupiah' }}">{{ abs($total_balance) }}</th>
                        </tr>

                        <tr>
                            <td colspan="8"></td>
                        </tr>

                        <tr>
                            <th colspan="7" class="text-uppercase">pajak penghasilan</th>
                            <th class="my_rupiah">0</th>
                        </tr>

                        <tr>
                            <td colspan="8"></td>
                        </tr>

                        <tr>
                            <th colspan="7" class="text-uppercase td-border-full">laba / (rugi) kotor sesudah pajak</th>
                            <th class="td-border-full {{ ($total_balance < 0) ? 'my_rupiah_acc' : 'my_rupiah' }}">{{ abs($total_balance) }}</th>
                        </tr>
                    @endif
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
            $('.my_rupiah_acc').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('(Rp. ' + x + ',-)');
            });
        }
    </script>
@endsection