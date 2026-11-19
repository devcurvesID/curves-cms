@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('bootstrap-toggle/css/bootstrap-toggle.min.css')}}" rel="stylesheet">

    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <style>
        .home-btn-title {
            font-family: 'Alegreya Sans', sans-serif;
        }
    </style>

    <style>
        .toggle.ios, .toggle-on.ios, .toggle-off.ios { border-radius: 20px; }
        .toggle.ios .toggle-handle { border-radius: 20px; }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/access-menu/club/'.$staff->club_id)}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.access_menu_setting')</h2>
                </center>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12">
                <table>
                    <tr>
                        <td><span class="fa fa-female"></span></td>
                        <td>:</td>
                        <td>{{$staff->name}}</td>
                    </tr>
                    <tr>
                        <td><span class="fa fa-briefcase"></span></td>
                        <td>:</td>
                        <td>{{$staff->user_role->role}}</td>
                    </tr>
                    <tr>
                        <td><span class="fa fa-building"></span></td>
                        <td>:</td>
                        <td>{{$staff->user_club->club_name}}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-12">
                {!! Form::open(['method' => 'PATCH', 'action' => ['AccessMenuController@update_staff_access_setting', $staff->id]]) !!}
                <div class="form-group">
                    @foreach($access_menu_list as $access)
                        @php($chosen = 0)
                        <div class="checkbox" title="{{$access->key}}">
                            <label>
                                @if(count($staff->user_accessmenu) > 0)
                                    @foreach($staff->user_accessmenu as $chosen_key)
                                        @if($access->id == $chosen_key->id)
                                            @php($chosen = 1)
                                        @endif
                                    @endforeach
                                @endif
                                @if($chosen == '1')
                                    {!! Form::checkbox('access[]', $access->id, true, ['data-toggle' => 'toggle', 'data-size' => 'normal', 'data-onstyle' => 'success', 'data-style' => 'ios']) !!} {{$access->menu_description}}
                                @elseif($chosen == '0')
                                    {!! Form::checkbox('access[]', $access->id, false, ['data-toggle' => 'toggle', 'data-size' => 'normal', 'data-onstyle' => 'success', 'data-style' => 'ios']) !!} {{$access->menu_description}}
                                @endif
                            </label>
                        </div>
                    @endforeach
                </div>
                <div class="form-horizontal">
                    <div class="form-group">
                        <div class="col-md-10">
                            {!! Form::button('<i class="fa fa-save"></i> '.trans('labels.save_changes'), ['type' => 'submit', 'class' => 'btn btn-primary pull-right']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('other-script')
    <script src="{{asset('bootstrap-toggle/js/bootstrap-toggle.min.js')}}"></script>
@endsection