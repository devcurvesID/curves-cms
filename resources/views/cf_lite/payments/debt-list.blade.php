@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <style>
        .btn-primary,
        .voucher-list-add a,
        #edit-voucher-btn {
            background-color: #ff1493;
            color: #ffffff;
            border-color: #ff1493;
        }

        .btn-primary:hover,
        .voucher-list-add a:hover,
        #edit-voucher-btn:hover {
            color: #ff1493;
            background-color: transparent;
            border-color: #ff1493;
        }

        div.dataTables_wrapper div.dataTables_length select {
            margin: 0 10px;
        }

        table th{
            color: #ff1493;
        }

    </style>
@endsection

@section('content')
    <section id="staff-home">
        @include('cf_lite.includes.error-message')
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/dashboard')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">
                        @lang('labels.debt')
                    </h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                {!! Form::open(['method' => 'GET', 'action' => ['PaymentController@debt_list_filter']]) !!}
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="input-group">
                                {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => trans('labels.search_member')]) !!}
                                <span class="input-group-addon"><span class="fa fa-search"></span></span>
                            </div>
                        </div>
                    </div>
                    <br/>
                    <div class="row">
                        <div class="col-md-2">
                            {!! Form::date('from_date', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-2">
                            {!! Form::date('thru_date', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-1">
                            {!! Form::submit(trans('labels.apply_filter'), ['class' => 'btn btn-primary']) !!}
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="data-table">
                                <thead>
                                <tr>
                                    <th>@lang('labels.number')</th>
                                    <th>@lang('labels.name')</th>
                                    <th>@lang('labels.due_date')</th>
                                    <th>@lang('labels.amount')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php($indexing = 1)
                                @foreach($paymentDebts as $paymentDebt)
                                    @if($paymentDebt->isDebt == 1)
                                    <tr>
                                        <td>{{$indexing}}</td>
                                        <td>
                                            <a href="{{url('cf-lite/payment/bill-list',$paymentDebt->memberpayment_user->id)}}">
                                                {{$paymentDebt->memberpayment_user->name}}
                                            </a>
                                        </td>
                                        <td>
                                            @if($paymentDebt->payment_status == 'Unpaid' && $paymentDebt->payment_category == 'Monthly')
                                            <a href="{{url('cf-lite/payment/invoice/'.$paymentDebt->id.'/detail')}}">
                                            @endif
                                                {{\Carbon\Carbon::parse($paymentDebt->payment_date)->format('d-m-Y')}}

                                            @if($paymentDebt->payment_status == 'Unpaid' && $paymentDebt->payment_category == 'Monthly')
                                            </a>
                                            @endif
                                        </td>
                                        <td class="my_rupiah">{{$paymentDebt->rest_of_bill}}</td>
                                    </tr>
                                    @endif
                                @php($indexing++)
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{--<div class="row">--}}
            {{--<div class="col-md-12">--}}
                {{--<div class="paging hiden rom">--}}
                    {{--{{$paymentDebts->links()}}--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    </section>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>

    <script type="text/javascript">
        function numberOnlyInput(event){
            var charCode = (event.which) ? event.which : event.keyCode;
            if(charCode > 31 && (charCode < 48 || charCode > 57)){
                return false;
            }
            return true;
        }
    </script>

    <script>
        $(document).ready(function () {
            init_format();

            $('#data-table').dataTable();
            paymentDebts.bindResetListeners();
        });

        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp.' + (x));
            });
        }
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>

    <script>
        $(document).ready(function () {
            $('input[name="search_option"]').on('click', function () {
                $('input[name="s"]').val('');
            })
        });
    </script>
@endsection
