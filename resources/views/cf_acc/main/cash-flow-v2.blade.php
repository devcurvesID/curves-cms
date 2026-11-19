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

    <style>
        .select2 {
            width: 100% !important;
        }
    </style>
@endsection

@section('content')
    <div class="col-xs-12">
        <center>
            <h2 class="page-title">
                @lang('labels.cash_flow')
                <br><small>({{Carbon\Carbon::parse($fromdate)->format('d-m-Y')}} @lang('labels.until') {{Carbon\Carbon::parse($thrudate)->format('d-m-Y')}})</small>
            </h2>
        </center>
    </div>
    <div class="col-xs-12">
        <div class="row">
            {!! Form::open(['method' => 'GET']) !!}
            <div class="col-xs-12">
                <div class="col-xs-3">
                    {!! Form::label('filter', trans('labels.filter'), ['class' => 'col-md-4 control-label']) !!} :
                </div>
                <div class="col-xs-3">
                    {!! Form::date('from_date', $fromdate , ['class' => 'form-control filter-date','placeholder'=>@trans('labels.from_date'), 'id'=>'filt-dari']) !!} <br/>
                </div>
                <div class="col-xs-3">
                    {!! Form::date('thru_date', $thrudate , ['class' => 'form-control filter-date','placeholder'=>@trans('labels.from_date'), 'id'=>'filt-sampai']) !!} <br/>
                </div>
            </div>
            <div class="col-xs-12" style="margin-bottom: 10px;">
                <div class="col-xs-3">
                    {!! Form::label('jubilant', trans('labels.choose_jubilant'), ['class' => 'col-md-4 control-label']) !!} :
                </div>
                <div class="col-xs-6">
                    {!! Form::select('jubilant', $jubilants, null, ['class' => 'form-control select2', 'id' => 'jubilant', 'placeholder'=> trans('labels.choose_jubilant')]) !!}
                </div>
            </div>
            <div class="col-xs-12">
                <div class="col-xs-3">
                    {!! Form::label('club', trans('labels.choose_club'), ['class' => 'col-md-4 control-label']) !!} :
                </div>
                <div class="col-xs-6">
                    <select name="clubs" id="clubs" class="form-control select2">
                        <option value="">@lang('labels.choose_club')</option>
                        @if(!empty($_GET['jubilant']))
                            @foreach($clubs as $club)
                                @if($club->jubilant_id == $_GET['jubilant'])
                                    @if($_GET['clubs'] == $club->id)
                                        <option value="{{ $club->id }}" selected="selected">{{ $club->club_name }}</option>
                                    @else
                                        <option value="{{ $club->id }}">{{ $club->club_name }}</option>
                                    @endif
                                @endif
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
                    <th>@lang('labels.date')</th>
                    <th>@lang('labels.acc_no')</th>
                    <th>@lang('labels.notes')</th>
                    <th>@lang('labels.club')</th>
                    <th>@lang('labels.debet')</th>
                    <th>@lang('labels.credit')</th>
                </tr>
                </thead>
                <tbody>
                @php($count = 1)
                @php($totaldeb = 0)
                @php($totalcre = 0)
                @foreach($accJournals as $aj)
                    <tr>
                        <td>{{ Carbon\Carbon::parse($aj->trans_date)->format('d-m-Y')}}</td>
                        <td>{{ $aj->accjournalv2_coa->coa_number}}</td>
                        <td>{{ $aj->information}}</td>
                        <td>{{ $aj->accjournalv2_club->club_name }}</td>
                        <td style="text-align: right;">
                            {{ $aj->debit < 0 ? '- ' : ''}}
                            <span class="my_rupiah">{{ ($aj->debit==0) ? '' : ($aj->debit >= 0 ? $aj->debit : $aj->debit*-1) }}</span>
                        </td>
                        <td style="text-align: right;">
                            {{ $aj->credit < 0 ? '- ' : ''}}
                            <span class="my_rupiah">{{ ($aj->credit==0) ? '' : ($aj->credit >= 0 ? $aj->credit : $aj->credit*-1) }}</span>
                        </td>
                    </tr>
                    @php($totaldeb += $aj->debit)
                    @php($totalcre += $aj->credit)
                @endforeach
                </tbody>
                <tfoot>
                <tr style="background-color: #716c6c42;">
                    <td style="font-weight: bold;">@lang('labels.total')</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right; font-weight: bold;">
                        Rp. {{ $totaldeb < 0 ? '- ' : ''}}
                        <span class='my_rupiah'>{{ $totaldeb >= 0 ? $totaldeb : $totaldeb*-1 }}</span>
                    </td>
                    <td style="text-align: right; font-weight: bold;">
                        Rp. {{ $totalcre < 0 ? '- ' : ''}}
                        <span class='my_rupiah'>{{ $totalcre >= 0 ? $totalcre : $totalcre*-1 }}</span>
                    </td>
                </tr>
                </tfoot>
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
    <script>
        $(document).ready(function () {
            $('#jubilant').select2();
            $('#clubs').select2();

            $('#data-table').DataTable({
                dom: 'Bfrtip',
                bPaginate: false,
                fixedHeader: true,
                ordering: false,
                buttons: [
                    {
                        extend: 'excel',
                        title: '@lang('labels.cash_flow') </br> ({{\Carbon\Carbon::parse($fromdate)->format('d-m-Y')}} - {{\Carbon\Carbon::parse($thrudate)->format('d-m-Y')}})',
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
                        // customize: function ( win ) {
                        //     $(win.document.body).find( 'h1' )
                        //         .css( 'font-size', '20px' );
                        //     $(win.document.body).find('h1').css('text-align', 'center');
                        // },
                    },
                    {
                        extend: 'print',
                        title: '@lang('labels.cash_flow') </br> ({{\Carbon\Carbon::parse($fromdate)->format('d-m-Y')}} - {{\Carbon\Carbon::parse($thrudate)->format('d-m-Y')}})',
                        footer: true,
                        customize: function ( win ) {
                            $(win.document.body).find( 'h1' )
                                .css( 'font-size', '20px' );
                            $(win.document.body).find('h1').css('text-align', 'center');
                        },
                    }
                ]
            });

            init_format();

            $('#jubilant').on('change', function () {
                var html = '<option value="">@lang('labels.choose_club')</option>';
                var choose_jubilant = $(this).val();

                @foreach($clubs as $club)
                    var jubilant = '{{ $club->jubilant_id }}';
                    if(jubilant == choose_jubilant) {
                        html += '<option value="{{ $club->id }}">{{ $club->club_name }}</option>';
                    }
                @endforeach
                $('#clubs').html(html);
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
