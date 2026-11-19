@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <style>
        .btn-primary,
        .voucher-list-add a,
        #edit-voucher-btn {
            background-color: #ff1493;
            color: #ffffff;
            border-color: #ff1493;
        }

        .btn-primary:hover,
        .voucher-list-add a:hover,
        #edit-voucher-btn:hover {
            color: #ff1493;
            background-color: transparent;
            border-color: #ff1493;
        }

        div.dataTables_wrapper div.dataTables_length select {
            margin: 0 10px;
        }

        table th{
            color: #ff1493;
        }

        tfoot tr td {
            font-weight: 600;
        }
    </style>
@endsection

@section('content')
    <section id="staff-home">
        @include('cf_lite.includes.error-message')
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/dashboard')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">
                        @lang('labels.debt')
                    </h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                {!! Form::open(['method' => 'GET']) !!}
                <div class="form-group">
                    <div class="row">
                        <div class="col-md-2">
                            {!! Form::date('from_date', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-2">
                            {!! Form::date('thru_date', null, ['class' => 'form-control']) !!}
                        </div>
                        <div class="col-md-1">
                            <label class="radio-inline">
                                <input type="radio" name="member_status" id="member-status1" value="Active" checked> @lang('labels.active')
                            </label>
                        </div>
                        <div class="col-md-1">
                            <label class="radio-inline">
                                <input type="radio" name="member_status" id="member-status2" value="All" {{ ((isset($_GET['member_status']) && ($_GET['member_status'] == 'All'))) ? 'checked' : '' }}> @lang('labels.all')
                            </label>
                        </div>
                        <div class="col-md-1">
                            {!! Form::submit(trans('labels.apply_filter'), ['class' => 'btn btn-primary']) !!}
                        </div>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="data-table">
                                <thead>
                                <tr>
                                    <th>@lang('labels.number')</th>
                                    <th width="130">@lang('labels.name')</th>
                                    <th>@lang('labels.membership_type')</th>
                                    <th>@lang('labels.status')</th>
                                    <th>@lang('labels.due_date')</th>
                                    <th>@lang('labels.outdated_debt')</th>
                                    <th>@lang('labels.debt_trial')</th>
                                    <th>@lang('labels.membership_expired')</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php
                                $indexing = 1;
                                foreach($debts as $debt):
                                ?>
                                    <tr>
                                        <td>{{$indexing}}</td>
                                        <td>{{$debt->name}}</td>
                                        <td>{{$debt->membership_type_name}}</td>
                                        <td>{{$debt->status}}</td>
                                        <td>
                                            @php
                                                $dates = str_replace('{', '', $debt->paydate);
                                                $dates = str_replace('}', '', $dates);
                                                $dates = str_replace('"', '', $dates);
                                                $dates = explode(',', $dates);
                                            @endphp
                                            @for($i = 0; $i < count($dates); $i++)
                                                {{\Carbon\Carbon::parse($dates[$i])->format('d-m-Y')}}
                                            @endfor
                                            {{--{{$debt->paydate}}--}}
                                            {{--{{count($dates)}}--}}
                                        </td>
                                        <td class="my_rupiah">{{$debt->due_debt}}</td>
                                        <td class="my_rupiah">{{$debt->due_trial}}</td>
                                        <td>{{\Carbon\Carbon::parse($debt->expired)->format('d-m-Y')}}</td>
                                    </tr>
                                    <?php $indexing++;
                                endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5" class="text-uppercase">@lang('labels.total')</td>
                                        <td></td>
                                        <td></td>
                                        <td></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        {{--<div class="row">--}}
            {{--<div class="col-md-12">--}}
                {{--<div class="paging hiden rom">--}}
                    {{--{{$paymentDebts->links()}}--}}
                {{--</div>--}}
            {{--</div>--}}
        {{--</div>--}}
    </section>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>

    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.5.2/js/buttons.html5.min.js"></script>

    <script type="text/javascript">
        function numberOnlyInput(event){
            var charCode = (event.which) ? event.which : event.keyCode;
            if(charCode > 31 && (charCode < 48 || charCode > 57)){
                return false;
            }
            return true;
        }
    </script>

    <script>
        $(document).ready(function () {
            init_format();

            $('#data-table').dataTable({
                'iDisplayLength': 10,
                'lengthMenu': [[10, 50, 100, -1], [10, 50, 100, '@lang('labels.all')']],
                'footerCallback': function (row, data, start, end, display) {
                    var api = this.api(), data;

                    // Remove the formatting to get integer data for summation
//                    var intVal = function ( i ) {
//                        return typeof i === 'string' ?
//                            i.replace(/[\Rp.,]/g, '')*1 :
//                            typeof i === 'number' ?
//                                i : 0;
//                    };
                    var intVal = function (i) {
                        return parseInt(rupiah_format_destroyer(i));
                    }

                    var outdatedDebt = api
                        .column(5)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                    var debtTrial = api
                        .column(6)
                        .data()
                        .reduce( function (a, b) {
                            return intVal(a) + intVal(b);
                        }, 0 );

                    // Update footer
                    $( api.column(5).footer() ).html('Rp. ' + rupiah_formater(outdatedDebt) + ',-');
                    $( api.column(6).footer() ).html('Rp. ' + rupiah_formater(debtTrial) + ',-');
                },
                dom: 'Bfrtip',
                buttons: [
                    {
                        extend: 'excelHtml5',
                        text: '<span class="fa fa-download" title="@lang('labels.export_excel')"></span> Excel',
                        footer: true,
                        title: '@lang('labels.debt')'
                    }
                ]
            });
            debts.bindResetListeners();
        });

        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp.' + (x));
            });
        }
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>

    <script>
        $(document).ready(function () {
            $('input[name="search_option"]').on('click', function () {
                $('input[name="s"]').val('');
            })
        });
    </script>
@endsection
