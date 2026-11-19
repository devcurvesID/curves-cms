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
            <a href="{{/*(url()->previous() == url()->full()) ? url('cf-acc/ledger') : url()->previous()*/url('cf-acc/ledger')}}" style="left: 25px; top: 5px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.trial_balance') </h2>
            </center>
        </div>
    </div>
    {{--<div class="row">--}}
        {{--<div class="col-sm-12">--}}
            {{--{!! Form::open(['method' => 'get', 'action' => 'AccountingJournalController@ledger', 'class' => 'form-inline']) !!}--}}
            {{--<div class="form-group">--}}
                {{--{!! Form::date('from_date', \Carbon\Carbon::parse($from_date), ['class' => 'form-control', 'placeholder' => trans('labels.from_date'), 'title' => trans('labels.from_date')]) !!}--}}
                {{--{!! Form::date('thru_date', \Carbon\Carbon::parse($thru_date), ['class' => 'form-control', 'placeholder' => trans('labels.thru_date'), 'title' => trans('labels.thru_date')]) !!}--}}
                {{--{!! Form::select('clubs[]', $clubs, null, ['class' => 'form-control select2', 'multiple', 'id' => 'clubs']) !!}--}}
                {{--{!! Form::submit(trans('labels.set_filter'), ['class' => 'btn btn-info']) !!}--}}
            {{--</div>--}}
            {{--{!! Form::close() !!}--}}
        {{--</div>--}}
    {{--</div>--}}
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
            <div class="col-sm-12">
                <button type="button" class="btn btn-default pull-left" id="generate-is-btn">@lang('labels.generate_income_statement')</button>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                                <tr>
                                    <th>@lang('labels.account_number')</th>
                                    <th>@lang('labels.account')</th>
                                    <th>@lang('labels.debit')</th>
                                    <th>@lang('labels.credit')</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach($coas as $coa)
                                @php($empty = true)
                                @foreach($tbs as $tb)
                                    @if($tb->coa_id == $coa->coa_number)
                                        <tr>
                                            <td>{{$tb->coa_id}}</td>
                                            <td>{{$tb->coa_name}}</td>
                                            @if($tb->coa_balance >= 0)
                                                <td class="my_rupiah">{{$tb->coa_balance}}</td>
                                                <td class="my_rupiah">0</td>
                                            @else
                                                <td class="my_rupiah">0</td>
                                                <td class="my_rupiah">{{abs($tb->coa_balance)}}</td>
                                            @endif
                                        </tr>
                                        @php($empty = false)
                                    @endif
                                @endforeach

                                @if($empty)
                                    <tr>
                                        <td>{{$coa->coa_number}}</td>
                                        <td>{{$coa->coa_name}}</td>
                                        <td class="my_rupiah">0</td>
                                        <td class="my_rupiah">0</td>
                                    </tr>
                                @endif
                            @endforeach
                            </tbody>
                            <tfoot>
                            <tr>
                                <td></td>
                                <td></td>
                                <td style="font-weight: bold; text-align: right"></td>
                                <td style="font-weight: bold; text-align: right"></td>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {!! Form::open(['method' => 'GET', 'action' => 'AccountingJournalController@income_statement', 'id' => 'income-statement-form']) !!}
    {!! Form::hidden('tbs', json_encode($tbs)) !!}
    {!! Form::hidden('from_date', $from_date) !!}
    {!! Form::hidden('thru_date', $thru_date) !!}
    {!! Form::hidden('chosen_clubs', json_encode($chosen_club)) !!}
    {!! Form::hidden('chosen_coas', json_encode($chosen_coa)) !!}
    {!! Form::close() !!}
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>

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

            $('#generate-is-btn').on('click', function () {
                $('#income-statement-form').submit();
            });

            init_format();

            $('#data-table').dataTable({
                'iDisplayLength': -1,
                'lengthMenu': [[10, 50, 100, -1], [10, 50, 100, '@lang('labels.all')']],
                'footerCallback': function (row, data, start, end, display) {
                    var api = this.api(), data;
                    var intVal = function (i) {
                        return parseInt(rupiah_format_destroyer(i));
                    }

                    var debit = api
                            .column(2)
                            .data()
                            .reduce( function (a, b) {
                                return intVal(a) + intVal(b);
                            }, 0 );

                    var credit = api
                        .column(3)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                    // Update footer
                    $( api.column(2).footer() ).html('Rp. ' + rupiah_formater(debit) + ',-');
                    $( api.column(3).footer() ).html('Rp. ' + rupiah_formater(credit) + ',-');
                },
                {{--dom: 'Bfrtip',--}}
                {{--buttons: [--}}
                    {{--{--}}
                        {{--extend: 'excelHtml5',--}}
                        {{--text: '<span class="fa fa-download" title="@lang('labels.export_excel')"></span> Excel',--}}
                        {{--footer: true,--}}
                        {{--title: '@lang('labels.product_sales') {{$the_club}} {{\Carbon\Carbon::parse($from_date)->format('d-m-Y')}} @lang('labels.until') {{\Carbon\Carbon::parse($thru_date)->format('d-m-Y')}}'--}}
                    {{--},--}}
                    {{--{--}}
                        {{--extend: 'print',--}}
                        {{--footer: true,--}}
                        {{--text: '<span class="fa fa-print" title="@lang('labels.print')"></span> Print',--}}
                        {{--title: '@lang('labels.product_sales') {{$the_club}} {{\Carbon\Carbon::parse($from_date)->format('d-m-Y')}} @lang('labels.until') {{\Carbon\Carbon::parse($thru_date)->format('d-m-Y')}}',--}}
                        {{--customize: function ( win ) {--}}
                            {{--$(win.document.body).find( 'table' )--}}
                                {{--.addClass( 'compact' )--}}
                                {{--.css( 'font-size', 'inherit' );--}}

                            {{--$(win.document.body).find( 'h1' )--}}
                                {{--.css( 'font-size', '16px' );--}}
                        {{--}--}}
                    {{--}--}}
                {{--]--}}
            });
        });

        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text((x));
            });
            $('.rupiah').each(function () {
                var x = rupiah_formater($(this).val().replace(/\s/g, ''));
                $(this).val((x));
            });
        }
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>
@endsection