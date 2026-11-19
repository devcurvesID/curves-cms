@extends('cf_back.layouts.master-dashboard')

@section('title', 'Member Appointment')

@section('other-style')
    <link href="{{asset('vendors/iCheck/skins/flat/green.css')}}" rel="stylesheet">
    <link href={{asset("vendors/select2/dist/css/select2.min.css")}} rel="stylesheet">
@endsection

@section('content')
    <div class="container">

        @include('cf_back.flash-message')
        @include('cf_back.error-message')

        <div class="my-section">
            {!! Form::open() !!}
            <div class="section-title">
                <center>
                    <h1 class="page-title">@lang('labels.lead')</h1>
                </center>
            </div>
            <div class="col-md-3 col-sm-6 col-xs-12 row pull-right staff-identitity">
                <div class="row">
                    {{--<div class="form-group col-xs-12">--}}
                    {{--<div class="col-xs-4">--}}
                    {{--{!! Form::label('staff', trans('labels.staff'), ['class' => 'control-label']) !!}--}}
                    {{--</div>--}}
                    {{--<div class="col-xs-8">--}}
                    {{--{!! Form::select('staff', $staffs, $staff->id, ['class' => 'form-control js-state', 'id' => 'select_staff', 'placeholder' => trans('labels.choose').' '.trans('labels.staff')]) !!}--}}
                    {{--</div>--}}
                    {{--</div>--}}
                    <div class="form-group col-xs-12">
                        <div class="col-xs-4">
                            {!! Form::label('fa_date', trans('labels.date'), ['class' => 'control-label']) !!}
                        </div>

                        <?php
                        $tanggal_fa = date('d/m/Y');
                        if($guest_detail != null){
                            if($guest_detail->fa_date == ""){
                                $tanggal_fa = date('d/m/Y');
                            }else{
                                $tanggal_fa = date('d/m/Y', strtotime($guest_detail->fa_date));
                            }
                        }
                        ?>

                        <div class="col-xs-8">
                            {!! Form::text('fa_date', $tanggal_fa, ['id' => 'tanggal-fa', 'class' => 'form-control']) !!}
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="row x_title">
                            <div class="col-xs-6">
                                <h2>@lang('labels.fitness_assessment')</h2>
                            </div>
                            <div class="col-md-6">
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    </li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="x_content">
                            <div>
                                <div class="form-group">
                                    {!! Form::label('full_name', trans('labels.fullname'), ['class' => 'control-label']) !!}
                                    {!! Form::text('full_name', $br->fullname, ['class' => 'form-control', 'placeholder' => trans('labels.input') . ' ' . trans('labels.fullname')]) !!}
                                </div>
                                <div class="form-group">
                                    {!! Form::label('phone', trans('labels.phone'), ['class' => 'control-label']) !!}
                                    {!! Form::text('phone', $br->phone, ['class' => 'form-control', 'placeholder' => trans('labels.input') . ' ' . trans('labels.phone')]) !!}
                                </div>
                                <div class="form-group">
                                    {!! Form::label('birthday', trans('labels.birthday'), ['class' => 'control-label']) !!}
                                    {!! Form::text('birthday', $guest_detail != "" ? date('d/m/Y', strtotime($guest_detail->birth)) : "", ['class' => 'form-control date', 'placeholder' => trans('labels.input') . ' ' . trans('labels.birthday')]) !!}
                                </div>
                                <div class="form-group">
                                    {!! Form::label('email', trans('labels.email'), ['class' => 'control-label']) !!}
                                    {!! Form::text('email', $guest_detail->email, ['class' => 'form-control', 'placeholder' => trans('labels.input') . ' ' . trans('labels.email')]) !!}
                                </div>
                                <div class="form-group">
                                    {!! Form::label('occupation', trans('labels.occupation'), ['class' => 'control-label']) !!}
                                    {!! Form::text('occupation', $guest_detail->occupation, ['class' => 'form-control', 'placeholder' => trans('labels.input') . ' ' . trans('labels.occupation')]) !!}
                                </div>
                                <div class="form-group">
                                    {!! Form::label('status', trans('labels.status'), ['class' => 'control-label']) !!}
                                    <br/>
                                    <input name="status" type="radio" value="Single" class="flat"
                                    <?php
                                        if ($guest_detail == "[]") {
                                            echo 'checked';
                                        } else {
                                            if ($guest_detail->status == "Single") {
                                                echo 'checked';
                                            }
                                        }
                                        ?>
                                    > @lang('labels.single')
                                    <br/>
                                    <input name="status" type="radio" value="Married"
                                           class="flat" <?php
                                        if ($guest_detail->status == "Married") {
                                            echo 'checked';
                                        }
                                        ?>
                                    > @lang('labels.married')
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="row x_title">
                            <div class="col-xs-6">
                                <h2>@lang('labels.emergency_contact')</h2>
                            </div>
                            <div class="col-md-6">
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    </li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="x_content">
                            <div>
                                <div class="form-group">
                                    {!! Form::label('emergency_name', trans('labels.emergency_name'), ['class' => 'control-label']) !!}
                                    {!! Form::text('emergency_name', $emergency_contact->family_name, ['class' => 'form-control', 'placeholder' => trans('labels.input') . ' ' . trans('labels.emergency_name')]) !!}
                                </div>
                                <div class="form-group">
                                    {!! Form::label('relation', trans('labels.relation'), ['class' => 'control-label']) !!}
                                    {!! Form::text('relation', $emergency_contact->relation, ['class' => 'form-control', 'placeholder' => trans('labels.input') . ' ' . trans('labels.relation')]) !!}
                                </div>
                                <div class="form-group">
                                    {!! Form::label('emergency_phone', trans('labels.emergency_phone'), ['class' => 'control-label']) !!}
                                    {!! Form::text('emergency_phone', $emergency_contact->emergency_contact_number, ['class' => 'form-control', 'placeholder' => trans('labels.input') . ' ' . trans('labels.emergency_phone')]) !!}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 col-sm-12 col-xs-12">
                    <div class="x_panel">
                        <div class="row x_title">
                            <div class="col-xs-6">
                                <h2>@lang('labels.health_history')</h2>
                            </div>
                            <div class="col-md-6">
                                <ul class="nav navbar-right panel_toolbox">
                                    <li><a class="collapse-link"><i class="fa fa-chevron-up"></i></a>
                                    </li>
                                </ul>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="x_content">
                            <div>
                                <div class="form-group">
                                    @foreach($attributes as $x)
                                        @php($checked = '')
                                        @foreach($saved_attrs as $save)
                                            <?php
                                                if($x->id == $save->member_attribute_id){
                                                    $checked = 'checked';
                                                }
                                            ?>
                                        @endforeach
                                            <div class="col-sm-4 col-xs-6">
                                                <input name="member_attributes[]" type="checkbox" value="{{$x->id}}"
                                                       class="flat" {{$checked}}> <span
                                                        class="nama-attribute">@lang('labels.'.strtolower($x->attribute))</span>
                                            </div>
                                    @endforeach

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="form-group">
                <br/>
                {!! Form::submit('Selesai', ['class' => 'btn btn-primary form-control']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </div>
@endsection

@section('other-script')
    <script src={{asset("js/jquery.dataTables.min.js")}}></script>
    <script src={{asset("js/dataTables.bootstrap.min.js")}}></script>
    <script src={{asset("vendors/moment/min/moment.min.js")}}></script>
    <script src={{asset("vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js")}}></script>

    <script src="{{asset('vendors/fastclick/lib/fastclick.js')}}"></script>
    <script src="{{asset('vendors/iCheck/icheck.min.js')}}"></script>
    <script src={{asset("vendors/select2/dist/js/select2.js")}}></script>
    <script>
        $('#data-table').dataTable();
        $('#select_staff').select2();

        $('#tanggal-fa').datetimepicker({
            format: 'DD/MM/YYYY',
            defaultDate: '<?php echo date('d/M/Y')?>',
        });
        $('.date').datetimepicker({
            format: 'DD/MM/YYYY',
        });

        $('.nama-attribute').each(function () {
            var x = $(this).text().replace('labels.', '');
            $(this).text(x);
            $(this).css('textTransform', 'capitalize');
        });
    </script>
@endsection
