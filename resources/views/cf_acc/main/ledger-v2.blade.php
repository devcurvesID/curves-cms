@extends('cf_acc.layouts.acc-dashboard-template')

@section('other-style')
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">

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
        <div class="col-sm-12">
            <center>
                <h2 class="page-title">@lang('labels.ledger')</h2>
            </center>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            {!! Form::open(['method' => 'get', 'class' => 'form-inline']) !!}
            <div class="col-xs-12">
                <div class="col-xs-3">
                    {!! Form::label('month_year', trans('labels.month').'/'.trans('labels.year'), ['class' => 'control-label']) !!}
                    :
                </div>
                <div class="col-xs-3 input-group" id="month" style="padding-left: 15px">
                    {!! Form::text('month', null, ['class' => 'form-control']) !!}
                    <span class="input-group-addon">
                        <span class="fa fa-calendar"></span>
                    </span>
                </div>
            </div>
            <div class="col-xs-12" style="margin-top: 10px;">
                <div class="col-xs-3">
                    {!! Form::label('jubilant', trans('labels.choose_jubilant'), ['class' => 'control-label']) !!}
                    :
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
                    <select name="clubs[]" class="form-control select2" multiple id="clubs">
                        <option value="">@lang('labels.choose_club')</option>
                        @if(!empty($_GET['jubilant']))
                            @foreach($clubs as $club)
                                @if($club->jubilant_id == $_GET['jubilant'])
                                    @php($club_gate = false)
                                    {{-- @if(!empty($_GET['clubs']))
                                        @foreach($_GET['clubs'] as $chosen_club)
                                            @if($chosen_club == $club->id)
                                                @php($club_gate = true)
                                            @endif
                                        @endforeach
                                    @endif --}}

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
            <div class="col-xs-12" style="margin-top: 10px;">
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
                    <button type="submit" class="btn btn-primary" id="save-btn"><i
                                class="fa fa-search"></i> @lang('labels.filter')</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    <div class="row">
        <div class="col-sm-12">
            <h3 class="text-center">@lang('labels.ledger') @lang('labels.from')
                : {{\Carbon\Carbon::parse($from_date)->format('d-m-Y')}}  @lang('labels.until')  {{\Carbon\Carbon::parse($thru_date)->format('d-m-Y')}}</h3>
            @php($club_list = '')
            @php($counter = 0)
                @if($chosen_club > 1)
                    @foreach($chosen_club as $chosen)
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
        {{--<div class="col-sm-12">--}}
        {{--<button type="button" class="btn btn-default pull-left"--}}
        {{--id="generate-tb-btn">@lang('labels.generate_trial_balance')</button>--}}
        {{--</div>--}}
        <div class="col-sm-12">
            @php($chosen_coas = [])
            @if(!empty($journals))
                <?php
                    class TB {
                        public $coa_id;
                        public $coa_name;
                        public $coa_balance;
                    }
                ?>
                @foreach($journals as $key => $value)
                    @php($coa = null)
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                            <tr>
                                <th colspan="{{($chosen_club > 1) ? '6' : '5'}}">
                                    @foreach($value as $val)
                                        @if($loop->first)
                                            {{$val->accjournalv2_coa->coa_name}} {{ ($val->club_id != null) ? '- '.$val->accjournalv2_club->club_name : ' (PT)' }}
                                            <span style="float: right">{{$val->accjournalv2_coa->coa_number}}</span>
                                            @php($coa = $val->coa_id)
                                            @php(array_push($chosen_coas, $val->coa_id))
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
                            @php($debit = 0)
                            @php($credit = 0)
                            @if(count($beginning_balances->where('coa_id', $coa)) > 0)
                                @foreach($beginning_balances->where('coa_id', $coa) as $the_key => $bb)
                                    @if($loop->first)
                                        <tr>
                                            <td>{{ \Carbon\Carbon::parse($from_date)->format('d-m-Y') }}</td>
                                            <td>@lang('labels.beginning_balance')</td>
                                            <td class="my_rupiah">{{ $bb->debit }}</td>
                                            <td class="my_rupiah">{{ $bb->credit }}</td>
                                            <td class="my_rupiah">{{ $balance += $bb->debit - $bb->credit }}</td>
                                        </tr>
                                        @php($debit += $bb->debit)
                                        @php($credit += $bb->credit)
                                    @endif
                                @endforeach
                            @endif

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
                                    @if($chosen_club > 1)
                                        <td>{{$val->accjournalv2_club->club_name}}</td>
                                    @endif
                                </tr>
                                @php($debit += $val->debit)
                                @php($credit += $val->credit)
                            @endforeach

                            <tr>
                                <td colspan="2" class="text-right"><strong>@lang('labels.total')</strong></td>
                                <td class="my_rupiah">{{ $debit }}</td>
                                <td class="my_rupiah">{{ $credit }}</td>
                                <td class="my_rupiah">{{ $balance }}</td>
                            </tr>

                            <?php
                                $tb_item = new TB();
                                foreach ($value as $val):
                                    $tb_item->coa_id = $val->accjournalv2_coa->coa_number;
                                    $tb_item->coa_name = $val->accjournalv2_coa->coa_name;
                                    break;
                                endforeach;

                                $tb_item->coa_balance = $balance;
                                $tb[] = $tb_item;
                            ?>
                            </tbody>
                        </table>
                    </div>
                    <br>
                @endforeach
            @else
                <h4>@lang('labels.no_data')</h4>
            @endif

            @if(count($beginning_balances) > count($chosen_coas))
                @foreach($beginning_balances as $the_key => $bb)
                    @php($meb_gate = false)
                    @if(!empty($_GET['coas']))
                        @foreach($_GET['coas'] as $chosen_coa)
                            @if($chosen_coa == $bb->coa_id)
                                @php($meb_gate = true)
                            @endif
                        @endforeach
                    @else
                        @php($meb_gate = true)
                    @endif

                    @if(!empty($_GET['clubs']))
                        @foreach($_GET['clubs'] as $chosen_club)
                            @if($chosen_club == $bb->club_id)
                                @php($meb_gate = true)
                            @else
                                @php($meb_gate = false)
                            @endif
                        @endforeach
                    @else
                        @php($meb_gate = true)
                    @endif

                    @if($meb_gate)
                        @php($bb_gate = true)
                        @if(count($chosen_coas) > 0)
                            @for($index = 0; $index < count($chosen_coas); $index++)
                                @if($bb->coa_id == $chosen_coas[$index])
                                    @php($bb_gate = false)
                                @endif
                            @endfor
                        @endif

                        @if($bb_gate)
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                    <tr>
                                        <th colspan="{{($chosen_club > 1) ? '6' : '5'}}">
                                            {{$bb->meb_coa->coa_name}} {{ ($bb->club_id != null) ? '- '.$bb->meb_club->club_name : ' (PT)' }}
                                            <span style="float: right">{{$bb->meb_coa->coa_number}}</span>
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
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($from_date)->format('d-m-Y') }}</td>
                                        <td>@lang('labels.beginning_balance')</td>
                                        <td class="my_rupiah">{{ $bb->debit }}</td>
                                        <td class="my_rupiah">{{ $bb->credit }}</td>
                                        <td class="my_rupiah">{{ $bb->debit - $bb->credit }}</td>
                                    </tr>

                                    <tr>
                                        <td colspan="2" class="text-right"><strong>@lang('labels.total')</strong>
                                        </td>
                                        <td class="my_rupiah">{{ $bb->debit }}</td>
                                        <td class="my_rupiah">{{ $bb->credit }}</td>
                                        <td class="my_rupiah">{{ $bb->debit - $bb->credit }}</td>
                                    </tr>
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    @endif
                @endforeach
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
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $.fn.select2.amd.define('select2/selectAllAdapter', [
                'select2/utils',
                'select2/dropdown',
                'select2/dropdown/attachBody'
            ], function (Utils, Dropdown, AttachBody) {

                function SelectAll() {
                }

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
                console.log('masuk');
//                Club
                var club = '<option value="">@lang('labels.choose_club')</option>';
                var choose_jubilant = $(this).val();
                $('#clubs').val("");
                        @foreach($clubs as $club)
                var jubilant = '{{ $club->jubilant_id }}';
                if (jubilant == choose_jubilant) {
                    club += '<option value="{{ $club->id }}">{{ $club->club_name }}</option>';
                }
                @endforeach

                $('#clubs').html(club);

//                COA
                var coa = '';
                $('#coas').val("");
                        @foreach($coas as $coa)
                var jubilant = '{{ $coa->jubilant_id }}';
                if (jubilant == choose_jubilant) {
                    coa += '<option value="{{$coa->id}}">{{$coa->coa_number}}: {{$coa->coa_name}}</option>';
                }
                @endforeach

                $('#coas').html(coa);
            });

            $('#jubilant').select2();

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

            $('#month').datetimepicker({
                viewMode: 'months',
                format: 'MM-YYYY'
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
