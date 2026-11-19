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
            <a href="{{url('cf-lite/staff/'.$coach_target[0]->coach_id.'/detail')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.setting_coach_target') - {{$coach_target[0]->coachtarget_user->name}}</h2>
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
                                <th>@lang('labels.target_category_name')</th>
                                <th>@lang('labels.point_target')</th>
                                <th>@lang('labels.acquired')</th>
                                <th>@lang('labels.target_interval')</th>
                                <th>@lang('labels.status')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php($indexing = 1)
                            @foreach($coach_target as $target)
                                <tr>
                                    <td>{{$indexing}}</td>
                                    <td>{{$target->coachtarget_targetcategorydetail->targetcategorydetail_targetcategory->category_name}}</td>
                                    <td>{{$target->target}}</td>
                                    <td>{{$target->acquired}}</td>
                                    <td>{{$target->coachtarget_targetcategorydetail->target_interval}}</td>
                                    <td style="text-align: center;">
                                        <div class="switch">
                                            @if($target->coachtarget_targetcategorydetail->status == 'Active')
                                                @if($target->status == 'Active')
                                                    <label>
                                                        <input class="cek" type="checkbox" checked id="status-{{$target->id}}">
                                                        <span class="slider round"></span>
                                                    </label>
                                                @elseif($target->status == 'Inactive')
                                                    <label>
                                                        <input class="cek" type="checkbox" id="status-{{$target->id}}">
                                                        <span class="slider round"></span>
                                                    </label>
                                                @endif
                                            @else
                                                <label>
                                                    <input class="cek" type="checkbox" id="status-{{$target->id}}" disabled>
                                                    <span class="slider round"></span>
                                                </label>
                                            @endif
                                        </div>
                                    </td>
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

@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>

    <script>
        $(document).ready(function () {
            $('#data-table').dataTable();

            var url = "{{url('cf-lite')}}/";

            $('#data-table').on('click', '.cek', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    coach_target_id: $(this).attr('id').split('-').pop(),
                    check: $(this).prop('checked')
                }
                console.log(formData);
                var type = 'POST';
                var my_url = url + 'coach-target/setting-coach-target/edit-status-ajax';

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