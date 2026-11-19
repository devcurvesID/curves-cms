@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <style>
        .home-btn-title {
            font-family: 'Alegreya Sans', sans-serif;
        }

        .home-btn span{
            color: #ffffff;
        }

        h3.home-btn-title{
            color: #ffffff;
            margin: 20px 20px 10px 20px;
        }

        .main-btn:hover .home-btn span,
        .main-btn:hover h3.home-btn-title{
            color: #666666;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/appointment/attendance')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.corporation_options')</h2>
                </center>
            </div>
            <div class="col-sm-12">
                <div class="col-md-10 col-md-offset-2">
                    <div class="col-md-5 main-btn" id="add-corporation">
                        <a href=":;" data-toggle="modal" data-target="#addCorporation" class="new-corporation">
                            <div class="home-btn"
                                 style="background-color: #afeecf; border: 5px solid #9dd6ba;">
                                <span class="fa fa-wrench"></span>
                                <h3 class="home-btn-title">@lang('labels.add_new_corporation')</h3>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-5 main-btn">
                        <a href="{{url('cf-lite/corporate/corporation-list')}}">
                            <div class="home-btn"
                                 style="background-color: #afeecf; border: 5px solid #9dd6ba;">
                                <span class="fa fa-wrench"></span>
                                <h3 class="home-btn-title">@lang('labels.add_member_to_corporate')</h3>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
            <!-- Add Modal -->
            <div class="modal fade" id="addCorporation" tabindex="-1" role="dialog">
                <div class="modal-dialog" role="document">
                    {!! Form::open(['method' => 'POST', 'action' => ['CorporationController@store_corporation']]) !!}
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title">@lang('labels.add_new_corporation')</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-inline">
                                        <div class="form-group">
                                            {!! Form::label('name', trans('labels.corporation_name').': ', ['class' => 'control-label']) !!}
                                            {!! Form::text('name', '', ['class' => 'form-control']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('address', trans('labels.corporation_address').': ', ['class' => 'control-label']) !!}
                                            {!! Form::text('address','', ['class' => 'form-control']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('phone_number', trans('labels.corporation_phone_number').': ', ['class' => 'control-label']) !!}
                                            {!! Form::text('phone_number', '', ['class' => 'form-control']) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('email', trans('labels.corporation_email').': ', ['class' => 'control-label']) !!}
                                            {!! Form::text('email', '', ['class' => 'form-control']) !!}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default"
                                    data-dismiss="modal">@lang('labels.close')</button>
                            <button type="submit" class="btn btn-primary">@lang('labels.save')</button>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
            <!-- End Add Modal -->
        </div>
    </section>
@endsection

@section('other-script')
<script type="text/javascript">
    $(document).ready(function () {
        $('#add-corporation').on('click', '.new-corporation', function () {

                $('input[name="name"]').val('');
                $('textarea[name="address"]').val('');
                $('input[name="phone_number"]').val('');
                $('input[name="email"]').val('');
            });
    });
</script>
@endsection