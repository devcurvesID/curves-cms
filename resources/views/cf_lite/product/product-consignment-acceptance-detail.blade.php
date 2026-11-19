@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('css/payment.css')}}" rel="stylesheet">
    <style>
        body{
            color: black;
        }
        .column-no-border, .column-no-border td {
            border: none !important;
        }
        .modal-dialog, .modal-dialog label{
            color: #414042;
        }
        table{
            color: #414042;
        }
    </style>

    <style>
        #logo-print, #ttd-print {
            display: none;
        }

        #logo-print img {
            width: 100px;
            height: auto;
            margin-bottom: 30px;
        }

        #ttd-print h4 {
            margin-top: 20px;
            font-size: 12px;
            color: #414042;
        }

        .select2{
            width: 100% !important;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/product-consignment-acceptance')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.product_consignment_acceptance_detail')</h2>
            </center>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 col-xs-6">
            <table class="member-detail">
                <tr>
                    <td id="member-detail-1">@lang('labels.from')</td>
                    <td>:</td>
                    <td>{{ $co->co_hq->club_name }}</td>
                </tr>
                <tr>
                    <td id="member-detail-2">@lang('labels.send_to')</td>
                    <td>:</td>
                    <td>{{ $co->co_club->club_name }}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-6 col-xs-6">
            <table class="member-detail">
                <tr>
                    <td>@lang('labels.date')</td>
                    <td>:</td>
                    <td>{{ \Carbon\Carbon::parse($co->order_date)->format('d-m-Y') }}</td>
                </tr>
                <tr>
                    <td id="member-detail-3">@lang('labels.order_number')</td>
                    <td>:</td>
                    <td>{{ $co->order_number }}</td>
                </tr>
            </table>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::open(['method' => 'POST', 'action' => ['ProductController@store_product_consignment_acceptance',$co->id]]) !!}
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>@lang('labels.indexing')</th>
                        <th>@lang('labels.product')</th>
                        <th>@lang('labels.amount_send')</th>
                        <th>@lang('labels.amount_receive')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($indexing = 0)
                    @foreach($co->co_codetail as $codetail)
                        @php($indexing++)
                        <tr>
                            {!! Form::hidden('id['.$codetail->id.']', $codetail->id) !!}
                            <td>{{ $indexing }}</td>
                            <td>{{ $codetail->codetail_product->item_name }}</td>
                            <td>{{ $codetail->qty }}</td>
                            <td>
                                {{ Form::text('amount_receive['.$codetail->id.']', $codetail->qty, ['class' => 'form-control', 'style' => 'width: 100px;', 'onkeypress' => 'return numberOnlyInput(event)']) }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="pull-right">
                {!! Form::submit(trans('labels.save'), ['class' => 'form-control btn btn-success']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('other-script')
    <script type="text/javascript">
        function numberOnlyInput(event){
            var charCode = (event.which) ? event.which : event.keyCode;
            if(charCode > 31 && (charCode < 48 || charCode > 57)){
                return false;
            }
            return true;
        }
    </script>
@endsection