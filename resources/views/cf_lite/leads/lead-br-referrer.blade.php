@extends('cf_lite.layouts.staff-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('css/appointment.css')}}" rel="stylesheet">
    <link href="{{asset('fullcalendar/fullcalendar.min.css')}}" rel="stylesheet">
    <link href="{{asset('fullcalendar/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">

    <link href="{{asset('css/appointment.css')}}" rel="stylesheet">
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">

    <style>
        body {
            padding-bottom: 50px;
            font-family: 'Alegreya Sans', sans-serif;
            font-size: 14px;
        }

        #data-table th {
            color: #ff1493;
        }

        div.dataTables_length select {
            margin: 0 10px
        }

        .pagination > .active > a, .pagination > .active > a:focus, .pagination > .active > a:hover {
            background-color: #ff1493;
            border-color: #ff1493;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination {
            margin: 20px 0;
        }

        .btn-primary{
            background-color: #ff1493;
            color: #ffffff;
            border-color: #ff1493;
        }

        .btn-primary:hover {
            color: #ff1493;
            background-color: transparent;
            border-color: #ff1493;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/lead')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">
                    @lang('labels.br_referrer')
                    <br>
                </h2>
            </center>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive" style="background-color: #fff;">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                            <tr>
                                <th>@lang('labels.indexing')</th>
                                <th>@lang('labels.lead_name')</th>
                                <th>@lang('labels.lead_status')</th>
                                <th>@lang('labels.referrer')</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($buddyreferrals) > 0)
                                @php
                                    $index = 1;
                                @endphp
                                @foreach($buddyreferrals as $br)
                                    {!! Form::open(['method' => 'PATCH' , 'style'=>'display:inline-block' , 'action' => ['LeadController@store_lead_referrer' , $br->id, ]])!!}
                                    <tr>
                                        <td>{{$index}}</td>
                                        <td>{{$br->fullname}}</td>
                                        <td>
                                            @if($br->br_status == 'Joined')
                                                @lang('labels.sale')
                                            @elseif($br->br_status == 'Pending')
                                                @lang('labels.have_not_fa_yet')
                                            @elseif($br->br_status == 'Failed')
                                                @lang('labels.no_sale')
                                            @endif
                                            {!! Form::hidden('referrer_status',$br->br_status) !!}
                                        </td>
                                        @if($br->referrer_id != -1)
                                            <td>
                                                {!! Form::select('referrer',$users, $br->referrer_id, ['class'=>'form-control','style'=>'width:200px;']) !!}
                                            </td>
                                            <td>
                                                {!! Form::submit(@trans('labels.update'),['class' => 'btn btn-primary']) !!} {!! Form::close() !!}
                                            </td>
                                        @else
                                            <td>
                                                {!! Form::select('referrer',$users, null, ['class'=>'form-control','style'=>'width:200px;','placeholder'=>'']) !!}
                                            </td>
                                            <td>
                                                {!! Form::submit(@trans('labels.submit'),['class' => 'btn btn-primary']) !!} {!! Form::close() !!}
                                            </td>
                                        @endif
                                    </tr>
                                    {!! Form::close() !!}
                                    @php($index++)
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/moment.min.js')}}"></script>

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
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script type="text/javascript">
        $('select[name="referrer"]').select2();
    </script>

@endsection