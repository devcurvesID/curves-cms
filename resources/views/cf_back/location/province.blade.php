@extends('cf_back.layouts.master-dashboard')

@section('title', 'Province')

@section('other-style')
    <style>
        .panel_toolbox > li {
            float: right;
        }

        .right-form {
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
                    <h1 class="page-title">@lang('labels.province')</h1>
                </center>
            </div>
            @include('cf_back.flash-message')

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="row x_title">
                            <div class="col-md-6">
                                <h3>@lang('labels.province')</h3>
                            </div>
                            <div class="col-md-6">
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    </li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="x_content">
                            <div class="row">
                                <div class="col-sm-9">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered" id="data-table">
                                            <thead>
                                            <tr>
                                                <th>
                                                    @lang('labels.id')
                                                </th>
                                                <th>
                                                    @lang('labels.province')
                                                </th>
                                                <th>
                                                    @lang('labels.country')
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
                                            @foreach($provincies as $provincy)
                                                <tr>
                                                    <td>{{$provincy->id}}</td>
                                                    <td>{{$provincy->province}}</td>
                                                    <td>{{$provincy->province_country->country}}</td>
                                                    <td>
                                                        <a href="{{url('cf-back/province/'.$provincy->id)}}"
                                                           class="btn btn-warning btn-edit-role"><i
                                                                    class="fa fa-pencil"></i> @lang('labels.edit')
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-danger btn-hapus"><i
                                                                    class="fa fa-trash"></i> @lang('labels.delete')</a>
                                                        <div class="hidden">
                                                            {!! Form::open(['method' => 'DELETE', 'action' => ['LocationController@destroyProvince', $provincy->id]]) !!}
                                                            {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                                                            {!! Form::close() !!}
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="right-form form-country">
                                        {!! Form::open(['url' => 'cf-back/province/createProvince']) !!}

                                        @if($errors->any())
                                            <ul class="alert alert-danger">
                                                @foreach($errors->all() as $error)
                                                    <li>{{$error}}</li>
                                                @endforeach
                                            </ul>
                                        @endif

                                        <div class="form-group">
                                            {!! Form::label('country_id', 'Pilih Negara', ['class' => 'control-label']) !!}
                                            @if(count($countriesForSelect) > 0):
                                            {!! Form::select('country_id', $countriesForSelect, null, ['class' => 'form-control', 'Locationholder' => 'Pilih Negara', 'id' => 'countries']) !!}
                                            @else
                                                <p>@lang('labels.country_not_found')</p>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('province', trans('labels.province'), ['class' => 'control-label']) !!}
                                            {!! Form::text('province', null, ['class' => 'form-control', 'Locationholder' => trans('labels.input').' '.trans('labels.name').' '.trans('labels.province')]) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary form-control']) !!}
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
    <script>
        $('#data-table').dataTable();
    </script>
@endsection

