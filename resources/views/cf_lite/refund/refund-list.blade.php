@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/payment.css')}}" rel="stylesheet">
    <style>
        body{
            color: white;
        }
        .column-no-border, .column-no-border td {
            border: none !important;
        }
        .modal-dialog, .modal-dialog label{
            color: #414042;
        }
        table{
            color: #414042;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/dashboard')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.refund_list')</h2>
            </center>
        </div>
    </div>

    <div class="row">
        <h3>@lang('labels.latest_refund_submission')</h3>
        @if(count($refunds) > 0)
            @foreach($refunds as $refund)
                <div class="col-md-4">
                    <a href="{{url('cf-lite/refund/'.$refund->id.'/detail')}}">
                        <div class="media">
                            <div class="media-left">
                                @if(!empty($refund->refundsubmission_user->user_memberpersonal->photo))
                                    <img class="media-object img-circle" src="{{asset('images/member_photos/'.$refund->refundsubmission_user->user_memberpersonal->photo)}}" style="width: 85px; height: 85px; object-fit: cover;">
                                @else
                                    <img class="media-object img-circle" src="{{asset('images/img.png')}}" style="width: 85px; height: 85px; object-fit: cover;">
                                @endif
                            </div>
                            <div class="media-body">
                                <h4 class="media-heading">{{$refund->refundsubmission_user->name}}</h4>
                                <table>
                                    <tr>
                                        <td>@lang('labels.membership_type')</td>
                                        <td>:</td>
                                        <td>{{$refund->refundsubmission_user->user_memberstatus->memberstatus_membershiptype->membership_type_name}}</td>
                                    </tr>
                                    <tr>
                                        <td>@lang('labels.joined')</td>
                                        <td>:</td>
                                        <td>{{date('d-m-Y', strtotime($refund->refundsubmission_user->user_memberpersonal->joined))}}</td>
                                    </tr>
                                </table>
                                <hr>
                                <h3 class="my_rupiah">{{$refund->refund_amount}}</h3>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
            <a href="#">
                <h5 class="pull-right">@lang('labels.see_others') <span class="fa fa-chevron-circle-right"></span></h5>
            </a>
        @else
            <h5>@lang('labels.no_refund_submission_at_this_time')</h5>
        @endif
    </div>
    <div class="row">
        <h3>@lang('labels.latest_paid_refund')</h3>
        @if(count($paid_refunds) > 0)
            @foreach($paid_refunds as $paid_refund)
                <div class="col-md-4">
                    <a href="{{url('cf-lite/refund/'.$paid_refund->id.'/detail')}}">
                        <div class="media">
                            <div class="media-left">
                                @if(!empty($paid_refund->refundsubmission_user->user_memberpersonal->photo))
                                    <img class="media-object img-circle" src="{{asset('images/member_photos/'.$paid_refund->refundsubmission_user->user_memberpersonal->photo)}}" style="width: 85px; height: 85px; object-fit: cover;">
                                @else
                                    <img class="media-object img-circle" src="{{asset('images/img.png')}}" style="width: 85px; height: 85px; object-fit: cover;">
                                @endif
                            </div>
                            <div class="media-body">
                                <h4 class="media-heading">{{$paid_refund->refundsubmission_user->name}}</h4>
                                <table>
                                    <tr>
                                        <td>@lang('labels.membership_type')</td>
                                        <td>:</td>
                                        <td>{{$paid_refund->refundsubmission_user->user_memberstatus->memberstatus_membershiptype->membership_type_name}}</td>
                                    </tr>
                                    <tr>
                                        <td>@lang('labels.joined')</td>
                                        <td>:</td>
                                        <td>{{date('d-m-Y', strtotime($paid_refund->refundsubmission_user->user_memberpersonal->joined))}}</td>
                                    </tr>
                                </table>
                                <hr>
                                <h3 class="my_rupiah">{{$paid_refund->refund_amount}}</h3>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
            <a href="#">
                <h5 class="pull-right">@lang('labels.see_others') <span class="fa fa-chevron-circle-right"></span></h5>
            </a>
        @endif
    </div>
@endsection

@section('other-script')
    <script>
        $(document).ready(function () {
            init_format();
        });


        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp.' + (x));
            });
        }
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>
@endsection