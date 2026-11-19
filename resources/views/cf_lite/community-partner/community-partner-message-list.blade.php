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
        #if_br {
            display: none;
        }

        .circle-medium {
            width: 200px;
            height: 200px;
            border-radius: 50%;
            padding-top: 50px;
        }
    </style>
    <style>
        body {
            padding-bottom: 50px;
            font-family: 'Alegreya Sans', sans-serif;
            font-size: 14px;
        }

        #calendar {
            max-width: 900px;
            margin: 0 auto;
        }

        .fc-day:hover {
            background-color: rgba(240, 248, 255, 0.4) !important;
            cursor: pointer;
        }

        @media screen and (max-width: 767px) {
            .xs-no-padding {
                padding: 0px;
            }

            .btn-big-apm {
                padding: 0px;
                font-size: 14px;
                border-radius: 0px;
            }

            .modal-body {
                padding-bottom: 0px;
            }

            .fc td, .fc th {
                padding: 5px;
            }

            h2 {
                font-size: 18px;
            }

            .fc-toolbar .fc-center:before, .fc-toolbar .fc-center:after {
                font-size: 14px;
                line-height: 20px;
                padding: 2.5px;
                width: 25px;
                height: 25px;
            }

            .fc-day-grid-container {
                height: 300px !important;
            }
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
            <a href="{{url('cf-lite/community-partner/choose-cp-menu')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">
                    @lang('labels.cp_message_list')
                    <br>
                </h2>
            </center>
        </div>
    </div>
    <div class="row">
        <div class="col-md-10 col-md-offset-1" id="add-cpmessage">
                <a class="btn btn-primary pull-right new-cpmessage" href="javascript:;" data-toggle="modal" data-target="#addNewCPMessage" title="@lang('labels.add_new_cp_message')"><span class="fa fa-plus"></span> @lang('labels.add_new_cp_message')</a>
        </div>
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive" style="background-color: #fff;">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                            <tr>
                                <th>@lang('labels.indexing')</th>
                                <th>@lang('labels.message')</th>
                                <th>@lang('labels.cp_name')</th>
                                <th>@lang('labels.from_date')</th>
                                <th>@lang('labels.thru_date')</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(count($cpms) > 0)
                                @php
                                    $index = 1;
                                @endphp
                                @foreach($cpms as $cpm)
                                    <tr>
                                        <td>{{$index}}</td>
                                        <td><a role="button" id="edit-cp-message-{{$cpm->id}}" class="edit-cp-message" data-toggle="modal" data-target="#addNewCPMessage">{{$cpm->message}}</a></td>
                                        <td>{{$cpm->communitypartnermessage_communitypartner->cp_name}}</td>
                                        <td>{{$cpm->from_date}}</td>
                                        <td>{{$cpm->thru_date}}</td>
                                        <td>
                                            {!! Form::open(['method' => 'DELETE' , 'style'=>'display:inline-block' , 'action' => ['CommunityPartnerController@delete_cpm' , $cpm->id, ]])!!}{!! Form::submit('Delete',['class' => 'btn btn-danger' , 'onclick' => 'return confirm("Anda yakin akan menghapus data ini?")']) !!} {!! Form::close() !!}
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
    <div class="modal fade" id="addNewCPMessage" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            {!! Form::open(['method' => 'PATCH', 'action' => 'CommunityPartnerController@store_cpm']) !!}
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.add_new_cp_message')</h4>
                    {{ Form::hidden('addoredit','add')}}
                    {{ Form::hidden('editid',0)}}
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-inline">
                                <div class="form-group">
                                    <a class="btn btn-primary" data-toggle="collapse" href="#collapseExample" role="button" aria-expanded="false" aria-controls="collapseExample">@lang('labels.community_partner')</a>
                                    <div class="collapse" id="collapseExample">
                                        <div class="card card-body">
                                            {!! Form::label('community_partner', trans('labels.community_partner').': ', ['class' => 'control-label']) !!}<br/>
                                            @foreach($cps as $cp)
                                                {!! Form::radio('cps', $cp->id, false, ['class' => 'form-control cp-name', 'id' => $cp->id]) !!}{{$cp->cp_name}} <br/>
                                            @endforeach
                                        </div>
                                    </div>
                                </div><br/>
                                <div class="form-group">
                                    {!! Form::label('message', trans('labels.message').': ', ['class' => 'control-label']) !!}<br/>
                                    {!! Form::textarea('message', null, ['class' => 'form-control']) !!} <br/>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('from_date', trans('labels.from_date').': ', ['class' => 'control-label']) !!}<br/>
                                    {!! Form::date('from_date', \Carbon\Carbon::now() , ['class' => 'form-control']) !!} <br/>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('thru_date', trans('labels.thru_date').': ', ['class' => 'control-label']) !!}<br/>
                                    {!! Form::date('thru_date', \Carbon\Carbon::now() , ['class' => 'form-control']) !!} <br/>
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
            
            $('#add-cpmessage').on('click', '.new-cpmessage', function () {
                $('.cp-name').prop('checked',false);
                $('textarea[name="message"]').val('');
                $('input[name="from_date"]').val({{\Carbon\Carbon::parse(\Carbon\Carbon::now())->format('d/m/Y')}});
                $('input[name="thru_date"]').val({{\Carbon\Carbon::parse(\Carbon\Carbon::now())->format('d/m/Y')}});
                $('#save-btn').text('@lang("labels.save")');
                $('#addNewCPMessage').find(':hidden[name=addoredit]').prop('value','add');
            });

            $('#data-table').on('click', '.edit-cp-message', function () {
                

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    cpm_id: $(this).attr('id').split('-').pop()
                };

                var type = 'POST';
                var my_url = url + 'community-partner/cp-message-list-ajax';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        console.log($('meta[name="csrf-token"]').attr('content'));
                        $('.cp-name').each(function(){
                            var id = $(this).attr('id');
                            document.getElementById(id).checked = false;
                        });
                        $('.cp-name').each(function(){
                            var id = $(this).attr('id');
                            if(id == data.cpm['cp_id']){
                                document.getElementById(id).checked = true;
                            };
                        });
                        console.log(data.cpm['from_date']);
                        $('textarea[name="message"]').val(data.cpm['message']);
                        $('input[name="from_date"]').val(data.cpm['from_date']);
                        $('input[name="thru_date"]').val(data.cpm['thru_date']);
                        $('#save-btn').text('@lang('labels.edit')');
                        $('#addNewCPMessage').find(':hidden[name=addoredit]').prop('value','edit');
                        $('#addNewCPMessage').find(':hidden[name=editid]').prop('value',data.cpm['id']);

                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });

        });
    </script>

@endsection