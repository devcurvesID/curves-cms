@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    <style>

        .result-container {
            padding: 30px;
            max-height: 425px;
            overflow-y: scroll;
        }

        .member-list-card {
            border-radius: 15px;
            box-shadow: 5px 5px 8px #888888;
            padding: 20px;

            background: white;
            margin-bottom: 15px;
        }

        .member-photo {
            width: 100px;
            height: 100px;
            background-size: cover;
            background-position: top center;
            border-radius: 50%;
        }

        .member-list-card:hover {
            -webkit-filter: grayscale(0);
            filter: grayscale(0);
            opacity: 1;
            background: linear-gradient(-90deg, #800000 0%, #ffeb7f 35%, #ffffff 40%, #ffeb7f 45%, #800000 100%);
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <a href="{{url('cf-lite/guest-member/list')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px; margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
    <div class="manual-section">
        <div class="filter" style="padding: 30px; border-bottom: 3px double black">
            {!! Form::open(['method' => 'GET']) !!}
            <div class="row">
                <div class="col-sm-2">
                    @lang('labels.find_guest') :
                </div>
                <div class="col-sm-8">
                    {!! Form::text('name',null,['class' => 'form-control','placeholder' => trans('labels.by_name') ,'onkeydown'=>'if (event.keyCode == 13) { this.form.submit(); return false; }']) !!}
                </div>
                <div class="col-sm-2">
                    {!! Form::submit(@trans('labels.search'),['class'=>'btn btn-primary']) !!}
                </div>
            </div>
            {!! Form::close() !!}
        </div>

        <div class="result-container">
            <div class="row">
                @if(count($guests) > 0)
                    @foreach($guests as $x)
                        <a href="{{url('cf-lite/guest-member/'.$x->id.'/past-wo')}}">
                            <div class="col-sm-6">
                                <div class="member-list-card col-xs-12" data-id="{{$x->id}}" data-value="{{$x->fullname}}">
                                    <div class="col-xs-4">
                                        <center>
                                            <div class="border-circle"
                                                 style="width: 100px; height: 100px; border-radius: 50%; border: 3px solid black; padding: 15px;">
                                                <i class="fa fa-user" style="font-size: 63px;"></i>
                                            </div>
                                        </center>
                                    </div>
                                    <div class="col-xs-8">
                                        <h4>{{$x->fullname}}</h4>
                                        <hr/>
                                        <p>{{$x->phone}}</p>
                                    </div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                @else
                    <h2>@lang('labels.guest_not_found')</h2>
                @endif
            </div>
        </div>
    </div>
@endsection

@section('other-script')
@endsection