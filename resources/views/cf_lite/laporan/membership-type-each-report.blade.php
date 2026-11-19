@extends('cf_lite.layouts.staff-dashboard-template')

@section('title', trans('labels.fa_source').' : ' . Auth::user()->user_club->club_name)

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('bootstrap-toggle/css/bootstrap-toggle.min.css')}}" rel="stylesheet">

    <style>
        .dt-buttons {
            float: left;
        }

        #dataTable_filter {
            float: right;
        }
    </style>

    <style>
        #menuutama {
            padding-left: 30px;
            padding-right: 30px;
        }

        .page-title {
            font-size: 44px;
            margin-top: 0;
        }
    </style>
@endsection

@section('content')
    <div class="row">
        <div class="col-xs-12">
            <left>
                <a href="{{url('cf-lite/laporan/membership-type')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            </left>
            <center>
                <h2 class="page-title">
                    @lang('labels.membership_type_report') : {{$membership->membership_type_name}}
                </h2>
            </center>
        </div>
        <div class="col-xs-12">
            <table id="dataTable" class="table table-bordered table-striped" style="background: white">
                <thead>
                <tr>
                    <th>@lang('labels.keytag')</th>
                    <th>@lang('labels.name')</th>
                    <th>@lang('labels.phone')</th>
                    <th>@lang('labels.email')</th>
                    <th>@lang('labels.membership_end')</th>
                    <th>@lang('labels.membership_type')</th>
                    <th>@lang('labels.status')</th>
                </tr>
                </thead>
                <tbody>
                @php($indexing = 1)
                @php($total = 0)
                @foreach($membershipmembers as $mm)
                    <tr>
                        <td>{{$mm->user_memberpersonal->key_tag_id}}</td>
                        <td>{{$mm->name}}</td>
                        <td>{{$mm->user_memberpersonal->phone}}</td>
                        <td>{{$mm->email}}</td>
                        <td>{{\Carbon\Carbon::parse($mm->user_memberstatus->thru_date)->format('d-m-Y')}}</td>
                        <td>{{$mm->user_memberstatus->memberstatus_membershiptype->membership_type_name}}</td>
                        <td>{{$mm->user_memberstatus->status}}</td>
                    </tr>
                    @php($indexing++)
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('other-script')

    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('js/dataTables.buttons.min.js')}}"></script>
    <script src="{{asset('js/buttons.print.min.js')}}"></script>
    <script>
        $('#dataTable').DataTable({
            info: false,
            dom: 'Bfrtip',
            buttons: [
                'print',
            ],
            pageLength: 100
        });

        $('.dt-buttons button').prepend('<i class="fa fa-print"></i> ');
        $('.dt-buttons button').addClass('btn btn-default');
    </script>
@endsection