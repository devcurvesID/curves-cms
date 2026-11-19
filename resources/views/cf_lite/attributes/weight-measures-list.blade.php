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
                <a href="{{url('cf-lite/attributes/attribute-setting-choice')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.weight_measures')</h2>
                </center>
            </div>
        </div>

        <div class="row">
            <div class="col-md-10 col-md-offset-1 voucher-list-add">
                <a class="btn btn-default pull-right" href="javascript:;" data-toggle="modal" data-target="#addNewWeightMeasuresModal"
                   title="@lang('labels.add_new_weight_measures')"><span class="fa fa-plus"></span> @lang('labels.add_new_weight_measures')</a>
            </div>
            <div class="col-md-10 col-md-offset-1">
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="data-table">
                                <thead>
                                <tr>
                                    <th>@lang('labels.indexing')</th>
                                    <th>@lang('labels.attribute_name')</th>
                                    <th>@lang('labels.description')</th>
                                    <th>@lang('labels.created')</th>
                                    <th>@lang('labels.action')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php($indexing = 1)
                                @foreach($weight_measures as $weight_measure)
                                    <tr>
                                        <td>{{$indexing}}</td>
                                        <td>{{$weight_measure->attribute_name}}</td>
                                        <td>{{$weight_measure->description}}</td>
                                        <td>{{date('d-m-Y', strtotime($weight_measure->created_at))}}</td>
                                        <td>
                                            <!-- Edit Button -->
                                            <a class="btn btn-warning" href="javascript:;" data-toggle="modal" data-target="#editWeightMeasuresModal{{$weight_measure->id}}"
                                            title="@lang('labels.edit_weight_measures')"> @lang('labels.update_weight_measures')</a>
                                            <!-- Delete Button -->
                                            {!! Form::open(['method' => 'DELETE' , 'style'=>'display:inline-block' , 'action' => ['WeightMeasuresController@delete_weight_measures' , $weight_measure->id, ]])!!}{!! Form::submit('Delete',['class' => 'btn btn-danger' , 'onclick' => 'return confirm("Anda yakin akan menghapus data ini?")']) !!} {!! Form::close() !!}
                                        </td>
                                    </tr>
                                    @php($indexing++)
                                    <!-- Edit Modal -->
                                    <div class="modal fade" id="editWeightMeasuresModal{{$weight_measure->id}}" tabindex="-1" role="dialog">
                                        <div class="modal-dialog" role="document">
                                            {!! Form::open(['method' => 'PATCH', 'action' => ['WeightMeasuresController@update_weight_measures', $weight_measure->id]]) !!}
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                                                aria-hidden="true">&times;</span></button>
                                                    <h4 class="modal-title">@lang('labels.update_weight_measures')</h4>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="row">
                                                        <div class="col-md-6">
                                                            <div class="form-inline">
                                                                <div class="form-group">
                                                                    {!! Form::label('attribute_name', trans('labels.attribute_name').': ', ['class' => 'control-label']) !!}
                                                                    {!! Form::text('attribute_name', $weight_measure->attribute_name, ['class' => 'form-control']) !!}<br/>
                                                                    {!! Form::label('description', trans('labels.description').': ', ['class' => 'control-label']) !!}<br/>
                                                                    {!! Form::textarea('description', $weight_measure->description, ['class' => 'form-control']) !!}
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-default"
                                                            data-dismiss="modal">@lang('labels.close')</button>
                                                    <button type="submit" class="btn btn-primary">@lang('labels.update')</button>
                                                </div>
                                            </div>
                                            {!! Form::close() !!}
                                        </div>
                                    </div>
                                    <!-- End Edit Modal -->
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Add Modal -->
        <div class="modal fade" id="addNewWeightMeasuresModal" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                {!! Form::open(['method' => 'POST', 'action' => 'WeightMeasuresController@store_weight_measures']) !!}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">@lang('labels.add_new_weight_measures')</h4>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-inline">
                                    <div class="form-group">
                                        {!! Form::label('attribute_name', trans('labels.attribute_name').': ', ['class' => 'control-label']) !!}
                                        {!! Form::text('attribute_name', null, ['class' => 'form-control']) !!} <br/>
                                        {!! Form::label('description', trans('labels.description').': ', ['class' => 'control-label']) !!} <br/>
                                        {!! Form::textarea('description', null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal">@lang('labels.close')</button>
                        <button type="submit" class="btn btn-primary">@lang('labels.save')</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <!-- End Add Modal -->

        <div class="row">
            <div class="col-md-12">
                <div class="paging hiden rom">
                    {{$weight_measures->links()}}
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
@endsection
