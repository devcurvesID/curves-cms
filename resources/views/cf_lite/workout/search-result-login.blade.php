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
    <a href={{url('cf-lite/dashboard')}} class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px; margin-top: 30px;s" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
    <div class="manual-section">
        <div class="filter" style="padding: 30px; border-bottom: 3px double black">
            {!! Form::open(['method' => 'GET', 'action' => ['WorkoutController@show_result_login_member']]) !!}
            <div class="row">
                <div class="col-sm-2">
                    Cari Member :
                </div>
                <div class="col-sm-4">
                    {!! Form::text('name',null,['class' => 'form-control','placeholder' => 'By Name','onkeydown'=>'if (event.keyCode == 13) { this.form.submit(); return false; }']) !!}
                </div>
                <div class="col-sm-4">
                    {!! Form::text('keytag',null,['class' => 'form-control','placeholder' => 'By Keytag','onkeydown'=>'if (event.keyCode == 13) { this.form.submit(); return false; }']) !!}
                </div>
                <div class="col-sm-2">
                    {!! Form::submit(@trans('labels.search'),['class'=>'btn btn-primary']) !!}
                </div>
            </div>
            {!! Form::close() !!}
        </div>

        <div class="result-container">
            <div class="row">
                @if(isset($_GET['name']))
                    @if(count($members) > 0)
                        @foreach($members as $x)
                            <a href="{{url('cf-lite/manual-login/'.$x->id)}}">
                                <div class="col-sm-6">
                                    <div class="member-list-card col-xs-12" data-id="{{$x->id}}" data-value="{{$x->name}}">
                                        <div class="col-xs-4">
                                            @if(!$x->user_memberpersonal)
                                                <center>
                                                    <div class="border-circle"
                                                         style="width: 100px; height: 100px; border-radius: 50%; border: 3px solid black; padding: 15px;">
                                                        <i class="fa fa-user" style="font-size: 63px;"></i>
                                                    </div>
                                                </center>
                                            @else
                                                @if($x->user_memberpersonal->photo)
                                                    <div class="member-photo"
                                                         style="background-image: url('{{asset('images/member_photos/' . $x->user_memberpersonal->photo)}}')">

                                                    </div>
                                                @else
                                                    <center>
                                                        <div class="border-circle"
                                                             style="width: 100px; height: 100px; border-radius: 50%; border: 3px solid black; padding: 15px;">
                                                            <i class="fa fa-user" style="font-size: 63px;"></i>
                                                        </div>
                                                    </center>
                                                @endif
                                            @endif
                                        </div>
                                        <div class="col-xs-8">
                                            <h4>{{$x->name}} #{{$x->user_memberpersonal->key_tag_id}}</h4>
                                            <hr/>
                                            <p>{{$x->email}}</p>
                                        </div>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    @else
                        <h2>@lang('labels.member_not_found')</h2>
                    @endif
                @endif
            </div>
        </div>
    </div>

    <!-- <button id="openpopup" type="button" class="btn btn-info btn-lg hidden" data-toggle="modal" data-target="#myModal">
        Open Modal
    </button> -->

    <!-- Modal -->
    <!-- <div class="modal fade" id="myModal" role="dialog">
        <div class="modal-dialog">

            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">@lang('labels.manual_login')</h4>
                </div>
                <div class="modal-body" id="modal-form-container">
                    {{form::open()}}
                    <input type="hidden" name="member_id" id="temp_member_id">
                    {{form::close()}}
                    <div>
                        <h4>@lang('labels.member') : <span class="display-name"></span></h4>
                        <h4>@lang('labels.date') : <?php //echo date('d F Y')?></h4>
                        @lang('messages.manual_wo_message')
                    </div>
                </div>
                <div class="modal-footer">
                    <a class="btn btn-danger">@lang('labels.no')</a>
                    <a id="the_submitz" class="btn btn-success">@lang('labels.yes')</a>
                </div>
            </div>

        </div>
    </div> -->
@endsection

@section('other-script')
    <script>
        $('.member-list-card').on('click', function () {
            var id = $(this).attr('data-id');
            var name = $(this).attr('data-value');

            $('.display-name').text(name);
            $('#temp_member_id').val(id);

            $('#openpopup').click();
        });

        $('#the_submitz').on('click', function () {
            $('#modal-form-container form').submit();
        });
    </script>
@endsection