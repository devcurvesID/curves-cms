@extends('cf_back.layouts.master-dashboard')

@section('title', 'Staff')

@section('other-style')
@endsection

@section('content')
    <div class="container">
        <div class="my-section">
            <div class="section-title">
                <center>
                    <h1 class="page-title">@lang('labels.list') @lang('labels.staff')</h1>
                </center>
            </div>
            @include('cf_back.flash-message')

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <a id="btn-add-staff" href="{{url('cf-back/staff/create')}}" class="btn btn-primary pull-right"><i
                                class="fa fa-plus"></i> @lang('labels.add') (@lang('labels.staff'))</a>
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
                                            @lang('labels.email')
                                        </th>
                                        <th>
                                            @lang('labels.club')
                                        </th>
                                        <th>
                                            @lang('labels.role')
                                        </th>
                                        {{--<th>--}}
                                            {{--@lang('labels.functional_role')--}}
                                        {{--</th>--}}
                                        <th>
                                            @lang('labels.see_detail')
                                        </th>
                                        <th>
                                            @lang('labels.delete')
                                        </th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($users as $user)
                                        <tr>
                                            <td>{{$user->name}}</td>
                                            <td>{{$user->email}}</td>
                                            <td>
                                                @if(isset($user->user_club->club_name))
                                                {{$user->user_club->club_name}}
                                                @endif
                                            </td>
                                            <td>{{$user->user_role->role}}</td>
                                            {{--<td>--}}
                                                {{--@if(!empty($user->user_frole))--}}
                                                    {{--@foreach($user->user_frole as $frole)--}}
                                                        {{--{{$frole->f_role}},--}}
                                                    {{--@endforeach--}}
                                                {{--@else--}}
                                                    {{-----}}
                                                {{--@endif--}}
                                            {{--</td>--}}
                                            <td>
                                                <a href="{{url('cf-back/staff/'.$user->id.'/detail')}}"
                                                   class="btn btn-info"><i class="fa fa-eye"></i> @lang('labels.see_detail')</a>
                                            </td>
                                            <td>
                                                <a class="btn btn-danger btn-hapus"><i
                                                            class="fa fa-trash"></i> @lang('labels.delete')</a>
                                                <div>
                                                    {!! Form::open(['method' => 'DELETE', 'action' => ['StaffController@destroy', $user->id]]) !!}
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
    <script src={{asset("js/jquery.dataTables.min.js")}}></script>
    <script src={{asset("js/dataTables.bootstrap.min.js")}}></script>
    <script>
        $('#data-table').dataTable();
    </script>
@endsection

