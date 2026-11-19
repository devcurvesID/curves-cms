@extends('cf_acc.layouts.acc-dashboard-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">

    <style>
        .select2 {
            width: 100% !important;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/dashboard')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.cost_revenue_center')</h2>
            </center>
        </div>
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive" style="background-color: #fff;">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                            <tr>
                                <th>@lang('labels.indexing')</th>
                                <th>@lang('labels.code')</th>
                                <th>@lang('labels.club_name')</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @php($indexing = 1)
                            @foreach($clubs as $club)
                                <tr>
                                    <td>{{$indexing}}</td>
                                    <td>
                                        {{ (!empty($club->club_costrevenuecenter)) ? $club->club_costrevenuecenter->cr_code : '-' }}
                                    </td>
                                    <td>{{$club->club_name}}</td>
                                    <td>
                                        <a href="javascript:;" class="btn btn-default btn-edit" id="btn-edit-{{$club->id}}" data-toggle="modal" data-target="#editCR">
                                            <span class="fa fa-edit"></span>
                                        </a>
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

    <!-- Modal -->
    <div class="modal fade" id="editCR" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'POST', 'action' => 'CostRevenueCenterController@update_cost_revenue', 'id' => 'form-cost-revenue']) !!}
                {!! Form::hidden('club', null) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.update_cost_revenue')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('club', trans('labels.club').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <p class="form-control-static" id="club-static"></p>
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('code', trans('labels.code').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::text('code', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('name', trans('labels.cost_revenue_name').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::text('name', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" name="submit" id="add-chart-of-account" class="btn btn-success">@lang('labels.add')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('.select3').select2();
            $('#data-table').dataTable();
        });

    </script>
    <script>
        $(document).ready(function () {
            var url = "{{url('cf-acc/accounting')}}/";

            $('#data-table').on('click', '.btn-edit', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var club_id = $(this).attr('id').split('-').pop();

                var formData = {
                    club_id: club_id
                };

                var type = 'POST';
                var my_url = url + 'cost-revenue-center/ajax';

                $.ajax({
                   type: type,
                   url: my_url,
                   data: formData,
                   success: function (data) {
                       console.log(data);
                       $('input[name="club"]').val(club_id);
                       $('#club-static').text(data.club_name);
                       if (data.club_costrevenuecenter !== null) {
                           $('input[name="code"]').val(data.cr_code);
                           $('input[name="name"]').val(data.cr_name);
                       } else {
                           $('input[name="code"]').val('');
                           $('input[name="name"]').val('');
                       }
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
            $(document).on('click', '.coa-delete-btn', function () {
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