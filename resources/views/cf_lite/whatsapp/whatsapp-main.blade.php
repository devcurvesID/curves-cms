@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <style>
        #staff-home {
            padding: 15px;
        }
    </style>
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/message')}}" style="left: 25px; top: 5px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">
                        @lang('labels.whatsapp_center')
                        <br>
                        <small>({{\Carbon\Carbon::today()->modify('this week')->format('d-m-Y')}} @lang('labels.until') {{\Carbon\Carbon::today()->modify('next week')->subDay()->format('d-m-Y')}})</small>
                    </h2>
                </center>
            </div>
        </div>
        <div class="form-group">
            @php
            $days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
            $n= \Carbon\Carbon::today()->format('w');
            @endphp
            <h3 class="form-control-static">@lang('labels.today'): {{$days[$n]}}, {{\Carbon\Carbon::today()->format('d-m-Y')}}</h3>
        </div>
        {!! Form::open(['method' => 'POST', 'action' => 'WhatsappController@send_message']) !!}
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    {!! Form::label('target', trans('labels.choose_message_type_for').': ', ['class' => 'control-label']) !!}
                    <div class="radio">
                        <label>
                            {!! Form::radio('target', 'wo1', false) !!} @lang('labels.one_time_workout')
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            {!! Form::radio('target', 'wo2', false) !!} @lang('labels.two_times_workout')
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            {!! Form::radio('target', 'wo3', false) !!} @lang('labels.three_times_workout')
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            {!! Form::radio('target', 'birthday', false) !!} @lang('labels.birthday_message')
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            {!! Form::radio('target', 'free', false) !!} @lang('labels.free_message')
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            {!! Form::radio('target', 'cancel', false) !!} @lang('labels.free_message')(@lang('labels.exmember'))
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            {!! Form::radio('target', 'A', false) !!} @lang('labels.flag') A
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            {!! Form::radio('target', 'B', false) !!} @lang('labels.flag') B
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            {!! Form::radio('target', 'C', false) !!} @lang('labels.flag') C
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            {!! Form::radio('target', 'D', false) !!} @lang('labels.flag') D
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            {!! Form::radio('target', 'E', false) !!} @lang('labels.flag') E
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            {!! Form::radio('target', 'expire', false) !!} @lang('labels.membership_expired_soon')
                        </label>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 input-group" id="month">
                            {!! Form::text('month', null, ['class' => 'form-control']) !!}
                            <span class="input-group-addon">
                                <span class="fa fa-calendar"></span>
                            </span>
                            <div class="col-xs-2">
                                <button id="btn-submit-tanggal" type="button" class="btn btn-primary">Filter</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <h4 class="form-control-static">@lang('labels.write_message')</h4>
                {{--<div class="form-group">--}}
                    {{--<div class="radio">--}}
                        {{--<label>--}}
                            {{--{!! Form::radio('message', 'predefine', true) !!} @lang('labels.template_message')--}}
                        {{--</label>--}}
                    {{--</div>--}}
                {{--</div>--}}
                <div class="form-group">
                    {!! Form::select('predefine', $predefined_messages, null, ['class' => 'form-control', 'placeholder' => ' =='.trans('labels.choose_template_message').'== ']) !!}
                </div>
                {{--<hr>--}}
                {{--<div class="form-group">--}}
                    {{--<div class="radio">--}}
                        {{--<label>--}}
                            {{--{!! Form::radio('message', 'free', false) !!} @lang('labels.write_message')--}}
                        {{--</label>--}}
                    {{--</div>--}}
                {{--</div>--}}
                <div class="form-group">
                    {!! Form::textarea('custom_message', null, ['class' => 'form-control', 'rows' => '4']) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h4>@lang('labels.send_to')</h4>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" id="select_all" value="select_all"> @lang('labels.select_all')
                                        </label>
                                    </div>
                                </th>
                                <th>@lang('labels.name')</th>
                                <th>@lang('labels.birthday')</th>
                                <th>@lang('labels.total_workout_this_week')</th>
                            </tr>
                        </thead>
                        <tbody id="wa_table_body"></tbody>
                    </table>
                </div>
                <div class="form-horizontal">
                    <div class="form-group">
                        {!! Form::label('sender', trans('labels.sender').': ', ['class' => 'control-label col-md-1']) !!}
                        <div class="col-md-2">
                            {!! Form::text('sender', \Illuminate\Support\Facades\Auth::user()->user_club->phones, ['class' => 'form-control']) !!}
                            <p class="help-block">*@lang('labels.example'): 081234567890</p>
                        </div>
                    </div>
                </div>
                {!! Form::submit(trans('labels.send_message'), ['class' => 'btn btn-primary']) !!}
            </div>
        </div>
        {!! Form::close() !!}
    </section>
@endsection

@section('other-script')
    <script src="{{asset('js/my_js.js')}}"></script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            /*$('input[name="message"]').on('click', function () {
                if ($(this).val() == 'free') {
                    $('textarea[name="custom_message"]').prop('disabled', false);
                    $('select[name="predefine"]').prop('disabled', true);
                } else if ($(this).val() == 'predefine') {
                    $('select[name="predefine"]').prop('disabled', false);
                    $('textarea[name="custom_message"]').prop('disabled', true);
                }
            });*/
            $('#month').datetimepicker({
               viewMode: 'months',
               format: 'MM-YYYY'
            });

            $('select[name="predefine"]').on('change', function () {
                var text = $(this).val();
                $('textarea[name="custom_message"]').val(text);
            });

            $('input[name="target"]').on('click', function() {
                var url = "{{url('cf-lite')}}/";
                var gate = 0;
                var target = $(this).val();
                var date = $('input[name="month"]').val();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    target: target,
                    month: date,
                };

                var type = 'POST';
                var my_url = url + 'get-whatsapp-target';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        console.log(target);

                        var table = "";
                        $.each(data.members, function (i, item) {
                            console.log(item);
                            console.log(data.date);
                            if (target == 'wo1') {
                                if (item.total_wo >= 1) {
                                    gate = 1;
                                }
                            } else if (target == 'wo2') {
                                if (item.total_wo >= 2) {
                                    gate = 1;
                                }
                            } else if (target == 'wo3') {
                                if (item.total_wo >= 3) {
                                    gate = 1;
                                }
                            } else {
                                gate = 1;
                            }

                            if (gate == 1) {
                                table += '<tr>';
                                table += '<td><input type="checkbox" name="member[' + item.id + ']" value="' + item.id + '" class="send_to"></td>';
                                table += '<td>' + item.name + '</td>';
                                table += '<td>' + item.birthday + '</td>';
                                table += '<td>' + item.total_wo + '<input type="hidden" name="total_wo[' + item.id + ']" value="' + item.total_wo + '"></td>';
                                table += '</tr>';
                            }

                            gate = 0;
                        });

                        $('#wa_table_body').html('');
                        $('#wa_table_body').append(table);
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });

            $('#btn-submit-tanggal').on('click', function() {
                var url = "{{url('cf-lite')}}/";
                var gate = 0;
                var target = 'expire';
                var date = $('input[name="month"]').val();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    target: target,
                    month: date,
                };

                var type = 'POST';
                var my_url = url + 'get-whatsapp-target';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        var table = "";
                        $.each(data.members, function (i, item) {
                            console.log(item);
                            console.log(data.date);
                            if (target == 'wo1') {
                                if (item.total_wo >= 1) {
                                    gate = 1;
                                }
                            } else if (target == 'wo2') {
                                if (item.total_wo >= 2) {
                                    gate = 1;
                                }
                            } else if (target == 'wo3') {
                                if (item.total_wo >= 3) {
                                    gate = 1;
                                }
                            } else {
                                gate = 1;
                            }

                            if (gate == 1) {
                                table += '<tr>';
                                table += '<td><input type="checkbox" name="member[' + item.id + ']" value="' + item.id + '" class="send_to"></td>';
                                table += '<td>' + item.name + '</td>';
                                table += '<td>' + item.birthday + '</td>';
                                table += '<td>' + item.total_wo + '<input type="hidden" name="total_wo[' + item.id + ']" value="' + item.total_wo + '"></td>';
                                table += '</tr>';
                            }

                            gate = 0;
                        });

                        $('#wa_table_body').html('');
                        $('#wa_table_body').append(table);
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });

            $('#select_all').on('click', function () {
                if ($(this).prop('checked') == true) {
                    $('.send_to').prop('checked', true);
                } else {
                    $('.send_to').prop('checked', false);
                }
            });

            $('#wa_table_body').on('click', '.send_to', function () {
                if ($(this).prop('checked') == false) {
                    $('#select_all').prop('checked', false);
                }
            });
        });
    </script>
@endsection