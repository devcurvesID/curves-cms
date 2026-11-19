@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <style>
        .home-btn-title{
            font-family: 'Alegreya Sans', sans-serif;
        }

        .home-btn{
            padding-top: 30px;
        }

        .home-btn span{
            color: #ffffff;
        }

        h3.home-btn-title{
            color: #ffffff;
            margin: 20px 20px 10px 20px;
        }

        .main-btn .home-btn li{
            color: #cc0000;
            font-weight: 600;
        }
        .main-btn:hover .home-btn span,
        .main-btn:hover h3.home-btn-title{
            color: #666666;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/settings')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.voucher_type')</h2>
                </center>
            </div>
        </div>
        <div class="col-md-10 col-md-offset-1">
        <div class="row">
            @if(!empty($voucher_types))
                @foreach($voucher_types as $voucher_type)
                    <div class="col-md-3 main-btn">
                        <a href="{{url('cf-lite/voucher/list/'.$voucher_type->id)}}">
                            <div class="home-btn" style="background-color: #afeecf; border: 5px solid #9dd6ba; margin-bottom: 20px">
                                <span class="fa fa-qrcode"></span>
                                <h3 class="home-btn-title">{{trans('labels.'.$voucher_type->voucher_type)}}</h3>
                            </div>
                        </a>
                        <ul style="list-style: none; padding-left: 0; text-align: center; font-weight: 400; color: #698e7c">
                            <li>@lang('labels.used'): {{$voucher_type->vouchertype_voucher->where('status', 'Used')->count()}}</li>
                            <li>@lang('labels.out'): {{$voucher_type->vouchertype_voucher->where('status', 'Out')->count()}}</li>
                            <li>@lang('labels.in_stock'): {{$voucher_type->vouchertype_voucher->where('status', 'InStock')->count()}}</li>
                        </ul>
                    </div>
                @endforeach
                <div class="col-md-3 main-btn">
                    <a href="javascript:;" title="@lang('labels.add_new_voucher_type')" data-toggle="modal" data-target="#addNewVoucherTypeModal">
                        <div class="home-btn" style="background-color: #afeeee; border: 5px solid #9dd6d6;">
                            <span class="fa fa-plus"></span>
                        </div>
                    </a>
                </div>
            @endif
        </div>
        </div>
    </section>

    <!-- Modal -->
    <div class="modal fade" id="addNewVoucherTypeModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.add_new_voucher_type')</h4>
                </div>
                {!! Form::open(['method' => 'POST', 'action' => 'VoucherController@store_voucher_type']) !!}
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-horizontal">
                                <div class="form-group">
                                    {!! Form::label('voucher_type', trans('labels.voucher_type').': ', ['class' => 'control-label col-md-3']) !!}
                                    <div class="col-md-4">
                                        {!! Form::text('voucher_type', null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('description', trans('labels.description').': ', ['class' => 'control-label col-md-3']) !!}
                                    <div class="col-md-6">
                                        {!! Form::textarea('description', null, ['class' => 'form-control', 'rows' => '4']) !!}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('labels.save')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('other-script')
@endsection