@extends('cf_lite.layouts.staff-template')

@section('other-style')
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    {!! Form::open(['method' => 'POST', 'action' => ['ClubController@store_member_scan_image', \Illuminate\Support\Facades\Auth::user()->user_club->jubilant_id], 'files' => 'true']) !!}
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/settings')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.member_scan_image')</h2>
            </center>
        </div>
        <hr style="border: 0.5px solid #EFEFEF">
        <div class="col-sm-12">
            @if(!empty($club->image))
                <img role="button" id="image_preview" class="center-block" src="{{asset('images/club_images/'.$club->image)}}" style="width: 75vh; height: 75vh; object-fit: cover;">
            @else
                <img role="button" id="image_preview" class="center-block" src="{{asset('images/img.png')}}" style="width: 75vh; height: 75vh; object-fit: cover;">
            @endif
            <div class="form-group hidden">
                {!! Form::file('photo', ['class' => 'file_multi_video']) !!}
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary pull-right']) !!}
        </div>
    </div>
    {!! Form::close() !!}
@endsection

@section('other-script')
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