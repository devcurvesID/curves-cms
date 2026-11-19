@extends('cf_back.layouts.master-dashboard')

@section('title', 'Member Appointment')

@section('other-style')
@endsection

@section('content')
    <div class="container">
        <div class="my-section">
            <div class="section-title">
                <center>
                    <h1 class="page-title">@lang('labels.list') @lang('labels.lead')</h1>
                </center>
            </div>

            @include('cf_back.flash-message')
            @include('cf_back.error-message')

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <a id="btn-add" class="btn btn-primary pull-right"><i
                                class="fa fa-plus"></i> @lang('labels.add') @lang('labels.lead')</a>
                    <div class="x_panel">
                        <div class="x_content">
                            <br/>
                            <div class="table-responsive">
                                <table class="table table-hover table-bordered" id="data-table">
                                    <thead>
                                    <tr>
                                        <th>@lang('labels.name')</th>
                                        <th>@lang('labels.phone')</th>
                                        <th>@lang('labels.appointment_date')</th>
                                        <th>@lang('labels.source')</th>
                                        <th>@lang('labels.status')</th>
                                        <th>@lang('labels.action')</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($brl_list as $brl)
                                        <tr>
                                            <td>
                                                <a href="{{url('cf-back/leads/'.$brl->id)}}">{{$brl->fullname}}</a>
                                            </td>
                                            <td>
                                                {{$brl->phone}}
                                            </td>
                                            <td>
                                                @if(!empty($brl->buddyreferrallead_guestappointment))
                                                    @foreach($brl->buddyreferrallead_guestappointment as $x)
                                                        {{date('d F Y', strtotime($x->fa_date))}}
                                                    @endforeach
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($brl->buddyreferrallead_membersource->source))
                                                    {{$brl->buddyreferrallead_membersource->source}}
                                                @else
                                                    -
                                                @endif
                                            </td>
                                            <td>
                                                @if(!empty($brl->buddyreferrallead_guestappointment))
                                                    @if(!empty($brl->buddyreferrallead_guestappointment->guestappointment_gastatus))
                                                        {{$brl->buddyreferrallead_guestappointment->guestappointment_gastatus->gastatus_appointmentstatus->status}}
                                                    @else
                                                        Sudah Membuat Appointment
                                                    @endif
                                                @else
                                                    Belum Appointment
                                                @endif
                                            </td>
                                            <td>
                                                @if(empty($brl->buddyreferrallead_guestappointment))
                                                    <a href="{{url('first-assessment/br/create/'.$brl->id)}}"
                                                       class="btn btn-sm btn-success">@lang('labels.create') @lang('labels.appointment') @lang('labels.first_assessment')</a>
                                                @else
                                                    @if(!empty($brl->buddyreferrallead_guestappointment->guestappointment_gastatus) && ($brl->buddyreferrallead_guestappointment->guestappointment_gastatus->status_id == '6'))
                                                        <a href="{{url('first-assessment/br/create/'.$brl->id)}}"
                                                           class="btn btn-sm btn-success">@lang('labels.create') @lang('labels.appointment') @lang('labels.first_assessment')</a>
                                                    @endif
                                                @endif
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
        </div>
    </div>

    {{--MODAL TAMBAH--}}
    <button type="button" id="btn-pop-add" class="hidden" data-toggle="modal"
            data-target="#add-appointment-type"></button>

    <!-- Modal -->
    <div class="modal fade" id="add-appointment-type" role="dialog">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">@lang('labels.add') @lang('labels.lead')</h4>
                </div>
                <div class="modal-body">
                    <div>
                        {!! Form::open(['url' => 'leads', 'id' => 'formcountry']) !!}
                        <div class="form-group">
                            {!! Form::label('name', trans('labels.full_name'), ['class' => 'control-label']) !!}
                            {!! Form::text('name', null, ['class' => 'form-control', 'placeholder' => 'Masukkan Nama Lengkap']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('phone', 'Nomor Telepon', ['class' => 'control-label']) !!}
                            {!! Form::text('phone', null, ['class' => 'form-control', 'placeholder' => 'Masukkan Nomor Telepon']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('cellphone', 'Nomor Ponsel', ['class' => 'control-label']) !!}
                            {!! Form::text('cellphone', null, ['class' => 'form-control', 'placeholder' => 'Masukkan Nomor Ponsel']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('email', 'Email', ['class' => 'control-label']) !!}
                            {!! Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'Masukkan Email']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('source_id', 'Source', ['class' => 'control-label']) !!}
                            @if(count($sources) > 0):
                            {!! Form::select('source_id', $sources, null, ['class' => 'form-control', 'placeholder' => 'Source', 'id' => 'sources']) !!}
                            @else
                                <p>Belum ada Source</p>
                            @endif
                        </div>
                        <div class="form-group" id="br-group" style="display: none;">
                            {!! Form::label('br_id', 'Pilih Buddy Referral', ['class' => 'control-label']) !!}
                            <div id="br-select"></div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('country_id', 'Pilih Negara', ['class' => 'control-label']) !!}
                            @if(count($countries) > 0):
                            {!! Form::select('country_id', $countries, null, ['class' => 'form-control', 'placeholder' => 'Pilih Negara', 'id' => 'countries']) !!}
                            @else
                                <p>Belum ada Negara</p>
                            @endif
                        </div>
                        <div class="form-group" id="province-group" style="display: none;">
                            {!! Form::label('province_id', 'Pilih Provinsi', ['class' => 'control-label']) !!}
                            <div id="province-select"></div>
                        </div>
                        <div class="form-group" id="city-group" style="display: none;">
                            {!! Form::label('city_id', 'Pilih Kota', ['class' => 'control-label']) !!}
                            <div id="city-select"></div>
                        </div>
                        <div class="form-group">
                            {!! Form::label('address', 'Alamat', ['class' => 'control-label']) !!}
                            {!! Form::text('address', null, ['class' => 'form-control', 'placeholder' => 'Masukkan Alamat']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('postal', 'Kode Pos', ['class' => 'control-label']) !!}
                            {!! Form::text('postal', null, ['class' => 'form-control', 'placeholder' => 'Masukkan Kode Pos']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::submit('Selesai', ['class' => 'btn btn-primary form-control']) !!}
                        </div>
                        {!! Form::close() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
    {{--MODAL TAMBAH--}}
@endsection

@section('other-script')
    <meta name="_token" content="{!! csrf_token() !!}"/>
    <script src={{asset("js/jquery.dataTables.min.js")}}></script>
    <script src={{asset("js/dataTables.bootstrap.min.js")}}></script>
    <script src={{asset("vendors/moment/min/moment.min.js")}}></script>
    <script src={{asset("vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js")}}></script>
    <script>
        $('#data-table').dataTable();

        $('#btn-add').click(function () {
            $('#btn-pop-add').click();
        });
    </script>
    <script src="{{asset('js/ajax-crud.js')}}"></script>
@endsection
