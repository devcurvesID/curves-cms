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
                <a href="{{url('cf-lite/laporan')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            </left>
            <center>
                <h2 class="page-title">
                    @lang('labels.tshirt_size_report') : {{Auth::user()->user_club->club_name}}
                </h2>
            </center>
        </div>
        <div class="col-xs-12 col-md-offset-2 col-md-8">
            <table id="dataTable" class="table table-bordered table-striped" style="background: white">
                <thead>
                <tr>
                    <th>@lang('labels.indexing')</th>
                    <th>@lang('labels.size')</th>
                    <th>@lang('labels.member')</th>
                    <th>@lang('labels.staff')</th>
                </tr>
                </thead>
                <tbody>
                @php($indexing = 1)
                @foreach($sizes as $size)
                    @php($countmember = 0)
                    @php($countstaff = 0)
                    @foreach($memberpersonals as $memberpersonal)
                        @if($memberpersonal->memberpersonal_user->role_id == '7' && $memberpersonal->tshirt_size == $size->id)
                            @php($countmember += 1)
                        @elseif($memberpersonal->memberpersonal_user->role_id != '7' && $memberpersonal->tshirt_size == $size->id)
                            @php($countstaff += 1)
                        @endif
                    @endforeach
                    <tr>
                        <td>{{$indexing}}</td>
                        <td><a href="{{url('cf-lite/laporan/tshirt-size-detail',$size->id)}}">{{$size->size}}</a></td>
                        <td>{{$countmember}}</td>
                        <td>{{$countstaff}}</td>
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