@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('css/appointment.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/iCheck/skins/flat/green.css')}}" rel="stylesheet">
    <link href="https://cdn.datatables.net/1.10.16/css/jquery.dataTables.min.css" rel="stylesheet">

    <style>
        #data-table {
            color: white;
        }

        table.dataTable tbody tr {
            background: transparent;
        }

        label {
            color: white;
        }

        label input {
            color: #414042;
        }

        .scroll::-webkit-scrollbar {
            width: 12px;
        }

        .scroll::-webkit-scrollbar-track {
            -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.3);
            border-radius: 10px;
        }

        .scroll::-webkit-scrollbar-thumb {
            border-radius: 10px;
            -webkit-box-shadow: inset 0 0 6px rgba(0, 0, 0, 0.5);
        }

        ​
    </style>
@endsection

@section('content')

    <div class="row">
        <div class="col-sm-12">
            <center>
                <h2 class="page-title">@lang('labels.lead_list_bulk')</h2>
            </center>
        </div>
    </div>
    @include('cf_lite.includes.error-message')
    <div class="row">
        @if(count($leads) > 0)
            <div class="scroll" style="max-height: 500px; overflow-y: scroll; margin-bottom: 20px">
                <center>
                    <table id="data-table" class="table bulk_action">
                        <thead>
                        <tr class="headings">
                            <th style="padding: 8px 10px">
                                <input type="checkbox" id="check-all" class="flat">
                            </th>
                            <td>@lang('labels.name')</td>
                            <td>@lang('labels.phone')</td>
                            <td>@lang('labels.staff')</td>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($leads as $lead)
                            <tr class="pointer">
                                <td class="a-center ">
                                    <input type="checkbox" class="flat" name="table_records">
                                </td>
                                <td>{{$lead->fullname}}</td>
                                <td>{{$lead->phone}}</td>
                                <td>{{$lead->buddyreferrallead_user->name}}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </center>
            </div>

            <center>
                <a class="btn btn-primary"><i class="fa fa-heart-o"></i> @lang('labels.next')</a>
            </center>
        @else
            <span class="no_data_found">@lang('messages.there_is_no_guest')</span>
        @endif
    </div>
    <div class="paging hiden rom">
        {{$leads->links()}}
    </div>
    <a href="../../fa/lead" class="btn btn-add-brlead" data-toggle="tooltip" title="Back To Single Action"
       style="top: 150px; background-color: #1abc9c"><span class="fa fa-arrow-left"></span></a>
@endsection

@section('other-script')

    <script src="{{asset('vendors/fastclick/lib/fastclick.js')}}"></script>
    <script src="{{asset('vendors/iCheck/icheck.min.js')}}"></script>
    <script src="{{asset('vendors/datatables.net/js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('build/js/custom.min.js')}}"></script>

    <script type="text/javascript">
        $('#data-table').dataTable({
            paging: false,
            info: false
        });
    </script>
@endsection
