@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">
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

        #table-staff-detail td,
        #table-staff-detail-add td{
            height: 50px;
            padding: 0px 9px;
            font-size: 20px;
        }

        #table-staff-detail td:first-child,
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
            <a href="{{url('cf-lite/member/list')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.member_transfer_detail')</h2>
            </center>
        </div>
    </div>

    {!! Form::model($member->user_memberpersonal, ['method' => 'PATCH', 'action' => ['MemberController@transfer_member',$member->id], 'id' => 'form-staff-detail']) !!}
    <div class="row">
        <div class="col-md-2 col-md-offset-1">
            @if(!empty($member->user_memberpersonal->photo))
                <img id="image_preview" class="img-circle"
                     src="{{asset('images/member_photos/'.$member->user_memberpersonal->photo)}}"
                     style="width: 150px; height: 150px; object-fit: cover;">
            @else
                <img id="image_preview" class="img-circle" src="{{asset('images/img.png')}}"
                     style="width: 150px; height: 150px; object-fit: cover;">
            @endif
        </div>
        <div class="col-md-3">
            <table id="table-staff-detail">
                <tr>
                    <td>
                        {{$member->name}}
                        {!! Form::hidden('member_id', $member->id, ['class' => 'form-control', 'readonly' => 'readonly']) !!}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{$member->user_memberstatus->memberstatus_membershiptype->membership_type_name}}
                        {!! Form::hidden('role', $member->role_id, ['class' => 'form-control','readonly'=>'readonly']) !!}
                    </td>
                </tr>
                <tr>
                    <td>
                        {{$member->user_memberstatus->from_date}} @lang('labels.until') {{$member->user_memberstatus->thru_date}}
                        {!! Form::hidden('role', $member->role_id, ['class' => 'form-control','readonly'=>'readonly']) !!}
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <hr>
    <div class="row">
        <div id="table-staff-detail" class="col-md-11 col-md-offset-1">
            <div style="font-size: 25px; font-weight: 600; margin: 10px 0;">@lang('labels.transfer_member')
            </div>
            <table id="transfer-staff">
                <tr>
                    @if(count($club)>0)
                    <td><span class="fa fa-briefcase" title="@lang('labels.current_club')"> @lang('labels.from')</span></td>
                    <td>:</td>
                    <td>
                        {!! Form::select('clubAsal', $allClub , $member->club_id, ['class' => 'form-control','readonly'=>'readonly']) !!}
                    </td>
                    @else
                        @lang('labels.no_data')
                    @endif
                </tr>
                <tr>
                    <td><span class="fa fa-briefcase" title="@lang('labels.club')"> @lang('labels.to')</span></td>
                    <td>:</td>
                    <td>
                        {!! Form::select('club', $club, null, ['class' => 'form-control']) !!}
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
    <script src="{{asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>
@endsection