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
        .home-btn-title{
            font-family: 'Alegreya Sans', sans-serif;
        }

        .value-base {
            display: block;
        }

        .percent-base {
            display: none;
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
                    <h2 class="page-title">@lang('labels.member_hold')</h2>
                </center>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <h3>@lang('labels.member_hold_list')</h3>
            </div>
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="data-table-2">
                                <thead>
                                <tr>
                                    <th>@lang('labels.indexing')</th>
                                    <th>@lang('labels.member')</th>
                                    <th>@lang('labels.hold_date')</th>
                                    <th>@lang('labels.category')</th>
                                    <th>@lang('labels.reason')</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @php($index = 1)
                                @foreach($hold_list as $hold)
                                    <tr>
                                        <td>{{$index}}</td>
                                        <td>{{$hold->memberhold_user->name}}</td>
                                        <td>{{date('d-m-Y', strtotime($hold->from_date))}} @lang('labels.until') {{date('d-m-Y', strtotime($hold->thru_date))}}</td>
                                        <td>{{$hold->memberhold_memberholdreason->member_hold_reason}}</td>
                                        <td>
                                            @if(!empty($hold->member_hold_note))
                                                {{$hold->member_hold_note}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            {!! Form::open(['method' => 'PATCH', 'action' => ['MemberHoldController@finish_member_on_hold', $hold->id]]) !!}
                                            {!! Form::submit(trans('labels.hold_finished'), ['class' => 'btn btn-default btn-sm']) !!}
                                            {!! Form::close() !!}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="paging hiden rom">
                                    {{$hold_list->links()}}
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
