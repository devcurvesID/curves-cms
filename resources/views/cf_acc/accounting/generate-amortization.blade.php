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
    </style>
@endsection

@section('content')
    <div class="col-xs-12">
        <center>
            <h2 class="page-title">
                @lang('labels.temp_amortization')
                <br>
                <small>{{ isset($_GET['month']) ? '('.$_GET['month'].')' : '' }}</small>
            </h2>
        </center>
    </div>
    <div class="col-xs-12">
        <div class="row">
            {!! Form::open(['method' => 'GET']) !!}
            <div class="col-xs-12" style="margin-bottom: 10px;">
                <div class="col-xs-4">
                    {!! Form::label('month_year', trans('labels.month').'/'.trans('labels.year'), ['class' => 'col-xs-6 control-label']) !!} :
                </div>
                <div class="col-xs-2 input-group" id="month">
                    {!! Form::text('month', null, ['class' => 'form-control']) !!}
                    <span class="input-group-addon">
                        <span class="fa fa-calendar"></span>
                    </span>
                </div>
            </div>
            <div class="col-xs-12" style="margin-bottom: 10px">
                <div class="col-xs-4">
                    {!! Form::label('jubilant', trans('labels.choose_jubilant'), ['class' => 'col-xs-6 control-label']) !!} :
                </div>
                <div class="col-xs-4" style="margin-left: -15px;">
                    {!! Form::select('jubilant', $jubilants, null, ['class' => 'form-control select2', 'placeholder' => trans('labels.choose_jubilant')]) !!}
                </div>
            </div>
            <div class="col-xs-12">
                <div class="col-xs-4">
                    {!! Form::label('club', trans('labels.choose_club'), ['class' => 'col-xs-6 control-label']) !!} :
                </div>
                <div class="col-xs-4" style="margin-left: -15px;">
                    <select name="club" id="club" class="form-control select2">
                        <option value="">@lang('labels.choose_club')</option>
                        @if(!empty($_GET['jubilant']))
                            @foreach($clubs as $club)
                                @if($club->jubilant_id == $_GET['jubilant'])
                                    @if($_GET['club'] == $club->id)
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
                    <th>@lang('labels.indexing')</th>
                    <th>@lang('labels.month') / @lang('labels.year')</th>
                    <th>@lang('labels.club')</th>
                    <th>@lang('labels.total')</th>
                    <th>@lang('labels.action')</th>
                </tr>
                </thead>
                <tbody>
                @if($amortizations != null)
                    @php($counter = 1)
                    @foreach($amortizations as $amortization)
                        <tr id="row-{{ $counter }}">
                            <td>{{ $counter }}</td>
                            <td>
                                {{ $amortization->month }} {{ $amortization->year }}
                                {!! Form::hidden('month_'.$counter, $amortization->month.'-'.$amortization->year) !!}
                            </td>
                            <td>
                                {{ ucwords($amortization->club) }}
                                {!! Form::hidden('club_id_'.$counter, $amortization->club_id) !!}
                            </td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-addon">Rp.</span>
                                    {!! Form::text('total_'.$counter, $amortization->total, ['class' => 'form-control rupiah total', 'id' => 'total-'.$counter]) !!}
                                    <span class="input-group-addon">,-</span>
                                </div>
                            </td>
                            <td>
                                <button class="btn btn-primary btn-submit" id="submit-{{ $counter }}">@lang('labels.submit')</button>
                            </td>
                        </tr>
                        @php($counter++)
                    @endforeach
                @endif
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>

    <script src="{{asset('js/my_js.js')}}"></script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script src="{{asset('js/moment.min.js')}}"></script>
    <script src="{{asset('bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            init_format();

            $('#month').datetimepicker({
                viewMode: 'months',
                format: 'MM-YYYY'
            });

            $('select[name="jubilant"]').select2();
            $('select[name="club"]').select2();

            $('#data-table').DataTable();

            $('select[name="jubilant"]').on('change', function () {
                html = '<option value="">@lang('labels.choose_club')</option>';

                @foreach($clubs as $club)
                    var jubilant = '{{ $club->jubilant_id }}';

                    if (jubilant == $(this).val()) {
                        html += '<option value="{{ $club->id }}">{{ $club->club_name }}</option>';
                    }
                @endforeach

                $('select[name="club"]').html(html);
            });

            $('.total').on('keyup', function () {
                var id = $(this).attr('id').split('-').pop();
                if ($(this).val() == "") {
                    var total = 0;
                } else {
                    var total = parseInt(rupiah_format_destroyer($(this).val()));
                }
                $('input[name="total_' + id + '"]').val(rupiah_formater(total));
            });

            $('#data-table').on('click', '.btn-submit', function() {
                var id = $(this).attr('id').split('-').pop();

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    date: $('input[name="month_' + id + '"]').val(),
                    club: $('input[name="club_id_' + id + '"]').val(),
                    total: rupiah_format_destroyer($('input[name="total_' + id + '"]').val())
                };

                var type = 'POST';
                var my_url = "{{url('cf-acc')}}/generate-amortization";

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    success: function (data) {
                        console.log(data);
                        if (data == true) {
                            $('#row-' + id).remove();
                        }
                    },
                    error: function (data) {
                        console.log('Error : ', data);
                    }
                });
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