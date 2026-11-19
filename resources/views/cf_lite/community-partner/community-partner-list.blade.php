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
                <a href="{{url('cf-lite/community-partner/choose-cp-menu')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">
                        @lang('labels.community_partner')
                    </h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                {!! Form::open(['method' => 'GET', 'action' => ['CommunityPartnerController@cp_list_filter']]) !!}
                <div class="form-group">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="input-group">
                                {!! Form::text('cp_name', null, ['class' => 'form-control', 'placeholder' => trans('labels.search_community_partner')]) !!}
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
            <div class="col-md-10 col-md-offset-1 voucher-list-add" id="add-cp">
                <a class="btn btn-default pull-right new-cp" href="javascript:;" data-toggle="modal" data-target="#addNewCP"
                   title="@lang('labels.add_new_cp')"><span class="fa fa-plus"></span> @lang('labels.add_new_cp')</a>
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
                                    <th>@lang('labels.cp_name')</th>
                                    <th>@lang('labels.pic_name')</th>
                                    <th>@lang('labels.address')</th>
                                    <th>@lang('labels.phone')</th>
                                    <th>@lang('labels.email')</th>
                                    <th>@lang('labels.status')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php($indexing = 1)
                                @foreach($cps as $cp)
                                    <tr>
                                        <td>{{$indexing}}</td>
                                        <td>
                                            <a role="button" id="edit-cp-{{$cp->id}}" class="edit-cp" data-toggle="modal" data-target="#addNewCP"> {{$cp->cp_name}} </a>
                                        </td>
                                        <td>{{$cp->pic_name}}</td>
                                        <td>{{$cp->address}}</td>
                                        <td>{{$cp->phone_number}}</td>
                                        <td>{{$cp->email}}</td>
                                        <td>{{$cp->status}}</td>
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
        <div class="modal fade" id="addNewCP" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                {!! Form::open(['method' => 'PATCH', 'action' => 'CommunityPartnerController@store_cp']) !!}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">@lang('labels.add_new_cp')</h4>
                        {{ Form::hidden('addoredit','add')}}
                        {{ Form::hidden('editid',0)}}
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-inline">
                                    <div class="form-group">
                                        {!! Form::label('cp_name', trans('labels.cp_name').': ', ['class' => 'control-label']) !!}<br/>
                                        {!! Form::text('cp_name', null, ['class' => 'form-control']) !!} <br/>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('pic_name', trans('labels.pic_name').':', ['class' => 'control-label']) !!} <br/>
                                        {!! Form::text('pic_name', null, ['class' => 'form-control']) !!}
                                    </div><br/>
                                    <div class="form-group">
                                    {!! Form::label('address', trans('labels.address').':', ['class' => 'control-label']) !!} <br/>
                                    {!! Form::textarea('address', null, ['class' => 'form-control']) !!}
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('phone_number', trans('labels.phone').': ', ['class' => 'control-label']) !!} <br/>
                                        {!! Form::text('phone_number', null, ['class' => 'form-control']) !!} <br/>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('email', trans('labels.email').': ', ['class' => 'control-label']) !!} <br/>
                                        {!! Form::text('email', null, ['class' => 'form-control']) !!} <br/>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('status', trans('labels.status').':', ['class' => 'control-label']) !!} <br/>
                                            <label class="radio-inline">
                                            {{ Form::radio('status','active',true),['class' => 'col-md-3'] }} @lang('labels.active')
                                            </label>
                                            <label class="radio-inline">
                                            {{ Form::radio('status','inactive') ,['class' => 'col-md-3'] }} @lang('labels.nonactive')
                                            </label>
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
                    {{$cps->links()}}
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

    <script src="{{asset('js/my_js.js')}}"></script>

    <script>
        $(document).ready(function () {
            var url = "{{url('cf-lite')}}/";

            $('#data-table').on('click', '.edit-cp', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    cp_id: $(this).attr('id').split('-').pop()
                };

                var type = 'POST';
                var my_url = url + 'community-partner/cp-list-ajax';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        $('input[name="cp_name"]').val(data.cp['cp_name']);
                        $('input[name="pic_name"]').val(data.cp['pic_name']);
                        $('textarea[name="address"]').text(data.cp['address']);
                        $('input[name="phone_number"]').val(data.cp['phone_number']);
                        $('input[name="email"]').val(data.cp['email']);
                        $('#save-btn').text('@lang('labels.edit')');
                        $('#addNewCP').find(':hidden[name=addoredit]').prop('value','edit');
                        $('#addNewCP').find(':hidden[name=editid]').prop('value',data.cp['id']);

                        if(data.cp['status'] == 'active')
                            $('#addNewCP').find(':radio[name=status][value="active"]').prop('checked',true);
                        else if (data.cp['status'] == 'inactive')
                            $('#addNewCP').find(':radio[name=status][value="inactive"]').prop('checked',true);
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });

            $('#add-cp').on('click', '.new-cp', function () {

                $('input[name="cp_name"]').val('');
                $('input[name="pic_name"]').val('');
                $('textarea[name="address"]').val('');
                $('input[name="phone_number"]').val('');
                $('input[name="email"]').val('');
                $('#save-btn').text('@lang("labels.save")');
                $('#addNewCP').find(':radio[name=status][value="active"]').prop('checked',true);
                $('#addNewCP').find(':hidden[name=addoredit]').prop('value','add');
            });
        });
    </script>
@endsection
