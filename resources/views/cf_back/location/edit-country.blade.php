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
                    <h1 class="page-title">@lang('labels.country')</h1>
                </center>
            </div>
            @include('cf_back.flash-message')
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="row x_title">
                            <div class="col-md-6">
                                <h3>@lang('labels.country')</h3>
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
                                            @foreach($countries as $x)
                                                <tr>
                                                    <td>{{$x->id}}</td>
                                                    <td>{{$x->country}}</td>
                                                    <td>
                                                        <a href="{{url('cf-back/country/'.$x->id)}}" class="btn btn-warning btn-edit-role"><i
                                                                    class="fa fa-pencil"></i> @lang('labels.edit')
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-danger btn-hapus"><i
                                                                    class="fa fa-trash"></i> @lang('labels.delete')</a>
                                                        {{--{!! Form::open(['method' => 'DELETE', 'action' => ['RolesController@destroy', $role->id]]) !!}--}}
                                                        {{--{!! Form::submit(trans('labels.delete'), ['class' => 'btn btn-danger hidden btn-delete-submit']) !!}--}}
                                                        {{--{!! Form::close() !!}--}}
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="right-form form-country">
                                        {!! Form::open(['url' => 'cf-back/country/updateCountry']) !!}
                                        @if($errors->any())
                                            <ul class="alert alert-danger">
                                                @foreach($errors->all() as $error)
                                                    <li>{{$error}}</li>
                                                @endforeach
                                            </ul>
                                        @endif
                                        <div class="form-group hidden">
                                            {!! Form::label('id', trans('labels.country'), ['class' => 'control-label']) !!}
                                            {!! Form::hidden('id', $country->id, ['class' => 'form-control', 'placeholder' => trans('labels.input').' '.trans('labels.name').' '.trans('labels.country')]) !!}
                                        </div>

                                        <div class="form-group">
                                            {!! Form::label('country', trans('labels.country'), ['class' => 'control-label']) !!}
                                            {!! Form::text('country', $country->country, ['class' => 'form-control', 'placeholder' => trans('labels.input').' '.trans('labels.name').' '.trans('labels.country')]) !!}
                                        </div>
                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-xs-6">
                                                    <a href="{{url('cf-back/country')}}" class="btn btn-info form-control">@lang('labels.cancel')</a>
                                                </div>
                                                <div class="col-xs-6">
                                                    {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary form-control']) !!}
                                                </div>
                                            </div>
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
        $('#data-table').dataTable({
            autoWidth: false,
            columnDefs: [
                {width: '10%', targets: 0},
                {width: '60%', targets: 1},
                {width: '15%', targets: 2},
                {width: '15%', targets: 3}
            ]
        });
    </script>
@endsection

