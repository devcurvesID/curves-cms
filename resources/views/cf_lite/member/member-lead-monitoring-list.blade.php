@extends('cf_lite.layouts.staff-template')

@section('other-style')
{{--DATA TABLE--}}
<link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
<link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
<link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
<link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
<link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

<link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">

<style>
    .walk-in-btn {
        width: 75px;
        height: 75px;
        position: absolute;
        top: -20px;
        right: 15px;
        background-color: rebeccapurple;
        border-radius: 75px;
        padding: 10px;
        text-align: center;
        font-size: 50px;
        color: #fff;
    }

    .walk-in-btn:hover {
        border: 1px solid rebeccapurple;
        background-color: transparent;
        color: rebeccapurple;
    }

    .select2 {
        width: 100% !important;
    }
</style>
@endsection

@section('content')
@include('cf_lite.includes.flash-message')
@include('cf_lite.includes.error-message')
<div class="row">
    <div class="col-sm-12">
        <a href="{{url('cf-lite/member/member-monitoring-option')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
        <center>
            <h2 class="page-title">@lang('labels.follow_up_lead') ({{$club->club_name}})<br><small>{{date('d F Y', strtotime(Carbon\Carbon::now()))}}</small></h2>
        </center>
    </div>
    {!! Form::open(['method' => 'POST', 'id' => 'submit-form', 'action' => 'MemberMonitoringController@store_bulk_lead_member']) !!}
    <div class="col-md-12">
        <div class="x_panel">
            <div class="x_content">
                <br/>
                <div class="table-responsive" style="background-color: #fff;">
                    <table class="table table-hover table-bordered" id="data-table">
                        <thead>
                        <tr>
                            <th></th>
                            <th>@lang('labels.name')</th>
                            <th>@lang('labels.lead_since')</th>
                            <th>@lang('labels.status')</th>
                            <th>@lang('labels.call')</th>
                            <th>@lang('labels.staff')</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($brls as $member)
                        <tr>
                            <td>
                                {!! Form::checkbox('member['.$member->id.']', $member->id, false, ['id' => 'member-'.$member->id, 'class' => 'member']) !!}
                            </td>
                            <td>
                                {{$member->fullname}}
                            </td>
                            <td>
                                {{date('d-m-Y', strtotime($member->created_at))}}
                            </td>
                            <td style="width: 200px">
                                <div class="form-group">
                                    {!! Form::radio('status['.$member->id.']', 'answer', false, ['class' => 'form-control status']) !!}
                                    {!! Form::label('answer', 'Answer') !!}
                                </div>
                                <div class="form-group  pull-right">
                                    {!! Form::radio('status['.$member->id.']', 'notanswer', false, ['class' => 'form-control status']) !!}
                                    {!! Form::label('not_answer', 'NotAnswer') !!}
                                </div>
                            </td>
                            <td style="width: 150px">
                                {!! Form::number('call['.$member->id.']', 0, ['class' => 'form-control', 'style' => 'width:70px', 'min' => '0', 'max' => '99', 'onkeypress' => 'return onlyNumber(event)', 'id' => 'call-'.$member->id, 'disabled'])!!}
                            </td>
                            <td style="width: 150px">
                                {!! Form::select('staff['.$member->id.']', $staff, $member->buddyreferrallead_user->id, ['class' => 'form-control select-2', 'id' => 'staff-'.$member->id, 'disabled']) !!}
                            </td>
                        </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <button type="button" id="submit-btn" class="btn btn-primary pull-right" style="margin-right: 15px; margin-top: 10px">@lang('labels.submit')</button>
    {!! Form::close() !!}
</div>
@endsection

@section('other-script')
<script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
<script>
    $(document).ready(function () {
        $('#data-table').dataTable();

        $('#data-table').on('click', '.member', function () {
            var m_id = $(this).attr('id').split('-').pop();
            if ($(this).prop('checked') == true) {
                $('#call-' + m_id).prop('disabled', false);
                $('#staff-' + m_id).prop('disabled', false);
            } else {
                $('#call-' + m_id).prop('disabled', true);
                $('#staff-' + m_id).prop('disabled', true);
            }
        });

        $('#submit-btn').on('click', function () {
            var radio = 0;
            var check = 0;
            $('.member').each(function () {
                var id = $(this).attr('id').split('-').pop();
                if (this.checked == true){
                    check += 1;
                    if ($('input[name="status['+id+']"]:checked').prop('checked')){
                        radio += 1;
                    }
                }
            });



            if (check != 0) {
                if (radio == check){
                    $('#submit-form').submit();
                } else {
                    alert('@lang("messages.status_not_choose")');
                }
            } else {
                alert('@lang("messages.no_data_choose")');
            }
        });
    });
</script>
<script>
    function onlyNumber(evt){
        var charCode = (evt.which) ? evt.which : event.keyCode;
        if(charCode == 8 || //space
            charCode == 9 || //tab
            charCode == 46 || //point
            (charCode > 47 && charCode < 58) // number
        ){
            return true;
        }
        return false;
    }
</script>
@endsection