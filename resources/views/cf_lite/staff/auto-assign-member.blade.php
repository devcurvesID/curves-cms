@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
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

        .select2 {
            width: 25% !important;
            float: left;
        }

        .card {
            position: relative;
            display: -ms-flexbox;
            display: flex;
            -ms-flex-direction: column;
            flex-direction: column;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background-clip: border-box;
            border: 1px solid rgba(0,0,0,.125);
            border-radius: .25rem;
            margin-bottom: 25px;
        }

        .card:hover {
            background-color: rgba(176, 127, 183, 0.2);
        }

        .card hr {
            border-color: rebeccapurple;
        }

        .card-img-top {
            object-fit: cover;
            border-top-left-radius: calc(.25rem - 1px);
            border-top-right-radius: calc(.25rem - 1px);
        }

        .card-body {
            -ms-flex: 1 1 auto;
            flex: 1 1 auto;
            padding: 1.25rem;
        }

        .card-title {
            margin-bottom: .75rem;
            font-weight: 600;
        }

        .card-text {
            color: rebeccapurple;
        }

        .card-text .label-success {
            font-size: 12px;
            background-color: rebeccapurple;
        }

        .checkbox label {
            color: rebeccapurple;
            font-size: 15px;
            font-weight: 600;
        }

        .purple-btn {
            border-color: rebeccapurple;
            color: rebeccapurple;
        }

        .purple-btn:hover {
            border-color: rebeccapurple;
            background-color: rebeccapurple;
            color: whitesmoke;
        }

        .total-member {
            font-size: 25px;
            font-weight: 600;
        }

    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/other-settings')}}" style="left: 25px; top: 5px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.auto_assign_member')</h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="row">
                    {!! Form::open(['method' => 'GET', 'id' => 'form-change-club']) !!}
                    <div class="form-group">
                        {!! Form::select('club', $clubs, $club->id, ['class' => 'form-control select2', 'placeholder' => trans('labels.choose_club')]) !!}
                        <span class="pull-right" style="font-size: 20px; font-weight: 600;">@lang('labels.staff_list'): {{$club->club_name}}</span>
                    </div>
                    {!! Form::close() !!}
                </div>

                {!! Form::open(['method' => 'POST', 'action' => 'SettingController@generate_auto_assign_member', 'id' => 'form-generate-auto']) !!}
                {!! Form::hidden('chosen_club', $club->id) !!}
                <div class="row">
                    <hr>
                    @if(($staffs != null) && (count($staffs) > 0))
                        @foreach($staffs as $staff)
                            <div class="col-sm-3">
                                <div class="card" id="card-{{$staff->id}}">
                                    @if(!empty($staff->photo))
                                        <img class="card-img-top" alt="100%x180" style="height: 180px; width: 100%; display: block;" src="{{asset('images/member_photos/'.$staff->photo)}}" data-holder-rendered="true">
                                    @else
                                        <img class="card-img-top" alt="100%x180" style="height: 180px; width: 100%; display: block;" src="{{asset('images/img.png')}}" data-holder-rendered="true">
                                    @endif
                                    <div class="card-body">
                                        <a href="{{url('cf-lite/auto-assign-member/'.$staff->id)}}">
                                            <h5 class="card-title">{{$staff->name}}</h5>
                                        </a>
                                        <p class="card-text"><span class="label label-success">{{$staff->role}}</span></p>
                                        <hr style="margin-bottom: 5px;">
                                        <p class="card-text" style="z-index: 99;"><span class="fa fa-group" title="@lang('labels.assigned_member')"></span> : <span class="total-member">{{$staff->total_member}}</span> </p>
                                        <div class="form-group card-select">
                                            <div class="checkbox">
                                                <label>
                                                    {!! Form::checkbox('staff['.$staff->id.']', $staff->role_id, false, ['id' => 'check-'.$staff->id]) !!} @lang('labels.choose')
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        @lang('labels.no_staff')
                    @endif
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <button type="button" class="btn btn-lg btn-default center-block purple-btn" id="auto-assign-btn">@lang('labels.generate_auto_assign')</button>
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </section>
@endsection

@section('other-script')
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('select[name="club"]').select2();

            $('.card-select').on('click', function () {
                if ($('#check-' + $(this).attr('id').split('-').pop()).prop('checked') === true) {
                    $('#check-' + $(this).attr('id').split('-').pop()).prop('checked', false);
                } else {
                    $('#check-' + $(this).attr('id').split('-').pop()).prop('checked', true);
                }
            });

            $('select[name="club"]').on('change', function () {
                $('#form-change-club').submit();
            });

            $('#auto-assign-btn').on('click', function () {
                $('#form-generate-auto').submit();
            });
        });
    </script>
@endsection