@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <style>
        body {
            -webkit-print-color-adjust: exact;
        }

        .dt-buttons {
            float: left;
        }

        #dataTable_filter {
            float: right;
        }

        @media print {
            th, td {
                vertical-align: middle !important;
            }

            tr td:nth-child(1), tr th:nth-child(1) {
                display: none;
            }

            tr td:nth-child(4) {
                text-align: right;
            }
        }
    </style>
@endsection

@section('content')
    <div class="col-xs-12">
        <left>
            <a href="{{url('cf-lite/laporan')}}" class="btn btn-default" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: deeppink;margin-left: 30px;margin-top: 30px;" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
        </left>
        <center>
            <h2 class="page-title">@lang('labels.member_not_fwo')
                : {{\Illuminate\Support\Facades\Auth::user()->user_club->club_name}}
            </h2>
        </center>
    </div>

    <div class="col-xs-12">
        <table id="dataTable" class="table table-bordered" style="background: white;">
            <thead>
            <tr>
                <th class="text-center" style="vertical-align: middle">@lang('labels.photo')</th>
                <th class="text-center" style="vertical-align: middle">@lang('labels.name')</th>
                <th class="text-center" style="vertical-align: middle">@lang('labels.membership_type')</th>
                <th class="text-center" style="vertical-align: middle">@lang('labels.joined_date')</th>
                <th class="text-center" style="vertical-align: middle">@lang('labels.keytag')</th>
            </tr>
            </thead>
            <tbody>
            @foreach($members as $member)
                <tr>
                    <td class="text-center">
                        @if(!empty($member->memberstatus_user->user_memberpersonal->photo))
                            <img class="img-circle" src="{{asset('images/member_photos/'.$member->memberstatus_user->user_memberpersonal->photo)}}" style="width: 40px; height: 40px; object-fit: cover;">
                        @else
                            <img class="img-circle" src="{{asset('images/img.png')}}" title="@lang('labels.photo')" style="width: 40px; height: 40px; object-fit: cover;">
                        @endif
                    </td>
                    <td>{{$member->memberstatus_user->name}}</td>
                    <td>{{$member->memberstatus_membershiptype->membership_type_name}}</td>
                    <td class="text-center">{{\Carbon\Carbon::parse($member->from_date)->format('d-m-Y')}}</td>
                    <td>{{$member->memberstatus_user->user_memberpersonal->key_tag_id}}</td>
                </tr>
            @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-darker text-right">
                    <th colspan="3"></th>
                    <th style="text-align: right">
                        @lang('labels.total_member'):
                    </th>
                    <td class="text-left">{{count($members)}}</td>
                </tr>
            </tfoot>
        </table>

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
                {
                    extend: 'print',
                    footer: true,
                    exportOptions: {
                        columns: ':visible'
                    }
                }
            ]
        });

        $('.dt-buttons button').prepend('<i class="fa fa-print"></i> ');
        $('.dt-buttons button').addClass('btn btn-default');
    </script>
@endsection