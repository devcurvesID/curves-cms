@extends('cf_back.layouts.master-dashboard')

@section('title', 'Club')

@section('other-style')
    <link href={{asset("vendors/select2/dist/css/select2.min.css")}} rel="stylesheet">
    <style>
        .select2 {
            width: 100% !important;
        }

        textarea {
            resize: none;
        }

        label {
            padding-left: 0px !important;
        }
    </style>
@endsection
@section('content')

    <div class="container">
        <div class="my-section">
            <div class="section-title">
                <center>
                    <h1 class="page-title">@lang('labels.list') @lang('labels.club')</h1>
                </center>
            </div>

            @include('cf_back.flash-message')

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <a id="btn-add-club" class="btn btn-sm btn-primary pull-right"><i
                                class="fa fa-plus"></i> @lang('labels.add') @lang('labels.club')</a>
                    <div class="x_panel">
                        <div class="x_content">
                            <br/>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="data-table">
                                    <thead>
                                    <tr>
                                        <th>
                                            @lang('labels.id')
                                        </th>
                                        <th>
                                            @lang('labels.name')
                                        </th>
                                        <th>
                                            @lang('labels.location')
                                        </th>
                                        <th>
                                            @lang('labels.postal_code')
                                        </th>
                                        <th>
                                            @lang('labels.phone')
                                        </th>
                                        <th>
                                            @lang('labels.address')
                                        </th>
                                        <th>
                                            @lang('labels.email')
                                        </th>
                                        <th>
                                            @lang('labels.facebook')
                                        </th>
                                        <th>
                                            @lang('labels.status')
                                        </th>
                                        <th>
                                            @lang('labels.edit')
                                        </th>
                                        <th>
                                            @lang('labels.delete')
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($clubs as $club)
                                        <tr>
                                            <td>{{$club->id}}</td>
                                            <td>{{$club->club_name}}</td>
                                            <td>
                                                @if (isset($club->clubinfo_city->city))
                                                {{$club->clubinfo_city->city}},
                                                @endif

                                                @if (isset($club->clubinfo_province->province))
                                                {{$club->clubinfo_province->province}},
                                                @endif
                                                @if (isset($club->clubinfo_country->country))
                                                {{$club->clubinfo_country->country}}
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($club->postal))
                                                {{$club->postal}}
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($club->phones))
                                                {{$club->phones}}
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($club->address))
                                                {{$club->address}}
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($club->email))
                                                {{$club->email}}
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($club->facebook))
                                                {{$club->facebook}}
                                                @endif
                                            </td>
                                            <td>
                                                @if (isset($club->status))
                                                {{$club->status}}
                                                @endif
                                            </td>
                                            <td>
                                                <a href="{{url('clubinfo/'.$club->id.'/edit')}}"
                                                   class="btn btn-warning btn-edit-club"><i
                                                            class="fa fa-pencil"></i> @lang('labels.edit')
                                                </a>
                                            </td>
                                            <td>
                                                <a class="btn btn-danger btn-hapus"><i
                                                            class="fa fa-trash"></i> @lang('labels.delete')</a>
                                                <div>
                                                    {!! Form::open(['method' => 'DELETE', 'action' => ['ClubController@destroy', $club->id]]) !!}
                                                    {!! Form::close() !!}
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{--MODAL TAMBAH--}}
        <button type="button" id="btn-pop-add" class="hidden" data-toggle="modal" data-target="#add-club"></button>

        <!-- Modal -->
        <div class="modal fade" id="add-club" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                        <h4 class="modal-title">@lang('labels.add')  @lang('labels.club')</h4>
                    </div>
                    <div class="modal-body">
                        <div>
                            {!! Form::open(['url' => 'clubinfo', 'id' => 'formcountry']) !!}
                            <div class="form-group">
                                {!! Form::label('club_name', trans('labels.name'), ['class' => 'control-label']) !!}
                                {!! Form::text('club_name', null, ['class' => 'form-control', 'placeholder' => trans('labels.input').' '.trans('labels.name')]) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('country_id', trans('labels.country'), ['class' => 'control-label']) !!}
                                @if(count($countries) > 0):
                                {!! Form::select('country_id', $countries, null, ['class' => 'form-control js-state', 'placeholder' => trans('labels.input').' '.trans('labels.country'), 'id' => 'countries']) !!}
                                @else
                                    <p>@lang('labels.country_not_found')</p>
                                @endif
                            </div>
                            <div class="form-group" id="province-group" style="display: none;">
                                {!! Form::label('province_id', trans('labels.province'), ['class' => 'control-label', 'placeholder' => 'x']) !!}
                                <div id="province-select"></div>
                            </div>
                            <div class="form-group" id="city-group" style="display: none;">
                                {!! Form::label('city_id', trans('labels.city'), ['class' => 'control-label', 'placeholder' => 'x']) !!}
                                <div id="city-select"></div>
                            </div>
                            <div class="form-group">
                                {!! Form::label('postal', trans('labels.postal_code'), ['class' => 'control-label']) !!}
                                {!! Form::text('postal', null, ['class' => 'form-control', 'data-inputmask' => '"mask": "99999"', 'placeholder' => trans('labels.input').' '.trans('labels.postal_code')]) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('phones', trans('labels.phone'), ['class' => 'control-label']) !!}
                                {!! Form::text('phones', null, ['class' => 'form-control', 'placeholder' => trans('labels.input').' '.trans('labels.phone')]) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('address', trans('labels.address'), ['class' => 'control-label']) !!}
                                {!! Form::textarea('address', null, ['class' => 'form-control', 'placeholder' => trans('labels.input').' '.trans('labels.address')]) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('email', trans('labels.email'), ['class' => 'control-label']) !!}
                                {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'Masukkan Email']) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::label('facebook', trans('labels.facebook'), ['class' => 'control-label']) !!}
                                {!! Form::text('facebook', null, ['class' => 'form-control', 'placeholder' => 'Masukkan Facebook']) !!}
                            </div>
                            <div class="form-group">
                                {!! Form::submit(trans('labels.add'), ['class' => 'btn btn-primary form-control']) !!}
                            </div>
                            {!! Form::close() !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{--MODAL TAMBAH--}}
    </div>

@endsection


@section('other-script')
    <script src={{asset("vendors/iCheck/icheck.min.js")}}></script>
    <script src={{asset("js/jquery.dataTables.min.js")}}></script>
    <script src={{asset("js/dataTables.bootstrap.min.js")}}></script>
    <script src={{asset("vendors/select2/dist/js/select2.js")}}></script>
    <script src={{asset("vendors/jquery.inputmask/dist/min/jquery.inputmask.bundle.min.js")}}></script>
    <script>
        $('#data-table').dataTable();

        $('#btn-add-club').click(function () {
            $('#btn-pop-add').click();
        });
        $('.btn-edit-club').click(function () {
            $('#btn-pop-edit').click();
        });

        $('#countries').select2({
            placeholder: "<?php echo trans('labels.choose') . ' ' . trans('labels.country'); ?>"
        });
    </script>

    <meta name="_token" content="{!! csrf_token() !!}"/>
    {{--tambah--}}
    <script>
        $(document).ready(function () {
            var url = "/curvesindonesia/public/";
            $('#countries').on('change', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                var formData = {
                    country_id: $('#countries').val()
                };
                var type = 'POST';
                var my_url = url + 'getprovince';
                var wew = "";

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        var province = '<select class="form-control js-state" id="provinces" name="province_id" placeholder="<?php echo trans('labels.club_province_placeholder'); ?>"><option selected><?php echo trans('labels.choose') . ' ' . trans('labels.province')?></option>';
                        $.each(data, function (i, item) {
                            province += '<option value="' + i + '">' + item + '</option>'
                        });
                        province += '</select>';
                        $('#province-select').html(province);
                        $('#province-group').show();
                        $('#provinces').select2({
                            placeholder: "<?php echo trans('labels.province'); ?>"
                        });
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });

            $('#formcountry').on('change', '#provinces', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                var formData = {
                    province_id: $('#provinces').val()
                };

                var type = 'POST';
                var my_url = url + 'getcities';
                var wew = "";

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        var city = '<select class="form-control js-state" id="cities" name="city_id" placeholder="<?php echo trans('labels.club_city_placeholder'); ?>"><option selected><?php echo trans('labels.choose') . ' ' . trans('labels.city')?></option>';
                        $.each(data, function (i, item) {
                            city += '<option value="' + i + '">' + item + '</option>'
                        });

                        city += '</select>';

                        $('#city-select').html(city);
                        $('#city-group').show();
                        $('#cities').select2({
                            placeholder: "<?php echo trans('labels.city'); ?>"
                        });
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });
        });
    </script>
    {{--tambah--}}

    {{--edit--}}
    <script>
        $(document).ready(function () {
            var url = "/curvesindonesia/public/";
            $('#countries-edit').select2({
                placeholder: "<?php echo trans('labels.input') . ' ' . trans('labels.country'); ?>"
            });
            $('#countries-edit').on('change', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });
                var formData = {
                    country_id: $('#countries-edit').val()
                };
                var type = 'POST';
                var my_url = url + 'getprovince';
                var wew = "";

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        var province = '<select class="form-control js-state" id="provinces-edit" name="province_id" placeholder="<?php echo trans('labels.club_province_placeholder'); ?>">';
                        $.each(data, function (i, item) {
                            province += '<option value="' + i + '">' + item + '</option>'
                        });
                        province += '</select>';

                        $('#province-select-edit').empty();
                        $('#province-select-edit').html(province);
                        $('#provinces-edit').select2({
                            placeholder: "<?php echo trans('labels.club_province_placeholder'); ?>"
                        });
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });

            $(document).on('change', '#provinces-edit', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="_token"]').attr('content')
                    }
                });

                var formData = {
                    province_id: $('#provinces-edit').val()
                };

                var type = 'POST';
                var my_url = url + 'getcities';
                var wew = "";

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        var city = '<select class="form-control js-state" id="cities-edit" name="city_id" placeholder="<?php echo trans('labels.club_city_placeholder'); ?>">';
                        $.each(data, function (i, item) {
                            city += '<option value="' + i + '">' + item + '</option>'
                        });

                        city += '</select>';

                        $('#city-select-edit').empty();
                        $('#city-select-edit').html(city);
                        $('#cities-edit').select2({
                            placeholder: "<?php echo trans('labels.club_city_placeholder'); ?>"
                        });
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });
        });
        $('#provinces-edit').select2({
            placeholder: "<?php echo trans('labels.club_province_placeholder'); ?>"
        });
        $('#cities-edit').select2({
            placeholder: "<?php echo trans('labels.club_city_placeholder'); ?>"
        });
    </script>
    {{--edit--}}
    {{--<script src="{{asset('js/ajax-crud.js')}}"></script>--}}
@endsection
