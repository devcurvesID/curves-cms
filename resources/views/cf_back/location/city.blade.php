@extends('cf_back.layouts.master-dashboard')

@section('title', 'City')

@section('other-style')
    <style>
        .panel_toolbox > li {
            float: right;
        }

        .right-form {
            height: inherit;
            width: 100%;
            border: 3px double #a0a0a0;
            padding: 30px;
        }

        .bootbox-body{
            font-size: 24px;
            text-align: center;
        }
        .modal-footer{
            text-align: center;
        }
        #data-table_filter{
            margin-right: 10px;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="my-section">
            <div class="section-title">
                <center>
                    <h1 class="page-title">@lang('labels.city')</h1>
                </center>
            </div>
            @include('cf_back.flash-message')

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="row x_title">
                            <div class="col-md-6">
                                <h3>@lang('labels.city')</h3>
                            </div>
                            <div class="col-md-6">
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    </li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="x_content">
                            <div class="row">
                                <div class="col-sm-9">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-bordered" id="data-table">
                                            <thead>
                                            <tr>
                                                <th>
                                                    @lang('labels.id')
                                                </th>
                                                <th>
                                                    @lang('labels.city')
                                                </th>
                                                <th>
                                                    @lang('labels.province')
                                                </th>
                                                <th>
                                                    @lang('labels.country')
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
                                            @foreach($cities as $city)
                                                <tr>
                                                    <td>{{$city->id}}</td>
                                                    <td>{{$city->city}}</td>
                                                    <td>{{$city->city_province->province}}</td>
                                                    <td>{{$city->city_province->province_country->country}}</td>
                                                    <td>
                                                        <a href="{{url('cf-back/city/'.$city->id)}}"
                                                           class="btn btn-warning btn-edit-role"><i
                                                                    class="fa fa-pencil"></i> @lang('labels.edit')
                                                        </a>
                                                    </td>
                                                    <td>
                                                        <a class="btn btn-danger btn-hapus"><i
                                                                    class="fa fa-trash"></i> @lang('labels.delete')</a>
                                                        <div class="hidden">
                                                            {!! Form::open(['method' => 'DELETE', 'action' => ['LocationController@destroyCity', $city->id]]) !!}
                                                            {!! Form::submit('Delete', ['class' => 'btn btn-danger']) !!}
                                                            {!! Form::close() !!}
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-sm-3">
                                    <div class="right-form form-country">
                                        {!! Form::open(['url' => 'cf-back/city/createCity']) !!}

                                        @if($errors->any())
                                            <ul class="alert alert-danger">
                                                @foreach($errors->all() as $error)
                                                    <li>{{$error}}</li>
                                                @endforeach
                                            </ul>
                                        @endif

                                        <div class="form-group">
                                            {!! Form::label('country_id', trans('labels.country'), ['class' => 'control-label']) !!}
                                            @if(count($countriesForSelect) > 0):
                                            {!! Form::select('country_id', $countriesForSelect, null, ['class' => 'form-control', 'Locationholder' => 'Pilih Negara', 'id' => 'countries']) !!}
                                            @else
                                                <p>@lang('labels.country_not_found')</p>
                                            @endif
                                        </div>
                                        <div class="form-group">
                                            <div class="form-group" id="province-group" style="display: none;">
                                                {!! Form::label('province_id', trans('labels.province'), ['class' => 'control-label']) !!}
                                                <div id="province-select">
                                                    @if(count($provinciesForSelect) > 0):
                                                    {!! Form::select('province_id', $provinciesForSelect, null, ['class' => 'form-control', 'Locationholder' => trans('labels.choose') . ' ' . trans('labels.province'), 'id' => 'countries']) !!}
                                                    @else
                                                        <p>@lang('labels.province_not_found')</p>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            {!! Form::label('city', trans('labels.city'), ['class' => 'control-label']) !!}
                                            {!! Form::text('city', null, ['class' => 'form-control', 'Locationholder' => trans('labels.input').' '.trans('labels.name').' '.trans('labels.city')]) !!}
                                        </div>
                                        <div class="form-group">
                                            {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary form-control']) !!}
                                        </div>
                                        {!! Form::close() !!}
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
    <script src={{asset("js/jquery.dataTables.min.js")}}></script>
    <script src={{asset("js/dataTables.bootstrap.min.js")}}></script>
    <script>
        $('#data-table').dataTable();
    </script>

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
                        var province = '<select class="form-control js-state" id="provinces" name="province_id" Locationholder="<?php echo trans('labels.club_province_Locationholder'); ?>"><option selected><?php echo trans('labels.choose') . ' ' . trans('labels.province')?></option>';
                        $.each(data, function (i, item) {
                            province += '<option value="' + i + '">' + item + '</option>'
                        });
                        province += '</select>';
                        $('#province-select').html(province);
                        $('#province-group').show();
                        $('#provinces').select2({
                            Locationholder: "<?php echo trans('labels.province'); ?>"
                        });
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });
        });
    </script>
@endsection

