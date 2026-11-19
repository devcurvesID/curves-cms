@extends('cf_lite.layouts.staff-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">
    <link href="{{asset('css/appointment.css')}}" rel="stylesheet">
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <left>
                <a href="/curvesapp/public/cf-lite/dashboard" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            </left>
            <center>
                <h2 class="page-title">@lang('labels.lead_attendance_list')<br><small>({{date('d F Y', strtotime($date))}})</small></h2>
            </center>
        </div>
        <div class="col-md-12">
            {!! Form::open(['method' => 'POST', 'action' => ['AppointmentController@store_bulk_attendance_leads', $date]]) !!}
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive" style="background-color: #fff;">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                                <tr>
                                    <th>@lang('labels.indexing')</th>
                                    <th>@lang('labels.fullname')</th>
                                    <th>@lang('labels.phone')</th>
                                    <th>@lang('labels.showed')</th>
                                    <th>@lang('labels.sale')</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @if(count($guest_appointments) > 0)
                                    @php
                                        $index = 1;
                                    @endphp
                                    @foreach($guest_appointments as $guest_appointment)
                                        <tr>
                                            <td>{{$index}}</td>
                                            <td>{{$guest_appointment->guestappointment_buddyreferrallead->fullname}}</td>
                                            <td>{{$guest_appointment->guestappointment_buddyreferrallead->phone}}</td>
                                            <td>
                                                @php
                                                    if (!empty($guest_appointment->guestappointment_gastatus)):
                                                        if ($guest_appointment->guestappointment_gastatus->status_id == '3' || $guest_appointment->guestappointment_gastatus->status_id == '5' || $guest_appointment->guestappointment_gastatus->status_id == '6'):
                                                            $showup = true;
                                                            $showup_enable = 'disabled';
                                                        else:
                                                            $showup = false;
                                                            $showup_enable = '';
                                                        endif;
                                                    else:
                                                        $showup = false;
                                                        $showup_enable = '';
                                                    endif;
                                                @endphp
                                                {!! Form::checkbox('showup['.$guest_appointment->id.']', $guest_appointment->id, $showup, ['id' => 'showup-'.$guest_appointment->id, $showup_enable, 'class' => 'showup']) !!}
                                            </td>
                                            <td>
                                                @php
                                                    if (!empty($guest_appointment->guestappointment_gastatus)):
                                                        if ($guest_appointment->guestappointment_gastatus->status_id == '5'):
                                                            $sale = true;
                                                        else:
                                                            $sale = false;
                                                        endif;
                                                    elseif ($guest_appointment->guestappointment_gastatus->status_id == '6'):
                                                        $sale = false;
                                                    endif;
                                                @endphp
                                                {!! Form::checkbox('sale['.$guest_appointment->id.']', $guest_appointment->id, $sale, ['id' => 'sale-'.$guest_appointment->id, 'disabled']) !!}
                                            </td>
                                            <td>
                                                @if(!empty($guest_appointment->guestappointment_gastatus))
                                                    @if(($guest_appointment->guestappointment_gastatus->status_id == '3') || ($guest_appointment->guestappointment_gastatus->status_id == '6'))
                                                        @if(empty($guest_appointment->guestappointment_buddyreferrallead->buddyreferrallead_guestdetail))
                                                            <a href="{{url('cf-lite/lead/'.$guest_appointment->id.'/complete-fa-data')}}" class="btn btn-info btn-sm">@lang('labels.complete_fa_data') <span class="fa fa-chevron-circle-right"></span></a>
                                                        @else
                                                            {{$guest_appointment->guestappointment_guestnosalereason->guestnosalereason_nosalereason->nosale_reason}}
                                                        @endif
                                                    @elseif($guest_appointment->guestappointment_gastatus->status_id == '5')
                                                        @if(!empty($guest_appointment->guestappointment_buddyreferrallead->buddyreferrallead_memberpersonal))
                                                            @if(empty($guest_appointment->guestappointment_buddyreferrallead->buddyreferrallead_guestdetail))
                                                                <a href="{{url('cf-lite/member/'.$guest_appointment->guestappointment_buddyreferrallead->buddyreferrallead_memberpersonal->user_id.'/edit')}}" class="btn btn-success btn-sm">@lang('labels.complete_member_data') <span class="fa fa-chevron-circle-right"></span></a>
                                                            @else
                                                                <a href="{{url('cf-lite/member/'.$guest_appointment->guestappointment_buddyreferrallead->buddyreferrallead_memberpersonal->user_id.'/edit')}}" target="_blank" class="btn btn-default btn-sm">@lang('labels.see_member_data') <span class="fa fa-chevron-circle-right"></span></a>
                                                            @endif
                                                        @else
                                                            <a href="{{url('cf-lite/pre-member/'.$guest_appointment->guest_id.'/membership')}}" class="btn btn-warning">
                                                                @lang('messages.please_finish_the_first_payment') <span class="fa fa-chevron-right"></span>
                                                            </a>
                                                        @endif
                                                    @endif
                                                @endif
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
            {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary pull-right']) !!}
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('vendors/moment/min/moment.min.js')}}"></script>
    <script src="{{asset('vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script>
        $('#data-table').dataTable();

        $('.contacted').on('click', function () {
            var c_id = $(this).attr('id').split('-').pop();
            if ($(this).prop('checked') == true) {
                $('#confirm-' + c_id).prop('disabled', false);
                $('#no_answer-' + c_id).prop('disabled', false);
                $('#reschedule-' + c_id).prop('disabled', false);
            } else {
                $('#confirm-' + c_id).prop('disabled', true);
                $('#no_answer-' + c_id).prop('disabled', true);
                $('#reschedule-' + c_id).prop('disabled', true);
            }
        });

        $('.showup').on('click', function () {
            var m_id = $(this).attr('id').split('-').pop();
            if ($(this).prop('checked') == true) {
                $('#sale-' + m_id).prop('disabled', false);
            } else {
                $('#sale-' + m_id).prop('disabled', true);
            }
        });
    </script>
@endsection