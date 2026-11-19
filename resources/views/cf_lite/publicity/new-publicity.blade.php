@extends('cf_lite.layouts.staff-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.css" rel="stylesheet">

    <style>
        #preview {
            max-height: 250px;
            border: 1px solid black;
            border-radius: 5px;
            object-fit: cover;
            margin-bottom: 10px;
            cursor: pointer;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-md-12">
            <a href="{{url('cf-lite/publicity')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.new_publicity')</h2>
            </center>
        </div>
        {!! Form::open(['action' => 'PublicityController@store_publicity', 'method' => 'post', 'enctype' => 'multipart/form-data']) !!}
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="form-group">
                        <div class="col-md-2">
                            {!! Form::label('photo', trans('labels.photo'), ['class' => 'control-label']) !!}
                        </div>
                        <div class="col-md-10">
                            <div style="margin-bottom: 10px;">
                                <input id="photo" type="file" accept="image/*" name="photo" style="display: none;">
                            </div>
                            <img src="{{ asset('images/sample.png') }}" id="preview" alt="image preview">
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('headline', trans('labels.headline'), ['class' => 'col-md-2 control-label']) !!}
                        <div class="col-md-10">
                            {!! Form::text('headline', null, ['class' => 'form-control', 'style' => 'width: 70%; margin-bottom: 10px;']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        {!! Form::label('date', trans('labels.date'), ['class' => 'col-md-2 control-label']) !!}
                        <div class="col-md-5">
                            {!! Form::date('from_date', null, ['class' => 'form-control', 'style' => 'margin-bottom: 10px;']) !!}
                        </div>
                        <div class="col-md-5">
                            {!! Form::date('thru_date', null, ['class' => 'form-control', 'style' => 'margin-bottom: 10px;']) !!}
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-2">
                            {!! Form::label('note', trans('labels.content'), ['class' => 'control-label']) !!}
                        </div>
                        <div class="col-md-10">
                            <textarea name="note" class="form-control summernote"></textarea>
                        </div>
                    </div>
                    <div class="form-group col-md-12">
                        {!! Form::checkbox('status') !!} @lang('labels.publish')
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::submit(trans('labels.save'), ['class' => 'pull-right btn btn-primary btn-lg']) !!}
        </div>
    </div>
    {!! Form::close() !!}

@endsection

@section('other-script')
    <script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.js"></script>
    <script>
        $(document).ready(function () {
            $('.summernote').summernote({
                height: 500,
            });

            $('#preview').on('click', function () {
                $('#photo').click();
            });

            $('#photo').on('change', function (e) {
                var oFReader = new FileReader();
                oFReader.readAsDataURL(document.getElementById("photo").files[0]);
                oFReader.onload = function(oFREvent) {
                    document.getElementById("preview").src = oFREvent.target.result;
                };
            });
        });
    </script>
@endsection