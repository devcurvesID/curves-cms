@extends('cf_acc.layouts.acc-dashboard-template')

@section('other-style')
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">

    <style>
        /*#clubs {*/
            /*width: 50%;*/
        /*}*/
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-sm-12">
            <center>
                <h2 class="page-title">@lang('labels.ledger')</h2>
            </center>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            {!! Form::open(['method' => 'get', 'action' => 'AccountingJournalController@ledger', 'class' => 'form-inline']) !!}
            <div class="form-group">
                {!! Form::date('from_date', \Carbon\Carbon::parse($from_date), ['class' => 'form-control', 'placeholder' => trans('labels.from_date'), 'title' => trans('labels.from_date')]) !!}
                {!! Form::date('thru_date', \Carbon\Carbon::parse($thru_date), ['class' => 'form-control', 'placeholder' => trans('labels.thru_date'), 'title' => trans('labels.thru_date')]) !!}
                {{--{!! Form::select('clubs[]', $clubs, null, ['class' => 'form-control select2', 'multiple', 'id' => 'clubs']) !!}--}}
                <select name="clubs[]" class="form-control select2" multiple id="clubs">
                    @foreach($clubs as $club)
                        <option value="{{$club->id}}">{{$club->club_name}}</option>
                    @endforeach
                </select>
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
                {!! Form::submit(trans('labels.set_filter'), ['class' => 'btn btn-info']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <h3 class="text-center">@lang('labels.ledger') @lang('labels.from'): {{\Carbon\Carbon::parse($from_date)->format('d-m-Y')}}  @lang('labels.until')  {{\Carbon\Carbon::parse($thru_date)->format('d-m-Y')}}</h3>
            @php($club_list = '')
            @php($counter = 0)
            @if(count($chosen_club) > 1)
                @foreach($chosen_club as $chosen)
                    {{--@foreach($clubs as $key => $value)--}}
                        {{--@if($chosen == $key)--}}
                            {{--@if($counter == 0)--}}
                                {{--@php($club_list .= $value)--}}
                            {{--@else--}}
                                {{--@php($club_list .= ', '.$value)--}}
                            {{--@endif--}}
                        {{--@endif--}}
                    {{--@endforeach--}}
                    @foreach($clubs as $club)
                        @if($chosen == $club->id)
                            @if($counter == 0)
                                @php($club_list .= $club->club_costrevenuecenter->cr_code)
                            @else
                                @php($club_list .= ', '.$club->club_costrevenuecenter->cr_code)
                            @endif
                        @endif
                    @endforeach
                    @php($counter++)
                @endforeach
            @else
                {{--@foreach($clubs as $key => $value)--}}
                    {{--@if($chosen_club == $key)--}}
                        {{--@if($counter == 0)--}}
                            {{--@php($club_list .= $value)--}}
                        {{--@else--}}
                            {{--@php($club_list .= ', '.$value)--}}
                        {{--@endif--}}
                        {{--@php($chosen_club = $key)--}}
                    {{--@endif--}}
                {{--@endforeach--}}
                @foreach($clubs as $club)
                    @if($chosen_club == $club->id)
                        @if($counter == 0)
                            @php($club_list .= $club->club_costrevenuecenter->cr_code)
                        @else
                            @php($club_list .= ', '.$club->club_costrevenuecenter->cr_code)
                        @endif
                        @php($chosen_club = $club->id)
                    @endif
                @endforeach
            @endif
            <h4 class="text-center">{{$club_list}}</h4>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <button type="button" class="btn btn-default pull-left" id="generate-tb-btn">@lang('labels.generate_trial_balance')</button>
        </div>
        <div class="col-sm-12">
            @if(!empty($journals))
                @php
                    class TB {
                        public $coa_id;
                        public $coa_name;
                        public $coa_balance;
                    }
                @endphp
                @foreach($journals as $key => $value)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th colspan="{{(count($chosen_club) > 1) ? '6' : '5'}}">
                                    @foreach($value as $val)
                                        @if($loop->first)
                                            {{$val->accountingjournal_chartofaccount->coa_name}}
                                            <span style="float: right">{{$val->accountingjournal_chartofaccount->coa_number}}</span>
                                        @endif
                                    @endforeach
                                </th>
                            </tr>
                            <tr>
                                <th>@lang('labels.date')</th>
                                <th>@lang('labels.information')</th>
                                <th>@lang('labels.debit')</th>
                                <th>@lang('labels.credit')</th>
                                <th>@lang('labels.balance')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php($balance = 0)
                            @foreach($value as $val)
                                <tr>
                                    <td>{{\Carbon\Carbon::parse($val->trans_date)->format('d-m-Y')}}</td>
                                    <td>{{$val->information}}</td>
                                    <td class="my_rupiah">{{$val->debit}}</td>
                                    <td class="my_rupiah">{{$val->credit}}</td>
                                    <td class="my_rupiah">
                                        @if($val->debit == 0)
                                            {{$balance -= $val->credit}}
                                        @elseif($val->credit == 0)
                                            {{$balance += $val->debit}}
                                        @endif
                                    </td>
                                    @if(count($chosen_club) > 1)
                                        <td>{{$val->accountingjournal_club->club_name}}</td>
                                    @endif
                                </tr>
                            @endforeach
                            @php
                                $tb_item = new TB();
                                foreach ($value as $val):
                                    $tb_item->coa_id = $val->accountingjournal_chartofaccount->coa_number;
                                    $tb_item->coa_name = $val->accountingjournal_chartofaccount->coa_name;
                                    break;
                                endforeach;

                                $tb_item->coa_balance = $balance;
                                $tb[] = $tb_item;
                            @endphp
                            </tbody>
                        </table>
                    </div>
                    <br>
                @endforeach
            @else
                <h4>@lang('labels.no_data')</h4>
            @endif
        </div>
    </div>

    {!! Form::open(['method' => 'GET', 'action' => 'AccountingJournalController@trial_balance', 'id' => 'trial-balance-form']) !!}
    {!! Form::hidden('tbs', !empty($tb) ? json_encode($tb) : null) !!}
    {!! Form::hidden('from_date', $from_date) !!}
    {!! Form::hidden('thru_date', $thru_date) !!}
    {!! Form::hidden('chosen_clubs', json_encode($chosen_club)) !!}
    {!! Form::hidden('chosen_coas', json_encode($chosen_coa)) !!}
    {!! Form::close() !!}
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

            $('#coas').select2({
                placeholder: '@lang('labels.choose_coa')',
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
                $(this).text('Rp.' + (x) + ',-');
            });
            $('.rupiah').each(function () {
                var x = rupiah_formater($(this).val().replace(/\s/g, ''));
                $(this).val((x));
            });
        }
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>
@endsection