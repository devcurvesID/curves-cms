@extends('cf_lite.layouts.staff-template')

@section('other-style')
    {{--DATA TABLE--}}
    <link href="{{asset('vendors/datatables.net-bs/css/dataTables.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-buttons-bs/css/buttons.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-fixedheader-bs/css/fixedHeader.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css')}}" rel="stylesheet">
    <link href="{{asset('vendors/datatables.net-scroller-bs/css/scroller.bootstrap.min.css')}}" rel="stylesheet">

    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">

    <style>
        .data td{
            padding: 10px;
        }

        table{
            color: #414042;
        }

        .select2 {
            width: 100% !important;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-md-12">
            <a href="{{url('cf-lite/settings')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.mst_guest_membership')</h2>
            </center>
        </div>
        <div class="col-md-12">
            <div class="btn btn-default pull-right" data-toggle="modal" data-target="#addguestmembership" role="button" style="border-radius: 50px;width: 50px; height: 50px; border-width: 3px; border-color: mediumvioletred; margin-right: 10px;" title="@lang('labels.new_guest_membership')">
                <span class="fa fa-plus-circle" style="margin-top: 10px; color: mediumvioletred;"></span>
            </div>
        </div>
        <div class="col-sm-12 col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive" style="background-color: #fff;">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                            <tr>
                                <th>@lang('labels.indexing')</th>
                                <th>@lang('labels.gm_name')</th>
                                <th>@lang('labels.price')</th>
                                <th>@lang('labels.duration')</th>
                                <th>@lang('labels.from_date')</th>
                                <th>@lang('labels.thru_date')</th>
                                <th>@lang('labels.club')</th>
                                <th>@lang('labels.action')</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php($indexing = 1)
                            @foreach($gms as $gm)
                                <tr>
                                    <td>{{ $indexing }}</td>
                                    <td>{{ $gm->gm_name }}</td>
                                    <td class="my_rupiah">{{ $gm->price }}</td>
                                    <td>{{ $gm->duration_long }} {{ $gm->duration_type }}</td>
                                    <td>{{ $gm->from_date }}</td>
                                    <td>{{ $gm->thru_date }}</td>
                                    <td>
                                        @foreach($gm->gm_clubgm as $club)
                                            @if(count($gm->gm_clubgm) > 1)
                                                {{ $club->club_name }},
                                            @else
                                                {{ $club->club_name }}
                                            @endif
                                        @endforeach
                                    </td>
                                    <td>
                                        <a class="btn btn-info edit-guest-membership" id="{{ $gm->id }}" data-toggle="modal" data-target="#editguestmembership" style="width: 40px; text-align: center;">
                                            <span class="btn-text fa fa-edit"></span>
                                        </a>
                                        <a class="btn btn-danger cost-delete-btn" style="width: 40px; text-align: center;">
                                            <span class="btn-text fa fa-trash"></span>
                                        </a>
                                        <div>
                                            {!! Form::button(trans('labels.edit'), ['class' => 'btn btn-xs btn-edit hidden']) !!}
                                        </div>
                                        <div>
                                            {!! Form::open(['method' => 'DELETE', 'action' => ['GuestMembershipController@delete_guest_membership',$gm->id]]) !!}
                                            {!! Form::submit(trans('labels.delete'), ['class' => 'btn btn-xs btn-danger hidden']) !!}
                                            {!! Form::close() !!}
                                        </div>
                                    </td>
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

    {{--Modal Add--}}
    <div class="modal fade" id="addguestmembership" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'POST', 'action' => 'GuestMembershipController@store_guest_membership', 'id' => 'form-guest-membership']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.add_new_guest_membership')</h4>
                </div>
                <div class="modal-body">
                    <table class="data">
                        <tr>
                            <td>{!! Form::label('gm_name', trans('labels.gm_name'), ['class' => 'control-label']) !!}</td>
                            <td>:</td>
                            <td>{!! Form::text('gm_name', null, ['class' => 'form-control']) !!}</td>
                        </tr>
                        <tr>
                            <td>{!! Form::label('price', trans('labels.price', ['class' => 'control-label'])) !!}</td>
                            <td>:</td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-addon">Rp.</span>
                                    {!! Form::text('price', 0, ['class' => 'form-control rupiah', 'onkeypress' => 'return onlyNumber(event)']) !!}
                                    <span class="input-group-addon">,-</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>{!! Form::label('duration', trans('labels.duration'), ['class' => 'control-label']) !!}</td>
                            <td>:</td>
                            <td>{!! Form::number('duration', null, ['class' => 'form-control', 'min' => '1', 'onkeypress' => 'return onlyNumber(event)']) !!}</td>
                            <td>
                                <select name="select_type" class="form-control" id="select_type">
                                    <option value="Year">@lang('labels.year')</option>
                                    <option value="Month" selected>@lang('labels.month')</option>
                                    <option value="Week">@lang('labels.week')</option>
                                    <option value="Day">@lang('labels.day')</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>{!! Form::label('from_date', trans('labels.from_date'), ['class' => 'control-label']) !!}</td>
                            <td>:</td>
                            <td>{!! Form::date('from_date', null, ['class' => 'form-control']) !!}</td>
                        </tr>
                        <tr>
                            <td>{!! Form::label('thru_date', trans('labels.thru_date'), ['class' => 'control-label']) !!}</td>
                            <td>:</td>
                            <td>{!! Form::date('thru_date', null, ['class' => 'form-control']) !!}</td>
                        </tr>
                        <tr>
                            <td>{!! Form::label('max_wo', trans('labels.max_wo'), ['class' => 'control-label']) !!}</td>
                            <td>:</td>
                            <td>{!! Form::number('max_wo', null, ['class' => 'form-control', 'min' => '1', 'onkeypress' => 'return onlyNumber(event)']) !!}</td>
                        </tr>
                        <tr>
                            <td>{!! Form::label('club', trans('labels.choose_club'), ['class' => 'control-label']) !!}</td>
                            <td>:</td>
                            <td style="width: 210px;">{!! Form::select('clubs[]', $clubs, null, ['class' => 'form-control select2', 'multiple', 'id' => 'clubs']) !!}</td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="button" id="btn-guest-membership" class="btn btn-success">@lang('labels.add')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    {{--Modal Edit--}}
    <div class="modal fade" id="editguestmembership" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'PATCH', 'action' => 'GuestMembershipController@update_guest_membership', 'id' => 'edit-form']) !!}
                {!! Form::hidden('gm_id', null) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">@lang('labels.edit_guest_membership')</h4>
                </div>
                <div class="modal-body">
                    <table class="data">
                        <tr>
                            <td>{!! Form::label('edit_gm_name', trans('labels.gm_name'), ['class' => 'control-label']) !!}</td>
                            <td>:</td>
                            <td>{!! Form::text('edit_gm_name', null, ['class' => 'form-control']) !!}</td>
                        </tr>
                        <tr>
                            <td>{!! Form::label('edit_price', trans('labels.price', ['class' => 'control-label'])) !!}</td>
                            <td>:</td>
                            <td>
                                <div class="input-group">
                                    <span class="input-group-addon">Rp.</span>
                                    {!! Form::text('edit_price', 0, ['class' => 'form-control rupiah', 'onkeypress' => 'return onlyNumber(event)']) !!}
                                    <span class="input-group-addon">,-</span>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td>{!! Form::label('edit_duration', trans('labels.duration'), ['class' => 'control-label']) !!}</td>
                            <td>:</td>
                            <td>{!! Form::number('edit_duration', null, ['class' => 'form-control', 'min' => '1', 'onkeypress' => 'return onlyNumber(event)']) !!}</td>
                            <td>
                                <select name="edit_select_type" class="form-control" id="edit_select_type">
                                    <option value="Year">@lang('labels.year')</option>
                                    <option value="Month" selected>@lang('labels.month')</option>
                                    <option value="Week">@lang('labels.week')</option>
                                    <option value="Day">@lang('labels.day')</option>
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td>{!! Form::label('edit_from_date', trans('labels.from_date'), ['class' => 'control-label']) !!}</td>
                            <td>:</td>
                            <td>{!! Form::date('edit_from_date', null, ['class' => 'form-control']) !!}</td>
                        </tr>
                        <tr>
                            <td>{!! Form::label('edit_thru_date', trans('labels.thru_date'), ['class' => 'control-label']) !!}</td>
                            <td>:</td>
                            <td>{!! Form::date('edit_thru_date', null, ['class' => 'form-control']) !!}</td>
                        </tr>
                        <tr>
                            <td>{!! Form::label('edit_max_wo', trans('labels.max_wo'), ['class' => 'control-label']) !!}</td>
                            <td>:</td>
                            <td>{!! Form::number('edit_max_wo', null, ['class' => 'form-control', 'min' => '1', 'onkeypress' => 'return onlyNumber(event)']) !!}</td>
                        </tr>
                        <tr>
                            <td>{!! Form::label('edit_club', trans('labels.choose_club'), ['class' => 'control-label']) !!}</td>
                            <td>:</td>
                            <td style="width: 210px;">{!! Form::select('edit_clubs[]', $clubs, null, ['class' => 'form-control select2', 'multiple', 'id' => 'edit_clubs']) !!}</td>
                        </tr>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="button" id="btn-edit-guest-membership" class="btn btn-success">@lang('labels.update')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('js/jquery.dataTables.min.js')}}"></script>
    <script src="{{asset('js/dataTables.bootstrap.min.js')}}"></script>
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>

    <script type="text/javascript">
        $(document).ready(function () {
            $('.select2').select2();
            $('#data-table').dataTable();
            init_format();
        });
    </script>

    <script>
        $(document).ready(function () {
            var url = "{{url('cf-lite')}}/"

            $('#data-table').on('click', '.edit-guest-membership', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    gm_id: $(this).attr('id')
                };

                var type = 'POST';
                var my_url = url + 'guest-membership-ajax';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    success: function (data) {
                        $('input[name="gm_id"]').val(data.gm.id);
                        $('input[name="edit_gm_name"]').val(data.gm.gm_name);
                        $('input[name="edit_price"]').val(rupiah_formater(data.gm.price));
                        $('input[name="edit_duration"]').val(data.gm.duration_long);
                        $('input[name="edit_from_date"]').val(data.gm.from_date);
                        $('input[name="edit_thru_date"]').val(data.gm.thru_date);
                        $('input[name="edit_max_wo"]').val(data.gm.max_workout);
                        $('select#edit_select_type').val(data.gm.duration_type);
                    },
                    error: function (data) {
                        console.log('Error : ', data);
                    }
                });
            });
        });

        $('input[name="price"]').on('keyup', function () {
            if ($(this).val() == "") {
                var i = 0;
            } else {
                var i = parseInt(rupiah_format_destroyer($(this).val()));
            }
            $('input[name="price"]').val(rupiah_formater(i));
        });

        $('input[name="edit_price"]').on('keyup', function () {
            if ($(this).val() == "") {
                var i = 0;
            } else {
                var i = parseInt(rupiah_format_destroyer($(this).val()));
            }
            $('input[name="edit_price"]').val(rupiah_formater(i));
        });

        $('#btn-guest-membership').on('click', function () {
            $('input[name="price"]').val(rupiah_format_destroyer($('input[name="price"]').val()));
            $('#form-guest-membership').submit();
        });

        $('#btn-edit-guest-membership').on('click', function () {
            $('input[name="edit_price"]').val(rupiah_format_destroyer($('input[name="edit_price"]').val()));
            $('#edit-form').submit();
        })

        function onlyNumber(evt) {
            var charCode = (evt.which) ? evt.which : event.keyCode;
            if (charCode == 8 || //space
                charCode == 9 || //tab
                charCode == 46 || //point
                (charCode > 47 && charCode < 58) // number
            ) {
                return true;
            }
            return false;
        }

        function init_format() {
            $('.my_rupiah').each(function () {
                var x = rupiah_formater($(this).text().replace(/\s/g, ''));
                $(this).text('Rp.' + (x) + ',-');
            });
            $('.rupiah').each(function () {
                var x = rupiah_formater($(this).val().replace(/\s/g, ''));
                $(this).val((x));
            });
        }
    </script>
    <script src="{{asset('js/my_js.js')}}"></script>
    <script src={{asset("bootbox/bootbox.min.js")}}></script>
    <script>
        $(document).ready(function () {
            $(document).on('click', '.cost-delete-btn', function () {
                var theform = $(this).siblings('div').children('form');
                bootbox.confirm({
                    message: "@lang('messages.are_you_sure')",
                    buttons: {
                        confirm: {
                            label: '@lang('labels.yes')',
                            className: 'btn-danger'
                        },
                        cancel: {
                            label: '@lang('labels.no')',
                        }
                    },
                    callback: function (result) {
                        if (result == false) {
                        } else {
                            theform.submit();
                        }
                    }
                });
            });
        });
    </script>
    <script>
        $(document).ready(function () {
            $.fn.select2.amd.define('select2/selectAllAdapter', [
                'select2/utils',
                'select2/dropdown',
                'select2/dropdown/attachBody'
            ], function (Utils, Dropdown, AttachBody) {

                function SelectAll() {
                }

                SelectAll.prototype.render = function (decorated) {
                    var self = this,
                        $rendered = decorated.call(this),
                        $selectAll = $(
                            '<button class="btn btn-xs btn-success" type="button" style="margin-left:6px;"><i class="fa fa-check-square-o"></i> @lang('labels.select_all')</button>'
                        ),
                        $unselectAll = $(
                            '<button class="btn btn-xs btn-danger" type="button" style="margin-left:6px;"><i class="fa fa-square-o"></i> @lang('labels.unselect_all')</button>'
                        ),
                        $btnContainer = $('<div style="margin-top:3px;">').append($selectAll).append($unselectAll);
                    if (!this.$element.prop("multiple")) {
                        // this isn't a multi-select -> don't add the buttons!
                        return $rendered;
                    }
                    $rendered.find('.select2-dropdown').prepend($btnContainer);
                    $selectAll.on('click', function (e) {
                        var $results = $rendered.find('.select2-results__option[aria-selected=false]');
                        $results.each(function () {
                            self.trigger('select', {
                                data: $(this).data('data')
                            });
                        });
                        self.trigger('close');
                    });
                    $unselectAll.on('click', function (e) {
                        var $results = $rendered.find('.select2-results__option[aria-selected=true]');
                        $results.each(function () {
                            self.trigger('unselect', {
                                data: $(this).data('data')
                            });
                        });
                        self.trigger('close');
                    });
                    return $rendered;
                };

                return Utils.Decorate(
                    Utils.Decorate(
                        Dropdown,
                        AttachBody
                    ),
                    SelectAll
                );

            });

            $('#clubs').select2({
                dropdownAdapter: $.fn.select2.amd.require('select2/selectAllAdapter')
            });
        });
    </script>
@endsection