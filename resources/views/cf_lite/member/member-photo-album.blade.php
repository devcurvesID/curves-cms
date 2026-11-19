@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('custom-css/my-global-css.css')}}" rel="stylesheet">
    <style>
        .btn-default, .btn-default:link {
            color: deeppink;
            background-color: transparent;
            border-color: deeppink;
        }

        .btn-default:hover {
            color: #fff;
            background-color: deeppink;
            border-color: deeppink;
        }

        #image_preview {
            display: none;
            width: 200px;
            height: auto;
        }

        #image_btn {
            width: 125px;
            height: 125px;
            padding-top: 52px;
        }

        .img-album {
            width: auto;
            height: 200px;
            object-fit: cover;
            transition: all .5s ease-in-out;
        }

        .img-album:hover {
            transform: scale(1.1);
        }

        .img-outer {
            overflow: hidden;
            max-width: 200px;
            height: 200px;
            border-radius: 5px;
            float: left;
            margin-left: 7.5px;
            margin-right: 7.5px;
            margin-bottom: 15px;
        }

        .container {
            width: auto;
            height: auto;
            margin: 0 auto;
        }

        .container img {vertical-align: middle;}

        .container .content {
            position: absolute;
            bottom: 15px;
            background: rgba(0, 0, 0, 0.7); /* Black background with transparency */
            color: #f1f1f1;
            width: 95%;
            padding: 20px;
            border-radius: 5px;
            overflow-wrap: break-word;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/member/'.$member->id.'/edit')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <h2 style="margin-top: 65px;">{{$member->name}}:</h2>
            <a href="javascript:;" class="btn btn-default pull-right" data-toggle="modal" data-target="#addPhotoModal" style="margin-top: -40px;"><span class="fa fa-plus"></span></a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            @if(!empty($member_photos))
                @php($temp_month = '')
                @foreach($member_photos as $member_photo)
                    @if($temp_month != date('m-y', strtotime($member_photo->created_at)))
                        <hr>
                        <h3 style="font-weight: 600; color: rebeccapurple; font-family: Allura;">{{date('M Y', strtotime($member_photo->created_at))}}</h3>
                        @php($temp_month = date('m-y', strtotime($member_photo->created_at)))
                    @endif

                    <div class="img-outer" style="position: relative;">
                        <img role="button" id="img-album-{{$member_photo->id}}" alt="{{$member_photo->description}}" alt2="{{Carbon\Carbon::parse($member_photo->date_taken)->format('d-m-Y')}}" src="{{asset('images/member_photos/'.$member_photo->photo)}}" class="img-album" data-toggle="modal" data-target="#showPhotoModal">
                        {!! Form::open(['method' => 'DELETE', 'action' => ['MemberController@delete_member_photo', $member_photo->id], 'id' => 'member-photo-del-'.$member_photo->id]) !!}
                        <a href="javascript:;" class="btn btn-sm btn-delete mpd-btn" id="mpd-btn-{{$member_photo->id}}" title="@lang('labels.delete')" style="position: absolute; bottom: 0; right: 0; font-size: 30px; color: #fff;"><span class="fa fa-trash"></span></a>
                        {!! Form::close() !!}
                    </div>
                @endforeach
            @else
                <h4>@lang('labels.no_photo_album')</h4>
            @endif
        </div>
    </div>

    <div class="modal fade" id="addPhotoModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'POST', 'action' => ['MemberController@add_member_photo', $member->id], 'files' => 'true']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.add_photo')</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <a href="javascript:;" class="btn btn-default" id="image_btn" title="@lang('labels.add_image')" style="margin-bottom: 30px;"><span class="fa fa-plus"></span></a>
                        </div>
                        <div class="col-md-8">
                            <img id="image_preview" style="margin-bottom: 30px;">
                        </div>
                        <div class="col-md-12">
                            {!! Form::label('description', Lang::get('labels.description')) !!}
                            {!! Form::textarea('description', null, ['id' => 'image_desc', 'class' => 'form-control','readonly' => 'readonly', 'rows'=>'2']) !!}
                        </div>
                        <div class="col-md-12">
                            {!! Form::label('date', Lang::get('labels.date')) !!}
                            {!! Form::date('date_taken', \Carbon\Carbon::now(), ['id' => 'date_taken', 'class' => 'form-control','readonly' => 'readonly']) !!}
                        </div>
                    </div>
                    <div class="form-group hidden">
                        {!! Form::file('photo', ['class' => 'file_multi_video']) !!}
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default"
                            data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" class="btn btn-primary">@lang('labels.save')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="modal fade" id="showPhotoModal" tabindex="-1" role="dialog">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="position: absolute; top: 25px; right: 60px; font-size: 60px; color: rgba(255, 255, 255, 1);"><span
                    aria-hidden="true">&times;</span></button>
        <div class="modal-dialog" role="document" style="position:relative;">
            <div class="modal-body container">
                <img src="#" id="photo-show" class="center-block img-rounded" style="width: 100%; height: auto; z-index: 2; ">
                <div class="content" >
                    <label id="photo_desc" value="None" style="font-family: Raleway-Regular; color: white;"></label>
                    <label class="pull-right" id="date_taken_show" value="None" style="font-family: Raleway-Regular; color: grey; font-size: 12px; "></label>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script>
        $('#image_btn').on('click', function () {
            $('.file_multi_video').click();
        });

        $(document).on('change', '.file_multi_video', function (evt) {
            var $source = $('#image_preview');
            $source[0].src = URL.createObjectURL(this.files[0]);
            $('#image_preview').show();
            document.getElementById('image_desc').removeAttribute('readonly');
            document.getElementById('date_taken').removeAttribute('readonly');
        });

        $(document).ready(function () {
            $('.img-album').on('click', function () {
                $('#photo-show').attr('src', $(this).attr('src'));
                $('#photo_desc').text( $(this).attr('alt'));
                $('#date_taken_show').text( $(this).attr('alt2'));
            });

            {{--$('.mpd-btn').on('click', function () {--}}
                {{--if (confirm('@lang('labels.delete_this_photo')?')) {--}}
                    {{--$('#member-photo-del-' + $(this).attr('id').split('-').pop()).submit();--}}
                {{--}--}}

                {{--return false;--}}
            {{--});--}}
        });
    </script>
@endsection