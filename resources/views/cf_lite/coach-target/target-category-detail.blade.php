@extends('cf_lite.layouts.staff-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">


    <style>
        .switch {
            position: relative;
            display: inline-block;
            width: 60px;
            height: 34px;
        }

        .switch input {display:none;}

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #ccc;
            -webkit-transition: .4s;
            transition: .4s;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 26px;
            width: 26px;
            left: 4px;
            bottom: 4px;
            background-color: white;
            -webkit-transition: .4s;
            transition: .4s;
        }

        input:checked + .slider {
            background-color: deeppink;
        }

        input:focus + .slider {
            box-shadow: 0 0 1px #2196F3;
        }

        input:checked + .slider:before {
            -webkit-transform: translateX(26px);
            -ms-transform: translateX(26px);
            transform: translateX(26px);
        }

        /* Rounded sliders */
        .slider.round {
            border-radius: 34px;
        }

        .slider.round:before {
            border-radius: 50%;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-md-12">
            <a href="{{ url('cf-lite/coach-target') }}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.target_category_detail') - {{$details[0]->targetcategorydetail_targetcategory->category_name}}</h2>
            </center>
        </div>

        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                            <tr>
                                <th>@lang('labels.indexing')</th>
                                <th>@lang('labels.type')</th>
                                <th>@lang('labels.target_interval')</th>
                                <th>@lang('labels.point_target')</th>
                                @if($details[0]->targetcategorydetail_targetcategory->status == 'Active')
                                    <th>@lang('labels.status')</th>
                                    <th>@lang('labels.action')</th>
                                @endif
                            </tr>
                            </thead>
                            <tbody>
                            @php($indexing = 1)
                            @foreach($details as $detail)
                                <tr>
                                    <td>{{$indexing}}</td>
                                    <td>{{$detail->type}}</td>
                                    <td>{{$detail->target_interval}}</td>
                                    <td>{{$detail->target}}</td>
                                    @if($detail->targetcategorydetail_targetcategory->status == 'Active')
                                        <td style="text-align: center;">
                                            <div class="switch">
                                                @if($detail->status == 'Active')
                                                    <label>
                                                        <input class="cek" type="checkbox" checked id="status-{{$detail->id}}">
                                                        <span class="slider round"></span>
                                                    </label>
                                                @elseif($detail->status == 'Inactive')
                                                    <label>
                                                        <input class="cek" type="checkbox" id="status-{{$detail->id}}">
                                                        <span class="slider round"></span>
                                                    </label>
                                                @endif

                                            </div>
                                        </td>
                                        <td style="text-align: center;">
                                            <a data-toggle="modal" data-target="#edit" id="edit-{{$detail->id}}" class="btn btn-info edit-btn">
                                                <i class="icon-rocket"></i>
                                                <span class="btn-text fa fa-edit"></span>
                                            </a>
                                        </td>
                                    @endif
                                </tr>
                                @php($indexing++)
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="edit" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'PATCH', 'action' => ['CoachTargetController@update_point_target', $details[0]->target_category_id]]) !!}
                {!! Form::hidden('detail_id', null) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.edit_point_target')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('target', trans('labels.point_target').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::number('target', null, ['class' => 'form-control', 'min' => '0', 'onkeypress' => 'return numberOnlyInput(event)']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" name="submit" class="btn btn-success">@lang('labels.update')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>

    <script>
        $(document).ready(function () {
            $('#data-table').dataTable();

            $('.edit-btn').on('click', function () {
                var url = "{{ url('cf-lite') }}/"
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    detail_id: $(this).attr('id').split('-').pop()
                }

                var type = 'POST';
                var my_url = url + 'coach-target/edit-point-target-ajax';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    success: function (data) {
                        $('input[name="detail_id"]').val(data.id);
                        $('input[name="target"]').val(data.target);
                    },
                    error: function (data) {
                        console.log('Error : ', data);
                    }
                });
            });

            $('.cek').on('click', function () {
                var url = "{{ url('cf-lite') }}/"
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    detail_id: $(this).attr('id').split('-').pop(),
                    check: $(this).prop('checked')
                }
                console.log(formData);
                var type = 'POST';
                var my_url = url + 'coach-target/edit-status-target-category-detail-ajax';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    success: function (data) {
                        console.log('Success : ', data);
                    },
                    error: function (data) {
                        console.log('Error : ', data);
                    }
                });
            });
        });
    </script>
    <script type="text/javascript">
        function numberOnlyInput(event){
            var charCode = (event.which) ? event.which : event.keyCode;
            if(charCode > 31 && (charCode < 48 || charCode > 57)){
                return false;
            }
            return true;
        }
    </script>

    <script src={{asset("bootbox/bootbox.min.js")}}></script>
    <script>
        $(document).ready(function () {
            $(document).on('click', '.delete-btn', function () {
                var theform = $(this).siblings('div').children('form');
                bootbox.confirm({
                    message: "@lang('messages.are_you_sure')",
                    buttons: {
                        confirm: {
                            label: '@lang('labels.yes')',
                            className: 'btn-success'
                        },
                        cancel: {
                            label: '@lang('labels.no')',
                            className: 'btn-danger'
                        }
                    },
                    callback: function (result) {
                        if (result == false) {
                        } else {
                            theform.submit();
                        }
                    }
                });
            });
        });
    </script>
@endsection