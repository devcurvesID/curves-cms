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
            <a href="{{url('cf-lite/dashboard')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.new_member_list') ({{$club->club_name}})<br><small>@lang('labels.joined'): {{date('d F Y', strtotime($date))}}</small></h2>
            </center>
        </div>
        <div class="col-md-3">
            {!! Form::open(['method' => 'GET', 'action' => 'MemberController@new_member_date']) !!}
            <div class="form-group">
                <div class="input-group">
                    {!! Form::date('joined_date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                    <span class="input-group-btn">
                        {!! Form::submit(trans('labels.set_date'), ['class' => 'btn btn-primary']) !!}
                    </span>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        <div class="col-md-9">
            <div class="walk-in-btn" data-toggle="modal" data-target="#addWalkInModal" role="button" title="@lang('labels.walk_in')"><div class="fa fa-paw"></div></div>
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
                                    <th>@lang('labels.photo')</th>
                                    <th>@lang('labels.fullname')</th>
                                    <th>@lang('labels.payment')</th>
                                    <th>@lang('labels.others')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php($indexing = 1)
                                @foreach($new_members as $new_member)
                                    <tr>
                                        <td>{{$indexing}}</td>
                                        <td>
                                            @if(!empty($new_member->user_memberpersonal->photo))
                                                <img class="img-circle" src="{{asset('images/member_photos/'.$new_member->user_memberpersonal->photo)}}" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                {{--<a href="{{url('cf-lite/member/'.$new_member->id.'/take-photo')}}">--}}
                                                    <img class="img-circle" src="{{asset('images/img.png')}}" title="@lang('labels.photo')" style="width: 40px; height: 40px; object-fit: cover;">
                                                {{--</a>--}}
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{url('cf-lite/member/'.$new_member->id.'/edit')}}" title="@lang('labels.edit')">
                                                {{$new_member->name}}
                                                @if($new_member->user_memberstatus->status == 'Stopped')
                                                    <span class="fa fa-ban" title="@lang('labels.cancelled')" style="color: red;"></span>
                                                @endif
                                            </a>
                                        </td>
                                        <td>
                                            @if($new_member->role_id == '7')
                                                @if($new_member->user_memberstatus->payment_status == 'NotSet')
                                                    <a href="{{url('cf-lite/payment/new-installment-setting/'.$new_member->id)}}" class="btn btn-success" title="@lang('labels.installment_setting')"><span class="fa fa-bank"></span></a>
                                                @endif
                                                <a href="{{url('cf-lite/payment/paid-list/'.$new_member->id)}}" class="btn btn-success" title="@lang('labels.payment_detail')"><span class="fa fa-usd"></span></a>
                                                <a href="{{url('cf-lite/payment/bill-list/'.$new_member->id)}}" class="btn btn-danger" title="@lang('labels.bill_list')"><span class="fa fa-history"></span></a>
                                            @else
                                                @lang('labels.role'): @lang('labels.staff')
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{url('cf-lite/weigh-measure/'.$new_member->id.'/add')}}" class="btn btn-default btn-sm" title="@lang('labels.add_new_weigh_n_measure')"><span class="fa fa-plus"></span> <span class="fa fa-balance-scale"></span></a>
                                                <a href="{{url('cf-lite/weigh-measure/'.$new_member->id.'/add/goal')}}" class="btn btn-info btn-sm" title="@lang('labels.add_weigh_n_measure_goal')"><span class="fa fa-plus"></span> <span class="fa fa-dashboard"></span></a>
                                            </div>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{url('cf-lite/weigh-measure/'.$new_member->id.'/history')}}" class="btn btn-default btn-sm" title="@lang('labels.weigh_n_measure_history')"><span class="fa fa-balance-scale"></span></a>
                                                <a href="{{url('cf-lite/weigh-measure/'.$new_member->id.'/history/goal')}}" class="btn btn-info btn-sm" title="@lang('labels.weigh_n_measure_goal_detail')"><span class="fa fa-dashboard"></span></a>
                                            </div>
                                            <a href="{{url('cf-lite/lead/add/br/member/'.$new_member->id)}}" class="btn btn-default btn-sm" title="@lang('labels.add_br')"><span class="fa fa-plus"></span> <span class="fa fa-group"></span></a>
                                        </td>
                                    </tr>
                                    @php($indexing++)
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addWalkInModal" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'POST', 'action' => 'WalkInController@store_walk_in', 'id' => 'form-walk-in']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.walk_in')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('staff', trans('labels.staff').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-4">
                                {!! Form::select('staff', $staffs, null, ['class' => 'form-control select2']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('fa_date', trans('labels.fa_date').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-4">
                                {!! Form::date('fa_date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('name', trans('labels.fullname').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-4">
                                {!! Form::text('name', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('phone', trans('labels.phone').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-4">
                                {!! Form::text('phone', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('source', trans('labels.source').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-4">
                                {!! Form::select('source', $sources, null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group success-store" style="color: #3c763d; display: none;">
                            <label class="control-label col-md-8"><h4><span class="glyphicon glyphicon-ok"></span> @lang('messages.member_has_been_stored')</h4></label>
                        </div>
                        <div class="form-group member-exist" style="color: red; display: none;">
                            <label class="control-label col-md-8"><h4><span class="glyphicon glyphicon-remove"></span> @lang('messages.member_exist')</h4></label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="button" name="member" id="add-member-walk-in" class="btn btn-primary"><span class="fa fa-plus"></span> @lang('labels.member')</button>
                    <button type="button" name="corporate" id="add-corporate-walk-in" class="btn btn-danger"><span class="fa fa-plus"></span> @lang('labels.no_sale')</button>
                    <button type="button" name="payment" id="add-payment-walk-in" class="btn btn-success">@lang('labels.payment') <span class="fa fa-money"></span> </button>
                </div>
                {!! Form::hidden('modal-attr', null) !!}
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
        $('#data-table').dataTable();
        $('select[name="source"]').select2();
        $('select[name="staff"]').select2();
    </script>

    <script type="text/javascript">
        $(document).ready(function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });

            $('#add-member-walk-in').on('click', function () {
                var url = "{{url('cf-lite')}}/";

                var formData = {
                    fa_date: $('input[name="fa_date"]').val(),
                    name: $('input[name="name"]').val(),
                    phone: $('input[name="phone"]').val(),
                    source: $('select[name="source"]').val(),
                    status: 5
                };

                var type = 'POST';
                var my_url = url + 'add-member-walk-in';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        if (data.exist == 'not') {
                            $('input[name="name"]').val('');
                            $('input[name="phone"]').val('');
                            $('.success-store').show();

                            setTimeout(function () {
                                $('.success-store').hide();
                            }, 1500);
                        } else if (data.exist == 'exist') {
                            $('.member-exist').show();
                            setTimeout(function () {
                                $('.member-exist').hide();
                            }, 1500);
                        }
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });

            $('#add-corporate-walk-in').on('click', function () {
                var url = "{{url('cf-lite')}}/";

                var formData = {
                    fa_date: $('input[name="fa_date"]').val(),
                    name: $('input[name="name"]').val(),
                    phone: $('input[name="phone"]').val(),
                    source: $('select[name="source"]').val(),
                    status: 6
                };

                var type = 'POST';
                var my_url = url + 'add-member-walk-in';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        $('input[name="name"]').val('');
                        $('input[name="phone"]').val('');
                        $('.success-store').show();

                        setTimeout(function () {
                            $('.success-store').hide();
                        }, 1500);
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });

            $('#add-payment-walk-in').on('click', function () {
                $('input[name="modal-attr"]').val('payment');
                $('#form-walk-in').submit();
            });
        });
    </script>
@endsection