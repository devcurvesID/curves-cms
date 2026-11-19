@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <style>
        #staff-home {
            padding: 15px;
        }

        .select2 {
            width: 100% !important;
        }
    </style>
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
                        @lang('labels.whatsapp_center') (@lang('labels.no_sale'))
                    </h2>
                </center>
            </div>
        </div>
        {!! Form::open(['method' => 'POST', 'action' => 'WhatsappController@send_nosale_blast', 'files' => true]) !!}
        <div class="row">
            <div class="col-md-6">
                <h4 class="form-control-static">@lang('labels.write_message')</h4>
                <div class="form-group">
                    {!! Form::select('predefine', $predefined_messages, null, ['class' => 'form-control', 'placeholder' => ' =='.trans('labels.choose_template_message').'== ']) !!}
                </div>
                <div class="form-group">
                    {!! Form::textarea('custom_message', null, ['class' => 'form-control', 'rows' => '4']) !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <h4>@lang('labels.send_to')</h4>
                <div class="form-horizontal">
                    <div class="form-group">
                        {!! Form::label('leads', trans('labels.import_excel').': ', ['class' => 'control-label col-sm-3']) !!}
                        <div class="col-sm-3">
                            {!! Form::file('leads') !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('clubs', trans('labels.club').': ', ['class' => 'control-label col-sm-1']) !!}
                        <div class="col-sm-6">
                            {!! Form::select('clubs', $clubs, $club->id, ['class' => 'form-control select2']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('sender', trans('labels.sender').': ', ['class' => 'control-label col-md-2']) !!}
                        <div class="col-md-6">
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
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>

    <script>
        $(document).ready(function () {
            $('.select2').select2();

            $('select[name="clubs"]').on('change', function () {
                var url = "{{url('cf-lite')}}/";
                var club = $(this).val();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    club: club
                };

                var type = 'POST';
                var my_url = url + 'get-whatsapp-club';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);

                        $('input[name="sender"]').val(data.phones);
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });

            $('select[name="predefine"]').on('change', function () {
                var text = $(this).val();
                $('textarea[name="custom_message"]').val(text);
            });
        });
    </script>
@endsection