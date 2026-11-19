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

        ul { list-style-type: none; margin: 0; padding: 0; margin-bottom: 10px; }
        #sortable li { margin: 5px; padding: 5px; border-radius: 15px; cursor: crosshair; }

        .draggable-card {
            border: 1px solid rgb(176, 127, 183);
            background-color: rgba(176, 127, 183, 0.2);
        }

        .sequence-text {
            font-size: 30px;
            color: rebeccapurple;
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
                    <h2 class="page-title">@lang('labels.coach_sequence')</h2>
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
                <hr>

                {!! Form::open(['method' => 'POST', 'action' => 'SettingController@update_coach_sequence', 'id' => 'form-coach-sequence']) !!}
                {!! Form::hidden('chosen_club', $club->id) !!}
                <div class="row">
                    <div class="col-sm-6">
                        <ul id="sortable">
                            @if(($staffs != null) && (count($staffs) > 0))
                                @foreach($staffs as $staff)
                                    <li class="draggable-card" id="card-{{$staff->id}}">
                                        <div class="media">
                                            <div class="media-left">
                                                @if(!empty($staff->photo))
                                                    <img class="media-object" alt="100%x180" style="height: 140px; width: 140px; display: block; object-fit: cover;" src="{{asset('images/member_photos/'.$staff->photo)}}" data-holder-rendered="true">
                                                @else
                                                    <img class="media-object" alt="100%x180" style="height: 140px; width: 140px; display: block; object-fit: cover;" src="{{asset('images/img.png')}}" data-holder-rendered="true">
                                                @endif
                                            </div>
                                            <div class="media-body">
                                                <h4 class="media-heading">{{$staff->name}}</h4>
                                                <p><span class="label label-success">{{$staff->role}}</span></p>
                                                <p>@lang('labels.queueu'): <span class="sequence-text">{{$staff->sequence}}</span></p>
                                                @if(!empty($staff->sequence))
                                                    <label>
                                                        {!! Form::checkbox('staff['.$staff->id.']', $staff->id, true, ['id' => 'check-'.$staff->id]) !!} @lang('labels.choose')
                                                    </label>
                                                    {!! Form::hidden('seq['.$staff->id.']', $staff->sequence, ['id' => 'sequence-'.$staff->id]) !!}
                                                @else
                                                    <label>
                                                        {!! Form::checkbox('staff['.$staff->id.']', $staff->id, false, ['id' => 'check-'.$staff->id]) !!} @lang('labels.choose')
                                                    </label>
                                                    {!! Form::hidden('seq['.$staff->id.']', '', ['id' => 'sequence-'.$staff->id]) !!}
                                                @endif
                                                {!! Form::hidden('remove['.$staff->id.']', 'false', ['id' => 'remove-'.$staff->id]) !!}
                                            </div>
                                        </div>
                                    </li>
                                @endforeach
                            @else
                                @lang('labels.no_staff')
                            @endif
                        </ul>
                    </div>
                </div>
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <button type="button" class="btn btn-lg btn-default center-block purple-btn" id="save-queueu-btn">@lang('labels.save_queueu_changes')</button>
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
    <script src="{{asset('js/jquery-ui.min.js')}}"></script>
    <script>
        $( function() {
            $( "#sortable" ).sortable({
                revert: true
            });
            $( "#draggable" ).draggable({
                connectToSortable: "#sortable",
                helper: "clone",
                revert: "invalid"
            });
            $( "ul, li" ).disableSelection();
        } );
    </script>
    <script>
        $(document).ready(function () {
            $('select[name="club"]').select2();

            $('select[name="club"]').on('change', function () {
                $('#form-change-club').submit();
            });

            $('#save-queueu-btn').on('click', function () {
                var counter = 0;
                var remove = 0;
                $('.draggable-card').each(function (index) {
                    var id = $(this).attr('id').split('-').pop();
                    console.log(id);

                    if ($('#check-' + id).prop('checked') === true) {
                        counter++;
                        $('#sequence-' + id).val(counter);
                    } else {
                        remove++;
                        $('#remove-' + id).val('true');
                    }
                });

                if ((counter > 0) || (remove > 0)) {
                    $('#form-coach-sequence').submit();
                } else {
                    alert('@lang('messages.nothing_has_chosen')');
                }
            });
        });
    </script>
@endsection