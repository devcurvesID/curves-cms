@extends('cf_acc.layouts.acc-dashboard-template')

@section('other-style')
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">

    <style>
        .detail td{
            padding: 10px;
        }

        table{
            color: #414042;
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
            <a class="btn btn-default btn-back-to" style="margin-left: calc(100% - 80px);" title="@lang('labels.import')" data-toggle="modal" data-target="#import"><span class="glyphicon glyphicon-import" style="margin-top: 10px; color: deeppink;"></span></a>
        </div>
        <div class="row">
            <div class="col-md-12">
                <table class="detail">
                    <tr>
                        <td>{!! Form::label('jubilant', trans('labels.jubilant'), ['class' => 'control-label']) !!}</td>
                        <td>:</td>
                        <td>
                            <select name="jubilant" class="form-control select2" id="jubilant">
                                <option value="">@lang('labels.choose') @lang('labels.jubilant')</option>
                                @foreach($jubilants as $jubilant)
                                    <option value="{{$jubilant->id}}">{{ $jubilant->jubilant_name }}</option>
                                @endforeach
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>{!! Form::label('club', trans('labels.club'), ['class' => 'control-label']) !!}</td>
                        <td>:</td>
                        <td>
                            <select name="club" class="form-control select2" id="club">
                                <option value="">@lang('labels.choose_club')</option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <td>{!! Form::label('date', trans('labels.month').'/'.trans('labels.year'), ['class' => 'control-label']) !!}</td>
                        <td>:</td>
                        <td>
                            <div class="input-group" id="date">
                                {!! Form::text('date', null, ['class' => 'form-control']) !!}
                                <span class="input-group-addon">
                                    <span class="fa fa-calendar"></span>
                                </span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>{!! Form::label('coa', trans('labels.coa'), ['class' => 'control-label']) !!}</td>
                        <td>:</td>
                        <td>
                            <select name="coa" class="form-control select2" id="coa">
                                <option value="">@lang('labels.choose') @lang('labels.coa')</option>
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
                </table>
            </div>
        </div>
    </div>
    <hr>
    <div class="form-group">
        <button type="button" name="submit-btn-form" class="btn btn-primary pull-right" id="submit-btn-form">@lang('labels.save')</button>
    </div>
    {!! Form::close() !!}

    {{--  MODAL  --}}
    <div class="modal fade" id="import" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'POST', 'action' => 'AccountingManualTransactionController@import_beginning_balance', 'files' => true]) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.import_beginning_balance')</h4>
                </div>
                <div class="modal-body">
                    {!! Form::file('data', null) !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" name="submit" id="add-bank" class="btn btn-success">@lang('labels.import')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>>
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2();
            init_format();

            $('#jubilant').on('change', function () {
                var html = '<option value="">@lang('labels.choose_club')</option>';
                var coa = '<option value="">@lang('labels.choose') @lang('labels.coa')</option>';
                var choose_jubilant = $(this).val();

                @foreach($clubs as $club)
                    var jubilant = '{{ $club->jubilant_id }}';
                    if(jubilant == choose_jubilant) {
                        html += '<option value="{{ $club->id }}">{{ $club->club_name }}</option>';
                    }
                @endforeach

                @foreach($coas as $coa)
                    var jubilant = '{{ $coa->jubilant_id }}';
                    if (jubilant == choose_jubilant) {
                        coa += '<option value="{{ $coa->id }}">{{ $coa->coa_number }} - {{ $coa->coa_name }}</option>';
                    }
                @endforeach

                $('#club').html(html);
                $('#coa').html(coa);
            });

            $('#date').datetimepicker({
                viewMode: 'months',
                format: 'MM-YYYY'
            });
        });
    </script>

    <script>
        $(document).ready(function () {
            $('#submit-btn-form').on('click', function () {
                $('input[name="debit"]').val(rupiah_format_destroyer($('input[name="debit"]').val()));
                $('input[name="credit"]').val(rupiah_format_destroyer($('input[name="credit"]').val()));

                if ($('#club').val() != "") {
                    if ($('#coa').val() != "") {
                        $('#store-form').submit();
                    } else {
                        alert('@lang('messages.choose_coa')');
                    }
                } else {
                    alert('@lang('messages.choose_club')');
                }
            });
        });

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