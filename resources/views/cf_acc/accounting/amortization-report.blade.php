@extends('cf_acc.layouts.acc-dashboard-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">
    <link href="https://cdn.datatables.net/buttons/1.6.1/css/buttons.dataTables.min.css" rel="stylesheet">

    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <link href="{{asset('bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css')}}" rel="stylesheet">

    <style>
        .select2 {
            width: 100% !important;
        }

        .my_rupiah {
            text-align: right;
        }
    </style>
@endsection

@section('content')
    <div class="col-xs-12">
        <center>
            <h2 class="page-title">
                @lang('labels.amortization_report')
                <br>
                <small>{{ isset($_GET['year']) ? '('.$_GET['year'].')' : '' }}</small>
            </h2>
        </center>
    </div>
    <div class="col-xs-12">
        <div class="row">
            {!! Form::open(['method' => 'GET']) !!}
            <div class="col-xs-12" style="margin-bottom: 10px;">
                <div class="col-xs-4">
                    {!! Form::label('year', trans('labels.year'), ['class' => 'col-xs-4 control-label']) !!} :
                </div>
                <div class="col-xs-2 input-group" id="year">
                    {!! Form::text('year', null, ['class' => 'form-control']) !!}
                    <span class="input-group-addon">
                        <span class="fa fa-calendar"></span>
                    </span>
                </div>
            </div>
            <div class="col-xs-12">
                <div class="col-xs-4">
                    {!! Form::label('clubs', trans('labels.choose_club'), ['class' => 'col-xs-4 control-label']) !!} :
                </div>
                <div class="col-xs-4" style="margin-left: -15px;">
                    <select name="clubs" id="clubs" class="form-control select2">
                        <option value="">@lang('labels.choose_club')</option>
                        @if(isset($_GET['clubs']))
                            @foreach($clubs as $club)
                                @if($_GET['clubs'] == $club->id)
                                    <option value="{{ $club->id }}" selected="selected">{{ $club->club_name }}</option>
                                @else
                                    <option value="{{ $club->id }}">{{ $club->club_name }}</option>
                                @endif
                            @endforeach
                        @else
                            @foreach($clubs as $club)
                                <option value="{{ $club->id }}">{{ $club->club_name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
                <div class="col-xs-2">
                    <button type="submit" class="btn btn-primary" id="save-btn"><i class="fa fa-search"></i> @lang('labels.search')</button>
                    </a>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
        <hr/>
        <div class="table-responsive">
            <table id="data-table" class="table table-bordered table-striped table-hover" style="background: white">
                <thead>
                <tr>
                    <th rowspan="2">@lang('labels.name')</th>
                    <th rowspan="2">@lang('labels.club')</th>
                    <th rowspan="2">@lang('labels.month')</th>
                    <th rowspan="2">@lang('labels.contract_start')</th>
                    <th rowspan="2">@lang('labels.variety')</th>
                    <th rowspan="2">@lang('labels.initial_value')</th>
                    <th rowspan="2">@lang('labels.addition_or_substraction')</th>
                    <th rowspan="2">@lang('labels.value_after_addition')</th>
                    <th rowspan="2">@lang('labels.period_per_year')</th>
                    <th rowspan="2">@lang('labels.amortization_per_month')</th>
                    <th rowspan="2">@lang('labels.amortization_month')</th>
                    <th rowspan="2">@lang('labels.rest_of_amortization')</th>
                    <th rowspan="2">@lang('labels.ak_initial_amortization') {{ !empty($_GET['year']) ? $_GET['year'] : '' }}</th>
                    <th rowspan="2">@lang('labels.rest_of_initial_book_value') {{ !empty($_GET['year']) ? $_GET['year'] : '' }}</th>
                    <th rowspan="2">@lang('labels.amortization') {{ !empty($_GET['year']) ? $_GET['year'] : '' }}</th>
                    <th rowspan="2">@lang('labels.ak_amortization_until_last') {{ !empty($_GET['year']) ? $_GET['year'] : '' }}</th>
                    <th rowspan="2">@lang('labels.rest_of_last_book_value')</th>
                    <th rowspan="2">@lang('labels.information')</th>
                    <th colspan="12">@lang('labels.month')</th>
                    <th rowspan="2">@lang('labels.total')</th>
                    <th rowspan="2">@lang('labels.difference')</th>
                </tr>
                <tr>
                    <th>1</th>
                    <th>2</th>
                    <th>3</th>
                    <th>4</th>
                    <th>5</th>
                    <th>6</th>
                    <th>7</th>
                    <th>8</th>
                    <th>9</th>
                    <th>10</th>
                    <th>11</th>
                    <th>12</th>
                </tr>
                </thead>
                <tbody>
                @php($count = 1)
                @php($total_nilai_awal = 0)
                @php($total_penambah_pengurangan = 0)
                @php($total_nilai_setelah_penambahan = 0)
                @php($total_amor_per_bulan = 0)
                @php($total_ak_amor_awal = 0)
                @php($total_nilai_sisa_buku_awal = 0)
                @php($total_amortisasi_selanjutnya = 0)
                @php($total_ak_amor_sd = 0)
                @php($total_nilai_sisa_buku_akhir = 0)
                @php($total_bulan = [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0])
                @php($total_from_total = 0)
                @php($total_difference = 0)

                @if(!empty($amortization_reports))
                @foreach($amortization_reports as $report)
                    <tr>
                        <td>{{ strtoupper($report->amorheader_user->name) }}</td>
                        <td>{{ $report->amorheader_user->user_club->club_name }}</td>
                        <td style="text-align: center;">{{ \Carbon\Carbon::parse($report->contract_start)->format('n') }}</td>
                        <td style="text-align: center;">{{-- Tanggal Mulai Kontrak --}}
                            @if(($report->amortization_month + $report->rest_of_amortization_month) < $report->membership_duration)
                                {{ \Carbon\Carbon::parse($report->contract_start)->subYears(floor(($report->amortization_month + $report->rest_of_amortization_month)/12))->format('d-m-Y') }}
                            @else
                                {{ \Carbon\Carbon::parse($report->contract_start)->format('d-m-Y') }}
                            @endif
                        </td>
                        <td>{{ $report->amorheader_membershiptype->membership_type_name }}</td>
                        <td class="my_rupiah">{{ ($report->amortization_month + $report->rest_of_amortization_month < $report->membership_duration) ? $report->membership_duration * $report->monthly_bill : 0 }}</td>
                        <td class="my_rupiah">{{ ($report->amortization_month + $report->rest_of_amortization_month >= $report->membership_duration) ? $report->membership_duration * $report->monthly_bill : 0 }}</td>
                        <td class="my_rupiah">{{ $report->membership_duration * $report->monthly_bill }}</td>
                        <td style="text-align: center;">{{ $report->amortization_month + $report->rest_of_amortization_month }}</td>
                        <td class="my_rupiah">{{ $report->monthly_bill }}</td>
                        <td style="text-align: center;"> {{-- Bulan Amortisasi --}}
                            {{ (($report->amortization_month + $report->rest_of_amortization_month) < $report->membership_duration) ? ($report->membership_duration - $report->amortization_month - $report->rest_of_amortization_month) : $report->amortization_month }}
                        </td>
                        <td style="text-align: center;"> {{-- Sisa Bulan Amortisasi --}}
                            {{ (($report->amortization_month + $report->rest_of_amortization_month) < $report->membership_duration) ? $report->amortization_month : $report->rest_of_amortization_month }}
                        </td>
                        <td class="my_rupiah"> {{-- Amortisasi Awal Tahun --}}
                            {{ (($report->amortization_month + $report->rest_of_amortization_month) < $report->membership_duration) ? (($report->membership_duration - $report->amortization_month - $report->rest_of_amortization_month) * $report->monthly_bill) : 0 }}
                        </td>
                        <td class="my_rupiah"> {{-- Sisa Buku Awal (Total Tagihan - Amortisasi Awal Tahun) --}}
                            {{ ($report->membership_duration * $report->monthly_bill) - (($report->amortization_month + $report->rest_of_amortization_month) < $report->membership_duration) ? (($report->membership_duration - $report->amortization_month - $report->rest_of_amortization_month) * $report->monthly_bill) : 0 }}
                        </td>
                        <td class="my_rupiah"> {{-- Amortisasi Tahun Ini --}}
                            {{ $report->amortization_month * $report->monthly_bill }}
                        </td>
                        <td class="my_rupiah"> {{-- Amortisasi Akhir Tahun Ini (Amortisasi Awal + Amortisasi Tahun Ini) --}}
                            {{ ((($report->amortization_month + $report->rest_of_amortization_month) < $report->membership_duration) ? (($report->membership_duration - $report->amortization_month - $report->rest_of_amortization_month) * $report->monthly_bill) : 0) + ($report->amortization_month * $report->monthly_bill) }}
                        </td>
                        <td class="my_rupiah"> {{-- Nilai Sisa Buku Akhir (Total Tagihan - Amortisasi Akhir Tahun Ini) --}}
                            {{ ($report->membership_duration * $report->monthly_bill) - (((($report->amortization_month + $report->rest_of_amortization_month) < $report->membership_duration) ? (($report->membership_duration - $report->amortization_month - $report->rest_of_amortization_month) * $report->monthly_bill) : 0) + ($report->amortization_month * $report->monthly_bill)) }}
                        </td>
                        <td style="text-align: center;">-</td>

                        {{--Bulan--}}
                        @php($total_per_row = 0)
                        @for($i = 1; $i < 13; $i++)
                            @php($row_gate = false)
                            @foreach($report->amorheader_amordetail as $amor_detail)
                                @if(($amor_detail->month_number == $i) && ($amor_detail->status == 'Published'))
                                    <td class="my_rupiah">{{ $amor_detail->amount_paid }}</td>
                                    @php($row_gate = true)
                                    @php($total_bulan[$i - 1] += $amor_detail->amount_paid)
                                    @php($total_per_row += $amor_detail->amount_paid)
                                @endif
                            @endforeach

                            @if(!$row_gate)
                                <td>-</td>
                            @endif
                        @endfor

                        <td class="my_rupiah">{{ $total_per_row }}</td> {{-- Total Pembayaran Hingga Akhir Tahun (Bulan 1:Bulan 12) --}}
                        <td class="my_rupiah"> {{-- Uang yang belum dibayarkan (Amortisasi Tahun Ini - Total Pembayaran) --}}
                            {{ ($report->amortization_month * $report->monthly_bill) - $total_per_row }}
                        </td>
                    </tr>
                    @php($total_nilai_awal += ($report->amortization_month + $report->rest_of_amortization_month < $report->membership_duration) ? $report->membership_duration * $report->monthly_bill : 0)
                    @php($total_penambah_pengurangan += ($report->amortization_month + $report->rest_of_amortization_month >= $report->membership_duration) ? $report->membership_duration * $report->monthly_bill : 0)
                    @php($total_nilai_setelah_penambahan += $report->membership_duration * $report->monthly_bill)
                    @php($total_amor_per_bulan += $report->monthly_bill)
                    @php($total_ak_amor_awal += (($report->amortization_month + $report->rest_of_amortization_month) < $report->membership_duration) ? (($report->membership_duration - $report->amortization_month - $report->rest_of_amortization_month) * $report->monthly_bill) : 0)
                    @php($total_nilai_sisa_buku_awal += ($report->membership_duration * $report->monthly_bill) - (($report->amortization_month + $report->rest_of_amortization_month) < $report->membership_duration) ? (($report->membership_duration - $report->amortization_month - $report->rest_of_amortization_month) * $report->monthly_bill) : 0)
                    @php($total_amortisasi_selanjutnya += $report->amortization_month * $report->monthly_bill)
                    @php($total_ak_amor_sd += ((($report->amortization_month + $report->rest_of_amortization_month) < $report->membership_duration) ? (($report->membership_duration - $report->amortization_month - $report->rest_of_amortization_month) * $report->monthly_bill) : 0) + ($report->amortization_month * $report->monthly_bill))
                    @php($total_nilai_sisa_buku_akhir += ($report->membership_duration * $report->monthly_bill) - (((($report->amortization_month + $report->rest_of_amortization_month) < $report->membership_duration) ? (($report->membership_duration - $report->amortization_month - $report->rest_of_amortization_month) * $report->monthly_bill) : 0) + ($report->amortization_month * $report->monthly_bill)))
                    @php($total_from_total += $total_per_row)
                    @php($total_difference += ($report->amortization_month * $report->monthly_bill) - $total_per_row)
                @endforeach
                @endif
                <tr style="background-color: #716c6c42;">
                    <td style="font-weight: bold;">@lang('labels.total')</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right; font-weight: bold;">
                        Rp. {{ $total_nilai_awal < 0 ? '- ' : ''}}
                        <span class='my_rupiah'>{{ $total_nilai_awal >= 0 ? $total_nilai_awal : 0 }}</span>
                    </td>
                    <td style="text-align: right; font-weight: bold;">
                        Rp. {{ $total_penambah_pengurangan < 0 ? '- ' : ''}}
                        <span class='my_rupiah'>{{ $total_penambah_pengurangan >= 0 ? $total_penambah_pengurangan : 0 }}</span>
                    </td>
                    <td style="text-align: right; font-weight: bold;">
                        Rp. {{ $total_nilai_setelah_penambahan < 0 ? '- ' : ''}}
                        <span class='my_rupiah'>{{ $total_nilai_setelah_penambahan >= 0 ? $total_nilai_setelah_penambahan : 0 }}</span>
                    </td>
                    <td></td>
                    <td style="text-align: right; font-weight: bold;">
                        Rp. {{ $total_amor_per_bulan < 0 ? '- ' : ''}}
                        <span class='my_rupiah'>{{ $total_amor_per_bulan >= 0 ? $total_amor_per_bulan : 0 }}</span>
                    </td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right; font-weight: bold;">
                        Rp. {{ $total_ak_amor_awal < 0 ? '- ' : ''}}
                        <span class='my_rupiah'>{{ $total_ak_amor_awal >= 0 ? $total_ak_amor_awal : 0 }}</span>
                    </td>
                    <td style="text-align: right; font-weight: bold;">
                        Rp. {{ $total_nilai_sisa_buku_awal < 0 ? '- ' : ''}}
                        <span class='my_rupiah'>{{ $total_nilai_sisa_buku_awal >= 0 ? $total_nilai_sisa_buku_awal : 0 }}</span>
                    </td>
                    <td style="text-align: right; font-weight: bold;">
                        Rp. {{ $total_amortisasi_selanjutnya < 0 ? '- ' : ''}}
                        <span class='my_rupiah'>{{ $total_amortisasi_selanjutnya >= 0 ? $total_amortisasi_selanjutnya : 0 }}</span>
                    </td>
                    <td style="text-align: right; font-weight: bold;">
                        Rp. {{ $total_ak_amor_sd < 0 ? '- ' : ''}}
                        <span class='my_rupiah'>{{ $total_ak_amor_sd >= 0 ? $total_ak_amor_sd : 0 }}</span>
                    </td>
                    <td style="text-align: right; font-weight: bold;">
                        Rp. {{ $total_nilai_sisa_buku_akhir < 0 ? '- ' : ''}}
                        <span class='my_rupiah'>{{ $total_nilai_sisa_buku_akhir >= 0 ? $total_nilai_sisa_buku_akhir : 0 }}</span>
                    </td>
                    <td></td>
                    @for($i = 0; $i < 12; $i++)
                        <td style="text-align: right; font-weight: bold;">
                            Rp. {{ $total_bulan[$i] < 0 ? '- ' : ''}}
                            <span class='my_rupiah'>{{ $total_bulan[$i] >= 0 ? $total_bulan[$i] : 0 }}</span>
                        </td>
                    @endfor
                    <td style="text-align: right; font-weight: bold;">
                        Rp. {{ $total_from_total < 0 ? '- ' : ''}}
                        <span class='my_rupiah'>{{ $total_from_total >= 0 ? $total_from_total : 0 }}</span>
                    </td>
                    <td style="text-align: right; font-weight: bold;">
                        Rp. {{ $total_difference < 0 ? '- ' : ''}}
                        <span class='my_rupiah'>{{ $total_difference >= 0 ? $total_difference : 0 }}</span>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.flash.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/1.6.1/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/fixedheader/3.1.6/js/dataTables.fixedHeader.min.js"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>

    <script src="{{asset('js/my_js.js')}}"></script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            init_format();

            $('#year').datetimepicker({
                viewMode: 'years',
                format: 'YYYY'
            });

            $('#clubs').select2();

            $('#data-table').DataTable({
                dom: 'Bfrtip',
                bPaginate: false,
                ordering: false,
                buttons: [
                    {
                        extend: 'excel',
                        title: '@lang('labels.amortization_report') {{ !empty($_GET['year']) ? $_GET['year'] : '' }}',
                        footer: true,
                        exportOptions: {
                            columns: ':visible',
                            format: {
                                body: function(data, row, column, node) {
                                    data = $('<p>' + data + '</p>').text();
                                    return rupiah_format_destroyer(data);
                                }
                            }
                        },
                        customize: function( xlsx, row ) {
                            var sheet = xlsx.xl.worksheets['sheet1.xml'];
                            $('row c', sheet).attr( 's', 63);
                        }
                    },
                    {
                        extend: 'print',
                        title: '@lang('labels.amortization_report')',
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

        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text(x);
            });
            $('.rupiah').each(function () {
                var x = rupiah_formater($(this).val().replace(/\s/g, ''));
                $(this).val((x));
            });
        }
    </script>
@endsection