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
        .select2 {
            width: 100% !important;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/dashboard')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <a href="{{url('cf-lite/guest-member/guest-weight-measure')}}" class="btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: rebeccapurple; margin-right: 10px; margin-top: 30px; " title="@lang('labels.guest_member_wm')"><span class="fa fa-heartbeat" style="margin-top: 10px; color: rebeccapurple;"></span></a>
            <a href="{{url('cf-lite/guest-member/wo-search')}}" class="btn btn-default pull-right" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: rebeccapurple; margin-right: 10px; margin-top: 30px; " title="@lang('labels.guest_member_workout')"><span class="glyphicon glyphicon-scale" style="margin-top: 10px; color: rebeccapurple;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.guest_member_list') ({{$club->club_name}})</h2>
            </center>
        </div>
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                            <tr>
                                <th>@lang('labels.fullname')</th>
                                <th>@lang('labels.phone')</th>
                                <th>@lang('labels.promo')</th>
                                <th>@lang('labels.promo_period')</th>
                                <th>@lang('labels.workout')</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            {{--@foreach($promo as $p)--}}
                                {{--<tr>--}}
                                    {{--<td><a role="button" class="edit-gm" id="edit-gm-{{$p->gmm_id}}" tlp="{{$p->phone}}" source="{{$p->source_id}}" staff="{{$p->staff_id}}" fdate="{{$p->from_date}}" promo="{{$p->gm_id}}" thdate="{{$p->thru_date}}" data-toggle="modal" data-target="#editGM">{{ $p->fullname }}</a></td>--}}
                                    {{--<td>{{ $p->phone }}</td>--}}
                                    {{--<td>{{ $p->gm_name }}</td>--}}
                                    {{--<td>{{ \Carbon\Carbon::parse($p->from_date)->format('d-m-Y') }} @lang('labels.until') {{ \Carbon\Carbon::parse($p->thru_date)->format('d-m-Y') }}</td>--}}
                                    {{--<td></td>--}}
                                {{--</tr>--}}
                            {{--@endforeach--}}
                            @foreach($promo as $gmm)
                                <tr>
                                    <td><a role="button" class="edit-gm" id="edit-gm-{{$gmm->id}}" tlp="{{$gmm->gmmember_guest->phone}}" source="{{$gmm->gmmember_guest->source_id}}" staff="{{$gmm->gmmember_guest->staff_id}}" fdate="{{$gmm->from_date}}" promo="{{$gmm->gmmember_gm->gm_name}}" thdate="{{$gmm->thru_date}}" data-toggle="modal" data-target="#editGM" style="color: rebeccapurple;">{{ $gmm->gmmember_guest->fullname }}</a></td>
                                    <td>{{ $gmm->gmmember_guest->phone }}</td>
                                    <td>{{ $gmm->gmmember_gm->gm_name }}</td>
                                    <td>{{ \Carbon\Carbon::parse($gmm->from_date)->format('d-m-Y') }} @lang('labels.until') {{ \Carbon\Carbon::parse($gmm->thru_date)->format('d-m-Y') }}</td>
                                    <td>
                                        @if(!empty($gmm->gmmember_guest->guest_guestwo))
                                            {{count($gmm->gmmember_guest->guest_guestwo)}}
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{url('cf-lite/guest-member/'.$gmm->id.'/past-wo')}}" title="@lang('labels.add_workout')" style="color: rebeccapurple;"><span class="glyphicon glyphicon-scale"></span></a>
                                        <a href="{{url('cf-lite/guest-member/guest-weight-measure/'.$gmm->guest_id.'/history')}}" title="@lang('labels.guest_weight_n_measure_history')" style="color: rebeccapurple; padding-left: 10px;"><span class="fa fa-heartbeat"></span></a>
                                        <a href="{{url('cf-lite/guest-membership/receipt/'.$gmm->id)}}" title="@lang('labels.print_receipt')" style="color: rebeccapurple; padding-left: 10px;"><span class="fa fa-print"></span></a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--Modal--}}
    <div class="modal fade" id="editGM" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'PATCH', 'action' => 'GuestMembershipController@update_guest_member_list']) !!}
                {!! Form::hidden('gmm_id', null) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">@lang('labels.edit_guest_membership_data')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('fullname', trans('labels.fullname').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::text('fullname', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('phone', trans('labels.phone').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::text('phone', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('source', trans('labels.source').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::select('source', $sources, null, ['class' => 'form-control select2', 'id' => 'edit-source', 'placeholder' => '-- '.trans('labels.choose').' '.trans('labels.source').' --']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('coach', trans('labels.coach').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::select('coach', $coach, null, ['class' => 'form-control select2', 'id' => 'edit-coach', 'placeholder' => '-- '.trans('labels.choose').' '.trans('labels.coach').' --']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('guest_membership', trans('labels.guest_membership').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <select name="promo" class="form-control select2" id="promo-name">
                                    @foreach($gms as $gm)
                                        <option value="{{$gm->id}}">{{$gm->gm_name}}</option>
                                    @endforeach
                                </select>
                                {{--{!! Form::label('guest_membership', null, ['class' => 'control-label', 'id' => 'promo-name']) !!}--}}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('from_date', trans('labels.from_date').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::date('from_date', null, ['class' => 'form-control', 'id' => 'from-date']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('thru_date', trans('labels.thru_date').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::date('thru_date', null, ['class' => 'form-control', 'id' => 'thru-date']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" name="submit" class="btn btn-success">@lang('labels.update')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script>
        $('.select2').select2();
        $('#data-table').dataTable();
    </script>
    <script type="text/javascript">
        $(document).ready(function () {
            $('.edit-gm').on('click', function () {
                var id = $(this).attr('id').split('-').pop();
                var name = $(this).text();
                var tlp = $(this).attr('tlp');
                var source = $(this).attr('source');
                var coach = $(this).attr('staff');
                var promo = $(this).attr('promo');
                var from = $(this).attr('fdate');
                var thru = $(this).attr('thdate');

                $('input[name="gmm_id"]').val(id);
                $('input[name="fullname"]').val(name);
                $('input[name="phone"]').val(tlp);
                $('select#edit-source').val(source).trigger('change.select2');
                $('select#edit-coach').val(coach).trigger('change.select2');
                $('select#promo-name').val(promo).trigger('change.select2');
                $('input[name="from_date"]').val(from);
                $('input[name="thru_date"]').val(thru);
            });
        });
    </script>
@endsection