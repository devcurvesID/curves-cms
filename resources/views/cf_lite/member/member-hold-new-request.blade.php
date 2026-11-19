@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <style>
        .home-btn-title {
            font-family: 'Alegreya Sans', sans-serif;
        }

        .bottom-right {
            background: transparent;
            border: 5px solid transparent;
            border-image: linear-gradient(-90deg, #800000 0%, #ffeb7f 45%, #ffffff 50%, #ffeb7f 55%, #800000 100%);
            border-image-slice: 1;

            padding: 15px;
        }

        .list-view {
            margin-top: 15px;
            padding: 10px;
        }

        .list-view:hover {
            background-color: rgba(0, 0, 0, .1);
            cursor: pointer;
        }

        .photo-img {
            width: 75px;
            height: 75px;
            border-radius: 50%;
        }

        .result-container {
            max-height: 220px;
            overflow-y: scroll;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/member-hold')}}" style="left: 25px; top: 5px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.new_member_hold_request')</h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">@lang('labels.request_form')</div>
                        <div class="panel-body">
                            {!! Form::open(['method' => 'POST', 'action' => 'MemberHoldController@store_new_member_hold_request']) !!}
                            <div class="form-horizontal">
                                <div class="form-group">
                                    {!! Form::label('member_name', trans('labels.member_name').': ', ['class' => 'control-label col-sm-4']) !!}
                                    <div class="col-md-6">
                                        <p class="form-control-static" id="member-name"
                                           style="font-weight: bold; color: deeppink;"></p>
                                        {!! Form::hidden('member_id', null) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('member_hold_reason', trans('labels.member_hold_reason').': ', ['class' => 'control-label col-sm-4']) !!}
                                    <div class="col-md-6">
                                        {!! Form::select('member_hold_reason', $reasons, null, ['class' => 'form-control', 'placeholder' => '== '.trans('labels.choose_reason').' ==']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('from_date', trans('labels.from_date').': ', ['class' => 'control-label col-sm-4']) !!}
                                    <div class="col-md-6">
                                        {!! Form::date('from_date', \Carbon\Carbon::today(), ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('thru_date', trans('labels.thru_date').': ', ['class' => 'control-label col-sm-4']) !!}
                                    <div class="col-md-6">
                                        {!! Form::date('thru_date', null, ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    {!! Form::label('member_hold_note', trans('labels.note').': ', ['class' => 'control-label col-sm-4']) !!}
                                    <div class="col-md-6">
                                        {!! Form::textarea('member_hold_note', null, ['class' => 'form-control', 'rows' => '3']) !!}
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-10">
                                        {!! Form::submit(trans('labels.submit'), ['class' => 'btn btn-primary pull-right']) !!}
                                    </div>
                                </div>
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-default">
                        <div class="panel-heading">@lang('labels.search_member')</div>
                        <div class="panel-body">
                            <div class="bottom-right">
                                {!! Form::text('search_member', null, ['class' => 'form-control', 'placeholder' => trans('labels.search_member')]) !!}
                                <div class="row">
                                    <div class="result-container"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('other-script')
    <script>
        $(document).ready(function () {
            var url = "{{url('cf-lite')}}/";

            $('input[name="search_member"]').on('keyup', function () {
                if ($('input[name="search_member"]').val() == '') {
                    return false;
                }

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    member_name: $('input[name="search_member"]').val(),
                    club_id: '{{\Illuminate\Support\Facades\Auth::user()->club_id}}'
                };

                var type = 'POST';
                var my_url = url + 'get-member-data';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        $('.result-container').empty();
                        var innerHTML = "";
                        var list = $('.result-container');
                        for(var i = 0, member; member = data.members[i]; i++ ){
                            innerHTML += "<div class='col-xs-12'>";
                            innerHTML += "<div class='list-view' id='list-view-" + member.id + "'>";
                            innerHTML += "<input type='hidden' value='" + member.name + "' name='name-user-" + member.id + "'>";
                            innerHTML += "<div class='row'>";
                            innerHTML += "<div class='col-xs-3'>";
                            if (member.photo != null) {
                                innerHTML += "<img class='photo-img' src='{{asset('images/member_photos')}}/" + member.photo + "'>";
                            } else {
                                innerHTML += "<img class='photo-img' src='{{asset('images/img.png')}}'>";
                            }
                            innerHTML += "</div>";
                            innerHTML += "<div class='col-xs-9'>";
                            innerHTML += "<h4>" + member.name + " <small style='display: block'>" + member.phone + "</small></h4>"
                            innerHTML += "</div>";
                            innerHTML += "</div>";
                            innerHTML += "</div>";
                            innerHTML += "</div>";
                        }

                        list.append(innerHTML);
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });
        });
    </script>
    <script>
        $('.result-container').on('click', '.list-view', function () {
            var id = $(this).attr('id').split('-').pop();
            $('input[name="member_id"]').val(id);
            $('#member-name').text($('input[name="name-user-' + id + '"]').val());
        });
    </script>
@endsection