@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <style>
        .home-btn-title {
            font-family: 'Alegreya Sans', sans-serif;
        }

        .value-base {
            display: block;
        }

        .percent-base {
            display: none;
        }

        .btn-primary,
        .voucher-list-add a,
        #edit-voucher-btn {
            background-color: #ff1493;
            color: #ffffff;
            border-color: #ff1493;
        }

        #edit-voucher-btn{
            margin: 20px 0;
        }

        .btn-primary:hover,
        .voucher-list-add a:hover,
        #edit-voucher-btn:hover {
            color: #ff1493;
            background-color: transparent;
            border-color: #ff1493;
        }

        div.dataTables_wrapper div.dataTables_length select {
            margin: 0 10px;
        }

        table th{
            color: #ff1493;
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
                    <h2 class="page-title">
                        @lang('labels.challenges')
                    </h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                {!! Form::open(['method' => 'GET', 'action' => ['ChallengesController@challenges_list_filter']]) !!}
                <div class="form-group">
                    <label class="checkbox-inline">
                        {!! Form::checkbox('day', 'day') !!} @lang('labels.day')
                    </label>
                    <label class="checkbox-inline">
                        {!! Form::checkbox('workout', 'workout') !!} @lang('labels.workout')
                    </label>
                    <label class="checkbox-inline">
                        {!! Form::checkbox('br', 'br') !!} @lang('labels.br')
                    </label>
                </div>
                <div class="form-group">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="input-group">
                                {!! Form::text('challenge_name', null, ['class' => 'form-control', 'placeholder' => trans('labels.search_challenge')]) !!}
                                <span class="input-group-addon"><span class="fa fa-search"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        {!! Form::submit(trans('labels.apply_filter'), ['class' => 'btn btn-primary']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-10 col-md-offset-1 voucher-list-add" id="add-challenge">
                <a class="btn btn-default pull-right new-challenge" href="javascript:;" data-toggle="modal" data-target="#addNewChallenges"
                   title="@lang('labels.add_new_challenges')"><span class="fa fa-plus"></span> @lang('labels.add_new_challenges')</a>
            </div>
            <div class="col-md-10 col-md-offset-1">
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="data-table">
                                <thead>
                                <tr>
                                    <th>@lang('labels.number')</th>
                                    <th>@lang('labels.challenge')</th>
                                    <th>@lang('labels.type')</th>
                                    <th>@lang('labels.variable')</th>
                                    <th>@lang('labels.variable_target')</th>
                                    <th>@lang('labels.status')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php($indexing = 1)
                                @foreach($challenges as $challenge)
                                    <tr>
                                        <td>{{$indexing}}</td>
                                        <td>
                                            <a role="button" id="edit-challenge-{{$challenge->id}}" class="edit-challenge" data-toggle="modal" data-target="#addNewChallenges"> {{$challenge->challenge}} </a>
                                        </td>
                                        <td>{{$challenge->type}}</td>
                                        <td>{{$challenge->variable}}</td>
                                        <td>
                                            @if($challenge->type == 'day' || $challenge->type == 'workout')
                                                {{$challenge->variable}}x
                                            @elseif($challenge->type == 'br')
                                                {{$challenge->variable}} orang
                                            @endif
                                        </td>
                                        <td>{{$challenge->status}}</td>
                                    </tr>
                                @php($indexing++)
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add Modal -->
        <div class="modal fade" id="addNewChallenges" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                {!! Form::open(['method' => 'PATCH', 'action' => 'ChallengesController@store_challenges', 'files' => 'true']) !!}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">@lang('labels.add_new_challenges')</h4>
                        {{ Form::hidden('addoredit','add')}}
                        {{ Form::hidden('editid',0)}}
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-inline">
                                    <div class="form-group">
                                        {!! Form::label('challenge', trans('labels.challenge').': ', ['class' => 'control-label']) !!}<br/>
                                        {!! Form::text('challenge', null, ['class' => 'form-control']) !!} <br/>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('type', trans('labels.type').':', ['class' => 'control-label']) !!} <br/>
                                            <label class="radio-inline">
                                            {{ Form::radio('type','day',true),['class' => 'col-md-3'] }} @lang('labels.day')
                                            </label>
                                            <label class="radio-inline">
                                            {{ Form::radio('type','workout') ,['class' => 'col-md-3'] }} @lang('labels.workout')
                                            </label>
                                            <label class="radio-inline">
                                            {{ Form::radio('type','br') ,['class' => 'col-md-3']}} @lang('labels.br')
                                            </label><br/>
                                    </div><br/>
                                    <div class="form-group">
                                    {!! Form::label('sequence', trans('labels.sequence').':', ['class' => 'control-label']) !!} <br/>
                                            <label class="radio-inline">
                                            {{ Form::radio('sequence','true',true),['class' => 'col-md-3'] }} @lang('labels.yes')
                                            </label>
                                            <label class="radio-inline">
                                            {{ Form::radio('sequence','false') ,['class' => 'col-md-3'] }} @lang('labels.no')
                                            </label>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('variable', trans('labels.variable').': ', ['class' => 'control-label']) !!} <br/>
                                        {!! Form::text('variable', null, ['min' => '0', 'class' => 'form-control', 'onkeypress' => 'return numberOnlyInput(event)']) !!} <br/>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('variable_target', trans('labels.variable_target').': ', ['class' => 'control-label']) !!} <br/>
                                        {!! Form::text('variable_target', null, ['min' => '0', 'class' => 'form-control', 'onkeypress' => 'return numberOnlyInput(event)']) !!} <br/>
                                    </div>
                                    <div class="form-group">
                                    {!! Form::label('status', trans('labels.status').':', ['class' => 'control-label']) !!} <br/>
                                            <label class="radio-inline">
                                            {{ Form::radio('status','aktif',true),['class' => 'col-md-3'] }} @lang('labels.active')
                                            </label>
                                            <label class="radio-inline">
                                            {{ Form::radio('status','nonaktif') ,['class' => 'col-md-3'] }} @lang('labels.nonactive')
                                            </label>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <img role="button" id="image_preview" class="center-block" src="{{asset('images/img.png')}}" style="width: 90%; height: 320px; object-fit: cover;">
                                <div class="form-group hidden">
                                    {!! Form::file('photo', ['class' => 'file_multi_video']) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal">@lang('labels.close')</button>
                        <button type="submit" class="btn btn-primary" id="save-btn">@lang('labels.save')</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <!-- End Add Modal -->
        <div class="row">
            <div class="col-md-12">
                <div class="paging hiden rom">
                    {{$challenges->links()}}
                </div>
            </div>
        </div>
    </section>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>

    <script>
        $('#data-table').dataTable();
    </script>

    <script type="text/javascript">
        function numberOnlyInput(event){
            var charCode = (event.which) ? event.which : event.keyCode;
            if(charCode > 31 && (charCode < 48 || charCode > 57)){
                return false;
            }
            return true;
        }
    </script>

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

    <script>
        $(document).ready(function () {
            var url = "{{url('cf-lite')}}/";

            $('#data-table').on('click', '.edit-challenge', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    challenge_id: $(this).attr('id').split('-').pop()
                };

                var type = 'POST';
                var my_url = url + 'challenges/challenges-list-ajax';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        $('input[name="challenge"]').val(data.challenge['challenge']);
                        $('input[name="variable"]').val(data.challenge['variable']);
                        $('input[name="variable_target"]').val(data.challenge['variable_target']);
                        $('#save-btn').text('@lang('labels.edit')');
                        $('#addNewChallenges').find(':radio[name=status][value="aktif"]').prop('disabled',false);
                        $('#addNewChallenges').find(':radio[name=status][value="nonaktif"]').prop('disabled',false);
                        $('#addNewChallenges').find(':text[name=variable]').prop('readonly','readonly');
                        $('#addNewChallenges').find(':hidden[name=addoredit]').prop('value','edit');
                        $('#addNewChallenges').find(':hidden[name=editid]').prop('value',data.challenge['id']);

                        if(data.challenge['sequencable'] == 'yes')
                            $('#addNewChallenges').find(':radio[name=sequence][value="true"]').prop('checked',true);
                        else if (data.challenge['sequencable'] == 'no')
                            $('#addNewChallenges').find(':radio[name=sequence][value="false"]').prop('checked',true);

                        if(data.challenge['type'] == 'day')
                            $('#addNewChallenges').find(':radio[name=type][value="day"]').prop('checked',true);
                        else if(data.challenge['type'] == 'workout')
                            $('#addNewChallenges').find(':radio[name=type][value="workout"]').prop('checked',true);
                        else if(data.challenge['type'] == 'br')
                            $('#addNewChallenges').find(':radio[name=type][value="br"]').prop('checked',true);

                        if(data.challenge['status'] == 'aktif')
                            $('#addNewChallenges').find(':radio[name=status][value="aktif"]').prop('checked',true);
                        else if (data.challenge['status'] == 'nonaktif')
                            $('#addNewChallenges').find(':radio[name=status][value="nonaktif"]').prop('checked',true);

                        if(data.challenge['picture'] != null) {
                            var src = '{{ asset('images') }}';
                            $('#image_preview').attr('src', src + '/rewards/' + data.challenge['picture']);
                        }
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });

            $('#add-challenge').on('click', '.new-challenge', function () {

                $('input[name="challenge"]').val('');
                $('#save-btn').text('@lang("labels.save")');
                $('input[name="variable"]').val('');
                $('input[name="variable_target"]').val('');
                $('#addNewChallenges').find(':text[name=variable]').prop('readonly',false);
                $('#addNewChallenges').find(':radio[name=type][value="day"]').prop('checked',true);
                $('#addNewChallenges').find(':radio[name=sequence]').prop('disabled',false);
                $('#addNewChallenges').find(':radio[name=status][value="nonaktif"]').prop('disabled',true);
                $('#addNewChallenges').find(':hidden[name=addoredit]').prop('value','add');
            });

            $('input[name="search_option"]').on('click', function () {
                $('input[name="s"]').val('');
            });

            $('.new-challenge').on('click', function() {
                var src = '{{ asset('images/img.png') }}';
                $('#image_preview').attr('src', src);
            });
        });
    </script>

    <script>
        $('#image_preview').on('click', function () {
            $('.file_multi_video').click();
        });

        $(document).on('change', '.file_multi_video', function (evt) {
            var $source = $('#image_preview');
            $source[0].src = URL.createObjectURL(this.files[0]);
            $source.load();
        });
    </script>
@endsection
