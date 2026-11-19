@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <style>
        .home-btn-title{
            font-family: 'Alegreya Sans', sans-serif;
        }

        .row{
            padding-top: 10px;
            padding-left: 20px;
            padding-right: 20px;
        }
    </style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/locker/locker-sale-list',Auth::user()->club_id)}}" style="left: 25px; top: 5px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.locker_list')</h2>
                </center>
            </div>
        </div>
        <div class="row">
            <div class="col-md-9">
                <div id="filter-zone">
                    {!! Form::open(['method' => 'GET', 'action' => ['LockerController@locker_sale_list_filtered']]) !!}
                    {!! Form::hidden('lockerinfo_id', $lockerinfo_id, ['class' => 'form-control']) !!}
                    <div class="form-group">
                        <label class="radio-inline">
                            {!! Form::radio('duration_type', 'daily', false) !!} @lang('labels.daily')
                        </label>
                        <label class="radio-inline">
                            {!! Form::radio('duration_type', 'monthly', true) !!} @lang('labels.monthly')
                        </label>
                        <label class="radio-inline">
                            {!! Form::radio('duration_type', 'yearly', true) !!} @lang('labels.yearly')
                        </label>
                        <label class="radio-inline">
                            {!! Form::radio('duration_type', 'All', true) !!} @lang('labels.all')
                        </label>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            {!! Form::text('s', null, ['class' => 'form-control', 'placeholder' => trans('labels.input_search')]) !!}
                            <span class="input-group-btn">
                            <button class="btn btn-default btn-filter-zone" type="submit"><span class="fa fa-search"></span></button>
                        </span>
                        </div>
                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                @if(count($lockers) > 0)
                    @foreach($lockers as $locker)
                            {!! Form::open(['method' => 'GET', 'action' => ['LockerController@locker_cart']]) !!}
                            <div class="thumbnail">
                                <h4>{{$locker->name}}</h4>
                                <h5><div class="my_rupiah">{{$locker->price}}</div></h5>
                                {!! Form::hidden('id', $locker->id, ['class' => 'form-control']) !!}
                                {!! Form::hidden('lockerinfo_id', $lockerinfo_id, ['class' => 'form-control']) !!}
                                <div class="form-inline">
                                    <div class="form-group">
                                        {!! Form::label('qty', trans('labels.quantity').': ', ['class' => 'control-label']) !!}
                                        {!! Form::number('qty', '1', ['class' => 'form-control']) !!}
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary center-block"><span class="fa fa-cart-plus"></span> @lang('labels.buy')</button>
                            </div>
                            {!! Form::close() !!}
                    @endforeach
                @endif
            </div>
            <div class="col-md-6">
                @if(count($memberlockers) > 0)
                    @foreach($memberlockers as $memberlocker)
                        <div class="thumbnail">
                            <h2>{{$memberlocker->memberlocker_user->name}}</h2>
                            <h4>{{Carbon\Carbon::parse($memberlocker->from_date)->format('d-m-Y')}} - {{Carbon\Carbon::parse($memberlocker->thru_date)->format('d-m-Y')}}</h4>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </section>
@endsection
<script>
    $(document).ready(function () {
        init_format();
    });


    function init_format() {
        $('.my_rupiah').each(function () {
            var x = rupiah_formater($(this).text().replace(/\s/g, ''));
            $(this).text('Rp.' + (x));
        });
        $('.rupiah').each(function () {
            var x = rupiah_formater($(this).val().replace(/\s/g, ''));
            $(this).val((x));
        });
    }
</script>