@extends('cf_back.layouts.master-dashboard')

@section('title', 'Member Appointment')

@section('other-style')
@endsection

@section('content')
    <div class="container">
        <div class="my-section">
            <div class="section-title">
                <center>
                    <h1 class="page-title">@lang('labels.leads_appointment_list')</h1>
                </center>
            </div>

            @include('cf_back.flash-message')
            @include('cf_back.error-message')

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <div class="col-xs-10">
                                <div class="form-group">
                                    <div class="col-xs-8">
                                        {!! Form::label('date', trans('labels.date'), ['class' => 'control-label']) !!}
                                        <div class="input-group">
                                            <input type="text" class="date form-control"/>
                                            <a id="filter-date" class="input-group-addon btn btn-default"><i
                                                        class="fa fa-search"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <meta name="_token" content="{!! csrf_token() !!}"/>
    <script src={{asset("js/jquery.dataTables.min.js")}}></script>
    <script src={{asset("js/dataTables.bootstrap.min.js")}}></script>
    <script src={{asset("vendors/moment/min/moment.min.js")}}></script>
    <script src={{asset("vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js")}}></script>
    <script>
        $('#data-table').dataTable();
        $('.date').datetimepicker({
            format: 'YYYY-MM-DD',
            defaultDate: '<?php echo date('Y m d');?>',
        });

        $('#filter-date').on('click', function () {
            var x = $('.date').val();
            if(x =="") return false;
            window.location = "{{url('cf-back/lead/date')}}/" + x;
        });
    </script>
@endsection
