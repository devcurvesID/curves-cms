@extends('cf_lite.layouts.staff-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <style>
        /*.zoom:hover {*/
            /*transform: scale(4);*/
        /*}*/
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-md-12">
            <a href="{{url('cf-lite/other-settings')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.publicity')</h2>
            </center>
            <a href="{{url('cf-lite/publicity/new-publicity')}}" class="btn btn-default pull-right" role="button" style="border-radius: 25px; width: 60px; height: 60px; border-width: 3px; border-color: deeppink;" title="@lang('labels.new_publicity')">
                <span class="fa fa-plus" style="font-size: 40px; color: #800000; margin-top: 2px;"></span>
            </a>
        </div>

        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                            <tr>
                                <th>@lang('labels.photo')</th>
                                <th>@lang('labels.headline')</th>
                                <th>@lang('labels.from_date')</th>
                                <th>@lang('labels.thru_date')</th>
                                <th>@lang('labels.status')</th>
                                <th>@lang('labels.action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($publicity as $p)
                                <tr>
                                    <td style="text-align: center;">
                                        <img class="zoom" src="{{asset('images/publicities/'.$p->photo)}}" style="width: 140px; height: 180px; object-fit: cover;">
                                    </td>
                                    <td>{{ucwords($p->headline)}}</td>
                                    <td>{{\Carbon\Carbon::parse($p->from_date)->format('d F Y')}}</td>
                                    <td>{{\Carbon\Carbon::parse($p->thru_date)->format('d F Y')}}</td>
                                    <td><button class="status btn btn-link" id="status-{{$p->id}}" style="text-decoration: none;">{{ucfirst($p->status)}}</button></td>
                                    <td>
                                        <a href="{{url('cf-lite/publicity/'.$p->id)}}" class="btn btn-info edit-btn" title="@lang('labels.edit')">
                                            <span class="btn-text fa fa-edit"></span>
                                        </a>
                                        <a class="btn btn-danger delete-btn" title="@lang('labels.delete')">
                                            <span class="btn-text fa fa-trash"></span>
                                        </a>
                                        <div>
                                            {!! Form::open(['method' => 'DELETE', 'action' => ['PublicityController@delete_publicity',$p->id]]) !!}
                                            {!! Form::submit(trans('labels.delete'), ['class' => 'hidden']) !!}
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

@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('#data-table').dataTable();
            var url = "{{url('cf-lite')}}/"

            $('#data-table').on('click', '.status', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    publicity_id: $(this).attr('id').split('-').pop()
                };
                var type = 'POST';
                var my_url = url + 'ajax-publicity-status';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    success: function (data) {
                        $('#status-'+data.id).html((data.status).charAt(0).toUpperCase() + (data.status).substr(1));
                    },
                    error: function (data) {
                        console.log('Error : ', data);
                    }
                });
            });
        });
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
                            className: 'btn-danger'
                        },
                        cancel: {
                            label: '@lang('labels.no')',
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