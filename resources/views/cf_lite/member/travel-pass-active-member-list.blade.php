@extends('cf_lite.layouts.staff-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('css/appointment.css')}}" rel="stylesheet">
    <link href="{{asset('fullcalendar/fullcalendar.min.css')}}" rel="stylesheet">
    <link href="{{asset('fullcalendar/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">

    <link href="{{asset('css/appointment.css')}}" rel="stylesheet">

    <style>
        body {
            padding-bottom: 50px;
            font-family: 'Alegreya Sans', sans-serif;
            font-size: 14px;
        }

        #data-table th {
            color: #ff1493;
        }

        div.dataTables_length select {
            margin: 0 10px
        }

        .pagination > .active > a, .pagination > .active > a:focus, .pagination > .active > a:hover {
            background-color: #ff1493;
            border-color: #ff1493;
        }

        div.dataTables_wrapper div.dataTables_paginate ul.pagination {
            margin: 20px 0;
        }

        .btn-primary{
            background-color: #ff1493;
            color: #ffffff;
            border-color: #ff1493;
        }

        .btn-primary:hover {
            color: #ff1493;
            background-color: transparent;
            border-color: #ff1493;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/travel-pass')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">
                    @lang('labels.active_travel_pass')
                    <br>
                </h2>
            </center>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive" style="background-color: #fff;">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                            <tr>
                                <th>@lang('labels.indexing')</th>
                                <th>@lang('labels.member_name')</th>
                                <th>@lang('labels.from_date')</th>
                                <th>@lang('labels.thru_date')</th>
                                <th>@lang('labels.status')</th>
                                <th>@lang('labels.notes')</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($travels) > 0)
                                @php
                                    $index = 1;
                                @endphp
                                @foreach($travels as $travel)
                                    <tr>
                                        <td>{{$index}}</td>
                                        <td>
                                            <a role="button" id="edit-travel-pass-{{$travel->id}}" class="edit-travel-pass" data-toggle="modal" data-target="#addNewTravelPass">{{$travel->travelpass_user->name}}</a>
                                        </td>
                                        <td>{{$travel->from_date}}</td>
                                        <td>{{$travel->thru_date}}</td>
                                        <td>{{$travel->status}}</td>
                                        <td>{{$travel->notes}}</td>
                                        <td>
                                            {!! Form::open(['method' => 'DELETE' , 'style'=>'display:inline-block' , 'action' => ['TravelPassController@delete_travel_pass' , $travel->id, ]])!!}{!! Form::submit('Delete',['class' => 'btn btn-danger' , 'onclick' => 'return confirm("Anda yakin akan menghapus data ini?")']) !!} {!! Form::close() !!}
                                        </td>
                                    </tr>
                                    @php($index++)
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Add Modal -->
    <div class="modal fade" id="addNewTravelPass" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            {!! Form::open(['method' => 'PATCH', 'action' => 'TravelPassController@update_travel_pass']) !!}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.update_travel_pass')</h4>
                    {{ Form::hidden('editid',0)}}
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-inline">
                                <br/>
                                <div class="form-group">
                                    {!! Form::label('member_name', trans('labels.member_name').': ', ['class' => 'control-label']) !!}<br/>
                                    {!! Form::text('member_name', null, ['class' => 'form-control','readonly'=>'readonly']) !!} <br/>
                                    {!! Form::hidden('member_id','1') !!}
                                </div>
                                <div class="form-group">
                                    {!! Form::label('from_date', trans('labels.from_date').': ', ['class' => 'control-label']) !!}<br/>
                                    {!! Form::text('from_date', null, ['class' => 'form-control','readonly'=>'readonly']) !!} <br/>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('thru_date', trans('labels.thru_date').': ', ['class' => 'control-label']) !!}<br/>
                                    {!! Form::text('thru_date', null, ['class' => 'form-control','readonly'=>'readonly']) !!} <br/>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('active_from_date', trans('labels.active_from_date').': ', ['class' => 'control-label']) !!}<br/>
                                    {!! Form::date('active_from_date', Carbon\Carbon::today(), ['class' => 'form-control']) !!} <br/>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('active_thru_date', trans('labels.active_thru_date').': ', ['class' => 'control-label']) !!}<br/>
                                    {!! Form::date('active_thru_date', Carbon\Carbon::today(), ['class' => 'form-control']) !!} <br/>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('notes', trans('labels.notes').': ', ['class' => 'control-label']) !!}<br/>
                                    {!! Form::textarea('notes',null , ['class' => 'form-control','rows'=>'3']) !!} <br/>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" class="btn btn-primary" id="save-btn">@lang('labels.save')</button>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
    <!-- End Add Modal -->
@endsection

@section('other-script')
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('fullcalendar/fullcalendar.min.js')}}"></script>

    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>

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
        var event_date;
        var url = "{{url('cf-lite')}}/";
        $(document).ready(function () {

            $('#data-table').on('click', '.edit-travel-pass', function () {
                

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    travelpass_id: $(this).attr('id').split('-').pop()
                };

                var type = 'POST';
                var my_url = url + 'travel-pass-detail-ajax';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        
                        $('input[name="member_name"]').val(data.travelpass['member_name']);
                        $('input[name="member_id"]').val(data.travelpass['user_id']);
                        $('input[name="from_date"]').val(data.travelpass['from_date']);
                        $('input[name="thru_date"]').val(data.travelpass['thru_date']);
                        $('input[name="active_from_date"]').val(data.travelpass['from_date']);
                        $('input[name="active_thru_date"]').val(data.travelpass['thru_date']);
                        $('textarea[name="notes"]').text(data.travelpass['notes']);
                        $('#save-btn').text('@lang('labels.update')');
                        $('#addNewTravelPass').find(':hidden[name=editid]').prop('value',data.travelpass['id']);

                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });

        });
    </script>

@endsection