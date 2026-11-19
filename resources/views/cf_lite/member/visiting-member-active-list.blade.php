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
                <a href="{{url('cf-lite/travel-pass')}}" style="left: 25px; top: 20px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">
                        @lang('labels.visiting_member')
                    </h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                {!! Form::open(['method' => 'GET', 'action' => ['TravelPassController@visiting_member_filter']]) !!}
                <div class="form-group">
                    <label class="checkbox-inline">
                        {!! Form::radio('filter','member_name', true) !!} @lang('labels.member_name')
                    </label>
                    <label class="checkbox-inline">
                        {!! Form::radio('filter','member_id', false) !!} @lang('labels.member_id')
                    </label>
                    <label class="checkbox-inline">
                        {!! Form::radio('filter','origin_club', false) !!} @lang('labels.origin_club')
                    </label>
                </div>
                <div class="form-group">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="input-group">
                                {!! Form::text('search_text', null, ['class' => 'form-control', 'placeholder' => trans('labels.search_visiting_member')]) !!}
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
            <div class="col-md-10 col-md-offset-1 voucher-list-add" id="add-visitingmember">
                <a class="btn btn-default pull-right new-visitingmember" href="javascript:;" data-toggle="modal" data-target="#addNewVisitingMember" title="@lang('labels.add_new_visiting_member')"><span class="fa fa-plus"></span> @lang('labels.add_new_visiting_member')</a>
            </div>
            <div class="col-md-10 col-md-offset-1">
                <div class="x_panel">
                    <div class="x_content">
                        <br/>
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered" id="data-table">
                                <thead>
                                <tr>
                                    <th>@lang('labels.member_id')</th>
                                    <th>@lang('labels.member_name')</th>
                                    <th>@lang('labels.origin_club')</th>
                                    <th>@lang('labels.origin_club_address')</th>
                                    <th>@lang('labels.origin_club_phone_number')</th>
                                    <th>@lang('labels.origin_club_fax_number')</th>
                                    <th>@lang('labels.origin_club_email')</th>
                                    <th>@lang('labels.first_use_date')</th>
                                    <th>@lang('labels.validity_date')</th>
                                    <th></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($members as $member)
                                    <tr>
                                        <td>{{$member->member_id}}</td>
                                        <td>
                                            <a role="button" id="edit-visitingmember-{{$member->id}}" class="edit-visitingmember" data-toggle="modal" data-target="#addNewVisitingMember"> {{$member->member_name}} </a>
                                        </td>
                                        <td>{{$member->origin_club}}</td>
                                        <td>{{$member->origin_club_address}}</td>
                                        <td>{{$member->origin_club_phone_number}}</td>
                                        <td>{{$member->origin_club_fax_number}}</td>
                                        <td>{{$member->origin_club_email}}</td>
                                        <td>{{Carbon\Carbon::parse($member->first_use_date)->format('d-m-Y')}}</td>
                                        <td>{{Carbon\Carbon::parse($member->validity_date)->format('d-m-Y')}}</td>
                                        <td>
                                            {!! Form::open(['method' => 'GET' , 'style'=>'display:inline-block' , 'action' => ['TravelPassController@past_wo_vm' , $member->id, ]])!!}{!! Form::submit(trans('labels.workout'),['class' => 'btn btn-primary']) !!} {!! Form::close() !!}
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
        <!-- Add Modal -->
        <div class="modal fade" id="addNewVisitingMember" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                {!! Form::open(['method' => 'PATCH', 'action' => 'TravelPassController@store_new_visiting_member']) !!}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">@lang('labels.add_new_visiting_member')</h4>
                        {{ Form::hidden('addoredit','add')}}
                        {{ Form::hidden('editid',0)}}
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-inline">
                                    <div class="form-group">
                                        {!! Form::label('member_id', trans('labels.member_id').': ', ['class' => 'control-label']) !!}<br/>
                                        {!! Form::text('member_id', null, ['class' => 'form-control']) !!} <br/>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('member_name', trans('labels.member_name').': ', ['class' => 'control-label']) !!}<br/>
                                        {!! Form::text('member_name', null, ['class' => 'form-control']) !!} <br/>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('origin_club', trans('labels.origin_club').': ', ['class' => 'control-label']) !!}<br/>
                                        {!! Form::text('origin_club', null, ['class' => 'form-control']) !!} <br/>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('origin_club_address', trans('labels.origin_club_address').': ', ['class' => 'control-label']) !!}<br/>
                                        {!! Form::textarea('origin_club_address', null, ['class' => 'form-control','rows'=>'3']) !!} <br/>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('origin_club_phone_number', trans('labels.origin_club_phone_number').': ', ['class' => 'control-label']) !!}<br/>
                                        {!! Form::text('origin_club_phone_number', null, ['class' => 'form-control']) !!} <br/>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('origin_club_fax_number', trans('labels.origin_club_fax_number').': ', ['class' => 'control-label']) !!}<br/>
                                        {!! Form::text('origin_club_fax_number', null, ['class' => 'form-control']) !!} <br/>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('origin_club_email', trans('labels.origin_club_email').': ', ['class' => 'control-label']) !!}<br/>
                                        {!! Form::text('origin_club_email', null, ['class' => 'form-control']) !!} <br/>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('first_use_date', trans('labels.first_use_date').': ', ['class' => 'control-label']) !!}<br/>
                                        {!! Form::date('first_use_date', Carbon\Carbon::now(), ['class' => 'form-control']) !!} <br/>
                                    </div>
                                    <div class="form-group">
                                        {!! Form::label('validity_date', trans('labels.validity_date').': ', ['class' => 'control-label']) !!}<br/>
                                        {!! Form::date('validity_date', Carbon\Carbon::now(), ['class' => 'form-control']) !!} <br/>
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
                    {{$members->links()}}
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

            $('#data-table').on('click', '.edit-visitingmember', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    visitingmember_id: $(this).attr('id').split('-').pop()
                };

                var type = 'POST';
                var my_url = url + 'visiting-member-ajax';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        console.log(data);
                        $('input[name="member_id"]').val(data.members['member_id']);
                        $('input[name="member_name"]').val(data.members['member_name']);
                        $('input[name="origin_club"]').val(data.members['origin_club']);
                        $('textarea[name="origin_club_address"]').text(data.members['origin_club_address']);
                        $('input[name="origin_club_phone_number"]').val(data.members['origin_club_phone_number']);
                        $('input[name="origin_club_fax_number"]').val(data.members['origin_club_fax_number']);
                        $('input[name="origin_club_email"]').val(data.members['origin_club_email']);
                        $('input[name="first_use_date"]').val(data.members['first_use_date']);
                        $('input[name="validity_date"]').val(data.members['validity_date']);
                        $('#save-btn').text('@lang('labels.edit')');
                        $('#addNewVisitingMember').find(':hidden[name=addoredit]').prop('value','edit');
                        $('#addNewVisitingMember').find(':hidden[name=editid]').prop('value',data.members['id']);
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });

            $('#add-visitingmember').on('click', '.new-visitingmember', function () {

                $('input[name="member_id"]').val('');
                $('input[name="member_name"]').val('');
                $('input[name="origin_club"]').val('');
                $('textarea[name="origin_club_address"]').text('');
                $('input[name="origin_club_phone_number"]').val('');
                $('input[name="origin_club_fax_number"]').val('');
                $('input[name="origin_club_email"]').val('');
                $('input[name="first_use_date"]').val();
                $('input[name="validity_date"]').val();
                $('#save-btn').text('@lang("labels.save")');
                $('#addNewVisitingMember').find(':hidden[name=addoredit]').prop('value','add');
            });

            $('input[name="search_option"]').on('click', function () {
                $('input[name="s"]').val('');
            })
        });
    </script>
@endsection
