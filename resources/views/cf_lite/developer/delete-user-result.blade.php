@extends('cf_lite.layouts.staff-template')

@section('other-style')
<link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
<link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
<link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
@endsection

@section('content')
<div class="row">
    <div class="col-sm-12">
        <a href="{{url('dev/delete-user')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span
                class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
        <center>
            <h2 class="page-title">Hasil Delete User</h2>
        </center>
    </div>
    <div class="col-md-12">
        <div class="x_panel">
            <div class="x_content">
                <pre>{!! $log_text !!}</pre>
            </div>
        </div>
    </div>
</div>
@endsection

@section('other-script')
<script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>

@endsection