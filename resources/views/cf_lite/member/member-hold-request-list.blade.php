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
        .home-btn-title {
            font-family: 'Alegreya Sans', sans-serif;
        }

        .value-base {
            display: block;
        }

        .percent-base {
            display: none;
        }

        .need-active h3 {
            font-size: 50px;
            font-weight: 600;
            color: #ffffff;
            margin: 0px;
        }

        .bg-warning{
            -webkit-animation: colorchange 2s cubic-bezier(1, 0, 0, 1) infinite;
            animation: colorchange 2s cubic-bezier(1, 0, 0, 1) infinite;
            border-radius: 3px;
        }

        /*.need-active h3:after {*/
        /*content: ' !';*/
        /*font-size: 40px;*/
        /*font-weight: 600;*/
        /*color: rgb(255, 0, 0);*/
        /*-webkit-animation: colorchange 2s cubic-bezier(1, 0, 0, 1) infinite;*/
        /*animation: colorchange 2s cubic-bezier(1, 0, 0, 1) infinite;*/
        /*}*/

        @-webkit-keyframes colorchange {
            0% {
                background-color: #b20000
            }
            33% {
                background-color: #ff0000
            }
            67% {
                background-color: #b20000
            }
            100% {
                colbackground-coloror: #ff0000
            }
        }

        @keyframes colorchange {
            0% {
                background-color: #b20000
            }
            33% {
                background-color: #ff0000
            }
            67% {
                background-color: #b20000
            }
            100% {
                background-color: #ff0000
            }
        }

        .need-active th {
            background-color: #a64047;
            color: #ffffff;
        }

        .request-list h3 {
            font-size: 40px;
            color: #663399;
            font-weight: 600;
        }

        .request-list th {
            background-color: #845bad;
            color: #ffffff;
        }

        div.dataTables_wrapper div.dataTables_length select {
            margin: 0 10px;
        }

        .pagination > .active > a, .pagination > .active > a:focus, .pagination > .active > a:hover {
            background-color: #ff1493;
            border-color: #ff1493;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination {
            margin: 20px 0;
        }

    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/member-hold')}}" style="left: 25px; top: 5px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.member_hold_request')</h2>
                </center>
            </div>
        </div>
        <hr>
        {!! Form::open(['method' => 'PATCH', 'action' => 'MemberHoldController@activate_member_hold']) !!}
        <div class="row">
            <div class="col-md-10 col-md-offset-1 need-active">
                <div class="col-md-12 text-center">
                    <div class="col-md-offset-3 col-md-6 bg-warning">
                        <h3>@lang('labels.needs_approval')</h3>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <br/>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="data-table">
                                    <thead>
                                    <tr>
                                        <th style="width:35px;"></th>
                                        <th style="width:300px;">@lang('labels.member')</th>
                                        <th style="width:165px;">@lang('labels.hold_date')</th>
                                        <th>@lang('labels.reason')</th>
                                        <th style="width:130px;">@lang('labels.category')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($to_activate_list as $to_activate)
                                        <tr>
                                            <td>{!! Form::checkbox('activate['.$to_activate->id.']', $to_activate->id) !!}</td>
                                            <td>{{$to_activate->memberhold_user->name}}</td>
                                            <td>{{date('d-m-Y', strtotime($to_activate->from_date))}} @lang('labels.until') {{date('d-m-Y', strtotime($to_activate->thru_date))}}</td>
                                            <td>
                                                @if(!empty($to_activate->member_hold_note))
                                                    {{$to_activate->member_hold_note}}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>{{$to_activate->memberhold_memberholdreason->member_hold_reason}}</td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    {!! Form::submit(trans('labels.approve_selected_request'), ['class' => 'btn btn-success']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}

        <hr>
        <div class="row">
            <div class="col-md-10 col-md-offset-1 request-list">
                <div class="col-md-12 text-center">
                    <h3>@lang('labels.request_list')</h3>
                </div>
                <div class="col-md-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <br/>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="data-table-2">
                                    <thead>
                                    <tr>
                                        <th style="width:35px;">@lang('labels.indexing')</th>
                                        <th style="width:300px;">@lang('labels.member')</th>
                                        <th style="width:165px;">@lang('labels.hold_date')</th>
                                        <th style="width:130px;">@lang('labels.category')</th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @php($index = 1)
                                    @foreach($request_list as $request)
                                        <tr>
                                            <td>{{$index}}</td>
                                            <td>{{$request->memberhold_user->name}}</td>
                                            <td>{{date('d-m-Y', strtotime($request->from_date))}} @lang('labels.until') {{date('d-m-Y', strtotime($request->thru_date))}}</td>
                                            <td>{{$request->memberhold_memberholdreason->member_hold_reason}}</td>
                                            <td>
                                                <a target="_blank"
                                                   href="{{url('cf-lite/member-hold/request/'.$request->id.'/edit')}}"
                                                   class="btn btn-default btn-sm">@lang('labels.edit')</a>
                                            </td>
                                            <td>
                                                {!! Form::open(['method' => 'PATCH', 'action' => ['MemberHoldController@cancel_member_hold_request', $request->id]]) !!}
                                                {!! Form::submit(trans('labels.cancel'), ['class' => 'btn btn-danger btn-sm']) !!}
                                                {!! Form::close() !!}
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="paging hidden rom">
                                        {{$request_list->links()}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>

    <script>
        $('#data-table').dataTable();
        $('#data-table-2').dataTable();
    </script>

    <script>
        $(document).ready(function () {
            init_format();
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
            $('input[name="expired"]').on('click', function () {
                if ($(this).prop('checked') == true) {
                    $('input[name="used"]').prop('checked', false);
                }
            });

            $('input[name="used"]').on('click', function () {
                if ($(this).prop('checked') == true) {
                    $('input[name="expired"]').prop('checked', false);
                }
            });

            $('#voucher-base-value').on('click', function () {
                if ($(this).prop('checked') == true) {
                    $('.value-base').show();
                    $('.percent-base').hide();
                }
            });

            $('#voucher-base-percent').on('click', function () {
                if ($(this).prop('checked') == true) {
                    $('.value-base').hide();
                    $('.percent-base').show();
                }
            });
        });
    </script>
@endsection
