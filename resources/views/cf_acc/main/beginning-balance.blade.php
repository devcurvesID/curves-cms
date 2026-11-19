@extends('cf_acc.layouts.acc-dashboard-template')

@section('other-style')
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
    {!! Form::open(['method' => 'POST', 'action' => ['AccountingManualTransactionController@store_beginning_balance'], 'id' => 'store-form']) !!}
    <div class="row">
        <div class="col-md-12">
            <a href="{{url('cf-acc/dashboard')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.beginning_balance')</h2>
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
                        <td>{!! Form::label('coa', trans('labels.coa'), ['class' => 'control-label']) !!}</td>
                        <td>:</td>
                        <td>
                            <select name="coa" class="form-control select2" id="coa">
                                <option value="">@lang('labels.choose') @lang('labels.coa')</option>
                                @foreach($coas as $coa)
                                    <option value="{{$coa->id}}">{{ $coa->coa_number }}: {{ $coa->coa_abv_name }}</option>
                                @endforeach
                            </select>
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
                        <td>{!! Form::label('via', trans('labels.via'), ['class' => 'control-label']) !!}</td>
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
            init_format();
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