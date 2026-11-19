@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">
    <style>
        #locationField, #controls {
            position: relative;
            /*width: 480px;*/
        }

        #autocomplete {
            position: absolute;
            top: 0px;
            left: 0px;
            width: 99%;
        }

        #locationField {
            height: 20px;
            margin-bottom: 2px;
        }

        table td{
            height: 50px;
            padding: 10px;
        }

        table td:first-child{
            color: #ff1493;
            font-size: 20px;
            font-weight: bold;
        }

        .select2-container--default .select2-selection--single{
            background-color: #ffffff;
            color: #ff1493;
            border: 1px solid #ff1493;
        }

        .select2-container--default .select2-selection--single:hover .select2-selection__rendered {
            color: #ff1493;
        }

        .select2-container--default .select2-selection--single:hover {
            border: 1px solid #ff1493;
        }

        .btn-primary{
            background-color: #ff1493;
            color: #ffffff;
            border-color: #ff1493;
        }
        .btn-primary:hover  {
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
            <a href="{{url('cf-lite/transfer-staff/choice-mutation')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.staff_rotation')</h2>
            </center>
        </div>
    </div>

    {!! Form::open(['method' => 'POST', 'action' => ['StaffController@store_rotation']]) !!}
    <div class="row" style="border: 1px solid grey">
        <div class="col-md-6 col-md-offset-0" style="border: 1px solid red">
            <table>
                <tr>
                    <td><span>@lang('labels.choose_club')</span></td>
                    <td>:</td>
                    <td>
                        {!! Form::select('club_from', $club, null, ['class'=>'form-control select2 club_from', 'placeholder' => trans('labels.choose_club'), 'id'=>'club_from']) !!}
                    </td>
                </tr>
                <tr>
                    <td><span>@lang('labels.choose_staff')</span></td>
                    <td>:</td>
                    <td>{!! Form::select('staff_from', array(), null, ['class'=>'form-control select2 staff_from', 'placeholder' => trans('labels.choose_staff'), 'id'=>'staff_from']) !!}</td>
                </tr>
            </table>
        </div>
        <div  class="col-md-6 col-md-offset-0" style="border: 1px solid red">
            <table>
                <tr>
                    <td><span>@lang('labels.choose_club')</span></td>
                    <td>:</td>
                    <td>
                        {!! Form::select('club_to', $club, null, ['class'=>'form-control select2 club_to', 'placeholder' => trans('labels.choose_club'), 'id'=>'club_to']) !!}
                    </td>
                </tr>
                <tr>
                    <td><span>@lang('labels.choose_staff')</span></td>
                    <td>:</td>
                    <td>{!! Form::select('staff_to', array(), null, ['class'=>'form-control select2 staff_to', 'placeholder' => trans('labels.choose_staff'), 'id'=>'staff_to']) !!}</td>
                </tr>
            </table>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary pull-right']) !!}
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@section('other-script')
    <script>
        var url = "{{url('cf-lite')}}/";
        $(document).ready(function () {
            //Ajax From Club
            $('#club_from').on('change', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formDataFrom = {
                    club_id: $(this).val()
                };

                var type = 'POST';
                var my_url_from = url + 'transfer-staff/rotation-ajax-from';

                $.ajax({
                    type: type,
                    url: my_url_from,
                    data: formDataFrom,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);

                        $('.staff_from').empty();
                        var nama = '<option>@lang('labels.choose_staff')</option>';
                        $.each(data.staffs, function (i, item) {
                            nama += '<option value="' + item.id + '">' + item.name + '</option>'
                        });
                        $('.staff_from').html(nama);

                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                })
            });

            //Ajax To Club
            $('#club_to').on('change', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formDataTo = {
                    club_id: $(this).val()
                }

                var type = 'POST';
                var my_url_to = url + 'transfer-staff/rotation-ajax-to';

                $.ajax({
                    type: type,
                    url: my_url_to,
                    data: formDataTo,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);

                        $('.staff_to').empty();
                        var staffTo = '<option>@lang('labels.choose_staff')</option>';
                        $.each(data.staffs, function (i, item) {
                            staffTo += '<option value="' + item.id + '">' + item.name + '</option>'
                        });
                        $('.staff_to').html(staffTo);
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                })
            })
        });

    </script>
@endsection