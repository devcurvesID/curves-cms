@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <div class="manual-section">
        <div class="filter" style="padding: 30px; border-bottom: 3px double black">
            <a href="{{url('cf-lite/dashboard')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a><br/><br/>
            {!! Form::open() !!}
            <div class="row">
                <div class="col-sm-2">
                    @lang('labels.search') @lang('labels.member') :
                </div>
                <div class="col-sm-4">
                    <input type="text" id="text_name" class="form-control" name="search_name" placeholder="By Name">
                </div>
                <div class="col-sm-4">
                    <input type="text" id="text_keytag" class="form-control" name="search_keytag" placeholder="By Keytag">
                </div>
                <div class="col-sm-2">
                    <a id="btn-go" class="btn btn-primary"> <i class="fa fa-search"></i> Cari</a>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('other-script')
    <script>
        $('#btn-go').click(function () {
            var name =  $('#text_name').val();
            if(name == ""){
                name = 0;
            }
            var keytag =  $('#text_keytag').val();
            if(keytag == ""){
                keytag = 0;
            }

            window.location = "{{url('cf-lite/manual-login')}}/"+ name + "/" + keytag;
        });
    </script>
@endsection