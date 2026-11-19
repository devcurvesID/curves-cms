@extends('cf_lite.layouts.staff-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <style>

        .page-title {
            margin-top: 0px;
        }

        .btn-filter-zone{
            padding: 9px 12px;
        }

        select[name="data-table_length"]{
            margin: 0px 10px;
        }

        .btn-member-status {
            position: absolute;
            top: 0;
            right: 0;
            border-radius: 50px;
            width: 50px;
            height: 50px;
            border: 3px solid rebeccapurple;
        }

        .btn-member-status:hover {
            background-color: rebeccapurple;
            border: 3px solid rebeccapurple;
        }

        .btn-member-status:hover > span {
            color: white !important;
        }

    </style>

@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/dashboard')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.member_list') ({{$club->club_name}})</h2>
            </center>
            @if((Auth::user()->role_id < 6) || (Auth::user()->role_id == 8))
                <a href="{{url('cf-lite/member/status-setting')}}" class="btn btn-default btn-member-status" title="@lang('labels.member_status_setting')"><span class="fa fa-user-secret" style="margin-top: 4px; color: rebeccapurple; font-size: 20px;"></span></a>
            @endif
        </div>
        <div class="col-md-3">
            <div id="filter-zone">
                {!! Form::open(['method' => 'GET', 'action' => 'MemberController@member_list_search']) !!}
                <div class="form-group">
                    <label class="radio-inline">
                        {!! Form::radio('search_option', 'name', true, ['id' => 'search-option-1']) !!} @lang('labels.name')
                    </label>
                    <label class="radio-inline">
                        {!! Form::radio('search_option', 'keytag', false, ['id' => 'search-option-2']) !!} @lang('labels.key_tag')
                    </label>
                </div>
                <div class="form-group">
                    <div class="input-group">
                        {!! Form::text('s', null, ['class' => 'form-control', 'placeholder' => trans('labels.input_search')]) !!}
                        <span class="input-group-btn">
                            <button class="btn btn-default btn-filter-zone" type="submit"><span class="fa fa-search"></span></button>
                        </span>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                                <tr>
                                    <th>@lang('labels.photo')</th>
                                    <th>@lang('labels.fullname')</th>
                                    <th>@lang('labels.phone')</th>
                                    <th>@lang('labels.payment')</th>
                                    <th>@lang('labels.others')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($members as $member)
                                    <tr>
                                        <td>
                                            @if(!empty($member->user_memberpersonal->photo))
                                                <img class="img-circle" src="{{asset('images/member_photos/'.$member->user_memberpersonal->photo)}}" style="width: 40px; height: 40px; object-fit: cover;">
                                            @else
                                                {{--<a href="#">--}}
                                                    <img class="img-circle" src="{{asset('images/img.png')}}" title="@lang('labels.photo')" style="width: 40px; height: 40px; object-fit: cover;">
                                                {{--</a>--}}
                                            @endif
                                        </td>
                                        <td>
                                            <a href="{{url('cf-lite/member/'.$member->id.'/edit')}}" title="@lang('labels.edit')">
                                                @if(!empty($member->user_memberpersonal->memberpersonal_buddyreferrallead->guest_gmmember[0]))
                                                    <span class="fa fa-ticket"></span>
                                                @endif
                                                {{$member->name}}
                                                @if($member->user_memberstatus->status == 'Stopped' || $member->user_memberstatus->status == 'Inactive')
                                                    <span class="fa fa-ban" title="@lang('labels.cancelled')" style="color: red;"></span>
                                                @endif
                                            </a>

                                            <a href="javascript:;" id="qr-{{$member->id}}" class="btn qr pull-right {{ ($member->user_memberstatus->qr_generate) ? 'btn-success' : 'btn-danger' }}"
                                               title="{{ ($member->user_memberstatus->qr_generate) ? trans('labels.generated') : trans('labels.not_generated') }}"><span class="fa fa-qrcode"></span> </a>

                                               @if(isset($member->user_memberpersonal->key_tag_id))
                                               <!--a href="http://chart.googleapis.com/chart?chs=500x500&cht=qr&chl={{ $member->user_memberpersonal->key_tag_id }}" rel="noopener noreferrer" target="_blank" class="btn btn-primary pull-right"><span class="fa fa-qrcode"> Show KeyTag</span></a-->

                                               <a href="https://qrcode.tec-it.com/API/QRCode?data={{ $member->user_memberpersonal->key_tag_id }}" rel="noopener noreferrer" target="_blank" class="btn btn-primary pull-right"><span class="fa fa-qrcode"> Show KeyTag</span></a>

                                               @endif
                                        </td>
                                        <td>
                                            @if(isset($member->user_memberpersonal->phone))
                                            {{$member->user_memberpersonal->phone}}
                                            @endif
                                        </td>
                                        <td>
                                            @if($member->role_id == '7')
                                                @if($member->user_memberstatus->payment_status == 'NotSet'
                                                    && ($member->ezypay_installment_remaining === null || $member->ezypay_installment_remaining > 0))
                                                    <a href="{{url('cf-lite/payment/new-installment-setting/'.$member->id)}}" class="btn btn-success" title="@lang('labels.installment_setting')"><span class="fa fa-bank"></span></a>
                                                @endif
                                            @else
                                                @lang('labels.role'): @lang('labels.staff')
                                            @endif
                                            <a href="{{url('cf-lite/payment/bill-list/'.$member->id)}}" class="btn btn-danger pull-right" title="@lang('labels.bill_list')"><span class="fa fa-history"></span></a>
                                            <a href="{{url('cf-lite/payment/paid-list/'.$member->id)}}" class="btn btn-success pull-right" title="@lang('labels.payment_detail')"><span class="fa fa-usd"></span></a>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{url('cf-lite/weigh-measure/'.$member->id.'/add')}}" class="btn btn-default btn-sm" title="@lang('labels.add_new_weigh_n_measure')"><span class="fa fa-plus"></span> <span class="fa fa-balance-scale"></span></a>
                                                <a href="{{url('cf-lite/weigh-measure/'.$member->id.'/add/goal')}}" class="btn btn-info btn-sm" title="@lang('labels.add_weigh_n_measure_goal')"><span class="fa fa-plus"></span> <span class="fa fa-dashboard"></span></a>
                                            </div>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <a href="{{url('cf-lite/weigh-measure/'.$member->id.'/history')}}" class="btn btn-default btn-sm" title="@lang('labels.weigh_n_measure_history')"><span class="fa fa-balance-scale"></span></a>
                                                <a href="{{url('cf-lite/weigh-measure/'.$member->id.'/history/goal')}}" class="btn btn-info btn-sm" title="@lang('labels.weigh_n_measure_goal_detail')"><span class="fa fa-dashboard"></span></a>
                                            </div>
                                            <a href="{{url('cf-lite/lead/add/br/member/'.$member->id)}}" class="btn btn-default btn-sm" title="@lang('labels.add_br')"><span class="fa fa-plus"></span> <span class="fa fa-group"></span></a>
                                            @if($member->role_id == '7')
                                            <a href="{{url('cf-lite/member/'.$member->id.'/transfer-member')}}" class="btn btn-default btn-sm pull-right" title="@lang('labels.change_club')"><span class="fa fa-exchange"></span> <span class="fa fa-home"></span></a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            @if(count($members) > 0)
                <div class="paging hidden rom">
                    {{$members->links()}}
                </div>
            @endif
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $('#data-table').dataTable();

        $(document).ready(function () {
            var url = "{{url('cf-lite')}}/";
            $('.qr').on('click', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var member_id = $(this).attr('id').split('-').pop();

                var formData = {
                    member_id: member_id,
                    club_id: '{{\Illuminate\Support\Facades\Auth::user()->club_id}}',
                };

                var type = 'POST';
                var my_url = url + 'qr-broadcast-ajax';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
//                        if (data == true) {
                            if ($('#qr-' + member_id).hasClass('btn-danger')) {
                                $('#qr-' + member_id).removeClass('btn-danger');
                                $('#qr-' + member_id).addClass('btn-success');
                            }
//                        }
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });
        });
    </script>
@endsection
