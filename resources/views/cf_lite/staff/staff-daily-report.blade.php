@extends('cf_lite.layouts.staff-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css" rel="stylesheet">
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">

    <style>
        .select2 {
            width: 100%;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/document')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">
                    @lang('labels.staff_daily_report')@if(!empty($_GET['staff'])) - {{ $staffs[$_GET['staff']] }} @endif
                    <br><small>({{Carbon\Carbon::parse($today)->format('d-m-Y')}})</small>
                </h2>
            </center>
            <a class="btn btn-default btn-back-to" style="margin-left: calc(100% - 80px);" title="@lang('labels.import')" data-toggle="modal" data-target="#import"><span class="glyphicon glyphicon-import" style="margin-top: 10px; color: deeppink;"></span></a>
        </div>
        <div class="col-md-12">
            {!! Form::open(['method' => 'GET']) !!}
                <div class="row">
                    <div class="col-md-2" style="margin-top: 8px;">
                        {!! Form::label('staff', trans('labels.staff_name'), ['class' => 'control-label']) !!} :
                    </div>
                    <div class="col-md-3 col-xs-6">
                        {!! Form::select('staff', $staffs, null, ['class' => 'form-control select2', 'placeholder' => trans('labels.choose_staff')]) !!}
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2" style="margin-top: 8px;">
                        {!! Form::label('date', trans('labels.date'), ['class' => 'control-label']) !!} :
                    </div>
                    <div class="col-md-3 col-xs-6">
                        {!! Form::date('date', null, ['class' => 'form-control']) !!}
                    </div>

                    <button type="submit" class="btn btn-primary" id="save-btn"><i class="fa fa-search"></i> @lang('labels.search')</button>
                </div>
            {!! Form::close() !!}
        </div>
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive" style="background-color: #fff;">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                            <tr>
                                <th></th>
                                <th>Total Calling</th>
                                <th>Answer</th>
                                <th>APO</th>
                                <th>Re-Schedule</th>
                                <th>Tolak</th>
                                <th>SS</th>
                                <th>Inv</th>
                                <th>NA</th>
                                <th>Leads</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php($total_call = 0)
                            @php($total_answer = 0)
                            @php($total_apo = 0)
                            @php($total_rejected = 0)
                            @php($total_invalid = 0)
                            @php($total_hanging = 0)
                            @php($total_lead = 0)
                            @if($daily_reports != null)
                                @foreach($daily_reports as $daily_report)
                                    <tr>
                                        <td>{{ $daily_report->source }}</td>
                                        <td>{{ ($daily_report->call_total == 0) ? 0 : $daily_report->call_total  }}</td>
                                        <td>{{ ($daily_report->answer == 0) ? 0 : $daily_report->answer  }}</td>
                                        <td>{{ ($daily_report->apo == 0) ? 0 : $daily_report->apo  }}</td>
                                        <td></td>
                                        <td>{{ ($daily_report->rejected == 0) ? 0 : $daily_report->rejected  }}</td>
                                        <td>{{ ($daily_report->invalid == 0) ? 0 : $daily_report->invalid  }}</td>
                                        <td>{{ ($daily_report->invalid == 0) ? 0 : $daily_report->invalid  }}</td>
                                        <td>{{ ($daily_report->hanging == 0) ? 0 : $daily_report->hanging  }}</td>
                                        <td>{{ ($daily_report->leads == 0) ? 0 : $daily_report->leads }}</td>
                                    </tr>
                                    @php($total_call += $daily_report->call_total)
                                    @php($total_answer += $daily_report->answer)
                                    @php($total_apo += $daily_report->apo)
                                    @php($total_rejected += $daily_report->rejected)
                                    @php($total_invalid += $daily_report->invalid)
                                    @php($total_hanging += $daily_report->hanging)
                                    @php($total_lead += $daily_report->leads)
                                @endforeach
                                    <tr style="background-color: #b2b4b4; font-weight: bold;">
                                        <td>@lang('labels.total')</td>
                                        <td>{{ ($total_call == 0) ? 0 : $total_call }}</td>
                                        <td>{{ ($total_answer == 0) ? 0 : $total_answer }}</td>
                                        <td>{{ ($total_apo == 0) ? 0 : $total_apo }}</td>
                                        <td></td>
                                        <td>{{ ($total_rejected == 0) ? 0 : $total_rejected }}</td>
                                        <td>{{ ($total_invalid == 0) ? 0 : $total_invalid }}</td>
                                        <td>{{ ($total_invalid == 0) ? 0 : $total_invalid }}</td>
                                        <td>{{ ($total_hanging == 0) ? 0 : $total_hanging }}</td>
                                        <td>{{ ($total_lead == 0) ? 0 : $total_lead }}</td>
                                    </tr>
                                    <tr>
                                        <td>Call-In</td>
                                        <th>FL</th>
                                        <th>Sign</th>
                                        <th>IG</th>
                                        <th>Koran</th>
                                        <th>LainLain</th>
                                        <th>Total</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Walk-In</td>
                                        <th>FL</th>
                                        <th>Sign</th>
                                        <th>IG</th>
                                        <th>Koran</th>
                                        <th>LainLain</th>
                                        <th>Total</th>
                                        <th></th>
                                        <th></th>
                                        <th></th>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Total Call In + Walk In</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Total FA</td>
                                        <td>{{ $total_fa }}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Sales</td>
                                        @if(count($memberships->where('duration', '<>', '18')->where('duration', '<>', '24')) >= 8)
                                            @foreach($memberships as $membership)
                                                @if($membership->membership_type == 'Regular' && $membership->duration == '6' && $membership->payment_type == 'Prepaid')
                                                    <th>PP 6</th>
                                                @elseif($membership->membership_type == 'Regular' && $membership->duration == '12' && $membership->payment_type == 'Prepaid')
                                                    <th>PP 12</th>
                                                @elseif($membership->membership_type == 'Corporate' && $membership->duration == '6' && $membership->payment_type == 'Prepaid')
                                                    <th>CP 6</th>
                                                @elseif($membership->membership_type == 'Corporate' && $membership->duration == '12' && $membership->payment_type == 'Prepaid')
                                                    <th>CP 12</th>
                                                @elseif($membership->membership_type == 'Student' && $membership->duration == '6' && $membership->payment_type == 'Prepaid')
                                                    <th>ST 6</th>
                                                @elseif($membership->membership_type == 'Student' && $membership->duration == '12' && $membership->payment_type == 'Prepaid')
                                                    <th>ST 12</th>
                                                @elseif($membership->membership_type == 'Regular' && $membership->duration == '1' && $membership->payment_type == 'Cash')
                                                    <th>MM</th>
{{--                                                @elseif($membership->membership_type == 'FS' && $membership->duration == '1' && $membership->payment_type == 'FS')--}}
{{--                                                    <th>FS</th>--}}
                                                @endif
                                            @endforeach
                                            <th>FS</th>
                                        @else
                                            @foreach($memberships as $membership)
                                                @if($membership->membership_type == 'Regular' && $membership->duration == '6' && $membership->payment_type == 'Prepaid')
                                                    <th>PP 6</th>
                                                @elseif($membership->membership_type == 'Regular' && $membership->duration == '12' && $membership->payment_type == 'Prepaid')
                                                    <th>PP 12</th>
                                                @elseif($membership->membership_type == 'Corporate' && $membership->duration == '6' && $membership->payment_type == 'Prepaid')
                                                    <th>CP 6</th>
                                                @elseif($membership->membership_type == 'Corporate' && $membership->duration == '12' && $membership->payment_type == 'Prepaid')
                                                    <th>CP 12</th>
                                                @elseif($membership->membership_type == 'Student' && $membership->duration == '6' && $membership->payment_type == 'Prepaid')
                                                    <th>ST 6</th>
                                                @elseif($membership->membership_type == 'Student' && $membership->duration == '12' && $membership->payment_type == 'Prepaid')
                                                    <th>ST 12</th>
                                                @elseif($membership->membership_type == 'Regular' && $membership->duration == '1' && $membership->payment_type == 'Cash')
                                                    <th>MM</th>
{{--                                                @elseif($membership->membership_type == 'FS' && $membership->duration == '1' && $membership->payment_type == 'FS')--}}
{{--                                                    <th>FS</th>--}}
                                                @endif
                                            @endforeach
                                            <th>FS</th>

                                            @for($i = count($memberships->where('duration', '<>', '18')->where('duration', '<>', '24'))+1; $i <= 8; $i++)
                                                <th></th>
                                            @endfor
                                        @endif
                                        <th>@lang('labels.total')</th>
                                    </tr>
                                    <tr>
                                        <td></td>
                                        @php($total_all_member = 0)
                                        @if(count($memberships->where('duration', '<>', '18')->where('duration', '<>', '24')) == 8)
                                            @foreach($memberships as $membership)
                                                @if($membership->membership_type == 'Regular' && $membership->duration == '6' && $membership->payment_type == 'Prepaid')
                                                    <td>{{ $membership->total_member }}</td>
                                                @elseif($membership->membership_type == 'Regular' && $membership->duration == '12' && $membership->payment_type == 'Prepaid')
                                                    <td>{{ $membership->total_member }}</td>
                                                @elseif($membership->membership_type == 'Corporate' && $membership->duration == '6' && $membership->payment_type == 'Prepaid')
                                                    <td>{{ $membership->total_member }}</td>
                                                @elseif($membership->membership_type == 'Corporate' && $membership->duration == '12' && $membership->payment_type == 'Prepaid')
                                                    <td>{{ $membership->total_member }}</td>
                                                @elseif($membership->membership_type == 'Student' && $membership->duration == '6' && $membership->payment_type == 'Prepaid')
                                                    <td>{{ $membership->total_member }}</td>
                                                @elseif($membership->membership_type == 'Student' && $membership->duration == '12' && $membership->payment_type == 'Prepaid')
                                                    <td>{{ $membership->total_member }}</td>
                                                @elseif($membership->membership_type == 'Regular' && $membership->duration == '1' && $membership->payment_type == 'Cash')
                                                    <td>{{ $membership->total_member }}</td>
{{--                                                @elseif($membership->membership_type == 'FS' && $membership->duration == '1' && $membership->payment_type == 'FS')--}}
{{--                                                    <td>FS</td>--}}
                                                @endif
{{--                                                @php($total_all_member += $membership->total_member)--}}
                                            @endforeach
                                            <td>{{ $promos }}</td>
                                        @else
                                            @foreach($memberships as $membership)
                                                @if($membership->membership_type == 'Regular' && $membership->duration == '6' && $membership->payment_type == 'Prepaid')
                                                    <td>{{ $membership->total_member }}</td>
                                                @elseif($membership->membership_type == 'Regular' && $membership->duration == '12' && $membership->payment_type == 'Prepaid')
                                                    <td>{{ $membership->total_member }}</td>
                                                @elseif($membership->membership_type == 'Corporate' && $membership->duration == '6' && $membership->payment_type == 'Prepaid')
                                                    <td>{{ $membership->total_member }}</td>
                                                @elseif($membership->membership_type == 'Corporate' && $membership->duration == '12' && $membership->payment_type == 'Prepaid')
                                                    <td>{{ $membership->total_member }}</td>
                                                @elseif($membership->membership_type == 'Student' && $membership->duration == '6' && $membership->payment_type == 'Prepaid')
                                                    <td>{{ $membership->total_member }}</td>
                                                @elseif($membership->membership_type == 'Student' && $membership->duration == '12' && $membership->payment_type == 'Prepaid')
                                                    <td>{{ $membership->total_member }}</td>
                                                @elseif($membership->membership_type == 'Regular' && $membership->duration == '1' && $membership->payment_type == 'Cash')
                                                    <td>{{ $membership->total_member }}</td>
{{--                                                @elseif($membership->membership_type == 'FS' && $membership->duration == '1' && $membership->payment_type == 'FS')--}}
{{--                                                    <td>{{ $membership->total_member }}</td>--}}
                                                @endif
{{--                                                @php($total_all_member += $membership->total_member)--}}
                                            @endforeach
                                            <td>{{ $promos }}</td>
{{--                                            @php($total_all_member += $promos)--}}

                                            @for($i = count($memberships->where('duration', '<>', '18')->where('duration', '<>', '24'))+1; $i <= 8; $i++)
                                                <td></td>
                                            @endfor
                                        @endif
                                        <td>{{ $total_sales }}</td>
                                    </tr>
                                    <tr>
                                        <td>No Sales</td>
                                        <td>{{ $total_fa - $total_sales }}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Total FWO</td>
                                        <td>{{ $total_fwo }}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Gloves</td>
                                        <td>{{ $gloves }}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>Locker</td>
                                        <td>{{ $lockers }}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>FL</td>
                                        <td>0</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                    <tr>
                                        <td>N&N</td>
                                        <td>{{ $total_leads }}</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{--  MODAL  --}}
    <div class="modal fade" id="import" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'POST', 'action' => 'StaffController@import_coach_report', 'files' => true]) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.import_staff_report')</h4>
                </div>
                <div class="modal-body">
                    {!! Form::file('data', null) !!}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" name="submit" id="add-bank" class="btn btn-success">@lang('labels.import')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2();
            $('#data-table').DataTable({
                dom: 'Bfrtip',
                bPaginate: false,
                ordering: false,
                buttons: [
                    {
                        extend: 'excel',
                        title: '@lang('labels.staff_daily_report') - ({{ !empty($_GET['staff']) ? $staffs[$_GET['staff']] : '' }}) {{ !empty($_GET['staff']) ?  ' - '.$_GET['staff'] : '' }} - {{ $today }}',
                        footer: true,
                        exportOptions: {
                            columns: ':visible',
                            format: {
                                body: function(data, row, column, node) {
                                    data = $('<p>' + data + '</p>').text();
                                    return data;
                                }
                            }
                        },
                        customize: function( xlsx, row ) {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];
                            $('row c', sheet).attr( 's', 63);
                            var mergeCells = $('mergeCells', sheet);

                            //Call In
                            mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                                attr: {
                                    ref: 'A21:A22'
                                }
                            }));
                            mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                                attr: {
                                    ref: 'G21:J21'
                                }
                            }));
                            mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                                attr: {
                                    ref: 'G22:J22'
                                }
                            }));

                            //Walk In
                            mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                                attr: {
                                    ref: 'A23:A24'
                                }
                            }));
                            mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                                attr: {
                                    ref: 'G23:J23'
                                }
                            }));
                            mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                                attr: {
                                    ref: 'G24:J24'
                                }
                            }));

                            //Total Call In + Walk In
                            mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                                attr: {
                                    ref: 'B25:J25'
                                }
                            }));

                            //Total FA
                            mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                                attr: {
                                    ref: 'B26:J26'
                                }
                            }));

                            //Sales
                            mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                                attr: {
                                    ref: 'A27:A28'
                                }
                            }));
                            // mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                            //     attr: {
                            //         ref: 'H27:J27'
                            //     }
                            // }));
                            // mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                            //     attr: {
                            //         ref: 'H28:J28'
                            //     }
                            // }));

                            //No Sales
                            mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                                attr: {
                                    ref: 'B29:J29'
                                }
                            }));

                            //Total FWO
                            mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                                attr: {
                                    ref: 'B30:J30'
                                }
                            }));

                            //Gloves
                            mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                                attr: {
                                    ref: 'B31:J31'
                                }
                            }));

                            //Locker
                            mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                                attr: {
                                    ref: 'B32:J32'
                                }
                            }));

                            //FL
                            mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                                attr: {
                                    ref: 'B33:J33'
                                }
                            }));

                            //N&N
                            mergeCells[0].appendChild(_createNode(sheet, 'mergeCell', {
                                attr: {
                                    ref: 'B34:J34'
                                }
                            }));

                            mergeCells.attr('count', mergeCells.attr('count') + 1);

                            function _createNode(doc, nodeName, opts) {
                                var tempNode = doc.createElement(nodeName);
                                if (opts) {
                                    if (opts.attr) {
                                        $(tempNode).attr(opts.attr);
                                    }

                                    if (opts.children) {
                                        $.each(opts.children, function (key, value) {
                                            tempNode.appendChild(value);
                                        });
                                    }

                                    if (opts.text !== null && opts.text !== undefined) {
                                        tempNode.appendChild(doc.createTextNode(opts.text));
                                    }
                                }

                                return tempNode;
                            }
                        }
                    },
                    {
                        extend: 'print',
                        title: '@lang('labels.staff_daily_report') - ({{ !empty($_GET['staff']) ? $staffs[$_GET['staff']] : '' }}) {{ !empty($_GET['staff']) ?  ' - '.$_GET['staff'] : '' }} {{ $today }}',
                        footer: true,
                        customize: function ( win ) {
                            $(win.document.body).find( 'h1' )
                                .css( 'font-size', '20px' );
                            $(win.document.body).find('h1').css('text-align', 'center');
                        },
                    }
                ]
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            var url = "{{url('cf-lite')}}/"

            $('#data-table').on('click', '.edit-btn', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    jubilant_id: $(this).attr('id').split('-').pop()
                };

                var type = 'POST';
                var my_url = url + 'master/jubilant/edit-jubilant-ajax';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    success: function (data) {
                        $('input[name="jubilant_id"]').val(data.id);
                        $('input[name="edit_jubilant_name"]').val(data.jubilant_name);
                        $('select#type').val(data.type);
                    },
                    error: function (data) {
                        console.log('Error : ', data);
                    }
                });
            });
        });
    </script>
    <script src={{asset("bootbox/bootbox.min.js")}}></script>
    <script>
        $(document).ready(function () {
            $(document).on('click', '.delete-btn', function () {
                var theform = $(this).siblings('div').children('form');
                bootbox.confirm({
                    message: "@lang('messages.are_you_sure')",
                    buttons: {
                        confirm: {
                            label: '@lang('labels.yes')',
                            className: 'btn-danger'
                        },
                        cancel: {
                            label: '@lang('labels.no')',
                        }
                    },
                    callback: function (result) {
                        if (result == false) {
                        } else {
                            theform.submit();
                        }
                    }
                });
            });
        });
    </script>
@endsection