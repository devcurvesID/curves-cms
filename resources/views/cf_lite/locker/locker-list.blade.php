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
                <a href="{{url('cf-lite/locker/locker-setting-choice')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">
                        @lang('labels.lockers')
                    </h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                {!! Form::open(['method' => 'GET', 'action' => ['LockerController@locker_list_filter']]) !!}
                <div class="form-group">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="input-group">
                                {!! Form::text('locker_name', null, ['class' => 'form-control', 'placeholder' => trans('labels.search_locker')]) !!}
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
            <div class="col-md-10 col-md-offset-1 voucher-list-add" id="add-locker">
                <a class="btn btn-default pull-right new-locker" href="javascript:;" data-toggle="modal" data-target="#addNewLocker"
                   title="@lang('labels.add_new_lockers')"><span class="fa fa-plus"></span> @lang('labels.add_new_lockers')</a>
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
                                    <th>@lang('labels.name')</th>
                                    <th>@lang('labels.duration')</th>
                                    <th>@lang('labels.duration_type')</th>
                                    <th>@lang('labels.price')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php($indexing = 1)
                                @foreach($lockers as $locker)
                                    <tr>
                                        <td>{{$indexing}}</td>
                                        <td>
                                            <a role="button" id="edit-locker-{{$locker->id}}" class="edit-locker" data-toggle="modal" data-target="#addNewLocker"> {{$locker->name}} </a>
                                        </td>
                                        <td>{{$locker->duration}}</td>
                                        <td>{{$locker->duration_type}}</td>
                                        <td class="my_rupiah">{{$locker->price}}</td>
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
        <div class="modal fade" id="addNewLocker" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                {!! Form::open(['method' => 'PATCH', 'action' => 'LockerController@store_locker']) !!}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">@lang('labels.add_new_lockers')</h4>
                        {{ Form::hidden('addoredit','add')}}
                        {{ Form::hidden('editid',0)}}
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-inline">
                                    <div class="form-group">
                                        {!! Form::label('name', trans('labels.name').': ', ['class' => 'control-label']) !!}<br/>
                                        {!! Form::text('name', null, ['class' => 'form-control']) !!} <br/>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('duration', trans('labels.duration').':', ['class' => 'control-label']) !!} <br/>
                                        {!! Form::text('duration', null, ['class' => 'form-control']) !!} <br/>
                                    </div><br/>
                                    <div class="form-group">
                                    {!! Form::label('duration_type', trans('labels.duration_type').':', ['class' => 'control-label']) !!} <br/>
                                            <label class="radio-inline">
                                            {{ Form::radio('duration_type','daily',true),['class' => 'col-md-3'] }} @lang('labels.daily')
                                            </label><br/>
                                            <label class="radio-inline">
                                            {{ Form::radio('duration_type','monthly') ,['class' => 'col-md-3'] }} @lang('labels.monthly')
                                            </label><br/>
                                            <label class="radio-inline">
                                            {{ Form::radio('duration_type','yearly') ,['class' => 'col-md-3'] }} @lang('labels.yearly')
                                            </label><br/>
                                    </div><br/>
                                    <div class="form-group">
                                        {!! Form::label('price', trans('labels.price').': ', ['class' => 'control-label','onkeypress'=>'numberOnlyInput(event)']) !!} <br/>
                                        {!! Form::text('price', null, ['min' => '0', 'class' => 'form-control', 'onkeypress' => 'return numberOnlyInput(event)']) !!} <br/>
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
                    {{$lockers->links()}}
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

            $('#data-table').on('click', '.edit-locker', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    locker_id: $(this).attr('id').split('-').pop()
                };

                var type = 'POST';
                var my_url = url + 'lockers/locker-list-ajax';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        $('input[name="name"]').val(data.locker['name']);
                        $('input[name="duration"]').val(data.locker['duration']);
                        $('input[name="price"]').val(data.locker['price']);
                        $('#save-btn').text('@lang('labels.edit')');
                        $('#addNewLocker').find(':hidden[name=addoredit]').prop('value','edit');
                        $('#addNewLocker').find(':hidden[name=editid]').prop('value',data.locker['id']);

                        if(data.locker['duration_type'] == 'daily')
                            $('#addNewLocker').find(':radio[name=duration_type][value="daily"]').prop('checked',true);
                        else if(data.locker['duration_type'] == 'monthly')
                            $('#addNewLocker').find(':radio[name=duration_type][value="monthly"]').prop('checked',true);
                        else if(data.locker['duration_type'] == 'yearly')
                            $('#addNewLocker').find(':radio[name=duration_type][value="yearly"]').prop('checked',true);
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });

            $('#add-locker').on('click', '.new-locker', function () {

                $('input[name="name"]').val('');
                $('input[name="duration"]').val('');
                $('input[name="price"]').val('');
                $('#save-btn').text('@lang("labels.save")');
                $('#addNewLocker').find(':radio[name=duration_type][value="daily"]').prop('checked',true);
                $('#addNewLocker').find(':hidden[name=addoredit]').prop('value','add');
            });

            $('input[name="search_option"]').on('click', function () {
                $('input[name="s"]').val('');
            })
        });
    </script>
@endsection
