@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <style>
        #locationField, #controls {
            position: relative;
            /*width: 480px;*/
        }

        /*#autocomplete {*/
        /*position: absolute;*/
        /*top: 0px;*/
        /*left: 0px;*/
        /*width: 99%;*/
        /*}*/

        #address td {
            font-size: 10pt;
        }

        #locationField {
            height: 20px;
            margin-bottom: 2px;
        }

        #table-staff-detail td,
        #table-staff-detail-add td{
            height: 50px;
            padding: 0px 9px;
            font-size: 20px;
        }

        #table-staff-detail td:first-child,
        #staff-functional-role label,
        #form-staff-detail button span[class="fa fa-save"],
        #staff-working-hour-box #table-staff-detail td label{
            color: #ec088f;
        }

        #table-staff-detail-add td:first-child,
        #table-staff-detail-add td:nth-child(3),
        #table-staff-detail-add td:nth-child(5),
        #table-staff-detail-add td:nth-child(7){
            color: #77619c;
        }

        button#add-new-working-hour {
            background-color: #fabbde;
            border-color: #fabbde;
            margin-left: 10px;
        }

        button#add-new-working-hour:hover {
            background-color: #ff98d1;
            border-color: #ff98d1;
        }

        #staff-functional-role label{
            font-size: 20px;
        }

        input[value="Simpan"]{
            background-color: #fabbde;
            border-color: #fabbde;
        }

        input[value="Simpan"]:hover{
            background-color: #ff98d1;
            border-color: #ff98d1;
        }


    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/settings')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.staff_transfer_detail')</h2>
            </center>
        </div>
    </div>

    {!! Form::model($staff->user_memberpersonal, ['method' => 'PATCH', 'action' => ['StaffController@transfer_staff', $staff->id], 'files' => 'true', 'id' => 'form-staff-detail']) !!}
    <div class="row">
        <div class="col-md-2 col-md-offset-1">
            @if(!empty($staff->user_memberpersonal->photo))
                <img id="image_preview" class="img-circle"
                     src="{{asset('images/member_photos/'.$staff->user_memberpersonal->photo)}}"
                     style="width: 150px; height: 150px; object-fit: cover;">
            @else
                <img id="image_preview" class="img-circle" src="{{asset('images/img.png')}}"
                     style="width: 150px; height: 150px; object-fit: cover;">
            @endif
        </div>
        <div class="col-md-3">
            <table id="table-staff-detail">
                <tr>
                    <td><span class="fa fa-female" title="@lang('labels.name')"></span></td>
                    <td>:</td>
                    <td>{!! Form::text('name', $staff->name, ['class' => 'form-control', 'readonly' => 'readonly']) !!}</td>
                </tr>
                <tr>
                    <td><span class="fa fa-briefcase" title="@lang('labels.role')"></span></td>
                    <td>:</td>
                    <td>
                        {!! Form::select('role', $roles, $staff->role_id, ['class' => 'form-control']) !!}
                    </td>
                </tr>
            </table>
        </div>
        <div class="col-md-3">
            <table id="table-staff-detail">
                <tr>
                    <td>
                        <span>{!! Form::label('key_tag_id', trans('labels.key_tag').'#', ['class' => 'control-label']) !!}</span>
                    </td>
                    <td>:</td>
                    <td>{!! Form::text('key_tag_id', null, ['class' => 'form-control']) !!}</td>
                </tr>
            </table>
        </div>
    </div>
    <hr>
    <div class="row">
        <div id="table-staff-detail" class="col-md-11 col-md-offset-1">
            <div style="font-size: 25px; font-weight: 600; margin: 10px 0;">@lang('labels.transfer_staff')
            </div>
            <table id="transfer-staff">
                <tr>
                    @if(count($clubs)>0)
                    <td><span class="fa fa-briefcase" title="@lang('labels.current_club')"> @lang('labels.from')</span></td>
                    <td>:</td>
                    <td>
                        {!! Form::select('clubAsal', $curClub, $staff->club_id, ['class' => 'form-control','readonly'=>'readonly']) !!}
                    </td>
                    @else
                        @lang('labels.no_data')
                    @endif
                </tr>
                <tr>
                    <td><span class="fa fa-briefcase" title="@lang('labels.club')"> @lang('labels.to')</span></td>
                    <td>:</td>
                    <td>
                        {!! Form::select('club', $clubs, $staff->club_id, ['class' => 'form-control','placeholder'=>"Please Select",'id'=>'club_transfer_select']) !!}
                    </td>
                </tr>
                <tr>
                    <td colspan="4"><span style="color: black; font-size: 25px; font-weight: 600; margin: 10px 0;">@lang('labels.transfer')&nbsp;@lang('labels.member')</span></td>
                </tr>
                <tr>
                    <td colspan="4">
                        <div class="radio">
                            <label>
                                {!! Form::radio('transfer_opt', 'still', true) !!}@lang('labels.member_stay_in_my_own')
                            </label>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <div class="radio">
                            <label>
                                {!! Form::radio('transfer_opt', 'swap', false) !!} @lang('labels.swap_with_the_coach')
                            </label>
                        </div>
                        <div>
                            <select name="selected_coach" class="form-control" id="coach-select" placeholder="@lang('labels.please_select')">
                            </select>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td colspan="4">
                        <div class="radio">
                            <label>
                                {!! Form::radio('transfer_opt', 'cutnoswap', false) !!}@lang('labels.cut_but_no_swap')
                            </label>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-10 col-md-offset-1">
            {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary pull-right']) !!}
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@section('other-script')
    <script src="{{asset('vendors/moment/min/moment.min.js')}}"></script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script src="{{asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script type="text/javascript">
        $(document).ready(function(){
            "use strict";
            $('#club_transfer_select').select2();
            $('#coach-select').select2();

            $('#club_transfer_select').on('select2:selecting',function(){
                var url = "{{url('cf-lite')}}/";
                var e = document.getElementById('club_transfer_select');

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    club_id: e.options[e.selectedIndex].value
                };

                var type = 'POST';
                var my_url = url + 'transfer-staff/get-staff-with-member-ajax';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    success: function (data) {

                        var via = '';

                        $.each(data.coaches, function (i, item){
                            via += '<option value="' + item.id + '">'
                                + item.name
                                + '</option>';
                        });

                        $('#coach-select').html(via);
                        $('#coach-select').val();
                    },
                    error: function (data) {
                        console.log('Error : ', data);
                    }
                });
            });
        });
    </script>
@endsection