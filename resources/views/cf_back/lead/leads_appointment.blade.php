@extends('cf_back.layouts.master-dashboard')

@section('title', 'Member Appointment')

@section('other-style')
@endsection

@section('content')
    <div class="container">
        <div class="my-section">
            <div class="section-title">
                <center>
                    <h1 class="page-title">@lang('labels.leads_appoinment_list')</h1>
                </center>
            </div>

            @include('cf_back.flash-message')
            @include('cf_back.error-message')

            <div class="row">
                <div class="x_panel">
                    <div class="col-md-12 col-sm-12 col-xs-12">
                        <div class="x_content">
                            <center>
                                <h3>{{date('d F Y', strtotime($tanggal))}}</h3>
                                {{--<h3>{{count($guest_appointments)}}</h3>--}}
                            </center>
                            <br/>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="data-table">
                                    <thead>
                                    <tr>
                                        <th>@lang('labels.name')</th>
                                        <th>@lang('labels.phone')</th>
                                        <th>@lang('labels.action')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($guest_appointments as $x)
                                        <tr>
                                            <td>
                                                {{$x->gastatus_guestappointment->guestappointment_buddyreferrallead->fullname}}
                                            </td>
                                            <td>
                                                {{$x->gastatus_guestappointment->guestappointment_buddyreferrallead->phone}}
                                            </td>
                                            <td>
                                                <a class="btn btn-primary" href="{{url('cf-back/lead/date/'.$tanggal.'/'.$x->gastatus_guestappointment->guestappointment_buddyreferrallead->id)}}">@lang('labels.edit')</a>
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
