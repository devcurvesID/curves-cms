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

        .bottom-left {
            background: transparent;
            border: 5px solid transparent;
            border-image: linear-gradient(-90deg, #800000 0%, #ffeb7f 45%, #ffffff 50%, #ffeb7f 55%, #800000 100%);
            border-image-slice: 1;

            padding: 15px;
        }

        .bottom-right {
            background: transparent;
            border: 5px solid transparent;
            border-image: linear-gradient(-90deg, #800000 0%, #ffeb7f 45%, #ffffff 50%, #ffeb7f 55%, #800000 100%);
            border-image-slice: 1;

            padding: 15px;
        }

        .photo-img {
            width: 75px;
            height: 75px;
            border-radius: 50%;
        }

        .list-view {
            margin-top: 15px;
            padding: 10px;
        }

        .list-view:hover {
            background-color: rgba(0, 0, 0, .1);
            cursor: pointer;
        }

        .result-container {
            max-height: 220px;
            overflow-y: scroll;
        }

        .btn-calon-anggota a {
            margin: 15px;
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

        .badge {
            font-size: 25px;
            width: 30px;
            border-radius: 50px;
            background-color: orangered;
            position: absolute;
            top: 45px;
            margin-left: -30px;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/corporate/corporation-options')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
        </div>
    </div><br/><br/><br/>
    {!! Form::open(['method' => 'POST', 'action' =>['CorporationController@store_bulk_member_corporation']]) !!}
    <div class="row">
        <div class="col col-md-4"><h4>@lang('labels.corporation') :</h4></div>
        <div class="col col-md-8">
            {!! Form::select('corporation', $corporations, null, ['class' => 'form-control corporate-opt','id'=>'corporate-group', 'placeholder'=>trans('labels.choose_corporation')]) !!}
        </div>
    </div>
    <div class="row">
        <div class="col col-md-4"><h4>@lang('labels.group_name') :</h4></div>
        <div class="col col-md-8">
            <div class="form-group">
                <select id="group-corp" class="form-control" name="group_select" disabled>
                    
                </select>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col col-md-4"><h4>@lang('labels.group_name') :</h4></div>
        <div class="col col-md-8">
            <div class="form-group">
                {!! Form::text('group_name','', ['class' => 'form-control']) !!}
            </div>
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
                                <th></th>
                                <th>@lang('labels.indexing')</th>
                                <th>@lang('labels.fullname')</th>
                                <th>@lang('labels.keytag')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @if(is_array($users) || is_object($users))
                                @php
                                    $index = 1;
                                @endphp
                                @foreach($users as $user)
                                    <tr>
                                        <td>{!! Form::checkbox('corporate['.$user->id.']', $user->id) !!}</td>
                                        <td>{{$index}}</td>
                                        <td>{{$user->name}}</td>
                                        <td>{{$user->user_memberpersonal->key_tag_id}}</td>
                                    </tr>
                                    @php($index++)
                                @endforeach
                            @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary pull-right']) !!}
            {!! Form::close() !!}
        </div>
        
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('fullcalendar/fullcalendar.min.js')}}"></script>

    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>

    <script>
        $(document).ready(function() {
            var url = "{{url('cf-lite')}}/";
            $("#corporate-group").on('change',function(){
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                var selected = $(this).val();
                $('#group-corp').removeAttr('disabled');
                $('#group-corp').empty();
                var formData = {
                    id: selected
                };

                var type = 'POST';
                var my_url = url + 'corporate/corporate-group-ajax';
                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        var newOption = "<option>@lang('labels.choose_group')</option>";
                        $.each(data,function(index,val){
                            $.each(val,function(a,b){
                                newOption += "<option value='"+val[a]['sequence_group']+"'>"+val[a]['group_name']+"</option>";
                            });
                            
                        });
                        $('#group-corp').append(newOption);
                        // for (var i = data.length - 1; i >= 0; i--) {
                            // var JSONObject = JSON.parse(data);
                            // var option = document.createElement("option");
                            // option.text = JSONObject[i]['sequence_group'];
                            // option.value = JSONObject[i]['group_name'];
                            // var select = document.getElementById("group-corp");
                            // select.appendChild(option);
                            // newOption += "<option value='"+data.sequence_group+"'>"+data.group_name+"</option>";
                        // }
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });
        });
    </script>
@endsection
