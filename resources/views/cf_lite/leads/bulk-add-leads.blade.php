@extends('cf_lite.layouts.staff-template')

@section('other-style')
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <style>
        .borderless-input {
            width: 100%;
            border: none;
        }

        .circle-medium{
            width: 200px;
            height: 200px;
            border-radius: 50%;
            padding-top: 50px;
        }

        .select2 {
            width: 30% !important;
            float: right;
        }

        .page-title {
            margin-top: 0px;
        }

        .select2-container--default .select2-selection--single{
            background-color: #ffffff;
            color: #ff1493;
            border: 1px solid #ff1493;

        }

        .select2-container--default .select2-selection--single:hover .select2-selection__rendered {
            color: #ff1493;
        }

        .select2-container--default .select2-selection--single:hover {
            border: 1px solid #ff1493;
        }

        .btn-primary{
            background-color: #ff1493;
            color: #ffffff;
            border-color: #ff1493;
        }

        .btn-primary:hover {
            color: #ff1493;
            background-color: transparent;
            border-color: #ff1493;
        }

    </style>
@endsection

@section('content')
    @include('cf_lite.includes.error-message')
    @include('cf_lite.includes.flash-message')
    <div class="row">
        <div class="col-sm-12">
            <left>
                <a href="{{url('cf-lite/lead')}}" style="left: 25px; top: 5px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
            </left>
            <center>
                <h2 class="page-title">@lang('labels.add') @lang('labels.lead')</h2>
            </center>
        </div>
        <div class="col-md-12">
            {!! Form::open(['method' => 'POST', 'action' => 'LeadController@store_bulk_leads']) !!}
            <button type="button" class="btn btn-primary pull-right" id="btn-add-more-row" title="@lang('labels.add') @lang('labels.new_row')" style="margin-bottom: 20px;"><span class="glyphicon glyphicon-plus"></span></button>
            <table class="table table-bordered" style="background-color: #fff;">
                <thead>
                    <tr>
                        @php
                            if (Session::get('lead_type') == 'regular'):
                                $source_text = trans('labels.source');
                            elseif (Session::get('lead_type') == 'br'):
                                $source_text = trans('labels.buddy_referral');
                            endif
                        @endphp
                        <td colspan="3">
                            {!! Form::select('sources', $sources, null, ['class' => 'form-control pull-right', 'id' => 'sources', 'placeholder' => trans('labels.choose').' '.$source_text]) !!}
                        </td>
                    </tr>
                    <tr>
                        <th>@lang('labels.indexing')</th>
                        <th>@lang('labels.fullname')</th>
                        <th>@lang('labels.phone')</th>
                    </tr>
                </thead>
                <tbody id="bulk-lead-table-body">
                    @for($i = 1; $i <= 10; $i++)
                        <tr>
                            <td>{{$i}}</td>
                            <td>{!! Form::text('fullname['.$i.']', null, ['class' => 'borderless-input form-control']) !!}</td>
                            <td>{!! Form::text('phone['.$i.']', null, ['class' => 'borderless-input form-control']) !!}</td>
                        </tr>
                    @endfor
                </tbody>
            </table>
            {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary pull-right']) !!}
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('other-script')
    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script type="text/javascript">
        $('#sources').select2();
    </script>
    <script>
        var counter = parseInt(10);
        $('#btn-add-more-row').on('click', function() {
            counter = counter + 1;
            var html = '<tr>' +
                '<td>' + counter + '</td>' +
                '<td><input type="text" name="fullname[' + counter + ']" class="borderless-input form-control"></td>' +
                '<td><input type="text" name="phone[' + counter + ']" class="borderless-input form-control"></td>' +
                '</tr>';
            $('#bulk-lead-table-body').append(html);
        });
    </script>
@endsection