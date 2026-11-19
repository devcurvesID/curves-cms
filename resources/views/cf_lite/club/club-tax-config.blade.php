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

        .to-show {
            display: block;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <center>
                    <h2 class="page-title">@lang('labels.tax_config')<br><small>({{$club->club_name}})</small></h2>
                </center>
            </div>
        </div>
        <hr>
        {!! Form::open(['method' => 'PATCH', 'action' => ['ClubController@update_club_tax_config', $club->id]]) !!}
        <div class="row">
            <div class="col-md-12 col-sm-12 col-xs-12">
                <a href="javascript:;" id="btn-add-tax" class="btn btn-primary pull-right" data-toggle="modal" data-target="#addTaxModal"><i class="fa fa-plus"></i></a>
            </div>
            <div class="col-md-12">
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="data-table">
                                <thead>
                                <tr>
                                    <th></th>
                                    <th>@lang('labels.tax_type')</th>
                                    <th>@lang('labels.rate') (%)</th>
                                    <th>@lang('labels.active_from_date')</th>
                                    <th>@lang('labels.set_inactive')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($tax_configs as $tax_config)
                                    <tr>
                                        <td>{!! Form::checkbox('tax_config['.$tax_config->id.']', $tax_config->id, false, ['class' => 'tax-config', 'id' => 'tax-config-'.$tax_config->id]) !!}</td>
                                        <td>{{$tax_config->taxconfig_taxtype->tax_type}}</td>
                                        <td>
                                            <div class="to-show show-{{$tax_config->id}}">{{$tax_config->rate}}</div>
                                            {!! Form::text('rate['.$tax_config->id.']', $tax_config->rate, ['class' => 'form-control to-hide rate-'.$tax_config->id, 'style' => 'display: none;']) !!}
                                        </td>
                                        <td>
                                            <div class="to-show show-{{$tax_config->id}}">{{date('d-m-Y', strtotime($tax_config->from_date))}}</div>
                                            {!! Form::date('from_date['.$tax_config->id.']', \Carbon\Carbon::parse($tax_config->from_date), ['class' => 'form-control to-hide from_date-'.$tax_config->id, 'style' => 'display: none;']) !!}
                                        </td>
                                        <td>{!! Form::checkbox('inactive['.$tax_config->id.']', $tax_config->id, false, ['id' => 'inactive-'.$tax_config->id, 'disabled']) !!}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="row">
                            <div class="col-md-3">
                                {!! Form::submit(trans('labels.update_selected'), ['class' => 'btn btn-success']) !!}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </section>

    <!-- Modal -->
    <div class="modal fade" id="addTaxModal" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">@lang('labels.add') @lang('labels.tax_config')</h4>
                </div>
                <div class="modal-body">
                    <div>
                        {!! Form::open(['method' => 'POST', 'action' => ['ClubController@store_club_tax_config', $club->id]]) !!}
                        <div class="form-horizontal">
                            <div class="form-group">
                                {!! Form::label('tax_type', trans('labels.tax_type').': ', ['class' => 'control-label col-md-3']) !!}
                                <div class="col-md-6">
                                    {!! Form::select('tax_type', $tax_types, null, ['class' => 'form-control', 'placeholder' => ' --'.trans('labels.choose').' '.trans('labels.tax_type').'-- ']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('rate', trans('labels.rate').' (%): ', ['class' => 'control-label col-md-3']) !!}
                                <div class="col-md-6">
                                    {!! Form::text('rate', '0', ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('from_date', trans('labels.from_date').': ', ['class' => 'control-label col-md-3']) !!}
                                <div class="col-md-6">
                                    {!! Form::date('from_date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="col-md-9">
                                    {!! Form::submit(trans('labels.save').' '.trans('labels.tax_config'), ['class' => 'btn btn-primary pull-right']) !!}
                                </div>
                            </div>
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>

    <script>
        $('#data-table').dataTable();
    </script>

    <script>
        $(document).ready(function () {
            $('.tax-config').on('click', function () {
                var id = $(this).attr('id').split('-').pop();
                if ($(this).prop('checked') == true) {
                    $('.show-' + id).hide();
                    $('.rate-' + id).show();
                    $('.from_date-' + id).show();
                    $('#inactive-' + id).prop('disabled', false);
                } else if ($(this).prop('checked') == false) {
                    $('.show-' + id).show();
                    $('.rate-' + id).hide();
                    $('.from_date-' + id).hide();
                    $('#inactive-' + id).prop('checked', false);
                    $('#inactive-' + id).prop('disabled', true);
                }
            });
        });
    </script>
@endsection