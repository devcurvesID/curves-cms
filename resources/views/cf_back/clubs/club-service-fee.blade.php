@extends('cf_back.layouts.master-dashboard')

@section('title', 'Club Service Fee')

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
                    <h1 class="page-title">Club Fees Setup</h1>
                </center>
            </div>

            @include('cf_back.flash-message')

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <a href="{{url('club/service-fee/create')}}" class="btn btn-sm btn-primary pull-right"><i
                                class="fa fa-plus"></i> @lang('labels.add')</a>
                    <div class="x_panel">
                        <div class="x_content">
                            <br/>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="data-table">
                                    <thead>
                                    <tr>
                                        <th>
                                            @lang('labels.club')
                                        </th>
                                        <th>
                                            @lang('labels.membership_type')
                                        </th>
                                        <th>
                                            @lang('labels.service_fee')
                                        </th>
                                        <th>
                                            @lang('labels.monthly_fee')
                                        </th>
                                        <th>
                                            @lang('labels.pre_pay_discount')
                                        </th>
                                        <th>
                                            @lang('labels.is_taxable')
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
                                    @foreach($clubsfs as $clubsf)
                                        <tr>
                                            <td>{{$clubsf->clubservicefee_clubinfo->club_name}}</td>
                                            <td>{{$clubsf->clubservicefee_membershiptype->membership_type}}</td>
                                            <td>{{$clubsf->sf_amount}}</td>
                                            <td>{{$clubsf->mf_amount}}</td>
                                            <td>{{$clubsf->pre_pay_discount}}</td>
                                            <td>{{$clubsf->is_taxable}}</td>
                                            <td>
                                                <a href="{{url('club/service-fee/edit/'.$clubsf->id)}}"
                                                   class="btn btn-warning btn-edit-club"><i
                                                            class="fa fa-pencil"></i> @lang('labels.edit')
                                                </a>
                                            </td>
                                            <td>
                                                <a class="btn btn-danger btn-hapus"><i
                                                            class="fa fa-trash"></i> @lang('labels.delete')</a>
                                                <div>
                                                    {!! Form::open(['method' => 'DELETE', 'action' => ['ClubController@csf_destroy', $clubsf->id]]) !!}
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
    {{--<script src="{{asset('js/ajax-crud.js')}}"></script>--}}
@endsection