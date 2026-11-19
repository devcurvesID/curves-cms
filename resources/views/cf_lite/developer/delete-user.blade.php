@extends('cf_lite.layouts.staff-template')

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
<div class="row">
    <div class="col-md-12">
        <div class="x_panel">
            <div class="x_content">
                {!! Form::open(['method' => 'DELETE', 'action' => 'DevController@delete_user']) !!}
                {!! Form::select('user_id',$users, null, ['class' => 'form-control select2']) !!}
                {!! Form::submit(trans('labels.delete'), ['class' => 'btn btn-xs btn-danger']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="x_panel">
            <div class="x_content" id="user_data">

            </div>
        </div>
    </div>
</div>
{{-- <div class="row">
    <div class="col-md-12">
        <div class="x_panel">
            <div class="x_content">
                <br />
                <div class="table-responsive" style="background-color: #fff;">
                    <table class="table table-hover table-bordered" id="data-table">
                        <thead>
                            <tr>
                                <th>@lang('labels.indexing')</th>
                                <th>@lang('labels.name')</th>
                                <th>@lang('labels.phone')</th>
                                <th>@lang('labels.club')</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @php($indexing = 1)
                            @foreach($users as $userloop)
                            <tr>
                                <td>{{$indexing}}</td>
                                <td>{{$userloop->name}}</td>
                                <td>{{$userloop->user_memberpersonal()->exists() ? $userloop->user_memberpersonal->phone
                                    : ''}}</td>
                                <td>{{$userloop->user_club->club_name}}</td>
                                <td style="text-align: center;">
                                    <div>
                                        {!! Form::open(['method' => 'GET', 'action' =>
                                        ['DevController@delete_user_index']]) !!}
                                        {!! Form::hidden('user_id',$userloop->id) !!}
                                        {!! Form::submit(trans('labels.choose'), ['class' => 'btn btn-xs btn-danger'])
                                        !!}
                                        {!! Form::close() !!}
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
</div> --}}
@endsection

@section('other-script')
<script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
<script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
<script src="{{asset('select2\dist\js\select2.min.js')}}"></script>

<script type="text/javascript">
    $(document).ready(function () {
        $('.select2').select2();
        $('#data-table').dataTable();

        var url = "{{url('dev')}}/";
        
        $(document).on('change', 'select[name="user_id"]', function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            var formData = {
                user_id: $(this).val()
            };

            console.log("user_id",formData);
            
            var type = 'POST';
            var my_url = url + 'delete-user';
            
            $.ajax({
                type: type,
                url: my_url,
                data: formData,
                success: function (data) {
                    console.log(data);
                    udata = `<pre>${JSON.stringify(data.user, null, 2)}</pre>`

                    $('#user_data').html(udata);
                },
                error: function (data) {
                    console.log('Error : ', data);
                }
            });
        });
    });
</script>

@endsection