@extends('cf_lite.layouts.staff-dashboard-template')

@section('title', 'Laporan Challenge Member '.$club->club_name)

@section('other-style')
    <link href="{{asset('bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">
    <link href="{{asset('select2/dist/css/select2.min.css')}}" rel="stylesheet">
    <style>
        .filter-box{
            padding-bottom: 20px;
            border-bottom: 5px double #414042;
            margin-bottom: 20px;
        }


        .box-menu {
            min-height: 620px;
        }
    </style>
@endsection

@section('content')
    <div class="col-xs-12">
        <left>
            <a href="{{url('cf-lite/laporan')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
        </left>
        <center>
            <h2 class="page-title">@lang('labels.list') @lang('labels.challenge') : {{$club->club_name}}</h2>
        </center>
    </div>
    <div class="col-xs-12">
        <div class="filter-box">
            <div class="row">
                <div class="col-xs-2">
                    Filter :
                </div>
                <div class="col-xs-3">
                    {!! Form::select('challenge', $list_challenge, null, ['class' => 'form-control', 'id' => 'challenge']) !!}
                </div>
                <div class="col-xs-2">
                    <input type="text" value="<?php if (isset($filter1)) {
                        if ($filter1 != "0") {
                            echo $filter1;
                        }
                    } ?>" id="filt-dari" class="form-control filter-date"
                           placeholder="@lang('labels.from_date')"/>
                </div>
                <div class="col-xs-2">
                    <input type="text" value="<?php if (isset($filter2)) {
                        if ($filter2 != "0") {
                            echo $filter2;
                        }
                    } ?>" id="filt-sampai" class="form-control filter-date"
                           placeholder="@lang('labels.thru_date')"/>
                </div>
                <div class="col-xs-2">
                    <a id="btn-filter" class="btn btn-primary"><i class="fa fa-search"></i> @lang('labels.search')
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12">
        <div class="box-result">

        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('fullcalendar/fullcalendar.min.js')}}"></script>
    <script src="{{asset('bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>

    <script src="{{asset('select2/dist/js/select2.js')}}"></script>
    <script>
        $('.filter-date').each(function () {
            $(this).datetimepicker({
                format: 'YYYY-MM-DD',
            });
        });

        $('#challenge').select2();


        $('#btn-filter').on('click', function () {
            var filter1 = $('#filt-dari').val();
            if (filter1 == "") {
                filter1 = 0;
            }

            var filter2 = $('#filt-sampai').val();
            if (filter2 == "") {
                filter2 = 0;
            }
            window.location = "{{url('cf-lite/laporan/challenge')}}" +"/" + $('#challenge').val() + "/" + filter1 + "/" + filter2;
        });
    </script>
@endsection