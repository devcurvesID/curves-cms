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
                    <h2 class="page-title">@lang('labels.finished_member_hold')</h2>
                </center>
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-12">
                <h3>@lang('labels.member_hold_list')</h3>
            </div>
            {!! Form::open(['method' => 'PATCH', 'action' => 'MemberHoldController@member_hold_finished']) !!}
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="data-table-2">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>@lang('labels.member')</th>
                                    <th>@lang('labels.hold_date')</th>
                                    <th>@lang('labels.category')</th>
                                    <th>@lang('labels.reason')</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($hold_finished_list as $finished)
                                    <tr>
                                        <td>{!! Form::checkbox('finished['.$finished->id.']', $finished->id) !!}</td>
                                        <td>{{$finished->memberhold_user->name}}</td>
                                        <td>{{date('d-m-Y', strtotime($finished->from_date))}} @lang('labels.until') {{date('d-m-Y', strtotime($finished->thru_date))}}</td>
                                        <td>{{$finished->memberhold_memberholdreason->member_hold_reason}}</td>
                                        <td>
                                            @if(!empty($finished->member_hold_note))
                                                {{$finished->member_hold_note}}
                                            @else
                                                -
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{url('cf-lite/member-hold/request/'.$finished->id.'/edit')}}" class="btn btn-default btn-sm">@lang('labels.rearrange')</a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                {!! Form::submit(trans('labels.finished_selected'), ['class' => 'btn btn-success']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="paging hidden rom">
                    {{$hold_finished_list->links()}}
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
