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
                        @lang('labels.membership_type')
                    </h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10 col-md-offset-1">
                {!! Form::open(['method' => 'GET', 'action' => ['MembershipTypeController@membership_type_list_filter']]) !!}
                <div class="form-group">
                    <div class="col-md-4">
                        <div class="row">
                            <div class="input-group">
                                {!! Form::text('membership_type', null, ['class' => 'form-control', 'placeholder' => trans('labels.search_membership_type')]) !!}
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
            <div class="col-md-10 col-md-offset-1 voucher-list-add" id="add-membership-type">
                <a class="btn btn-default pull-right new-membership-type" href="javascript:;" data-toggle="modal" data-target="#addNewMembershipTypes"
                   title="@lang('labels.add_new_membership_type')"><span class="fa fa-plus"></span> @lang('labels.add_new_membership_type')</a>
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
                                    <th>@lang('labels.membership_type_name')</th>
                                    <th>@lang('labels.membership_type')</th>
                                    <th>@lang('labels.duration')</th>
                                    <th>@lang('labels.payment_type')</th>
                                    <th>@lang('labels.status')</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php($indexing = 1)
                                @foreach($membershipTypes as $mt)
                                    <tr>
                                        <td>{{$indexing}}</td>
                                        <td>
                                            <a role="button" id="edit-membership-type-{{$mt->id}}" class="edit-membership-type" data-toggle="modal" data-target="#addNewMembershipTypes"> {{$mt->membership_type_name}} </a>

                                        </td>
                                        <td>{{$mt->membership_type}}</td>
                                        <td>{{$mt->duration}}</td>
                                        <td>{{$mt->payment_type}}</td>
                                        <td>{{ $mt->status }}</td>
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
        <div class="modal fade" id="addNewMembershipTypes" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                {!! Form::open(['method' => 'PATCH', 'action' => 'MembershipTypeController@store_membership_type']) !!}
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title">@lang('labels.add_new_membership_type')</h4>
                        {{ Form::hidden('addoredit','add')}}
                        {{ Form::hidden('editid',0)}}
                    </div>
                    <div class="modal-body">
                        <div class="form-horizontal">
                            <div class="form-group">
                                {!! Form::label('membership_type_name', trans('labels.membership_type_name').' : ', ['class' => 'control-label col-md-4']) !!}
                                <div class="col-md-8">
                                    {!! Form::text('membership_type_name', null, ['class' => 'form-control']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                {!! Form::label('membership_type', trans('labels.membership_type').' : ', ['class' => 'control-label col-md-4']) !!}
                                <div class="col-md-8">
                                    {!! Form::select('membership_type', $optMembershipType, 'Regular', ['class' => 'form-control','id'=>'memType']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                {!! Form::label('duration', trans('labels.duration').' : ', ['class' => 'control-label col-md-4']) !!}
                                <div class="col-md-8">
                                    {!! Form::select('duration', $optDuration, '1', ['class' => 'form-control','id'=>'dur']) !!}
                                </div>
                            </div>
                        </div>
                        <div class="form-horizontal">
                            <div class="form-group">
                                {!! Form::label('payment_type', trans('labels.payment_type').' : ', ['class' => 'control-label col-md-4']) !!}
                                <div class="col-md-8">
                                    {!! Form::select('payment_type', $optPaymentType, 'Cash', ['class' => 'form-control','id'=>'payType']) !!}
                                </div>
                            </div>
                        </div>

                        <!-- ACCURATE -->
                        {{-- @if(isset(Session::get('accurate')->accurate_id))
                            <div class="form-horizontal">
                        @else
                            <div class="form-horizontal" style="display: none">
                        @endif

                            <div class="form-group">
                                {!! Form::label('accurate_code', 'Accurate Item Code : ', ['class' => 'control-label col-md-4']) !!}
                                <div class="col-md-8">
                                    {!! Form::select('accurate_code', $accurateItems, 'Accurate Item Code', ['class' => 'form-control','id'=>'accurate_code']) !!}
                                    <span style="display:block;margin-top:5px;font-size:12px;color: #858585;">If you can't find appropriate ACCURATE CODE for this membership type, please create it first in Accurate and reload this page.</span>
                                </div>
                            </div>
                            <?php //print_r($accurateItems); ?>
                        </div> --}}
                        <!-- /ACCURATE -->

                        <div class="form-horizontal">
                            <div class="form-group">
                                {!! Form::label('status', trans('labels.status').' : ', ['class' => 'control-label col-md-4']) !!}
                                <div class="col-md-8">
                                    <div class="input-group">
                                        <input name="status" value="Active" type="radio" id="active" checked >
                                        {!! Form::label('status', trans('labels.active'), ['class' => 'control-label']) !!}
                                    </div>
                                    <div class="input-group">
                                        <input name="status" value="Inactive" type="radio" id="inactive">
                                        {!! Form::label('status', trans('labels.inactive'), ['class' => 'control-label']) !!}
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
                <div class="paging hidden rom">
                    {{$membershipTypes->links()}}
                </div>
            </div>
        </div>
    </section>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
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
            $('#data-table').dataTable();
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

            $('#data-table').on('click', '.edit-membership-type', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    membership_type_id: $(this).attr('id').split('-').pop()
                };

                var type = 'POST';
                var my_url = url + 'membership-types/membership-type-list-ajax';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    dataType: 'json',
                    success: function (data) {
                        $('input[name="membership_type_name"]').val(data.membershipType['membership_type_name']);
                        $('#addNewMembershipTypes').find('#memType').val(data.membershipType['membership_type']);
                        $('#addNewMembershipTypes').find('#dur').val(data.membershipType['duration']);
                        $('#addNewMembershipTypes').find('#payType').val(data.membershipType['payment_type']);
                        //$('#addNewMembershipTypes').find('#accurate_code').val(data.membershipType['accurate_code']);
                        $('#addNewMembershipTypes').find(':hidden[name=addoredit]').prop('value','edit');
                        $('#addNewMembershipTypes').find(':hidden[name=editid]').prop('value',data.membershipType['id']);

                        if (data.membershipType['status'] == 'Active') {
                            document.getElementById("active").checked = true;
                        } else {
                            document.getElementById("inactive").checked = true;
                        }
                    },
                    error: function (data) {
                        console.log('Error: ', data);
                    }
                });
            });

            $('#add-membership-type').on('click', '.new-membership-type', function () {

                $('#save-btn').text('@lang("labels.save")');
                $('input[name="membership_type_name"]').val('');
                $('#addNewMembershipTypes').find('#memType').val('Regular');
                $('#addNewMembershipTypes').find('#dur').val('1');
                $('#addNewMembershipTypes').find('#payType').val('Cash');
                $('#addNewMembershipTypes').find(':hidden[name=addoredit]').prop('value','add');
            });

            $('input[name="search_option"]').on('click', function () {
                $('input[name="s"]').val('');
            })
        });
    </script>
@endsection
