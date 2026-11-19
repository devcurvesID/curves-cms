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
            <a href="{{url('cf-lite/locker/locker-setting-choice')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">
                    @lang('labels.locker_rent_management')
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
                                <th>@lang('labels.locker_name')</th>
                                <th>@lang('labels.member_locker_name')</th>
                                <th>@lang('labels.from_date')</th>
                                <th>@lang('labels.thru_date')</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($memberlockers) > 0)
                                @php
                                    $index = 1;
                                @endphp
                                @foreach($memberlockers as $memberlocker)
                                    <tr>
                                        <td>{{$index}}</td>
                                        <td>
                                            <a role="button" id="edit-member-locker-{{$memberlocker->id}}" class="edit-member-locker" data-toggle="modal" data-target="#addNewMemberLocker">{{$memberlocker->memberlocker_lockerinfo->locker_name}}</a>
                                        </td>
                                        <td>{{$memberlocker->memberlocker_user->name}}</td>
                                        <td>{{$memberlocker->from_date}}</td>
                                        <td>{{$memberlocker->thru_date}}</td>
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
    <div class="modal fade" id="addNewMemberLocker" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            {!! Form::open(['method' => 'PATCH', 'action' => 'LockerController@update_memberlocker']) !!}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.locker_rent_management')</h4>
                    {{ Form::hidden('addoredit','add')}}
                    {{ Form::hidden('editid',0)}}
                    {{ Form::hidden('edituserid',0)}}
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-inline">
                                <div class="form-group">
                                    {!! Form::label('locker_name', trans('labels.locker_name').': ', ['class' => 'control-label']) !!}<br/>
                                    {!! Form::select('locker_name',$lockers, null, ['class' => 'form-control','id'=>'locker-name-select']) !!} <br/>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('member_name', trans('labels.member_name').': ', ['class' => 'control-label']) !!}<br/>
                                    {!! Form::text('member_name',null , ['class' => 'form-control','readonly'=>'readonly']) !!} <br/>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('from_date', trans('labels.from_date').': ', ['class' => 'control-label']) !!}<br/>
                                    {!! Form::date('from_date', null , ['class' => 'form-control']) !!} <br/>
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
            
            $('#data-table').on('click', '.edit-member-locker', function () {
                

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    memberlocker_id: $(this).attr('id').split('-').pop()
                };

                var type = 'POST';
                var my_url = url + 'locker/memberlocker-list-ajax';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        $('#locker-name-select').val(data.memberlocker['locker_info_id']);
                        $('input[name="member_name"]').val(data.memberlocker['namauser']);
                        $('input[name="from_date"]').val(data.memberlocker['from_date']);
                        $('#save-btn').text('@lang('labels.edit')');
                        $('#addNewMemberLocker').find(':hidden[name=addoredit]').prop('value','edit');
                        $('#addNewMemberLocker').find(':hidden[name=edituserid]').prop('value',data.memberlocker['iduser']);
                        $('#addNewMemberLocker').find(':hidden[name=editid]').prop('value',data.memberlocker['id']);

                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });

        });
    </script>

@endsection