@extends('cf_back.layouts.master-dashboard')

@section('title', 'Tax')

@section('other-style')
    <link href={{asset("vendors/select2/dist/css/select2.min.css")}} rel="stylesheet">
@endsection

@section('content')
    <div class="container">
        <div class="my-section">
            <div class="section-title">
                <center>
                    <h1 class="page-title">@lang('labels.list') @lang('labels.tax')</h1>
                </center>
            </div>
            @include('cf_back.flash-message')


            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <a id="btn-add-taxtype" class="btn btn-primary pull-right"><i
                                class="fa fa-plus"></i> @lang('labels.add') @lang('labels.tax')</a>
                    <a id="btn-add-role" class="btn btn-primary pull-right"><i
                                class="fa fa-plus"></i> @lang('labels.add') @lang('labels.tax')</a>
                    <div class="x_panel">
                        <div class="x_content">
                            <br/>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="data-table">
                                    <thead>
                                    <tr>
                                        <th>
                                            @lang('labels.id')
                                        </th>
                                        <th>
                                            @lang('labels.tax')
                                        </th>
                                        <th>
                                            @lang('labels.club')
                                        </th>
                                        <th>
                                            @lang('labels.rate')
                                        </th>
                                        <th>
                                            @lang('labels.from_date')
                                        </th>
                                        <th>
                                            @lang('labels.thru_date')
                                        </th>
                                        <th>
                                            @lang('labels.edit')
                                        </th>
                                        <th>
                                            @lang('labels.delete')
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($taxes as $x)

                                        <tr id="{{$x->id}}">
                                            <td>{{$x->id}}</td>
                                            <td>
                                                {{$x->taxconfig_taxtype->tax_type}}
                                            </td>
                                            <td>
                                                {{$x->taxconfig_clubinfo->club_name}}
                                            </td>
                                            <td>
                                                {{$x->rate}}
                                            </td>
                                            <td>
                                                {{date('d F Y', strtotime($x->from_date))}}
                                            </td>
                                            <td>
                                                {{$x->thru_date != "" ? date('d F Y', strtotime($x->thru_date)) : "Hari ini dan seterusnya"}}
                                            </td>
                                            <td>
                                                <a href="{{url('cf-back/tax/'.$x->id.'/edit')}}" class="btn btn-warning btn-edit-role"><i
                                                            class="fa fa-pencil"></i> @lang('labels.edit')
                                                </a>
                                            </td>
                                            <td>
                                                <a class="btn btn-danger btn-hapus"><i
                                                            class="fa fa-trash"></i> @lang('labels.delete')</a>
                                                <div>
                                                    {!! Form::open(['method' => 'DELETE', 'action' => ['TaxController@destroyTax', $x->id]]) !!}
                                                    {!! Form::close() !!}
                                                </div>
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
        </div>
    </div>

    {{--MODAL TAMBAH TYPE--}}
    <button type="button" id="btn-pop-taxtype" class="hidden" data-toggle="modal" data-target="#add-taxtype">tr</button>

    <!-- Modal -->
    <div class="modal fade" id="add-taxtype" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">@lang('labels.add') @lang('labels.tax')</h4>
                </div>
                <div class="modal-body">
                    <div>
                        {!! Form::open(['url' => 'cf-back/tax/createTaxType']) !!}
                        <div class="form-group">
                            {!! Form::label('tax_type', trans('labels.name').' '.trans('labels.tax'), ['class' => 'control-label']) !!}
                            {!! Form::text('tax_type', null, ['required', 'class' => 'form-control', 'placeholder' => trans('labels.insert').' '.trans('labels.tax')]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit(trans('labels.add').' '.trans('labels.tax'), ['class' => 'btn btn-primary form-control']) !!}
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--MODAL TAMBAH TYPE--}}

    {{--MODAL TAMBAH CONFIG--}}
    <button type="button" id="btn-pop-add" class="hidden" data-toggle="modal" data-target="#add-role"></button>

    <!-- Modal -->
    <div class="modal fade" id="add-role" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">@lang('labels.add') @lang('labels.tax')</h4>
                </div>
                <div class="modal-body">
                    <div>
                        {!! Form::open(['url' => 'cf-back/tax/createTaxConfig']) !!}
                        <div class="form-group">
                            {!! Form::label('tax_type_id', trans('labels.name').' '.trans('labels.tax'), ['class' => 'control-label']) !!}
                            @if(count($tax_type) > 0):
                            {!! Form::select('tax_type_id', $tax_type, null, ['required', 'class' => 'form-control js-state', 'id' => 'select_taxtype', 'placeholder' => trans('labels.choose').' '.trans('labels.tax')]) !!}
                            @else
                                <p>@lang('labels.club_not_found')</p>
                            @endif
                        </div>
                        <div class="form-group">
                            {!! Form::label('club_id', trans('labels.name').' '.trans('labels.club'), ['class' => 'control-label']) !!}
                            @if(count($clubs) > 0):
                            {!! Form::select('club_id', $clubs, null, ['required', 'class' => 'form-control js-state', 'id' => 'select_club', 'placeholder' => trans('labels.choose').' '.trans('labels.club')]) !!}
                            @else
                                <p>@lang('labels.club_not_found')</p>
                            @endif
                        </div>
                        <div class="form-group">
                            {!! Form::label('rate', trans('labels.rate'), ['class' => 'control-label']) !!}
                            {!! Form::number('rate', null, ['required', 'class' => 'form-control', 'placeholder' => trans('labels.insert').' '.trans('labels.rate')]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('from_date', trans('labels.from_date'), ['class' => 'control-label']) !!}
                            {!! Form::date('from_date', null, ['required', 'class' => 'form-control', 'placeholder' => trans('labels.insert').' '.trans('labels.from_date')]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('thru_date', trans('labels.thru_date'), ['class' => 'control-label']) !!}
                            {!! Form::date('thru_date', null, ['class' => 'form-control', 'placeholder' => trans('labels.insert').' '.trans('labels.thru_date')]) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit(trans('labels.add').' '.trans('labels.tax'), ['class' => 'btn btn-primary form-control']) !!}
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--MODAL TAMBAH CONFIG--}}
@endsection

@section('other-script')
    <script src={{asset("js/jquery.dataTables.min.js")}}></script>
    <script src={{asset("js/dataTables.bootstrap.min.js")}}></script>
    <script src={{asset("vendors/select2/dist/js/select2.js")}}></script>
    <script>
        $('#data-table').dataTable();

        $('#btn-add-taxtype').click(function () {
            $('#btn-pop-taxtype').click();
        });

        $('#btn-add-role').click(function () {
            $('#btn-pop-add').click();
        });

        $('#select_club').select2({
            placeholder: 'Pilih Club',
        });
        $('#select_taxtype').select2({
            placeholder: 'Pilih Taxtype',
        });

    </script>
@endsection

