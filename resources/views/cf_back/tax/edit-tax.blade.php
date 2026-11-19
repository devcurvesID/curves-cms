@extends('cf_back.layouts.master-dashboard')

@section('title', 'Country')

@section('other-style')
    <style>
        .panel_toolbox > li {
            float: right;
        }

        .right-form{
            height: inherit;
            width: 100%;
            border: 3px double #a0a0a0;
            padding: 30px;
        }
        #data-table_filter{
            margin-right: 10px;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="my-section">
            <div class="section-title">
                <center>
                    <h1 class="page-title">@lang('labels.tax')</h1>
                </center>
            </div>
            @include('cf_back.flash-message')
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <div class="row">
                                <div class="col-sm-12">
                                    <div class="right-form form-country">

                                        {!! Form::model($taxes, ['method' => 'PATCH', 'action' => ['TaxController@updateTax', $taxes->id]]) !!}
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
                                            {!! Form::text('from_date', null, ['required', 'class' => 'form-control', 'placeholder' => trans('labels.insert').' '.trans('labels.from_date')]) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('thru_date', trans('labels.thru_date'), ['class' => 'control-label']) !!}
                                            {!! Form::text('thru_date', null, ['class' => 'form-control', 'placeholder' => trans('labels.insert').' '.trans('labels.thru_date')]) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::submit(trans('labels.edit').' '.trans('labels.tax'), ['class' => 'btn btn-primary form-control']) !!}
                                        </div>
                                        {!! Form::close() !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script src={{asset("js/jquery.dataTables.min.js")}}></script>
    <script src={{asset("js/dataTables.bootstrap.min.js")}}></script>
    <script src={{asset("vendors/moment/min/moment.min.js")}}></script>
    <script src={{asset("vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js")}}></script>
    <script>
        $('#from_date').datetimepicker({
            format: 'YYYY/MM/DD'
        });
        $('#thru_date').datetimepicker({
            format: 'YYYY/MM/DD'
        });
    </script>
@endsection

