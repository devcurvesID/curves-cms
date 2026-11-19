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
        .home-btn-title {
            font-family: 'Alegreya Sans', sans-serif;
        }

        .value-base {
            display: block;
        }

        .percent-base {
            display: none;
        }

        .btn-primary,
        .voucher-list-add a,
        #edit-voucher-btn {
            background-color: #ff1493;
            color: #ffffff;
            border-color: #ff1493;
        }

        #edit-voucher-btn{
            margin: 20px 0;
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

    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/settings')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">
                        @lang('labels.corporation')
                    </h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                {!! Form::open(['method' => 'GET', 'action' => ['CorporationController@corporation_list_filter']]) !!}
                <div class="form-group">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="input-group">
                                {!! Form::text('corporate_name', null, ['class' => 'form-control', 'placeholder' => trans('labels.search_corporation')]) !!}
                                <span class="input-group-addon"><span class="fa fa-search"></span></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-2">
                        {!! Form::submit(trans('labels.apply_filter'), ['class' => 'btn btn-primary']) !!}
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <hr>
        <div class="row">
            <div class="col-md-10 col-md-offset-1 voucher-list-add" id="add-corporate">
                <a class="btn btn-default pull-right new-challenge" href="javascript:;" data-toggle="modal" data-target="#addNewCorporate"
                   title="@lang('labels.add_new_corporation')"><span class="fa fa-plus"></span> @lang('labels.add_new_corporation')</a>
            </div>
            <div class="col-md-10 col-md-offset-1">
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="data-table">
                                <thead>
                                <tr>
                                    <th>@lang('labels.number')</th>
                                    <th>@lang('labels.corporation_name')</th>
                                    <th>@lang('labels.address')</th>
                                    <th>@lang('labels.phone')</th>
                                    <th>@lang('labels.email')</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @php($indexing = 1)
                                @foreach($corporates as $corporate)
                                    <tr>
                                        <td>{{$indexing}}</td>
                                        <td>
                                            <a role="button" id="edit-corporate-{{$corporate->id}}" class="edit-corporate" data-toggle="modal" data-target="#addNewCorporate"> {{$corporate->name}} </a>
                                        </td>
                                        <td>{{$corporate->address}}</td>
                                        <td>{{$corporate->phone_number}}</td>
                                        <td>{{$corporate->email}}</td>
                                        <td><a role="button" id="detail-corporate-{{$corporate->id}}" href="{{url('cf-lite/corporate/corporate-mst-detail',$corporate->id)}}" class="btn btn-primary">Detail</a></td>
                                    </tr>
                                @php($indexing++)
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Add Modal -->
        <div class="modal fade" id="addNewCorporate" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                {!! Form::open(['method' => 'PATCH', 'action' => 'CorporationController@store_new_corporation']) !!}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">@lang('labels.add_new_corporation')</h4>
                        {{ Form::hidden('addoredit','add')}}
                        {{ Form::hidden('editid',0)}}
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-inline">
                                    <div class="form-group">
                                        {!! Form::label('corporate_name', trans('labels.corporation_name').': ', ['class' => 'control-label']) !!}<br/>
                                        {!! Form::text('name', null, ['class' => 'form-control']) !!} <br/>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('address', trans('labels.address').':', ['class' => 'control-label']) !!} <br/>
                                        {!! Form::text('address', null, ['class' => 'form-control']) !!} <br/>
                                    </div><br/>
                                    <div class="form-group">
                                    {!! Form::label('phone_number', trans('labels.corporation_phone_number').':', ['class' => 'control-label']) !!} <br/>
                                    {!! Form::text('phone_number', null, ['class' => 'form-control']) !!} <br/>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('email', trans('labels.email').': ', ['class' => 'control-label']) !!} <br/>
                                        {!! Form::text('email', null, ['min' => '0', 'class' => 'form-control']) !!} <br/>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default"
                                data-dismiss="modal">@lang('labels.close')</button>
                        <button type="submit" class="btn btn-primary" id="save-btn">@lang('labels.save')</button>
                    </div>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
        <!-- End Add Modal -->
        <div class="row">
            <div class="col-md-12">
                <div class="paging hiden rom">
                    {{$corporates->links()}}
                </div>
            </div>
        </div>
    </section>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>

    <script>
        $('#data-table').dataTable();
    </script>

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
            var url = "{{url('cf-lite')}}/";

            $('#data-table').on('click', '.edit-corporate', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    corporate_id: $(this).attr('id').split('-').pop()
                };

                var type = 'POST';
                var my_url = url + 'corporate/corporate-group-list-ajax';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        $('input[name="name"]').val(data.corporate['name']);
                        $('input[name="address"]').val(data.corporate['address']);
                        $('input[name="phone_number"]').val(data.corporate['phone_number']);
                        $('input[name="email"]').val(data.corporate['email']);
                        $('#save-btn').text('@lang('labels.edit')');
                        $('#addNewCorporate').find(':hidden[name=addoredit]').prop('value','edit');
                        $('#addNewCorporate').find(':hidden[name=editid]').prop('value',data.corporate['id']);

                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });

            $('#add-corporate').on('click', '.new-challenge', function () {

                $('input[name="name"]').val('');
                $('input[name="address"]').val('');
                $('input[name="phone_number"]').val('');
                $('input[name="email"]').val('');
                $('#save-btn').text('@lang("labels.save")');
                $('#addNewCorporate').find(':hidden[name=addoredit]').prop('value','add');
            });

            $('input[name="search_option"]').on('click', function () {
                $('input[name="s"]').val('');
            })
        });
    </script>
@endsection
