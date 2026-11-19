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
                {!! Form::open(['method' => 'DELETE', 'action' => 'DevController@delete_locker']) !!}
                {!! Form::select('user_id',$users, null, ['class' => 'form-control select2']) !!}
                {!! Form::select('memberlocker_id',[], null, ['class' => 'form-control select2']) !!}
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
            
            var type = 'POST';
            var my_url = url + 'get-user-locker';
            
            $.ajax({
                type: type,
                url: my_url,
                data: formData,
                success: function (data) {
                    console.log(data.member_lockers);
                    $('select[name="memberlocker_id"]').empty().trigger('change');

                    for (const property in data.member_lockers) {
                        var newOption = new Option(data.member_lockers[property], property, false, false);
                        $('select[name="memberlocker_id"]').append(newOption);
                    }

                    $('select[name="memberlocker_id"]').trigger('change');
                },
                error: function (data) {
                    console.log('Error : ', data);
                }
            });
        });
        
        $(document).on('change', 'select[name="memberlocker_id"]', function () {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            var formData = {
                memberlocker_id: $(this).val()
            };
            
            var type = 'POST';
            var my_url = url + 'delete-locker';
            
            $.ajax({
                type: type,
                url: my_url,
                data: formData,
                success: function (data) {
                    console.log(data);
                    udata = `<pre>${JSON.stringify(data.member_locker, null, 2)}</pre>`

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