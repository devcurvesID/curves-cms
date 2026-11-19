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
                {!! Form::open(['method' => 'DELETE', 'action' => 'DevController@delete_member_payment']) !!}
                {!! Form::select('member_payment_id',[], null, ['class' => 'form-control select2']) !!}
                {!! Form::submit(trans('labels.delete'), ['class' => 'btn btn-xs btn-danger']) !!}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <div class="x_panel">
            <div class="x_content" id="member_payment_data">

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
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        var url = "{{url('dev')}}/";

        $('.select2').select2({
            minimumInputLength: 2,
            ajax: {
                method: 'POST',
                url: url + 'get-member-payment-data',
                data: function(params){
                    return {
                        search: params.term,
                    };
                },
                processResults: function (data) {
                    return {
                        results: $.map(data, function (item) {
                            return {
                                text: item.payment_number,
                                id: item.id
                            }
                        })
                    };
                }
            }
        });

        $('#data-table').dataTable();

        $(document).on('change', 'select[name="member_payment_id"]', function () {
            
            var formData = {
                member_payment_id: $(this).val()
            };

            console.log("member_payment_id",formData);
            
            var type = 'POST';
            var my_url = url + 'get-member-payment-detail';
            
            $.ajax({
                type: type,
                url: my_url,
                data: formData,
                success: function (data) {
                    console.log(data);
                    udata = `<pre>${JSON.stringify(data, null, 2)}</pre>`

                    $('#member_payment_data').html(udata);
                },
                error: function (data) {
                    console.log('Error : ', data);
                }
            });
        });
    });
</script>

@endsection