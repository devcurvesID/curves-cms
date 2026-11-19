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

        .btn-primary,
        .voucher-list-add a,
        #edit-voucher-btn {
            background-color: #ff1493;
            color: #ffffff;
            border-color: #ff1493;
        }

        #edit-voucher-btn{
            margin: 20px 0;
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
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/travel-pass')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">
                        @lang('labels.visiting_member_inactive')
                    </h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                {!! Form::open(['method' => 'GET', 'action' => ['TravelPassController@visiting_member_inactive_filter']]) !!}
                <div class="form-group">
                    <label class="checkbox-inline">
                        {!! Form::radio('filter','member_name', true) !!} @lang('labels.member_name')
                    </label>
                    <label class="checkbox-inline">
                        {!! Form::radio('filter','member_id', false) !!} @lang('labels.member_id')
                    </label>
                    <label class="checkbox-inline">
                        {!! Form::radio('filter','origin_club', false) !!} @lang('labels.origin_club')
                    </label>
                </div>
                <div class="form-group">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="input-group">
                                {!! Form::text('search_text', null, ['class' => 'form-control', 'placeholder' => trans('labels.search_visiting_member')]) !!}
                                <span class="input-group-addon"><span class="fa fa-search"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        {!! Form::submit(trans('labels.apply_filter'), ['class' => 'btn btn-primary']) !!}
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
                                    <th>@lang('labels.member_id')</th>
                                    <th>@lang('labels.member_name')</th>
                                    <th>@lang('labels.origin_club')</th>
                                    <th>@lang('labels.origin_club_address')</th>
                                    <th>@lang('labels.origin_club_phone_number')</th>
                                    <th>@lang('labels.origin_club_fax_number')</th>
                                    <th>@lang('labels.origin_club_email')</th>
                                    <th>@lang('labels.first_use_date')</th>
                                    <th>@lang('labels.validity_date')</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($members as $member)
                                    <tr>
                                        <td>{{$member->member_id}}</td>
                                        <td>{{$member->member_name}}</td>
                                        <td>{{$member->origin_club}}</td>
                                        <td>{{$member->origin_club_address}}</td>
                                        <td>{{$member->origin_club_phone_number}}</td>
                                        <td>{{$member->origin_club_fax_number}}</td>
                                        <td>{{$member->origin_club_email}}</td>
                                        <td>{{Carbon\Carbon::parse($member->first_use_date)->format('d-m-Y')}}</td>
                                        <td>{{Carbon\Carbon::parse($member->validity_date)->format('d-m-Y')}}</td>
                                        <td>
                                            {!! Form::open(['method' => 'GET' , 'style'=>'display:inline-block' , 'action' => ['TravelPassController@past_wo_vm' , $member->id, ]])!!}{!! Form::submit(trans('labels.workout'),['class' => 'btn btn-primary']) !!} {!! Form::close() !!}
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="paging hidden rom">
                    {{$members->links()}}
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
    </script>

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
        });

        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp.' + (x));
            });
        }
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>
@endsection
