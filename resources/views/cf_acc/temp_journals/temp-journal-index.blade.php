@extends('cf_acc.layouts.acc-dashboard-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">

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
                <a href="{{url('cf-acc/dashboard')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">
                        @lang('labels.temporary_journal')
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
                        <div class="col-md-3">
                            {!! Form::select('jubilant', $jubilants, null, ['class' => 'form-control select2', 'placeholder' => trans('labels.choose_jubilant')]) !!}
                        </div>
                        <div class="col-md-3">
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
                                    <th>@lang('labels.indexing')</th>
                                    <th>@lang('labels.date')</th>
                                    <th>@lang('labels.bank_date')</th>
                                    <th>@lang('labels.information')</th>
                                    <th>@lang('labels.total')</th>
                                    <th>@lang('labels.action')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php($indexing = 1)
                                @foreach($temp_journals as $tj)
                                    <tr id="row-{{ $tj->id }}">
                                        <td>{{$indexing}}</td>
                                        <td>
                                            {!! Form::date('trans_date_'.$tj->id, \Carbon\Carbon::parse($tj->trans_date), ['class' => 'form-control', 'id' => 'td-'.$tj->id]) !!}
                                        </td>
                                        <td>
                                            {!! Form::date('trans_date_'.$tj->id, !empty($tj->bank_date) ? \Carbon\Carbon::parse($tj->bank_date) : null, ['class' => 'form-control', 'id' => 'bd-'.$tj->id]) !!}
                                        </td>
                                        <td>{{ $tj->information }}</td>
                                        <td>
                                            <div class="input-group">
                                                <span class="input-group-addon">Rp.</span>
                                                {!! Form::text('debit_'.$tj->id, $tj->debit, ['class' => 'form-control rupiah', 'id' => 'debit-'.$tj->id, 'style' => 'min-width: 100px']) !!}
                                                <span class="input-group-addon">,-</span>
                                            </div>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-default btn-submit" id="btn-submit-{{$tj->id}}" title="@lang('labels.submit')">
                                                <span class="fa fa-check"></span>
                                            </button>
                                        </td>
                                    </tr>
                                    @php($indexing++)
                                @endforeach
                                </tbody>
                            </table>
                            <div>{{ $temp_journals->onEachSide(3)->links() }} </div>
                            <div> Showing {{ $temp_journals->firstItem() }} to {{ $temp_journals->lastItem() }}
                                of total {{$temp_journals->total()}} entries</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>

    <script src="https://cdn.datatables.net/buttons/1.5.2/js/dataTables.buttons.min.js"></script>

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

            /* $('#data-table').dataTable({
                'iDisplayLength': -1,
                'lengthMenu': [[10, 50, 100, -1], [10, 50, 100, '@lang('labels.all')']],
            });
            temp_journals.bindResetListeners(); */
        });

        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp.' + (x));
            });

            $('.rupiah').each(function () {
                var x = rupiah_formater($(this).val().replace(/\s/g, ''));
                $(this).val((x));
            });
        }
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>

    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script>
        $(document).ready(function () {
            $('input[name="search_option"]').on('click', function () {
                $('input[name="s"]').val('');
            });

            $('select[name="jubilant"]').select2();
            $('select[name="club"]').select2();

            $('#data-table').on('click', '.btn-submit', function () {
                var id = $(this).attr('id').split('-').pop();
                console.log(id);

                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    tj_id: id,
                    trans_date: $('#td-' + id).val(),
                    bank_date: $('#bd-' + id).val(),
                    debit: rupiah_format_destroyer($('#debit-' + id).val())
                };

                var type = 'POST';
                var my_url = "{{url('cf-acc')}}/temp-journal/store";

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
        });
    </script>
@endsection
