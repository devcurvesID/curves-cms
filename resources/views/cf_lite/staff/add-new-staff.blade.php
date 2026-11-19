@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">
    <style>
        #locationField, #controls {
            position: relative;
            /*width: 480px;*/
        }

        #autocomplete {
            position: absolute;
            top: 0px;
            left: 0px;
            width: 99%;
        }

        #address td {
            font-size: 10pt;
        }

        #locationField {
            height: 20px;
            margin-bottom: 2px;
        }

        table td{
            height: 50px;
            padding: 10px;
        }
        
        table td:first-child{
            color: #ff1493;
            font-size: 20px;
        }

        .btn-primary{
            background-color: #ff1493;
            color: #ffffff;
            border-color: #ff1493;
        }
        .btn-primary:hover  {
            color: #ff1493;
            background-color: transparent;
            border-color: #ff1493;
        }

    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-sm-12">
            <center>
                <h2 class="page-title">@lang('labels.add') @lang('labels.staff')</h2>
            </center>
        </div>
    </div>

    {!! Form::open(['method' => 'POST', 'action' => ['StaffController@store_new_staff', $club->id], 'id'=>'form-add-staff']) !!}
    <div class="row">
        <div class="col-md-2 col-md-offset-1">
            <img role="button" id="image_preview" class="img-circle" src="{{asset('images/img.png')}}"
                 style="width: 150px; height: 150px; object-fit: cover;">
            <div class="form-group hidden">
                {!! Form::file('photo', ['class' => 'file_multi_video']) !!}
            </div>
        </div>
        <div class="col-md-4">
            <table>
                <tr>
                    <td><span class="fa fa-female" title="@lang('labels.name')"></span></td>
                    <td>:</td>
                    <td>{!! Form::text('name', null, ['class' => 'form-control']) !!}</td>
                </tr>
                <tr>
                    <td><span class="fa fa-briefcase" title="@lang('labels.role')"></span></td>
                    <td>:</td>
                    <td>
                        {!! Form::select('role', $roles, null, ['class' => 'form-control']) !!}
                    </td>
                </tr>
                <tr>
                    <td><span class="fa fa-phone" title="@lang('labels.phone')"></span></td>
                    <td>:</td>
                    <td>{!! Form::text('phone', null, ['class' => 'form-control']) !!}</td>
                </tr>
                <tr>
                    <td><span class="fa fa-envelope-square" title="@lang('labels.personal_email')"></span></td>
                    <td>:</td>
                    <td>{!! Form::text('email_personal', null, ['class' => 'form-control']) !!}</td>
                </tr>
            </table>
        </div>
        <div class="col-md-4">
            <table>
                <tr>
                    <td>@lang('labels.username')</td>
                    <td>:</td>
                    <td>{!! Form::text('username', null, ['class' => 'form-control']) !!}</td>
                </tr>
                <tr>
                    <td>@lang('labels.password')</td>
                    <td>:</td>
                    <td>{!! Form::password('password', ['class' => 'form-control']) !!}</td>
                </tr>
                <tr>
                    <td>@lang('labels.repassword')</td>
                    <td>:</td>
                    <td>{!! Form::password('password_confirmation', ['class' => 'form-control']) !!}</td>
                </tr>
                <tr>
                    <td>{!! Form::label('key_tag_id', trans('labels.key_tag').' #', ['class' => 'control-label']) !!}</td>
                    <td>:</td>
                    <td>{!! Form::text('key_tag_id', null, ['class' => 'form-control']) !!}</td>
                </tr>
                <tr>
                    <td>@lang('labels.code')</td>
                    <td>:</td>
                    <td>{!! Form::text('staff_code',null, ['class' => 'form-control upper-class','maxlength'=>'3']) !!}</td>
                </tr>
            </table>
        </div>
    </div>
    <hr>
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

    <script>
        $('#form-add-staff').on('submit',function () {
            $('.upper-class').val($('.upper-class').val().toUpperCase());
        });
    </script>
@endsection