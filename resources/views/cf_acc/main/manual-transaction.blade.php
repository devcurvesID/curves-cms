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

        .select2 {
            width: 100% !important;
        }

        #cost-revenue-addon {
            background-color: transparent;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    {!! Form::open(['method' => 'POST', 'action' => ['AccountingManualTransactionController@store_manual_transaction'], 'id' => 'store-form']) !!}
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
                        <td>{!! Form::label('club', trans('labels.club'), ['class' => 'control-label']) !!}</td>
                        <td>:</td>
                        <td>
                            <select name="club" class="form-control select2" id="club-id">
                                <option value="">@lang('labels.choose') @lang('labels.club')</option>
                                @foreach($clubs as $club)
                                    <option value="{{$club->id}}">{{ $club->club_name }}</option>
                                @endforeach
                            </select>
                         </td>
                    </tr>
                    <tr>
                        <td>{!! Form::label('trans_date', trans('labels.date'), ['class' => 'control-label']) !!}</td>
                        <td>:</td>
                        <td>
                            {!! Form::date('trans_date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                        </td>
                    </tr>
                    <tr>
                        <td>{!! Form::label('cost_revenue', trans('labels.cost').' / '.trans('labels.revenue'), ['class' => 'control-label']) !!}</td>
                        <td>:</td>
                        <td>
                            <div class="input-group" style="min-width: 100%">
                                <span class="input-group-addon" id="cost-revenue-addon">
                                    <input name="radio" type="radio" id="cost_revenue" value="cost_revenue" checked>
                                </span>
                                <select name="select_cost_revenue" class="form-control select2" id="select_cost_revenue">
                                    <option value="">@lang('labels.choose') @lang('labels.cost') / @lang('labels.revenue')</option>
                                    @foreach($cost_revenues as $cr)
                                        <option value="{{$cr->id}}" trinfo="{{$cr->default_transaction_info}}">{{ $cr->account_info }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>{!! Form::label('assets_liability', trans('labels.assets').' / '.trans('labels.liability'), ['class' => 'control-label']) !!}</td>
                        <td>:</td>
                        <td>
                            <div class="input-group" style="min-width: 100%">
                                <span class="input-group-addon" id="assets-liability-addon">
                                    <input name="radio" type="radio" id="assets_liability" value="assets_liability">
                                </span>
                                <select name="select_assets_liability" class="form-control select2" id="select_assets_liability">
                                    <option value="">@lang('labels.choose') @lang('labels.assets') / @lang('labels.liability')</option>
                                    @foreach($coas as $coa)
                                        <option value="{{$coa->id}}">{{ $coa->coa_number }}: {{ $coa->coa_abv_name }}</option>
                                    @endforeach
                                </select>
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
                    <tr>
                        <td>{!! Form::label('debit', trans('labels.debet'), ['class' => 'control-label']) !!}</td>
                        <td>:</td>
                        <td colspan="2">
                            <div class="input-group" style="max-width: 250px;">
                                <span class="input-group-addon">Rp.</span>
                                {!! Form::text('debit', 0, ['class' => 'form-control rupiah', 'onkeypress' => 'return onlyNumber(event)']) !!}
                                <span class="input-group-addon">,-</span>
                            </div>
                        </td>
                        <td>{!! Form::label('credit', trans('labels.credit'), ['class' => 'control-label']) !!}</td>
                        <td>:</td>
                        <td colspan="2">
                            <div class="input-group" style="max-width: 250px;">
                                <span class="input-group-addon">Rp.</span>
                                {!! Form::text('credit', 0, ['class' => 'form-control rupiah', 'onkeypress' => 'return onlyNumber(event)']) !!}
                                <span class="input-group-addon">,-</span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>{!! Form::label('via', trans('labels.via_payment'), ['class' => 'control-label']) !!}</td>
                        <td>:</td>
                        <td>
                            <select name="via" class="form-control select2" id="via">
                                <option value="1">@lang('labels.kas')</option>
                            </select>
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
            $('#select_assets_liability').attr("disabled", "disabled");
            $('#data-table').dataTable();
            init_format();

            $('select[name="select_cost_revenue"]').on('change', function () {
                var tinfo = $(this).find(':selected').attr('trinfo');
                $('#transaction_info').val(tinfo);
            });

            $('#assets_liability').on('click', function () {
                $('#select_cost_revenue').attr("disabled", "disabled");
                $('#select_assets_liability').removeAttr("disabled");
                $('#assets-liability-addon').css('background-color', 'transparent');
                $('#cost-revenue-addon').css('background-color', '#eee');
            });
            $('#cost_revenue').on('click', function () {
                $('#select_assets_liability').attr("disabled", "disabled");
                $('#select_cost_revenue').removeAttr("disabled");
                $('#cost-revenue-addon').css('background-color', 'transparent');
                $('#assets-liability-addon').css('background-color', '#eee');
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#submit-btn-form').on('click', function () {
                $('input[name="debit"]').val(rupiah_format_destroyer($('input[name="debit"]').val()));
                $('input[name="credit"]').val(rupiah_format_destroyer($('input[name="credit"]').val()));

                if ($('#club-id').val() != "") {
                    if ($('input[name="debit"]').val() != 0 && $('input[name="credit"]').val() == 0 || $('input[name="debit"]').val() == 0 && $('input[name="credit"]').val() != 0){
                        $('#store-form').submit();
                    } else {
                        alert('@lang('messages.debit_or_credit_should_be_zero')');
                    }
                } else {
                    alert('@lang('messages.choose_club')');
                }
            });

            var url = "{{url('cf-acc')}}/"


            $('#club-id').on('change', function () {
                var club = $(this).find(':selected').val();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    club_id: club
                };
                
                var type = 'POST';
                var my_url = url + 'via-transaction-ajax';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    success: function (data) {
                        json = data;
                        var via = '<option value=1>@lang('labels.kas')</option>';
                        $.each(data.bank, function (i, item) {
                            via += '<option value="' + item.id + '">'
                                + item.bank_abv + ' - ' + item.bank_account_number
                                + '</option>';
                        });

                        $('#via').html(via);
                        $('#via').val();
                    },
                    error: function (data) {
                        console.log('Error : ', data);
                    }
                });
            });
        })

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