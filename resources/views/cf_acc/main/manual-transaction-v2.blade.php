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
        .detail td{
            padding: 10px;
        }

        table{
            color: #414042;
        }

        #cost-revenue-addon {
            background-color: transparent;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    {!! Form::open(['method' => 'POST', 'action' => ['AccountingManualTransactionController@store_manual_transactionV2'], 'id' => 'store-form']) !!}
    <div class="row">
        <div class="col-md-12">
            <a href="{{url('cf-acc/dashboard')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.cost') / @lang('labels.revenue')</h2>
            </center>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="detail">
                    <tr>
                        <td>{!! Form::label('trans_date', trans('labels.date'), ['class' => 'control-label']) !!}</td>
                        <td>:</td>
                        <td>
                            {!! Form::date('trans_date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                        </td>
                    </tr>
                    <tr>
                        <td>{!! Form::label('jubilant', trans('labels.jubilant'), ['class' => 'control-label']) !!}</td>
                        <td>:</td>
                        <td>
                            {!! Form::select('jubilant', $jubilants, null, ['class' => 'form-control select2', 'id' => 'jubilant', 'placeholder'=> trans('labels.choose_jubilant')]) !!}
                        </td>
                    </tr>
                    <tr>
                        <td>{!! Form::label('club', trans('labels.club'), ['class' => 'control-label']) !!}</td>
                        <td>:</td>
                        <td>
                            <select name="club" class="form-control select2" id="clubs">
                                <option value="">@lang('labels.choose_club')</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>{!! Form::label('coa', trans('labels.chart_of_account') . ' (' . strtoupper(trans('labels.debit')) . ')', ['class' => 'control-label']) !!}</td>
                        <td>:</td>
                        <td>
                            <select name="coa_debit" class="form-control select2" id="coas_debit">
                                <option value="">@lang('labels.choose') @lang('labels.chart_of_account')</option>
                            </select>
                        </td>
                        <td>
                            <div class="input-group" style="max-width: 250px;">
                                <span class="input-group-addon">Rp.</span>
                                {!! Form::text('debit', 0, ['class' => 'form-control rupiah', 'onkeypress' => 'return onlyNumber(event)']) !!}
                                <span class="input-group-addon">,-</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>{!! Form::label('coa', trans('labels.chart_of_account') . ' (' . strtoupper(trans('labels.credit')) . ')', ['class' => 'control-label']) !!}</td>
                        <td>:</td>
                        <td>
                            <select name="coa_credit" class="form-control select2" id="coas_credit">
                                <option value="">@lang('labels.choose') @lang('labels.chart_of_account')</option>
                            </select>
                        </td>
                        <td>
                            <div class="input-group" style="max-width: 250px;">
                                <span class="input-group-addon">Rp.</span>
                                {!! Form::text('credit', 0, ['class' => 'form-control rupiah', 'onkeypress' => 'return onlyNumber(event)']) !!}
                                <span class="input-group-addon">,-</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            {!! Form::label('transaction_info', trans('labels.information'), ['class' => 'control-label']) !!}
                            <p class="help-block" style="color: red">*@lang('labels.required')</p>
                        </td>
                        <td>:</td>
                        <td>
                            <textarea name="transaction_info" class="form-control" id="transaction_info" style="width: 300px; height: 130px; resize:none;"></textarea>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <hr>
    <div class="form-group">
        <button type="button" name="submit-btn-form" class="btn btn-primary pull-right" id="submit-btn-form">@lang('labels.save')</button>
    </div>
    {!! Form::close() !!}
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2();
            $('#data-table').dataTable();
            init_format();

            $('#jubilant').on('change', function () {
                var html = '<option value="">@lang('labels.choose_club')</option>';
                var coa = '<option value="">@lang('labels.choose') @lang('labels.chart_of_account')</option>'
                var choose_jubilant = $(this).val();

                @foreach($clubs as $club)
                    var jubilant = '{{ $club->jubilant_id }}';
                    if(jubilant == choose_jubilant) {
                        html += '<option value="{{ $club->id }}">{{ $club->club_name }}</option>';
                    }
                @endforeach

                @foreach($coas as $coa)
                    var jubilant_coa = '{{ $coa->jubilant_id }}';
                    if (jubilant_coa == choose_jubilant) {
                        coa += '<option value="{{ $coa->id }}">{{ $coa->coa_abv_name }}</option>';
                    }
                @endforeach
                $('#clubs').html(html);
                $('#coas_debit').html(coa);
                $('#coas_credit').html(coa);
            });

            $('#submit-btn-form').on('click', function () {
                $('input[name="debit"]').val(rupiah_format_destroyer($('input[name="debit"]').val()));
                $('input[name="credit"]').val(rupiah_format_destroyer($('input[name="credit"]').val()));

                if ($('#club-id').val() != "") {
                    if ($('#transaction_info').val() != "") {
                        $('#store-form').submit();
                    } else {
                        alert('@lang('messages.information_cannot_empty')');
                    }
                } else {
                    alert('@lang('messages.choose_club')');
                }
            });
        });
    </script>
    <script>
        $('input[name="debit"]').on('keyup', function () {
            if ($(this).val() == "") {
                var pay = 0;
            } else {
                var pay = parseInt(rupiah_format_destroyer($(this).val()));
            }
            $('input[name="debit"]').val(rupiah_formater(pay));
        });

        $('input[name="credit"]').on('keyup', function () {
            if ($(this).val() == "") {
                var pay = 0;
            } else {
                var pay = parseInt(rupiah_format_destroyer($(this).val()));
            }
            $('input[name="credit"]').val(rupiah_formater(pay));
        });

        // Only Number
        function onlyNumber(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode;
            if (charCode == 8 || //space
                charCode == 9 || //tab
                charCode == 46 || //point
                (charCode > 47 && charCode < 58) // number
            ) {
                return true;
            }
            return false;
        }

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