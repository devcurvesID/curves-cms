@extends('cf_lite.layouts.staff-dashboard-template')

@section('title', 'Laporan Workout Member : ' . $club->club_name)

@section('other-style')

    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">
    
    <link href="{{asset('fullcalendar/fullcalendar.min.css')}}" rel="stylesheet">
    <link href="{{asset('fullcalendar/fullcalendar.print.min.css')}}" rel="stylesheet" media="print">

    <link href="{{asset('bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">
    <style>
        .dt-buttons {
            float: left;
        }

        #dataTable_filter {
            float: right;
        }

        .dataTables_length{
            margin-left: -460px;
        }


        table th{
            color: #ff1493;
        }

        table, th{
            text-align: center;
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
    <div class="row">
        <div class="col-xs-12">
            <left>
                <a href="{{url('cf-lite/member/member-monitoring-option')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            </left>
            <center>
                <h2 class="page-title">@lang('labels.list') @lang('labels.workout') : {{$club->club_name}}</h2>
            </center>
        </div>
    </div>
    <div class="row" style="margin: 20px;">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-xs-12">
                    {!! Form::open(['method' => 'GET', 'action' => ['MemberMonitoringController@workout_monitoring_list_filter']]) !!}
                    <div class="col-xs-2">
                        Filter :
                    </div>
                    <div class="col-xs-3">
                        <label>@lang('labels.from_date') : (@lang('labels.from') 00.00)</label>
                        {!! Form::date('from_date', \Carbon\Carbon::now() , ['class' => 'form-control filter-date','placeholder'=>@trans('labels.from_date'), 'id'=>'filt-dari']) !!} <br/>
                    </div>
                    <div class="col-xs-3">
                        <label>@lang('labels.thru_date') : (@lang('labels.from') 00.00)</label>
                        {!! Form::date('thru_date', \Carbon\Carbon::now() , ['class' => 'form-control filter-date','placeholder'=>@trans('labels.from_date'), 'id'=>'filt-sampai']) !!} <br/>
                    </div>
                    <div class="col-xs-2">
                        <br>
                        <button type="submit" class="btn btn-primary" id="save-btn"><i class="fa fa-search"></i> @lang('labels.search')</button>
                    </div>
                {!! Form::close() !!}
                </div>
            </div>
            <hr/>
            {{--<div class="input-group my-hiddenz" style="margin-bottom: 20px;">--}}
            {{--<input type="text" class="tanggal form-control">--}}
            {{--<span class="input-group-addon btn btn-default"><i class="fa fa-calendar"></i></span>--}}
            {{--</div>--}}
            <div class="table-responsive">
                <table id="dataTable" class="table table-bordered table-striped table-hover" style="background: white">
                    <thead>
                    <tr>
                        <th>@lang('labels.indexing')</th>
                        <th>@lang('labels.name')</th>
                        <th>@lang('labels.phone')</th>
                        <th>@lang('labels.wo_count')</th>
                        <th>@lang('labels.call_count')</th>
                        <th>@lang('labels.message_count')</th>
                        <th>@lang('labels.staff')</th>
                        <th>@lang('labels.contact_date')</th>
                        <th>@lang('labels.call')</th>
                        <th>@lang('labels.message')</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($count = 1)
                    @foreach($list_wo as $x)
                        <tr>
                        {!! Form::open(['method'=>'POST','action'=>'MemberMonitoringController@store_member_monitoring','class'=>'formContact','id'=> $x->id]) !!}
                            {!! Form::hidden('user_id',$x->id) !!}
                            {!! Form::hidden('neworfilter',$neworfilter) !!}
                            <td>{{$count++}}</td>
                            <td>{{$x->name}}</td>
                            <td>{{$x->user_memberpersonal->phone}}</td>
                            <td>{{$x->total_wo}}</td>
                            <td class="call-count-{{$x->id}}">
                                {{$x->total_call}}
                                {!! Form::hidden('total_call',$x->total_call,['class'=>'total-call-'.$x->id]) !!}
                            </td>
                            <td class='message-count-{{$x->id}}'>
                                {{$x->total_message}}
                                {!! Form::hidden('total_message',$x->total_message,['class'=>'total-message-'.$x->id]) !!}
                            </td>
                            <td>{!! Form::select('staff', $staffs, null, ['class' => 'form-control','id'=>'staff']) !!}</td>
                            <td>{!! Form::date('contact_date',Carbon\Carbon::now(),['class'=> 'form-control','id'=>'contact-date']) !!}</td>
                            <td style="width:60px;">{!! Form::number('call_count',0,['class' => 'form-control','id'=>'call-'.$x->id,'min'=>'1','style'=>'width:60px;']) !!}</td>
                            <td>{!! Form::number('message_count',0,['class' => 'form-control','id'=>'message-'.$x->id,'min'=>'1','style'=>'width:60px;']) !!}</td>
                            <td>{!! Form::button('Submit',['class'=>'btn btn-primary btn-submit','id'=>'submit-button-'.$x->id]) !!}</td>
                        {!! Form::close() !!}
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('other-script')

    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('js/buttons.print.min.js')}}"></script>

    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('fullcalendar/fullcalendar.min.js')}}"></script>
    <script src="{{asset('bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>
    
    <script>
        $(document).ready( function() {

            $('#dataTable').on('click','.btn-submit', function(e) {
                e.preventDefault();
                var id_form = $(this).attr('id').split('-').pop();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var type = 'POST';
                $.ajax({
                    url: $('#'+id_form).prop('action'),
                    type: type,
                    data: $('#'+id_form).serialize(), // Remember that you need to have your csrf token included
                    dataType: 'json',
                    success: function( data ){
                        $('.call-count-'+id_form).text(data.call_count)
                        $('.message-count-'+id_form).text(data.message_count);

                        $('.total-call-'+id_form).text(data.call_count)
                        $('.total-message-'+id_form).text(data.message_count);

                        $('#call-'+id_form).val('0');
                        $('#message-'+id_form).val('0');
                    },
                    error: function( data ){
                        console.log('Error!');                    }
                });
            });
        });
    </script>

    <script>

        $('#dataTable').DataTable({
            info: false,
            dom: 'Bfrtip',
            buttons: [
                'print',
            ]
        });

        $('#btn-filter').on('click', function () {
            var filter1 = $('#filt-dari').val();
            var filter2 = $('#filt-sampai').val();
            if (filter1 == "") {
                filter1 = 0;
            }

            if (filter2 == 0) {
                filter2 = 0;
            }

            window.location = '{{url('cf-lite/laporan/wo')}}' + "/" + filter1 + "/" + filter2;
        });

        $('.dt-buttons button').prepend('<i class="fa fa-print"></i> ');
        $('.dt-buttons button').addClass('btn btn-default');
    </script>
@endsection