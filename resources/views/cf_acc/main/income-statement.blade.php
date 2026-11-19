@extends('cf_acc.layouts.acc-dashboard-template')

@section('other-style')
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">

    <style>
        #clubs {
            width: 100%;
        }

        .my_rupiah {
            text-align: right;
        }

        .page-title {
            margin-bottom: -10px !important;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{(url()->previous() == url()->full()) ? url('cf-acc/ledger') : url()->previous()}}" style="left: 25px; top: 5px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.income_statement') </h2>
            </center>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <h3 class="text-center">@lang('labels.from'): {{\Carbon\Carbon::parse($from_date)->format('d-m-Y')}}  @lang('labels.until')  {{\Carbon\Carbon::parse($thru_date)->format('d-m-Y')}}</h3>
            @php($club_list = '')
            @php($counter = 0)
            @if(count($chosen_club) > 1)
                @foreach($chosen_club as $chosen)
                    @foreach($clubs as $key => $value)
                        @if($chosen == $key)
                            @if($counter == 0)
                                @php($club_list .= $value)
                            @else
                                @php($club_list .= ', '.$value)
                            @endif
                        @endif
                    @endforeach
                    @php($counter++)
                @endforeach
            @else
                @foreach($clubs as $key => $value)
                    @if($chosen_club == $key)
                        @if($counter == 0)
                            @php($club_list .= $value)
                        @else
                            @php($club_list .= ', '.$value)
                        @endif
                    @endif
                @endforeach
            @endif
            <h4 class="text-center">{{$club_list}}</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <div class="table-responsive">
                {{--Rincian Penjualan--}}
                @php($net_sales = 0)
                @php($hpp_sales = 0)
                @php($sales_expenses = 0)
                @php($operational_expenses = 0)
                @php($external_source = 0)
                <table class="table">
                    <thead>
                        <tr>
                            <th colspan="3">@lang('labels.sales_detail')</th>
                            <th>@lang('labels.total')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($coas->where('main_account_id', '15') as $coa)
                        @php($empty = true)
                        @foreach($tbs as $tb)
                            @if($tb->coa_id == $coa->coa_number)
                                <tr>
                                    <td>{{$tb->coa_id}}</td>
                                    <td>{{$tb->coa_name}}</td>
                                    <td class="my_rupiah">{{abs($tb->coa_balance)}}</td>
                                    <td></td>
                                </tr>
                                @php($net_sales += abs($tb->coa_balance))
                                @php($empty = false)
                            @endif
                        @endforeach

                        @if($empty)
                            <tr>
                                <td>{{$coa->coa_number}}</td>
                                <td>{{$coa->coa_name}}</td>
                                <td class="my_rupiah">0</td>
                                <td></td>
                            </tr>
                        @endif
                    @endforeach
                        <tr>
                            <td style="font-weight: bold">@lang('labels.net_sales')</td>
                            <td colspan="3" class="my_rupiah" style="text-align: right">{{$net_sales}}</td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr>
                            <th colspan="4">@lang('labels.hpp_detail')</th>
                        </tr>
                    </thead>
                    <tfoot>
                        <tr>
                            <th>@lang('labels.gross_profit')</th>
                            <th colspan="3" class="my_rupiah">{{$net_sales - $hpp_sales}}</th>
                        </tr>
                    </tfoot>
                </table>

                <br>

                <table class="table">
                    <thead>
                        <tr>
                            <th colspan="3">@lang('labels.sales_expenses')</th>
                            <th>@lang('labels.total')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($coa_sales_expenses as $coa)
                        @php($empty = true)
                        @foreach($tbs as $tb)
                            @if($tb->coa_id == $coa->coa_number)
                                <tr>
                                    <td>{{$tb->coa_id}}</td>
                                    <td>{{$tb->coa_name}}</td>
                                    <td class="my_rupiah">{{abs($tb->coa_balance)}}</td>
                                    <td></td>
                                </tr>
                                @php($sales_expenses += abs($tb->coa_balance))
                                @php($empty = false)
                            @endif
                        @endforeach

                        @if($empty)
                            <tr>
                                <td>{{$coa->coa_number}}</td>
                                <td>{{$coa->coa_name}}</td>
                                <td class="my_rupiah">0</td>
                                <td></td>
                            </tr>
                        @endif
                    @endforeach
                        <tr>
                            <td style="font-weight: bold">@lang('labels.sales_expenses_total')</td>
                            <td colspan="3" class="my_rupiah" style="text-align: right">{{$sales_expenses}}</td>
                        </tr>
                    </tbody>
                    <thead>
                        <tr>
                            <th colspan="4">@lang('labels.operational_expenses')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($coas->where('main_account_id', '18') as $coa)
                        @php($empty = true)
                        @foreach($tbs as $tb)
                            @if($tb->coa_id == $coa->coa_number)
                                <tr>
                                    <td>{{$tb->coa_id}}</td>
                                    <td>{{$tb->coa_name}}</td>
                                    <td class="my_rupiah">{{abs($tb->coa_balance)}}</td>
                                    <td></td>
                                </tr>
                                @php($operational_expenses += abs($tb->coa_balance))
                                @php($empty = false)
                            @endif
                        @endforeach

                        @if($empty)
                            <tr>
                                <td>{{$coa->coa_number}}</td>
                                <td>{{$coa->coa_name}}</td>
                                <td class="my_rupiah">0</td>
                                <td></td>
                            </tr>
                        @endif
                    @endforeach
                        <tr>
                            <td style="font-weight: bold">@lang('labels.operational_expenses_total')</td>
                            <td colspan="3" class="my_rupiah" style="text-align: right">{{$operational_expenses}}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>@lang('labels.profit_and_loss')</th>
                            <th colspan="3" class="my_rupiah">{{($net_sales - $hpp_sales) - $sales_expenses - $operational_expenses}}</th>
                        </tr>
                    </tfoot>
                </table>

                <br>

                <table class="table">
                    <thead>
                        <tr>
                            <th colspan="3">@lang('labels.income_and_cost_from_external_source')</th>
                            <th>@lang('labels.total')</th>
                        </tr>
                    </thead>
                    <tbody>
                    @foreach($coa_out_of_businesses as $coa)
                        @php($empty = true)
                        @foreach($tbs as $tb)
                            @if($tb->coa_id == $coa->coa_number)
                                <tr>
                                    <td>{{$tb->coa_id}}</td>
                                    <td>{{$tb->coa_name}}</td>
                                    <td class="my_rupiah">{{abs($tb->coa_balance)}}</td>
                                    <td></td>
                                </tr>
                                @php($external_source += abs($tb->coa_balance))
                                @php($empty = false)
                            @endif
                        @endforeach

                        @if($empty)
                            <tr>
                                <td>{{$coa->coa_number}}</td>
                                <td>{{$coa->coa_name}}</td>
                                <td class="my_rupiah">0</td>
                                <td></td>
                            </tr>
                        @endif
                    @endforeach
                        <tr>
                            <td style="font-weight: bold">@lang('labels.total_income_and_cost_from_external_source')</td>
                            <td colspan="3" class="my_rupiah" style="text-align: right">{{$external_source}}</td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                            <th>@lang('labels.net_profit_and_loss_before_tax')</th>
                            <th colspan="3" class="my_rupiah">{{($net_sales - $hpp_sales) - $sales_expenses - $operational_expenses - $external_source}}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
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

            $('#clubs').select2({
                placeholder: '@lang('labels.choose_club')',
                dropdownAdapter: $.fn.select2.amd.require('select2/selectAllAdapter')
            });

            $('#generate-tb-btn').on('click', function () {
                $('#trial-balance-form').submit();
            });

            init_format();
        });

        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp. ' + (x) + ',-');
            });
            $('.rupiah').each(function () {
                var x = rupiah_formater($(this).val().replace(/\s/g, ''));
                $(this).val((x));
            });
        }
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>
@endsection