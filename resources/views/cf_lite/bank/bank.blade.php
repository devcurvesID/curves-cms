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
        .select2 {
            width: 100% !important;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    @include('cf_lite.includes.error-message')
    <div class="row">
        <div class="col-sm-12">
            <a href="{{url('cf-lite/settings')}}" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            <center>
                <h2 class="page-title">@lang('labels.master_bank')</h2>
            </center>
        </div>
        <div class="col-md-12">
            <div class="btn btn-default pull-right" data-toggle="modal" data-target="#addBank" role="button" style="border-radius: 50px; width: 65px; height: 65px; border-width: 3px; border-color: limegreen; margin-right: 10px;" title="@lang('labels.add_bank')">
                <span class="fa fa-bank" style="font-size: 40px; margin-left: -4px; color: limegreen;"></span>
            </div>
        </div>
        <div class="col-md-12">
            <div class="x_panel">
                <div class="x_content">
                    <br/>
                    <div class="table-responsive" style="background-color: #fff;">
                        <table class="table table-hover table-bordered" id="data-table">
                            <thead>
                            <tr>
                                <th>@lang('labels.indexing')</th>
                                <th>@lang('labels.bank_name')</th>
                                <th>@lang('labels.bank_abv')</th>
                                <th>@lang('labels.bank_account_number')</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @php($indexing = 1)
                            @php($bank_gate = true)
                            @foreach($banks as $bank)
                                @if(\Illuminate\Support\Facades\Auth::user()->user_club->jubilant_id != $bank->bank_chartofaccount->jubilant_id)
                                    {{--@if(\Illuminate\Support\Facades\Auth::user()->user_club->club_jubilant->type == 'Franchise')--}}
                                        @php($bank_gate = false)
                                    {{--@endif--}}
                                @endif

                                @if($bank_gate)
                                <tr>
                                    <td>{{$indexing}}</td>
                                    <td><a role="button" class="edit-bank" id="edit-bank-{{$bank->id}}" data-toggle="modal" data-target="#editBank">{{$bank->bank_name}}</a></td>
                                    <td>{{$bank->bank_abv}}</td>
                                    <td>{{$bank->bank_account_number}}</td>
                                    <td style="text-align: center;">
                                        <a class="btn btn-danger coa-delete-btn">
                                            <i class="icon-rocket"></i>
                                            <span class="btn-text fa fa-trash"></span>
                                        </a>
                                        <div>
                                            {!! Form::open(['method' => 'DELETE', 'action' => ['BankController@delete_bank',$bank->id]]) !!}
                                            {!! Form::submit(trans('labels.delete'), ['class' => 'btn btn-xs btn-danger hidden']) !!}
                                            {!! Form::close() !!}
                                        </div>
                                    </td>
                                </tr>
                                @php($indexing++)
                                @endif
                                @php($bank_gate = true)
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="addBank" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'POST', 'action' => 'BankController@store_new_bank', 'id' => 'new-bank']) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">@lang('labels.add_bank')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('name', trans('labels.bank_name').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::text('name', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('abv_name', trans('labels.bank_abv').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::text('abv_name', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('number', trans('labels.bank_account_number').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::text('number', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('coa', trans('labels.chart_of_account').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {{--@if($bank_gate)--}}
                                    {!! Form::select('coa', $coa, null, ['class' => 'form-control select2', 'required', 'placeholder' => '-- '.trans('labels.choose').' '.trans('labels.chart_of_account').' --']) !!}
                                {{--@else--}}
                                    {{--{!! Form::select('coa', [], null, ['class' => 'form-control select2', 'required', 'placeholder' => '-- '.trans('labels.choose').' '.trans('labels.chart_of_account').' --']) !!}--}}
                                {{--@endif--}}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('club', trans('labels.club').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <select name="clubs[]" id="club" class="form-control select2" multiple="multiple">
                                    @foreach($clubs->where('jubilant_id', \Illuminate\Support\Facades\Auth::user()->user_club->jubilant_id) as $club)
                                        <option value="{{ $club->id }}">{{$club->club_name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" name="submit" id="add-bank" class="btn btn-success">@lang('labels.add')</button>
                </div>
                {!! Form::close() !!}
            </div>
        </div>
    </div>

    <div class="modal fade" id="editBank" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                {!! Form::open(['method' => 'PATCH', 'action' => 'BankController@update_bank']) !!}
                {!! Form::hidden('bank_id', null) !!}
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">@lang('labels.edit_bank')</h4>
                </div>
                <div class="modal-body">
                    <div class="form-horizontal">
                        <div class="form-group">
                            {!! Form::label('edit_name', trans('labels.bank_name').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::text('edit_name', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('edit_abv_name', trans('labels.bank_abv').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::text('edit_abv_name', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('edit_number', trans('labels.bank_account_number').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                {!! Form::text('edit_number', null, ['class' => 'form-control']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('edit_coa', trans('labels.chart_of_account').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                @if($bank_gate)
                                    {!! Form::select('edit_coa', $coa, null, ['class' => 'form-control select2', 'id' => 'edit-coa', 'placeholder' => '-- '.trans('labels.choose').' '.trans('labels.chart_of_account').' --']) !!}
                                @else
                                    {!! Form::select('edit_coa', [], null, ['class' => 'form-control select2', 'id' => 'edit-coa', 'placeholder' => '-- '.trans('labels.choose').' '.trans('labels.chart_of_account').' --']) !!}
                                @endif
                            </div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('club', trans('labels.club').': ', ['class' => 'control-label col-md-4']) !!}
                            <div class="col-md-8">
                                <select name="clubs[]" id="edit-club" class="form-control select2" multiple="multiple">

                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('labels.close')</button>
                    <button type="submit" name="submit" class="btn btn-success">@lang('labels.update')</button>
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
        });
    </script>
    <script>
        $(document).ready(function () {
            var url = "{{url('cf-lite')}}/";

            $('#data-table').on('click', '.edit-bank', function () {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });

                var formData = {
                    bank_id: $(this).attr('id').split('-').pop()
                };

                var type = 'POST';
                var my_url = url + 'bank-ajax';

                $.ajax({
                    type: type,
                    url: my_url,
                    data: formData,
                    success: function (data) {
                        $('input[name="bank_id"]').val(data.bank.id);
                        $('input[name="edit_name"]').val(data.bank.bank_name);
                        $('input[name="edit_abv_name"]').val(data.bank.bank_abv);
                        $('input[name="edit_number"]').val(data.bank.bank_account_number);
                        $('select#edit-coa').val(data.bank.coa_id).trigger('change.select2');
                        // $('.club-name').each(function(){
                        //     var id = $(this).attr('id');
                        //     document.getElementById('edit-'+id).checked = false;
                        // });
                        // $('.club-name').each(function(){
                        //     var id = $(this).attr('id');
                        //     $.each(data.selectedclub,function(index, value){
                        //         if(id == value['id']){
                        //             document.getElementById('edit-'+id).checked = true;
                        //         }
                        //     });
                        // });

                        var via = '';

                        $.each(data.allclub, function (i, item)
                        {
                            var gate = false;
                            $.each(data.selectedclub, function (j, item2) {
                                if (item.id == item2.id) {
                                    gate = true;
                                }
                            });

                            if (gate) {
                                via += '<option selected="selected" value="' + item.id + '">'
                                    + item.club_name
                                    + '</option>';
                            } else {
                                via += '<option value="' + item.id + '">'
                                    + item.club_name
                                    + '</option>';
                            }
                        });

                        $('#edit-club').html(via);
                        $('#edit-club').val();
                    },
                    error: function (data) {
                        console.log('Error : ', data);
                    }
                });
            });
        });
    </script>
    <script src={{asset("bootbox/bootbox.min.js")}}></script>
    <script>
        $(document).ready(function () {
            $(document).on('click', '.coa-delete-btn', function () {
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

            $('#club').select2({
                dropdownAdapter: $.fn.select2.amd.require('select2/selectAllAdapter')
            });
            $('#edit-club').select2({
                dropdownAdapter: $.fn.select2.amd.require('select2/selectAllAdapter')
            });
        });
    </script>
@endsection