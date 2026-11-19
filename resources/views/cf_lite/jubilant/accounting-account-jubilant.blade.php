@extends('cf_lite.layouts.staff-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <style>

        .page-title {
            margin-top: 0px;
        }

        .btn-filter-zone{
            padding: 9px 12px;
        }

        select[name="data-table_length"]{
            margin: 0px 10px;
        }

        .btn-member-status {
            position: absolute;
            top: 0;
            right: 0;
            border-radius: 50px;
            width: 50px;
            height: 50px;
            border: 3px solid rebeccapurple;
        }

        .btn-member-status:hover {
            background-color: rebeccapurple;
            border: 3px solid rebeccapurple;
        }

        .btn-member-status:hover > span {
            color: white !important;
        }

    </style>

@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/setting/accounting-account')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.setting_accounting_account_jubilant')</h2>
            </center>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <img src="{{asset('images/icons/female_avatar.png')}}" class="img-circle" style="width: 150px; height: 150px; object-fit: cover;">
        </div>
        <div class="col-md-6">
            <h3>{{$user->name}}</h3>
        </div>
    </div>

    <div class="row">
        {!! Form::open(['method' => 'POST', 'action' => ['JubilantController@store_accounting_account_setting', $user->id]]) !!}
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                            <tr>
                                <th></th>
                                <th>@lang('labels.jubilant')</th>
                                <th>@lang('labels.club')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($jubilants as $jubilant)
                                <tr>
                                    <td>
                                        @if(count($user->accounting_jubilant) > 0)
                                            @php($checked_jubilant = 0)
                                            @foreach($user->accounting_jubilant as $list_jubilant)
                                                @if($list_jubilant->id == $jubilant->id)
                                                    @php($checked_jubilant = 1)
                                                @endif
                                            @endforeach

                                            @if($checked_jubilant == 1)
                                                {!! Form::checkbox('jubilant['.$jubilant->id.']', $jubilant->id, true) !!}
                                            @else
                                                {!! Form::checkbox('jubilant['.$jubilant->id.']', $jubilant->id, false) !!}
                                            @endif
                                        @else
                                            {!! Form::checkbox('jubilant['.$jubilant->id.']', $jubilant->id, false) !!}
                                        @endif
                                    </td>
                                    <td>{{$jubilant->jubilant_name}}</td>
                                    <td>
                                        @foreach($jubilant->jubilant_club as $club)
                                            {{ $club->club_name }} <br>
                                        @endforeach
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-12">
            {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary pull-right', 'style' => 'margin-top: 20px;']) !!}
        </div>
        {!! Form::close() !!}
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $('#data-table').dataTable({
            paginate: false,
        });
    </script>
@endsection