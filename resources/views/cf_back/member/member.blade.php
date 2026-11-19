@extends('cf_back.layouts.master-dashboard')

@section('title', 'Member')

@section('other-style')
    <link href={{asset("vendors/select2/dist/css/select2.min.css")}} rel="stylesheet">
@endsection

@section('content')
    <div class="container">
        <div class="my-section">
            <div class="section-title">
                <center>
                    <h1 class="page-title">@lang('labels.list') @lang('labels.member')</h1>
                </center>
            </div>
            @include('cf_back.flash-message')

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="x_content">
                            <br/>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="data-table">
                                    <thead>
                                    <tr>
                                        <th>
                                            @lang('labels.fullname')
                                        </th>
                                        <th>
                                            @lang('labels.phone')
                                        </th>
                                        <th>
                                            @lang('labels.role')
                                        </th>
                                        <th>
                                            @lang('labels.membership_type')
                                        </th>
                                        <th>
                                            @lang('labels.status')
                                        </th>
                                        <th>
                                            @lang('labels.edit')
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($members as $member)
                                        <tr>
                                            <td>{{$member->name}}</td>
                                            <td>{{$member->user_memberpersonal->phone}}</td>
                                            <td>{{$member->user_role->role}}</td>
                                            <td>{{$member->user_memberstatus->memberstatus_membershiptype->membership_type}}</td>
                                            <td>{{$member->user_memberstatus->status}}</td>
                                            <td>
                                                <a href="{{url('cf-back/member/'.$member->id.'/edit')}}"
                                                   class="btn btn-warning">Edit</a>
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
    <script src={{asset("js/jquery.dataTables.min.js")}}></script>
    <script src={{asset("js/dataTables.bootstrap.min.js")}}></script>
    <script>
        $('#data-table').dataTable();
    </script>
@endsection

