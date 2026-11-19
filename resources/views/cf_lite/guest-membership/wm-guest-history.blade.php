@extends('cf_lite.layouts.staff-template')

@section('other-style')

    <style>

        .profile{
            margin-top: 17px;
        }

        .profile table td:first-child{
            color: #ff1493;
        }

        .profile table td{
            height: 30px;
            padding: 10px;
            font-size: 20px;
        }

        .table th,
        .table td:first-child{
            color: #ff1493;
        }

        .table td{
            font-size: 17px;
        }

        .table a{
            text-decoration: none;
        }

    </style>

@endsection

@section('content')
    @include('cf_lite.includes.error-message')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/guest-member/list')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.weigh_n_measure_history')</h2>
            </center>
        </div>
    </div>
    <div class="row">
        <div class="col-md-2 col-md-offset-1">
            <img class="img-circle member-card-img" src="{{asset('images/img.png')}}">
        </div>
        <div class="col-md-9 profile">
            <table>
                <tr>
                    <td><span class="fa fa-female" title="@lang('labels.name')"></span></td>
                    <td>:</td>
                    <td>{{$member->fullname}}</td>
                </tr>
                <tr>
                    <td><span class="fa fa-calendar" title="@lang('labels.joined')"></span></td>
                    <td>:</td>
                    <td>{{date('d F Y', strtotime($member->from_date))}}</td>
                </tr>
                <tr>
                    <td><span class="fa fa-heartbeat" title="@lang('labels.last_wm')"></span></td>
                    <td>:</td>
                    <td>
                        @if(!empty($last_wm->wm_date))
                            {{date('d F Y', strtotime($last_wm->wm_date))}}
                        @else
                            -
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-9 col-md-offset-1">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                    <tr>
                        <th>@lang('labels.indexing')</th>
                        <th>@lang('labels.weigh_n_measure_date')</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @php($indexing = 1)
                    @foreach($wm_list as $wm)
                        <tr>
                            <td>{{$indexing}}</td>
                            <td><a href="{{url('cf-lite/guest-member/guest-weight-measure/'.$wm->id.'/detail')}}">{{date('d F Y', strtotime($wm->wm_date))}}</a></td>
                            <td>
                                {!! Form::open(['method' => 'DELETE' , 'style'=>'display:inline-block' , 'action' => ['GuestMembershipController@delete_gwm' , $wm->id, ]])!!}
                                {!! Form::submit('X',['class' => 'btn btn-danger' , 'onclick' => 'return confirm("Anda yakin akan menghapus data wm ini?")']) !!}
                                {!! Form::close() !!}
                            </td>
                        </tr>
                        @php($indexing++)
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection

@section('other-script')
@endsection