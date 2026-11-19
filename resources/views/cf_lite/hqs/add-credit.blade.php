@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/payment.css')}}" rel="stylesheet">
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <style>
        body{
            color: black;
        }
        .modal-dialog, .modal-dialog label{
            color: #414042;
        }
        table{
            color: #414042;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/hq/credit-index')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.create_credit')</h2>
            </center>
        </div>
    </div>

    @include('cf_lite.includes.error-message')
    @include('cf_lite.includes.flash-message')

    {!! Form::open(['method' => 'POST', 'action' => 'HQController@store_credit', 'id' => 'form-credit']) !!}
    <div class="row">
        <div class="col-md-6">
            <table class="member-detail">
                <tr>
                    <td style="color: deeppink; margin-left: 10px; font-weight: bold;">@lang('labels.staff')</td>
                    <td>:</td>
                    <td>
                        {!! Form::select('staff',$staffs,Auth::user()->id,['class'=>'form-control', 'id'=>'staff']) !!}
                    </td>
                </tr>
                <tr>
                    <td style="color: deeppink; margin-left: 10px; font-weight: bold;">@lang('labels.date')</td>
                    <td>:</td>
                    <td>{!! Form::date('invoice_date',Carbon\Carbon::now(),['class'=>'form-control']) !!}</td>
                </tr>
            </table>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-6">
            <table class="member-detail">
                <tr>
                    <td>@lang('labels.invoice_number')</td>
                    <td>:</td>
                    <td>
                        @php($month_roman = array(1=>'I', 'II', 'III', 'IV', 'V', 'VI', 'VII', 'VIII', 'IX', 'X', 'XI', 'XII'))
                        {{sprintf('%08d', $temp_auto_number->current_number)}}/PIU/CIND/{{sprintf('%03d', \Illuminate\Support\Facades\Auth::user()->user_club->jubilant_id)}}/{{$month_roman[\Carbon\Carbon::now()->format('n')]}}/{{\Carbon\Carbon::now()->format('Y')}}
                        {!! Form::hidden('invoice_number', '/PIU/CIND/'.sprintf('%03d', \Illuminate\Support\Facades\Auth::user()->user_club->jubilant_id).'/'.$month_roman[\Carbon\Carbon::now()->format('n')].'/'.\Carbon\Carbon::now()->format('Y')) !!}
                    </td>
                </tr>
                <tr>
                    <td>@lang('labels.jubilant')</td>
                    <td>:</td>
                    <td>
                        {!! Form::select('jubilant',$jubilants,null,['class'=>'form-control', 'id'=>'jubilant', 'placeholder' => '-- '.trans('labels.choose').' '.trans('labels.jubilant').' --']) !!}
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-md-6">

        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>@lang('labels.indexing')</th>
                            <th>@lang('labels.credit_type')</th>
                            <th>@lang('labels.information')</th>
                            <th>@lang('labels.subtotal')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php($index = 1)
                        <tr>
                            <td>{{$index}}</td>
                            <td>
                                {!! Form::select('coa', $coas, ['class' => 'form-control select2']) !!}
                            </td>
                            <td>
                                {!! Form::textArea('information', null, ['class' => 'form-control', 'rows' => 3]) !!}
                            </td>
                            <td>
                                {!! Form::text('subtotal', 0, ['class' => 'form-control rupiah','style'=>'max-width: 250px;']) !!}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button type="button" class="btn btn-success btn-lg pull-right" id="invoice-process" style="width: 200px; height: 100px;">@lang('labels.create_invoice')</button>
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@section('other-script')
    <script>
        $(document).ready(function () {
            // $('.select2').select2();

            $('.rupiah').each(function () {
                $(this).wrap('<div class="input-group" style="max-width: 250px;"></div>')
                $(this).before('<span class="input-group-addon">Rp.</span>');
                $(this).after('<span class="input-group-addon">,-</span>');
            });

            init_format();

            $('input[name="subtotal"]').on('keyup', function () {
                if ($(this).val() == "") {
                    var total_pay = 0;
                } else {
                    var total_pay = parseInt(rupiah_format_destroyer($('input[name="subtotal"]').val()));
                }
                $('input[name="subtotal"]').val(rupiah_formater(total_pay));
            });
        });


        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp.' + (x));
            });
            $('.rupiah').each(function () {
                var x = rupiah_formater($(this).val().replace(/\s/g, ''));
                $(this).val((x));
            });
        }
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>

    <script src={{asset("bootbox/bootbox.min.js")}}></script>
    <script>
        $(document).ready(function () {
            $(document).on('click', '#invoice-process', function () {
                if (rupiah_format_destroyer($('input[name="subtotal"]').val()) == 0) {
                    bootbox.confirm({
                        message: "@lang('messages.are_you_sure')",
                        buttons: {
                            confirm: {
                                label: '@lang('labels.yes')',
                                className: 'btn-success'
                            },
                            cancel: {
                                label: '@lang('labels.no')',
                                className: 'btn-danger'
                            }
                        },
                        callback: function (result) {
                            if (result == false) {
                            } else {

                            }
                        }
                    });
                } else {
                    $('input[name="subtotal"]').val(rupiah_format_destroyer($('input[name="subtotal"]').val()));
                    $('#form-credit').submit();
                }
            });

        });
    </script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script type="text/javascript">
        $('select[name="jubilant"]').select2();
        $('select[name="staff"]').select2();
        $('select[name="coa"]').select2();
    </script>

@endsection