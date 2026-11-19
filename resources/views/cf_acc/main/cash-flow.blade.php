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
    <div class="col-xs-12">
        <center>
            <h2 class="page-title">
                @lang('labels.cash_flow')
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
            </div>
            <div class="col-xs-12">
                <div class="col-xs-2">
                    {!! Form::label('coa', trans('labels.choose_coa'), ['class' => 'col-md-4 control-label']) !!}
                </div>
                <div class="col-xs-6">
                    <select name="coas[]" class="form-control select2" id="coas" multiple>
                        @foreach($coas as $coa)
                            @php($coa_gate = false)
                            @if(!empty($_GET['coas']))
                                @foreach($_GET['coas'] as $chosen_coa)
                                    @if($chosen_coa == $coa->id)
                                        @php($coa_gate = true)
                                    @endif
                                @endforeach
                            @endif

                            @if($coa_gate)
                                <option value="{{$coa->id}}" selected>{{$coa->coa_number}}: {{$coa->coa_name}}</option>
                            @else
                                <option value="{{$coa->id}}">{{$coa->coa_number}}: {{$coa->coa_name}}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-xs-2">
                    <button type="submit" class="btn btn-primary" id="save-btn"><i class="fa fa-search"></i> @lang('labels.search')</button>
                    </a>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        <hr/>
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-striped table-hover" style="background: white">
                <thead>
                <tr>
                    <th>@lang('labels.date')</th>
                    <th>@lang('labels.acc_no')</th>
                    <th>@lang('labels.notes')</th>
                    <th>@lang('labels.club')</th>
                    <th>@lang('labels.debet')</th>
                    <th>@lang('labels.credit')</th>
                </tr>
                </thead>
                <tbody>
                @php($count = 1)
                @php($totaldeb = 0)
                @php($totalcre = 0)
                @foreach($accjournals as $aj)
                    <tr>
                        <td>{{ Carbon\Carbon::parse($aj->trans_date)->format('d-m-Y')}}</td>
                        <td>{{ $aj->accountingjournal_chartofaccount->coa_number}}</td>
                        <td>{{ $aj->information}}</td>
                        <td>{{ $aj->accountingjournal_club->club_name }}</td>
                        <td style="text-align: right;">
                            {{ $aj->credit < 0 ? '- ' : ''}}
                            <span class="my_rupiah">{{ $aj->credit==0 ? '' : $aj->credit >= 0 ? $aj->credit : $aj->credit*-1 }}</span>
                        </td>
                        <td style="text-align: right;">
                            {{ $aj->debit < 0 ? '- ' : ''}}
                            <span class="my_rupiah">{{ $aj->debit==0 ? '' : $aj->debit >= 0 ? $aj->debit : $aj->debit*-1 }}</span>
                        </td>
                    </tr>
                    @php($totaldeb += $aj->debit)
                    @php($totalcre += $aj->credit)
                @endforeach
                    <tr>
                        <td colspan="4" style="font-weight: bold;">@lang('labels.total')</td>
                        <td style="text-align: right; font-weight: bold;">
                            Rp. {{ $totalcre < 0 ? '- ' : ''}}
                            <span class='my_rupiah'>{{ $totalcre >= 0 ? $totalcre : $totalcre*-1 }}</span>
                        </td>
                        <td style="text-align: right; font-weight: bold;">
                            Rp. {{ $totaldeb < 0 ? '- ' : ''}}
                            <span class='my_rupiah'>{{ $totaldeb >= 0 ? $totaldeb : $totaldeb*-1 }}</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
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

            $('#coas').select2({
                dropdownAdapter: $.fn.select2.amd.require('select2/selectAllAdapter')
            });
        });

        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text(x);
            });
            $('.rupiah').each(function () {
                var x = rupiah_formater($(this).val().replace(/\s/g, ''));
                $(this).val((x));
            });
        }
    </script>
@endsection