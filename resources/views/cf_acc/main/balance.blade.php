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
                @lang('labels.neraca')
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
            @php($total_cashbanks = 0)
            @php($total_acc_receivables = 0)
            @php($total_prepaid_expenses = 0)
            @php($total_fixed_assets = 0)
            @php($total_accu_depreciations = 0)
            @php($total_etc_assets = 0)
            @php($total_debts = 0)
            @php($total_ymhs = 0)
            @php($total_equities = 0)
            @php($total_balance = 0)

            <center>
                <h4 class="text-uppercase">
                    <strong>
                        PT Unity Brands Indonesia
                        <br>Neraca
                        <br>Periode {{\Carbon\Carbon::parse($fromdate)->format('d F Y')}} s/d {{\Carbon\Carbon::parse($thrudate)->format('d F Y')}}
                    </strong>
                </h4>
            </center>
            <br>

            <div class="table-responsive">
                <table class="table">
                    {{--AKTIVA--}}
                    <tr>
                        <th colspan="8" class="text-uppercase td-border-full">aktiva</th>
                    </tr>
                    <tr>
                        <td></td>
                        <th colspan="2" class="text-uppercase">aktiva lancar</th>
                        <th colspan="2" class="text-uppercase">current assets</th>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <th colspan="6" class="text-uppercase">kas dan bank</th>
                    </tr>
                    @if(!empty($cashbanks))
                        @php($count_cashbanks = 0)
                        @foreach($cashbanks as $cashbank)
                            @php($count_cashbanks++)
                            <tr>
                                <td colspan="2"></td>
                                <td>{{ $cashbank->coa }}</td>
                                <td></td>
                                <td>{{ $cashbank->coa }}</td>
                                <td class="my_rupiah {{ ($count_cashbanks == count($cashbanks)) ? 'td-border-bottom' : '' }}">
                                    {{ $cashbank->total }}
                                    @php($total_balance += $cashbank->total)
                                    @php($total_cashbanks += $cashbank->total)
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2"></td>
                            <th class="text-uppercase" style="text-align: center">total kas dan bank</th>
                            <td colspan="3"></td>
                            <th class="my_rupiah">{{ $total_cashbanks }}</th>
                            <td></td>
                        </tr>
                    @endif

                    <tr>
                        <td colspan="8"></td>
                    </tr>

                    <tr>
                        <td colspan="2"></td>
                        <th colspan="6" class="text-uppercase">piutang</th>
                    </tr>
                    @if(!empty($acc_receivables))
                        @php($count_acc_receivables = 0)
                        @foreach($acc_receivables as $acc_receivable)
                            @php($count_acc_receivables++)
                            <tr>
                                <td colspan="2"></td>
                                <td>{{ $acc_receivable->coa }}</td>
                                <td></td>
                                <td>{{ $acc_receivable->coa }}</td>
                                <td class="my_rupiah {{ ($count_acc_receivables == count($acc_receivables)) ? 'td-border-bottom' : '' }}">
                                    {{ $acc_receivable->total }}
                                    @php($total_balance += $acc_receivable->total)
                                    @php($total_acc_receivables += $acc_receivable->total)
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2"></td>
                            <th class="text-uppercase" style="text-align: center">total piutang</th>
                            <td colspan="3"></td>
                            <th class="my_rupiah">{{ $total_acc_receivables }}</th>
                            <td></td>
                        </tr>
                    @endif

                    <tr>
                        <td colspan="8"></td>
                    </tr>

                    <tr>
                        <td colspan="2"></td>
                        <th colspan="4" class="text-uppercase">persediaan barang dagangan</th>
                        <th class="my_rupiah">0</th>
                        <td></td>
                    </tr>

                    <tr>
                        <td colspan="8"></td>
                    </tr>

                    <tr>
                        <td colspan="2"></td>
                        <th colspan="6" class="text-uppercase">biaya dibayar dimuka</th>
                    </tr>

                    @if(!empty($prepaid_expenses))
                        @php($count_prepaid_expenses = 0)
                        @foreach($prepaid_expenses as $prepaid_expense)
                            @php($count_prepaid_expenses++)
                            <tr>
                                <td colspan="2"></td>
                                <td>{{ $prepaid_expense->coa }}</td>
                                <td></td>
                                <td>{{ $prepaid_expense->coa }}</td>
                                <td class="my_rupiah {{ ($count_prepaid_expenses == count($prepaid_expenses)) ? 'td-border-bottom' : '' }}">
                                    {{ $prepaid_expense->total }}
                                    @php($total_balance += $prepaid_expense->total)
                                    @php($total_prepaid_expenses += $prepaid_expense->total)
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2"></td>
                            <th class="text-uppercase" style="text-align: center">total biaya dibayar dimuka</th>
                            <td colspan="3"></td>
                            <th class="my_rupiah">{{ $total_prepaid_expenses }}</th>
                            <td></td>
                        </tr>
                    @endif

                    <tr>
                        <td class="td-border-full"></td>
                        <th colspan="3" class="text-uppercase td-border-full">total aktiva lancar</th>
                        <th colspan="3" class="text-uppercase td-border-full">total current assets</th>
                        <th class="my_rupiah td-border-full">{{ $total_balance }}</th>
                    </tr>

                    <tr>
                        <td colspan="8"></td>
                    </tr>

                    <tr>
                        <td></td>
                        <th colspan="2" class="text-uppercase">aktiva tetap</th>
                        <th colspan="2" class="text-uppercase">fixed assets</th>
                        <td colspan="3"></td>
                    </tr>
                    @if(!empty($fixed_assets))
                        @php($count_fixed_assets = 0)
                        @php($software = 0)
                        @foreach($fixed_assets as $fixed_asset)
                            @if(strtolower($fixed_asset->coa) != 'software')
                                @php($count_fixed_assets++)
                                <tr>
                                    <td colspan="2"></td>
                                    <td>{{ $fixed_asset->coa }}</td>
                                    <td></td>
                                    <td>{{ $fixed_asset->coa }}</td>
                                    <td class="my_rupiah {{ ($count_fixed_assets == (count($fixed_assets) - $software)) ? 'td-border-bottom' : '' }}">
                                        {{ $fixed_asset->total }}
                                        @php($total_balance += $fixed_asset->total)
                                        @php($total_fixed_assets += $fixed_asset->total)
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            @else
                                @php($software++)
                            @endif
                        @endforeach
                        <tr>
                            <td colspan="2"></td>
                            <th class="text-uppercase" style="text-align: center">total aktiva tetap</th>
                            <td colspan="3"></td>
                            <th class="my_rupiah">{{ $total_fixed_assets }}</th>
                            <td></td>
                        </tr>
                    @endif

                    <tr>
                        <td colspan="8"></td>
                    </tr>

                    <tr>
                        <td colspan="2"></td>
                        <th colspan="6" class="text-uppercase">akumulasi penyusutan</th>
                    </tr>
                    @if(!empty($accu_depreciations))
                        @php($count_accu_depreciations = 0)
                        @foreach($accu_depreciations as $accu_depreciation)
                            @php($count_accu_depreciations++)
                            <tr>
                                <td colspan="2"></td>
                                <td>{{ $accu_depreciation->coa }}</td>
                                <td></td>
                                <td>{{ $accu_depreciation->coa }}</td>
                                <td class="my_rupiah_acc {{ ($count_accu_depreciations == count($accu_depreciations)) ? 'td-border-bottom' : '' }}">
                                    {{ $fixed_asset->total }}
                                    @php($total_balance -= $accu_depreciation->total)
                                    @php($total_accu_depreciations += $accu_depreciation->total)
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2"></td>
                            <th class="text-uppercase" style="text-align: center">total akumulasi penyusutan</th>
                            <td colspan="3"></td>
                            <th class="my_rupiah_acc">{{ $total_accu_depreciations }}</th>
                            <td></td>
                        </tr>
                    @endif

                    <tr>
                        <td colspan="8"></td>
                    </tr>

                    <tr>
                        <td></td>
                        <th colspan="6" class="text-uppercase">nilai sisa buku aktiva tetap</th>
                        <th class="my_rupiah">{{ $total_fixed_assets - $total_accu_depreciations }}</th>
                    </tr>

                    <tr>
                        <td colspan="8"></td>
                    </tr>

                    <tr>
                        <td colspan="2"></td>
                        <th colspan="6" class="text-uppercase">aktiva lain-lain</th>
                    </tr>

                    @if(!empty($fixed_assets))
                        @foreach($fixed_assets as $fixed_asset)
                            @if(strtolower($fixed_asset->coa) == 'software')
                                <tr>
                                    <td colspan="2"></td>
                                    <td>{{ $fixed_asset->coa }}</td>
                                    <td></td>
                                    <td>{{ $fixed_asset->coa }}</td>
                                    <td class="my_rupiah td-border-bottom">
                                        {{ $fixed_asset->total }}
                                        @php($total_balance += $fixed_asset->total)
                                        @php($total_etc_assets += $fixed_asset->total)
                                    </td>
                                    <td colspan="2"></td>
                                </tr>
                            @endif
                        @endforeach
                        <tr>
                            <td colspan="2"></td>
                            <th class="text-uppercase" style="text-align: center">total aktiva lainnya</th>
                            <td colspan="4"></td>
                            <th class="my_rupiah">{{ $total_etc_assets }}</th>
                        </tr>
                    @endif

                    <tr>
                        <td colspan="8"></td>
                    </tr>

                    <tr>
                        <td class="td-border-full"></td>
                        <th colspan="3" class="text-uppercase td-border-full">total aktiva tetap & aktiva lain-lain</th>
                        <th colspan="3" class="text-uppercase td-border-full">fixed assets</th>
                        <th class="my_rupiah td-border-full">{{ $total_fixed_assets - $total_accu_depreciations + $total_etc_assets }}</th>
                    </tr>

                    <tr>
                        <td colspan="8"></td>
                    </tr>

                    <tr>
                        <th colspan="3" class="text-uppercase td-border-full">total aktiva</th>
                        <th colspan="2" class="text-uppercase td-border-full">total assets</th>
                        <td colspan="2" class="td-border-full"></td>
                        <th class="my_rupiah td-border-full">{{ $total_balance }}</th>
                    </tr>

                    {{--KEWAJIBAN DAN EKUITAS--}}
                    <tr>
                        <th colspan="8" class="text-uppercase td-border-full">kewajiban dan ekuitas</th>
                    </tr>

                    <tr>
                        <td></td>
                        <th colspan="2" class="text-uppercase">kewajiban</th>
                        <th colspan="2" class="text-uppercase">liabilities</th>
                        <td colspan="3"></td>
                    </tr>
                    <tr>
                        <td colspan="2"></td>
                        <th colspan="6" class="text-uppercase">utang usaha</th>
                    </tr>
                    @if(!empty($debts))
                        @php($count_debts = 0)
                        @foreach($debts as $debt)
                            @php($count_debts++)
                            <tr>
                                <td colspan="2"></td>
                                <td>{{ $debt->coa }}</td>
                                <td></td>
                                <td>{{ $debt->coa }}</td>
                                <td class="my_rupiah {{ ($count_debts == count($debts)) ? 'td-border-bottom' : '' }}">
                                    {{ $debt->total }}
                                    @php($total_balance -= $debt->total)
                                    @php($total_debts += $debt->total)
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2"></td>
                            <th class="text-uppercase" style="text-align: center">total utang usaha</th>
                            <td colspan="3"></td>
                            <th class="my_rupiah">{{ $total_debts }}</th>
                            <td></td>
                        </tr>
                    @endif

                    <tr>
                        <td colspan="8"></td>
                    </tr>

                    <tr>
                        <td colspan="2"></td>
                        <th colspan="6" class="text-uppercase">biaya yang masih harus dibayar</th>
                    </tr>
                    @if(!empty($ymhs))
                        @php($count_ymhs = 0)
                        @foreach($ymhs as $ymh)
                            @php($count_ymhs++)
                            <tr>
                                <td colspan="2"></td>
                                <td>{{ $ymh->coa }}</td>
                                <td></td>
                                <td>{{ $ymh->coa }}</td>
                                <td class="my_rupiah {{ ($count_ymhs == count($ymhs)) ? 'td-border-bottom' : '' }}">
                                    {{ $ymh->total }}
                                    @php($total_balance -= $ymh->total)
                                    @php($total_ymhs += $ymh->total)
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        @endforeach
                        <tr>
                            <td colspan="2"></td>
                            <th class="text-uppercase" style="text-align: center">total biaya ymh dibayar</th>
                            <td colspan="3"></td>
                            <th class="my_rupiah">{{ $total_ymhs }}</th>
                            <td></td>
                        </tr>
                    @endif

                    <tr>
                        <td colspan="8"></td>
                    </tr>

                    <tr>
                        <td class="td-border-full"></td>
                        <th colspan="3" class="text-uppercase td-border-full">total kewajiban</th>
                        <th colspan="3" class="text-uppercase td-border-full">total liability</th>
                        <th class="my_rupiah td-border-full">{{ $total_debts + $total_ymhs }}</th>
                    </tr>

                    <tr>
                        <td colspan="8"></td>
                    </tr>

                    <tr>
                        <td></td>
                        <th colspan="2" class="text-uppercase">ekuitas</th>
                        <th colspan="2" class="text-uppercase">owner's equity</th>
                        <td colspan="3"></td>
                    </tr>
                    @if(!empty($equities))
                        @php($count_equities = 0)
                        @foreach($equities as $equity)
                            @php($count_equities++)
                            <tr>
                                <td colspan="2"></td>
                                <td>{{ $equity->coa }}</td>
                                <td></td>
                                <td>{{ $equity->coa }}</td>
                                <td class="my_rupiah {{ ($count_equities == count($equities)) ? 'td-border-bottom' : '' }}">
                                    {{ $equity->total }}
                                    @php($total_balance -= $equity->total)
                                    @php($total_equities += $equity->total)
                                </td>
                                <td colspan="2"></td>
                            </tr>
                        @endforeach
                    @endif

                    <tr>
                        <td class="td-border-full"></td>
                        <th colspan="3" class="text-uppercase td-border-full">total ekuitas</th>
                        <th colspan="3" class="text-uppercase td-border-full">total equity</th>
                        <th class="my_rupiah td-border-full">{{ $total_equities }}</th>
                    </tr>

                    <tr>
                        <td colspan="8"></td>
                    </tr>

                    <tr>
                        <th colspan="3" class="text-uppercase td-border-full">total kewajiban dan ekuitas</th>
                        <th colspan="2" class="text-uppercase td-border-full">total liability and equity</th>
                        <td colspan="2" class="td-border-full"></td>
                        <th class="my_rupiah td-border-full">{{ $total_debts + $total_ymhs + $total_equities }}</th>
                    </tr>
                    <tr>
                        <td colspan="7"></td>
                        <th class="{{ ($total_balance < 0) ? 'my_rupiah_acc' : 'my_rupiah' }}">{{ $total_balance }}</th>
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
            $('.my_rupiah_acc').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('(Rp. ' + x + ',-)');
            });
        }
    </script>
@endsection