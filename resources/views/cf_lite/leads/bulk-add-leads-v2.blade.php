@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <style>
        .borderless-input {
            width: 100%;
            border: none;
        }

        .circle-medium{
            width: 200px;
            height: 200px;
            border-radius: 50%;
            padding-top: 50px;
        }

        .select2 {
            width: 30% !important;
            float: right;
        }

        /*#staff {*/
            /*width: 100% !important;*/
            /*float: right;*/
            /*color: red;*/
        /*}*/

        .page-title {
            margin-top: 0px;
        }

        .select2-container--default .select2-selection--single{
            background-color: #ffffff;
            color: #ff1493;
            border: 1px solid #ff1493;
        }

        .select2-container--default .select2-selection--single:hover .select2-selection__rendered {
            color: #ff1493;
        }

        .select2-container--default .select2-selection--single:hover {
            border: 1px solid #ff1493;
        }

        .btn-primary{
            background-color: #ff1493;
            color: #ffffff;
            border-color: #ff1493;
        }

        .btn-primary:hover {
            color: #ff1493;
            background-color: transparent;
            border-color: #ff1493;
        }

        .list{
            padding-left: 0px;
            list-style: none;
        }
        .list li{
            text-align: left;
            padding: 0px;
        }
        .list li a{
            color: black;
            font-weight: bold;
        }

        .list li:hover a{
            color: deeppink;
            cursor: pointer;
            text-decoration: none;
            text-transform: none;
        }

        .box{
            height: 210px;
            background-color: pink;
            padding: 5px;
        }

        .box h4{
            text-align: center;
            font-weight: bold;
            font-size: 16px;
            padding-bottom: 5px;
            border-bottom: 2px solid purple;
        }

        .box div{
            height: 150px;
            overflow-y: scroll;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.error-message')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <left>
                <a href="{{url('cf-lite/lead')}}" style="left: 25px; top: 5px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            </left>
            <center>
                <h2 class="page-title">@lang('labels.add') @lang('labels.lead')</h2>
            </center>
        </div>
        <div class="col-md-12">
            {!! Form::open(['method' => 'POST', 'action' => 'LeadController@store_bulk_leads_v2']) !!}
            <button type="button" class="btn btn-primary pull-right" id="btn-add-more-row" title="@lang('labels.add') @lang('labels.new_row')" style="margin-bottom: 20px;"><span class="glyphicon glyphicon-plus"></span></button>

            <div class="col-md-4" style="margin-top: -30px;">
                <div class="box" style="height: 150px; margin-bottom: 10px;">
                    <h4>@lang('labels.number_of_lead_coach')</h4>
                    <div style="height: 90px;">
                        <ul class="list">
                            @foreach($coach as $co)
                                <li class="list-group-item">
                                    <a role="button" class="col-sm-6">{{$co->name}}</a>
                                    <a role="button">: {{$co->status}}</a>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <table class="table table-bordered" style="background-color: #fff;">
                <thead>
                <tr>
                    @php
                        if (Session::get('lead_type') == 'regular'):
                            $source_text = trans('labels.source');
                        elseif (Session::get('lead_type') == 'br'):
                            $source_text = trans('labels.buddy_referral');
                        endif
                    @endphp
                    @if(Session::get('lead_type') == 'regular')
                    <td colspan="7">
                    @else
                    <td colspan="6">
                    @endif
                        {!! Form::select('sources', $sources, null, ['class' => 'form-control pull-right', 'id' => 'sources', 'placeholder' => trans('labels.choose').' '.$source_text]) !!}
                    </td>
                </tr>
                <tr>
                    <th>@lang('labels.indexing')</th>
                    <th>@lang('labels.fullname')</th>
                    <th>@lang('labels.phone')</th>
                    <th>@lang('labels.staff')</th>
                    <th>@lang('labels.date')</th>
                    <th>@lang('labels.time')</th>
                    @if(Session::get('lead_type') == 'regular')
                    <th>@lang('labels.source_detail')</th>
                    @endif
                </tr>
                </thead>
                <tbody id="bulk-lead-table-body">
                @php($counter = 0)
                @for($i = 1; $i <= 10; $i++)
                    <tr>
                        <td>{{$i}}</td>
                        <td>{!! Form::text('fullname['.$i.']', null, ['class' => 'borderless-input form-control']) !!}</td>
                        <td>{!! Form::text('phone['.$i.']', null, ['class' => 'borderless-input form-control']) !!}</td>
                        <td>{!! Form::select('staff['.$i.']', $staff_name, null, ['class' => 'form-control', 'id' => 'staff', 'placeholder' => trans('labels.choose_staff')]) !!}</td>
                        <td>{!! Form::date('fa_date['.$i.']', null, ['class' => 'borderless-input form-control app-date']) !!}</td>
                        <td>{!! Form::text('fa_hour['.$i.']', null, ['class' => 'borderless-input  form-control time', 'id' => 'time-'.$counter]) !!}</td>
                        @if(Session::get('lead_type') == 'regular')
                        <td>{!! Form::text('source_detail['.$i.']', null, ['class' => 'borderless-input form-control']) !!}</td>
                        @endif
                    </tr>
                    @php($counter++)
                @endfor
                </tbody>
            </table>
            {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary pull-right']) !!}
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('other-script')
    <script src="https://code.jquery.com/jquery-1.7.2.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.maskedinput/1.4.1/jquery.maskedinput.js"></script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script>
            $('.time').mask("99:99", {placeholder:"--:--"});
            $('.time').on('keyup', function(){
                $id = $(this).attr('id').split('-').pop();
                $jm = $('#time-'+$id).val().toString().substr(0,2);
                $mnt = $('#time-'+$id).val().toString().substr(3,2);

                $jmInt = parseInt($('#time-'+$id).val().toString().substr(0,2));
                $mntInt = parseInt($('#time-'+$id).val().toString().substr(3,2));

                if($mnt[1] != '-'){ //berarti ud ngisi 4 angka
                    if($jmInt > 19) $jm = '19';
                    else if($jmInt < 9) $jm = '09';

                    if($jm == '19') $mnt = '00';
                    else if($mntInt >= 0 && $mntInt <= 29) $mnt = '00';
                    else $mnt = '30';

                    $('#time-'+$id).val($jm + ':' + $mnt);
                }
            });
    </script>
    <script type="text/javascript">
        $('#sources').select2();
        // $('#staff').select2();
    </script>
    <script>
        function cekWaktu($id) {
            $jm = $('#time-'+$id).val().toString().substr(0,2);
            $mnt = $('#time-'+$id).val().toString().substr(3,2);

            $jmInt = parseInt($('#time-'+$id).val().toString().substr(0,2));
            $mntInt = parseInt($('#time-'+$id).val().toString().substr(3,2));

            if($mnt[1] != '-'){ //berarti ud ngisi 4 angka
                if($jmInt > 19) $jm = '19';
                else if($jmInt < 9) $jm = '09';

                if($jm == '19') $mnt = '00';
                else if($mntInt >= 0 && $mntInt <= 29) $mnt = '00';
                else $mnt = '30';

                $('#time-'+$id).val($jm + ':' + $mnt);
            }
        }

        var counter = parseInt(10);
        $('#btn-add-more-row').on('click', function() {
            counter = counter + 1;
            var html = '<tr>' +
                '<td>' + counter + '</td>' +
                '<td><input type="text" name="fullname[' + counter + ']" class="borderless-input form-control"></td>' +
                '<td><input type="text" name="phone[' + counter + ']" class="borderless-input form-control"></td>' +
                '<td>' +
                    '<select name="staff[' + counter + ']" class="form-control">' +
                        '<option value="">@lang('labels.choose_staff')</option>' +
                    @foreach($staff_name as $staff)
                        '<option value="{{$staff}}">{{$staff}}</option>' +
                    @endforeach
                    '</select>' +
                '</td>' +
                '<td><input type="date" name="fa_date[' + counter + ']" class="borderless-input form-control"></td>' +
                '<td><input type="text" name="fa_hour[' + counter + ']" class="borderless-input form-control time" id="time-'+counter+'" onkeyup="cekWaktu('+counter+')"></td>' +
                '</tr>';
            $('#bulk-lead-table-body').append(html);
            $('.time').mask("99:99", {placeholder:"--:--"});
        });
    </script>
@endsection