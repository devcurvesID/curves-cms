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
                @lang('labels.memorial_journal')
                <br><small>({{Carbon\Carbon::parse($fromdate)->format('d-m-Y')}} @lang('labels.until') {{Carbon\Carbon::parse($thrudate)->format('d-m-Y')}})</small>
            </h2>
        </center>
    </div>
    <div class="col-xs-12">
        <div class="row">
            {!! Form::open(['method' => 'GET']) !!}
            <div class="col-xs-12">
                <div class="col-xs-3">
                    {!! Form::label('filter', trans('labels.filter'), ['class' => 'col-md-4 control-label']) !!} :
                </div>
                <div class="col-xs-3">
                    {!! Form::date('from_date', \Carbon\Carbon::now() , ['class' => 'form-control filter-date','placeholder'=>@trans('labels.from_date'), 'id'=>'filt-dari']) !!} <br/>
                </div>
                <div class="col-xs-3">
                    {!! Form::date('thru_date', \Carbon\Carbon::now() , ['class' => 'form-control filter-date','placeholder'=>@trans('labels.from_date'), 'id'=>'filt-sampai']) !!} <br/>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="col-xs-3">
                    {!! Form::label('jubilant', trans('labels.choose_jubilant'), ['class' => 'col-md-4 control-label']) !!} :
                </div>
                <div class="col-xs-6">
                    {!! Form::select('jubilant', $jubilants, null, ['class' => 'form-control select2', 'id' => 'jubilant', 'placeholder' => trans('labels.choose_jubilant')]) !!}
                </div>
            </div>
            <div class="col-xs-12" style="margin-top: 10px;">
                <div class="col-xs-3">
                    {!! Form::label('clubs', trans('labels.choose_club'), ['class' => 'col-md-4 control-label']) !!} :
                </div>
                <div class="col-xs-6">
                    <select name="clubs" id="club" class="form-control select2">
                        <option value="">@lang('labels.choose_club')</option>
                        @if(!empty($_GET['jubilant']))
                            @foreach($clubs as $club)
                                @if($club->jubilant_id == $_GET['jubilant'])
                                    @if($_GET['clubs'] == $club->id)
                                        <option value="{{ $club->id }}" selected="selected">{{ $club->club_name }}</option>
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
                <div class="col-xs-3">
                    {!! Form::label('coa', trans('labels.choose_coa'), ['class' => 'col-md-4 control-label']) !!}
                </div>
                <div class="col-xs-6">
                    <select name="coas[]" class="form-control select2" id="coas" multiple>
                        @if(!empty($_GET['jubilant']))
                            @foreach($coas as $coa)
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
                                        <option value="{{$coa->id}}" selected>{{$coa->coa_number}}: {{$coa->coa_name}}</option>
                                    @else
                                        <option value="{{$coa->id}}">{{$coa->coa_number}}: {{$coa->coa_name}}</option>
                                    @endif
                                @endif
                            @endforeach
                        @endif
                    </select>
                </div>

                <div class="col-xs-2">
                    <button type="submit" class="btn btn-primary" id="save-btn"><i class="fa fa-search"></i> @lang('labels.filter')</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        <hr/>
        <div class="table-responsive">
            <table id="dataTable" class="table table-bordered table-striped table-hover" style="background: white">
                <thead>
                <tr>
                    {{--<th>@lang('labels.date')</th>--}}
                    {{--<th>@lang('labels.acc_no')</th>--}}
                    <th>@lang('labels.notes')</th>
                    {{--<th>@lang('labels.club')</th>--}}
                    <th>@lang('labels.debet')</th>
                    <th>@lang('labels.credit')</th>
                </tr>
                </thead>
                <tbody>
                @php($count = 1)
                @php($totaldeb = 0)
                @php($totalcre = 0)
                    {{--Payment / Pendapatan diterima di muka--}}
                    <tr>
                        <td>Pendapatan diterima di muka</td>
                        <td class="my_rupiah" style="text-align: right;">{{ round($payments[0]->payment) }}</td>
                        <td class="my_rupiah" style="text-align: right;">0</td>
                        @php($totaldeb += round($payments[0]->payment))
                    </tr>

                    {{--Dues / Iuran Bulanan--}}
                    <tr>
                        <td>Iuran Bulanan</td>
                        <td class="my_rupiah" style="text-align: right;">0</td>
                        <td class="my_rupiah" style="text-align: right;">{{ round($dues[0]->dues) }}</td>
                        @php($totalcre += round($dues[0]->dues))
                    </tr>

                    {{--Amortization--}}
                    <tr>
                        <td>Amortisasi</td>
                        <td class="my_rupiah" style="text-align: right;">0</td>
                        <td class="my_rupiah" style="text-align: right;">{{ round($amortizations[0]->amortization) }}</td>
                        @php($totalcre += round($amortizations[0]->amortization))
                    </tr>

                    <tr>
                        <td style="font-weight: bold;">@lang('labels.total')</td>
                        <td style="text-align: right; font-weight: bold;">
                            Rp. {{ $totaldeb < 0 ? '- ' : ''}}
                            <span class='my_rupiah'>{{ $totaldeb >= 0 ? $totaldeb : $totaldeb*-1 }}</span>
                        </td>
                        <td style="text-align: right; font-weight: bold;">
                            Rp. {{ $totalcre < 0 ? '- ' : ''}}
                            <span class='my_rupiah'>{{ $totalcre >= 0 ? $totalcre : $totalcre*-1 }}</span>
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

            $('#jubilant').on('change', function () {
//                Club
                var club = '<option value="">@lang('labels.choose_club')</option>';
                var choose_jubilant = $(this).val();
                $('#clubs').val("");
                @foreach($clubs as $club)
                    var jubilant = '{{ $club->jubilant_id }}';
                    if(jubilant == choose_jubilant) {
                        club += '<option value="{{ $club->id }}">{{ $club->club_name }}</option>';
                    }
                @endforeach

                $('#club').html(club);

//                COA
                var coa = '';
                $('#coas').val("");
                @foreach($coas as $coa)
                    var jubilant = '{{ $coa->jubilant_id }}';
                    if(jubilant == choose_jubilant) {
                        coa += '<option value="{{$coa->id}}">{{$coa->coa_number}}: {{$coa->coa_name}}</option>';
                    }
                @endforeach

                $('#coas').html(coa);
            });

            $('#jubilant').select2();
            $('#clubs').select2();

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