@extends('cf_lite.layouts.staff-dashboard-template')

@section('other-style')
    <link href="{{asset('css/staff-home.css')}}" rel="stylesheet">
    <link href="{{asset('select2\dist\css\select2.min.css')}}" rel="stylesheet">
    <style type="text/css">
        
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


<style>
    .select2 {
        width: 100% !important;
    }
</style>
@endsection

@section('content')
    @include('cf_lite.includes.flash-message')
    <section id="staff-home">
        <div class="row">
            <div class="col-sm-12">
                <a href="{{url('cf-lite/locker/choose-packet',$lockerinfo_id)}}" style="left: 25px; top: 5px;" class="btn btn-default btn-back-to" title="@lang('labels.back')"><span class="glyphicon glyphicon-chevron-left" style="margin-top: 10px; color: deeppink;"></span></a>
                <center>
                    <h2 class="page-title">@lang('labels.cart')</h2>
                </center>
            </div>
        </div>
        {!! Form::open(['method' => 'GET', 'action' => 'LockerController@cart_payment']) !!}
        {!! Form::hidden('locker_id', $locker->id) !!}
        {!! Form::hidden('lockerinfo_id', $lockerinfo_id) !!}
        <div class="row">
            <div class="col-sm-12">
                <div class="col-md-6">@lang('labels.member_name')</div>
                <select name = "member" class="form-control">
                    @foreach($members as $member)
                        <option value="{{$member->id}}">{{$member->name}} - {{$member->email}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                    <ul class="list-group">
                        <li>
                            <div class="media">
                                <div class="media-body">
                                    <div class="col-md-6">@lang('labels.locker_name')</div>
                                    <div class="col-md-2">@lang('labels.price')</div>
                                    <div class="col-md-2">@lang('labels.subtotal')</div>
                                </div>
                            </div>
                        </li>
                        <li class="list-group-item">
                            <div class="media">
                                <div class="media-body">
                                    <h4 class="media-heading">{{$locker->name}}</h4>
                                    <div class="col-md-6">
                                        <table>
                                            <tr>
                                                <td>{!! Form::label('qty', trans('labels.quantity').': ', ['class' => 'control-label']) !!} </td>
                                                <td>{!! Form::number('qty', $qty, ['class' => 'form-control']) !!}</td>
                                            </tr>
                                        </table>
                                    </div>
                                    <div class="col-md-2 my_rupiah">{{$locker->price}}</div>
                                    <div class="col-md-2 my_rupiah">{{$qty * $locker->price}}</div>
                                </div>
                            </div>
                        </li>
                    </ul>
            </div>
            <div class="col-md-12">
            {!! Form::submit(trans('labels.save'), ['class' => 'btn btn-primary pull-right']) !!}
            </div>
            {!! Form::close() !!}
        </div>
    </section>
@endsection

@section('other-script')
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


    <script src="{{asset('select2\dist\js\select2.min.js')}}"></script>
    <script type="text/javascript">
        $('select[name="member"]').select2();
    </script>

    <script src="{{asset('js/my_js.js')}}"></script>
@endsection
